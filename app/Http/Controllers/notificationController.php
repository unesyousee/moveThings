<?php

namespace App\Http\Controllers;

use App\CarrierUser;
use App\User;
use Illuminate\Http\Request;
use App\Order;;
use App\Transaction;
use App\Notification;
use JWTAuth;
use App\Jobs\ProcessNotification;

class notificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications.index');
    }

    public function drivers(Request $request)
    {
        $drivers=CarrierUser::all();
        foreach ($drivers as $driver){
            if($driver->user->id) {
                ProcessNotification::dispatch(['title' => $request->title, 'body' => $request->body], $driver->user)->onQueue('notification');
            }
        }
        return back();
    }
    public function users(Request $request)
    {
        $users=User::doesntHave('carrierUsers')->get();
        foreach ($users as $user)
            ProcessNotification::dispatch(['title' => $request->title,'body' => $request->body], $user)->onQueue('notification');
        return back();
    }
    public function user(Request $request)
    {
        $user = User::where('id',$request->id)->get()->first();
        ProcessNotification::dispatch(['title' => $request->title,'body' => $request->body], $user,true)->onQueue('notification');
        return back();
    }
	public function getUserNotification(Request $request){

    	$user = JWTAuth::parseToken()->toUser();
        $user_id= $user->id;
        if ($user->id) {
			$order = Order::where('status','2')->where('user_id', $user_id)->get()->first();
			if (isset($order->id)) {

			

			$driver = $order->carrierUsers()->where('parent_id', null)->first()->user->first_name.' '. $order->carrierUsers()->where('parent_id', null)->first()->user->last_name;
			$order = $order->id;
			$notifs = Notification::where('user_id', $user_id)->get(['order_id'])->pluck('order_id')->toArray();
		    $new_order = false;
			
				if(!in_array($order, $notifs )){
					$new_order = true;

			    	$notif = new Notification;
			    	$notif->order_id = $order;
			    	$notif->user_id = $user_id;
			    	$notif->save();
				}
			
	    	if ($new_order) {
	        		$status = "200";
	        		$message = "اطلاعات با موفقیت ارسال شد";
    		        $data =array(
    		        	"content" => "سفارش شما در سامانه نوبار توسط ". $driver ." پذیرفته شد",
    		        	"id" => rand(1000, 9999)
    		        	);
	        	}else{

	        		$status = "404";
	        		$message = "اعلانی وجود ندارد";
    		        $data = [];
	        	}

	        }else{
        		$status = "201";
        		$message = "اعلانی وجود ندارد";
		        $data = [];
	        }


        }else{
    				$status = "400";
	        		$message = "کاربر یافت نشد";
    		        $data = [];
    	}
    

        $final = [
        	"massege" =>$message,
            "status" => $status,
            "data" => [$data]
        ];
	return $final;
}
    
    public function getNotification(Request $request){

    	$user = JWTAuth::parseToken()->toUser();

    	if ($user->id) {
	        $carrierUser = $user->carrierUsers()->first();
	        $user_id = $user->id;
    		$carrier_id = $carrierUser->carrier_id;
	        $orders = Order::where('status','1')->where('carrier_id', $carrier_id)->get(['id'])->pluck('id');
	        $notifs = Notification::where('user_id', $user_id)->get(['order_id'])->pluck('order_id')->toArray();
	        $new_order = [];
	        foreach ($orders as  $key=>$value) {
	        	if(!in_array($value, $notifs )){
	        		$new_order[$key] = $value;

		        	$notif = new Notification;
		        	$notif->order_id = $value;
		        	$notif->user_id = $user_id;
		        	$notif->save();
	        	}
	        }
	        if (count($new_order) > 0) {
	        		$status = "200";
	        		$message = "اطلاعات با موفقیت ارسال شد";
    		        $data =array(
    		        	"content" => "سفارش جدید دارید ",
    		        	"id" => rand(1000, 9999)
    		        	);
	        	}else{

	        		$status = "404";
	        		$message = "اعلانی وجود ندارد";
    		        $data = [];
	        	}
	        // return response()->json($notifs);
	        // return response()->json($carrierUser->carrier);    	
    	}else{
    				$status = "400";
	        		$message = "کاربر یافت نشد";
    		        $data = [];
    	}
    

	            $final = [
	            	"massege" =>$message,
	                "status" => $status,
	                "data" => [$data]
	            ];
			return $final;
    }
}

