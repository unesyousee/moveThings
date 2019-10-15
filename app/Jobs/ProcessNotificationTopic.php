<?php

namespace App\Jobs;

use App\NotifMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;

class ProcessNotificationTopic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $notifContent, $user, $topic, $store;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notifContent, $user, $topic, $store = false)
    {
        $this->notifContent = $notifContent;
        $this->user = $user;
        $this->topic = $topic;
        $this->store = $store;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $notificationBuilder = new PayloadNotificationBuilder($this->notifContent['title']);
        $notificationBuilder->setBody($this->notifContent['body'])
            ->setSound('default');

        $notification = $notificationBuilder->build();

        $topic = new Topics();
        $topic->topic($this->topic);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

        if ($this->store){
            $notif = new NotifMessage();
            $notif->topic = $this->topic;
            $notif->title = $this->notifContent['title'];
            $notif->body = $this->notifContent['body'];
            $notif->save();
        }

//        Log::info("Notif sent to topic ".$this->topic.$topicResponse->isSuccess());
//        $topicResponse->shouldRetry();
//        $topicResponse->error();
    }
}
