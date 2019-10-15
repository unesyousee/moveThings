<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNotification;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class AlertController extends Controller
{
    public function disk()
    {
        $logs = `du -ab /home/nobaar/server/storage/logs/ | sort -n -r | head -n 1|xargs echo |cut -f1 -d' '`;
        if ($logs > 1000)
            $logs = (int)$logs / 1000 . 'کیلوبایت';
        elseif ($logs > 1000000)
            $logs = (int)$logs / 1000000 . 'گیگابایت';
        elseif ($logs < 1000)
            $logs = (int)$logs / 1000000 . 'بایت';
        $free = `df -h |grep sda2 | xargs echo | cut -f4 -d' '`;
        $body = 'حجم باقیمانده سرور' . $free;
        $title = 'سایز بزرگترین لاگ' . $logs;
        ProcessNotification::dispatch(['title' => $body, 'body' => $title], $user = \App\User::find(8))->onQueue('notification');
        ProcessNotification::dispatch(['title' => $body, 'body' => $title], $user = \App\User::find(1710))->onQueue('notification');
        ProcessNotification::dispatch(['title' => $body, 'body' => $title], $user = \App\User::find(1402))->onQueue('notification');
        return 'ok';
    }

    public function successBackUp()
    {
        ProcessNotification::dispatch(['title' => 'پشتبان گیری', 'body' => 'پشتیبان گیری با موفقیت انجام شد'], $user = \App\User::find(8))->onQueue('notification');
        //  ProcessNotification::dispatch(['title' => 'پشتبان گیری', 'body' => 'پشتیبان گیری با موفقیت انجام شد'], $user = \App\User::find(1710))->onQueue('notification');
    }

    public function failBackUp()
    {
        ProcessNotification::dispatch(['title' => 'پشتبان گیری', 'body' => 'پشتیبان گیری با مشکل مواجه شده است'], $user = \App\User::find(8))->onQueue('notification');
        ProcessNotification::dispatch(['title' => 'پشتبان گیری', 'body' => 'پشتیبان گیری با مشکل مواجه شده است'], $user = \App\User::find(1710))->onQueue('notification');
    }

    public function driverAlert()
    {
        $tody_orders = Order::whereDate('moving_time', gmdate('Y-m-d', time()))->orderBy('moving_time')->where('status', 2)->paginate(15, ['*'], 'today');
        foreach ($tody_orders as $order) {
            $diff = strtotime($order->moving_time) - time();
            $diff = (int)$diff;
            $msg =  'باربری شما تا' . floor( (int)$diff / 60) . 'دقیقه آینده اغاز خواهد شد لطفا جهت رسیدن سر وقت به محل بارگیری برنامه ریزی های لازم را انجام دهید';
            $cache = Cache::get("order" . (string)$order->id, true);
            if (($diff > 0 && $diff < 7200) && $cache) {
//                dump($order->id .' ====>'.$diff);
                Cache::add("order".(string)$order->id, false, 50);
                ProcessNotification::dispatch(['title' => 'راننده گرامی نوبار ', 'body' => $msg], $user = $order->carrierUsers->first()->user)->onQueue('notification');
            }
        }
    }

    public function discountToFirstUsers()
    {
        $users = User::whereHas('orders', function ($q) {
            $q->where('status', 0);
        })->where(function ($q) {
            $q->whereBetween('created_at', [now()->subDays(8)->toDateTimeString(), now()->toDateTimeString()]);
        })->whereDoesntHave('orders', function ($q) {
            $q->whereIn('status', [1, 2, 3, 4, 5, 6]);
        })->get();
        foreach ($users as $user){
            \Log::info("sending discount code to ".$user->phone);
        }
    }
}
