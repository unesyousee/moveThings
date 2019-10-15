<?php

namespace App\Providers;

use App\Events\OrderUpdateEvent;
use App\Events\OrderUpdateLog;
use App\Events\OrderUpdating;
use App\Listeners\OrderSubscriber;
use App\Listeners\SendWorkerSms;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderUpdating::class=>[
            SendWorkerSms::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
       OrderUpdateEvent::class =>[
            'App\Listeners\OrderUpdateNotification'
        ],
        OrderUpdateLog::class=>[
            'App\Listeners\UpdateLogger'
            ]
    ];
    protected $subscribe=[
            OrderSubscriber::class
        ];
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
