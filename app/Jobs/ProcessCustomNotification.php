<?php

namespace App\Jobs;


use App\FcmRegisterId;
use App\NotifMessage;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class ProcessCustomNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $notifContent, $user, $store;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notifContent, $user, $store = false)
    {
        $this->notifContent = $notifContent;
        $this->user = $user;
        $this->store = $store;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->status == 0)
            return;
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($this->notifContent['title']);
        $notificationBuilder->setBody($this->notifContent['body'])
            ->setSound('default');

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        // start data builder

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(
            [
                'a_data' => 'my_data',
                'url' => "https://google.com",
            ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // end data builder
        $tokens = $this->user->registrationIds->pluck('registration_id')->toArray();
        foreach ($tokens as $token)
            Log::info('notification send to ' . $this->user->phone . ' ' . $this->user->first_name . ' ' . $this->user->last_name);
        if (count($tokens) == 0) {
            return;
        }

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $tokensToModify = $downstreamResponse->tokensToModify();
        foreach ($tokensToModify as $oldToken => $newToken) {
            $reg = FcmRegisterId::where('registration_id', $oldToken)->first();
            $reg->registration_id = $newToken;
            $reg->save();
        }
        $downstreamResponse->tokensToRetry();

        if ($this->store) {
            $notif = new NotifMessage();
            $notif->user_id = $this->user->id;
            $notif->title = $this->notifContent['title'];
            $notif->body = $this->notifContent['body'];
            $notif->save();
        }
        /*
        //        Log::info("Sending notif for user #".$this->user->id);
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60*20);

                $notificationBuilder = new PayloadNotificationBuilder($this->notifContent['title']);
                $notificationBuilder->setBody($this->notifContent['body'])
                    ->setSound('default');

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();

                $token = $this->user->registration_id;

                $downstreamResponse = FCM::sendTo($token, $option, $notification);

                $tokensToModify = $downstreamResponse->tokensToModify();
                foreach ($tokensToModify as $oldToken => $newToken) {
                    $user = User::where('registration_id', $oldToken)->first();
                    $user->registration_id = $newToken;
                    $user->save();
        //            $reg = FcmRegisterId::where('registration_id', $oldToken)->first();
        //            $reg->registration_id=$newToken;
        //            $reg->save();
                }


                $tokensToRetry = $downstreamResponse->tokensToRetry();*/


        $tokensToRetry = $downstreamResponse->tokensToRetry();

//        Log::info("Sent notif for user #".$downstreamResponse->numberSuccess());
//        Log::info("Couldn't send notif for user #".$downstreamResponse->numberFailure());
//        foreach ($downstreamResponse->tokensWithError() as $token => $error)
//            Log::info($token.' => '.$error);
//        } while (!empty($tokensToRetry));*/

    }
}
