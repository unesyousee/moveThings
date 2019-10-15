<?php

namespace App\Http\Controllers;

use App\Carrier;
use Carbon\Carbon;
use App\Discount;
use Illuminate\Http\Request;
use App\DiscountUsage;
use App\Order;
use App\User;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use App\Price;
use function MongoDB\BSON\fromJSON;

class DiscountController extends Controller
{
    public function index()
    {
        $now = Carbon::now()->toDateString();
        $carriers = Carrier::all();
        $discounts = Discount::whereDate("expire_time", '>', $now)->orderBy('id', 'DESC')->paginate(50);
        return view('admin.discount.index', compact('discounts', 'carriers'));
    }

    public function store(Request $request)
    {
        if ($request->has('amount')) {
            $expire = $request->expire ?? 1;
            $amount = str_replace(',', '', $request->amount) ?? 0;
            $number = $request->number ?? 0;
            $limit = $request->limit ?? 1;
            $date = Carbon::now()->addDays($expire)->toDateString() . ' 23:59:59';
            if ($request->has('random')) {
                $num = $request->number ?? 1;

                for ($i = 0; $i < $num; $i++) {

                    $discount = new Discount();
                    $discount->expire_time = $date;
                    $discount->type = 0;
                    $discount->status = 1;
                    $discount->amount = $amount;
                    $discount->Limitations = 1;
                    $discount->carrier_id = $request->carrier ?? 2;
                    $discount->code = str_random(15);
                    $discount->save();
                }
            } elseif ($request->has('code')) {

                $discount = new Discount();
                $discount->expire_time = $date;
                $discount->type = 0;
                $discount->status = 1;
                $discount->amount = $amount;
                $discount->Limitations = $limit;
                $discount->code = $request->code;
                $discount->carrier_id = $request->carrier;
                $discount->save();


            } else {

                return '<h1>کد وارد نشده است</h1>';
            }


            return back();
        }
        return 'قیمت وارد نشده است';

    }

    public function destroy(Request $request)
    {
        if ($request->has('multiDiscount')) {
//            Discount::find(array_keys($request->multiDiscount))->delete();
            Discount::whereIn('id', array_keys($request->multiDiscount))->delete();
        }
        return back();
    }

    public function checkDiscount(Request $request)
    {

        $user = JWTAuth::parseToken()->toUser();

        $bon = $request->bon;
        $order_id = $request->order_id;
        $share_code = User::where('share_code', $bon)->first();
        if ($share_code != null) {

            $is_usage = DiscountUsage::where('user_id', $user->id)->where('share_code', $bon)->where('status', 1)->first();

            if ($is_usage == null) {

                $amount_discount = Price::where('title', 'کد معرف')->first();

                $order = Order::find($order_id);
                if ($amount_discount->status == 0) {
                    $new_price = $order->price - $amount_discount->amount;
                } else {
                    $new_price = $order->price - ($order->price * $amount_discount->amount / 100);
                }
                $discount_usage = new DiscountUsage;
                $discount_usage->order_id = $order_id;
                $discount_usage->share_code = $bon;
                $discount_usage->status = 0;
                $discount_usage->user_id = $user->id;
                $discount_usage->save();

                $status = "200";
                $message = "کد معرف با موفقیت اعمال شد";

                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $new_price
                );


            } else {

                $status = "400";
                $message = "شما قبلا از این کد معرف استفاده کرده اید";

                $final = array(
                    "status" => $status,
                    "message" => $message
                );

            }


        } else {

            $discount = Discount::where('code', $bon)->where('status', 1)->first();
            if ($discount == null) {
                $status = "400";
                $message = "کد تخفیف صحیح نمی باشد و یا اینکه منقضی شده است";

                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

            if (!isset($final)) {
                date_default_timezone_set("Asia/Tehran");
                $now = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                $expire_time = date_create_from_format('Y-m-d H:i:s', $discount->expire_time);
                if ($now > $expire_time) {
                    $status = "402";
                    $message = "کد تخفیف منقضی شده است";

                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }

            }


            if (!isset($final)) {

                $is_usage = DiscountUsage::where('user_id', $user->id)->where('discount_id', $discount->id)->where('status', 1)->first();
                if ($is_usage != null) {
                    $status = "400";
                    $message = "شما قبلا از این کد تخفیف استفاده کرده اید";

                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }


            }

            if (!isset($final)) {
                if ($discount->usage_number >= $discount->Limitations) {
                    $status = "400";
                    $message = "استفاده از این کد تخفیف به سقف مجاز خود رسیده است.";

                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }
            }


            if (!isset($final)) {
                $order = Order::find($order_id);
                if ($discount->carrier_id != 0 && $discount->carrier_id != $order->carrier_id) {
                    $status = "400";
                    $message = "کد تخفیف متعلق به خدمات دیگری میباشد";
                    $final = array(
                        "status" => $status,
                        "message" => $message

                    );
                }
            }

            if (!isset($final)) {

                $order = Order::find($order_id);
                if ($discount->type == 0) {
                    $new_price = $order->price - $discount->amount;
                } else {
                    $new_price = $order->price - ($order->price * $discount->amount / 100);
                }


                if ($request->has("platform") && $request->platform == "web") {

                    $discount->usage_number += 1;
                    $discount->save();


                    $discount_usage = new DiscountUsage;
                    $discount_usage->order_id = $order_id;
                    $discount_usage->discount_id = $discount->id;
                    $discount_usage->status = 1;
                    $discount_usage->user_id = $user->id;
                    $discount_usage->save();

                } else {
                    $discount_usage = new DiscountUsage;
                    $discount_usage->order_id = $order_id;
                    $discount_usage->discount_id = $discount->id;
                    $discount_usage->status = 0;
                    $discount_usage->user_id = $user->id;
                    $discount_usage->save();
                }

                $status = "200";
                $message = "کد تخفیف با موفقیت اعمال شد";

                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $new_price
                );


            }

        }

        return response()->json($final);

    }

    public function deleteDiscount(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $order_id = $request->order_id;

        $discount_usage = DiscountUsage::where('order_id', $order_id)->where('status', 0)->first();
        if (!is_null($discount_usage)) {
            $discount_usage->status = 2;
            $discount_usage->save();
            $order = Order::find($order_id);
            $status = "200";
            $message = "کد تخفیف با موفقیت حذف شد";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => $order
            );
        } else {
            $status = "404";
            $message = "کد تخفیفی برای این سفارش پیدا نشد";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }

    public function show($id)
    {
        $discount = Discount::find($id);
        return view('admin/discount/show', compact('discount'));
    }

    public function thirdDiscount(Request $request)
    {
        $order = Order::find($request->id);
        if(is_null($order->thirdparty_id))
            return back()->with('alert', ['این سفارش طرف سوم ندارد']);
        $price = str_replace(',','',$request->price);

        $discountData = [
            "code" => str_random(10),
            "amount" => $price,
            "Limitations" => 1,
            "usage_number" => 1,
            "expire_time" => now()->toDateTimeString(),
            "type" => 0,
            "carrier_id" => $order->carrier_id,
            "status" => 1
        ];
        $discount = Discount::create($discountData);
        $discountUsegeDate = [
            "discount_id" => $discount->id,
            "user_id" => $order->user_id,
            "status" => $order->user_id,
            "order_id" => $order->id,
        ];
        DiscountUsage::create($discountUsegeDate);
        $t = new TransactionSaver();
        $t->order_id = $order->id;
        $t->thirdDiscount($price);
        return back();
    }

    public function counter(){
        $discount = Discount::where("code", 'onlinejoonb')->first();
        $count = $discount->discountUsages->where("status",1)->count();
        return response()->json([
            "count"=>$count
        ]);
    }
}
