<?php

namespace App\Console;

use App\CarrierUser;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\TransactionSaver;
use App\RatingPenalty;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $sendSms = new AlertController();
        $schedule->call(function() use ($sendSms){$sendSms->discountToFirstUsers();})->dailyAt('16:27');
        foreach (['14:00', '23:00', '09:00'] as $time) {
            $schedule->call(function() use ($sendSms){$sendSms->disk();})->dailyAt($time);
        }
        $schedule->call(function(){$this->ratingPenalty();})->quarterly();
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected function ratingPenalty(){
        $drivers = CarrierUser::whereHas('comments', function ($q) {
            $q->whereDate('created_at', '<', now()->subDays(20)->toDateTimeString());
        })->get();
        foreach ($drivers as $driver) {
            $comments = $driver->comments()->where('rating', '<', 4)->get();
            if ($comments->count() >= 3) {
                $penalties = $driver->ratingPenalty->pluck('comment_id')->toArray();

                $notRecorded = $driver->comments()->where('rating', '<', 4)->whereNotIn('id',$penalties)->pluck('id')->toArray();
                if(count($notRecorded) > 2 ){
                    $insert = [];
                    foreach ($notRecorded as $val){
                        $insert[]= ["comment_id" => $val, "carrier_user_id" => $driver->id, 'created_at'=> now()->toDateTimeString(), 'updated_at'=> now()->toDateTimeString()];
                    }
                    $penalty = new RatingPenalty();
                    $penalty->insert($insert);
                    $t = new TransactionSaver();
                    $t->driver_user_id = $driver->id;
                    $t->RatingPenalty(300000);
                    if(count($penalties) > 5){
                        $user = User::find($driver->ic);
                        $user->status = 0;
                        $user->disabled_at =  date('Y-m-d H:i:s');
                        $user->save();
                    }
                }

            }


        }
    }
}
