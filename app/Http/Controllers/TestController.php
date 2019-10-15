<?php

namespace App\Http\Controllers;

use App\CarrierUser;
use App\FcmRegisterId;
use App\Jobs\ProcessCustomNotification;
use App\Order;
use App\RatingPenalty;
use App\Transaction;
use App\User;
use function foo\func;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Query\Builder;

class TestController extends Controller
{
    public function test()
    {
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


//        $user= User::find(8);
//        ProcessCustomNotification::dispatch([
//            'title' => '🚛سفارش جدید🚚',
//            'body' => '🚛درخواست جدید در نوبار ثبت شد🚚'
//        ], $user)->onQueue('notification');
    }
}
