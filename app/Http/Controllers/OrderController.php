<?php

namespace App\Http\Controllers;

use App\Events\OrderUpdateEvent;
use App\Events\OrderUpdateLog;
use App\Jobs\ProcessNotification;
use App\Jobs\ProcessSendSms;
use App\Observer;
use App\Thirdparty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Address;
use App\Carrier;
use App\CarrierUser;
use App\HeavyThing;
use App\Transaction;
use App\Order;
use App\Price;
use App\StatusLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use App\Discount;
use App\DiscountUsage;

class orderController extends Controller
{
    private $statuses = ['در حال سفارش ', 'جدید', 'پذیرفته شده', 'نیاز به ویرایش', 'شروع باربری', 'اتمام باربری', 'فرایند تکمیل شده', 'لغو شده'];

    public function search(Request $request)
    {
        $heavyThings = HeavyThing::all();
        $drivers = CarrierUser::where('parent_id', null)->get();
        if ($request->has(['start_date', 'end_date'])) {
            $orders = Order::whereBetween('moving_time', [gmdate('y-m-d', $request->start_date / 1000), gmdate('y-m-d', $request->end_date / 1000)])->get();
        } else {
            if ($request->field == 'name') {
                $orders = Order::whereHas('user', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->search . '%');
                })->orWhereHas('user', function ($q) use ($request) {
                    $q->where('last_name', 'like', '%' . $request->search . '%');
                })
                    ->with('user')
                    ->orderBy('created_at', 'desc')->limit(30)->get();
            } elseif ($request->field == 'phone') {
                $orders = Order::whereHas('user', function ($q) use ($request) {
                    $q->where('phone', 'like', '%' . $request->search . '%');
                })
                    ->with('user')
                    ->orderBy('created_at', 'desc')->limit(30)->get();
            } else {
                $orders = Order::Where($request->field, $request->search)
                    ->with('user')
                    ->orderBy('created_at', 'desc')->limit(30)->get();

                return response()->json($orders);
            }


        }
        if (isset($request->json))
            return response()->json($orders);

        return view('admin.orders.search', compact('orders', 'drivers', 'heavyThings'));
    }

    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['supporter', 'manager', 'observer']);
        $heavyThings = HeavyThing::all();
        $thirdparty = Thirdparty::all();
        $drivers = CarrierUser::where('parent_id', null)->get();
        $tody_orders = Order::whereDate('moving_time', gmdate('Y-m-d', time()))->orderBy('moving_time')->where('status', 2)->paginate(40, ['*'], 'today');
        $coming_orders = Order::where('status', '0')->orderBy('id', 'desc')->paginate(40, ['*'], 'coming');
        $new_orders = Order::where('status', '1')->orderBy('id', 'desc')->paginate(40, ['*'], 'new_orders');
        $accepted_orders = Order::where('status', '2')->orderBy('moving_time', 'desc')->paginate(40, ['*'], 'accepted_orders');
        $conflict_orders = Order::where('status', '3')->orderBy('id', 'desc')->paginate(40, ['*'], 'conflict_orders');
        $moving_start_orders = Order::where('status', '4')->orderBy('updated_at', 'desc')->paginate(40, ['*'], 'moving_start_orders');
        $moving_done_orders = Order::where('status', '5')->orderBy('updated_at', 'desc')->paginate(40, ['*'], 'moving_done_orders');
        $all_done_orders = Order::where('status', '6')->orderBy('updated_at', 'desc')->paginate(40, ['*'], 'all_done_orders');
        $cancelled_orders = Order::where('status', '7')->orderBy('updated_at', 'desc')->paginate(40, ['*'], 'cancelled_orders');
        return view('admin/orders/index', compact(
            'heavyThings', 'coming_orders',
            'drivers', 'new_orders', 'accepted_orders', 'tody_orders',
            'conflict_orders', 'moving_done_orders', 'moving_start_orders', 'all_done_orders', 'cancelled_orders', 'thirdparty'
        ));
    }

    public function store(Request $request)
    {
        $order = Order::find($request->id);
        $order->first_name = $request->first_name;
        $order->last_name = $request->last_name;
        $order->address = $request->address;
        $order->phone = $request->phone;
        $order->password = $request->password;
        $order->email = $request->email;
        $order->save();
        return back();
    }

    public function show($id)
    {
        $statuses = ['در حال سفارش ', 'جدید', 'پذیرفته شده', 'نیاز به ویرایش', 'شروع باربری', 'اتمام باربری', 'فرایند تکمیل شده', 'لغو شده'];
        $order = Order::find($id);
        $order->seen = 1;
        $order->save();
        $observers = Observer::all();
        $transactions = Transaction::where('order_id', $order->id)->get();
        $logs = $order->statusLogs;
//        dd($observers);
        return view('admin.orders.show', compact('order', 'statuses', 'observers', 'logs', 'transactions'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (isset($request->change_status)) {
            if ($request->change_status == 7 || $request->change_status == 1) {
                $order_carrierUser = $order->carrierUsers->where('parent_id', null)->first()->id ?? null;
                if ($order_carrierUser != null)
                    $order->carrierUsers()->detach();

            }
            if ($request->change_status == 6) {
                if (!$order->carrierUsers()->count()) {
                    return back()->with('alert', ['سفارش بدون راننده را نمیتواند به این استیت ببرید']);
                }
                if ($order->end_time == null) {
                    return back()->with('alert', ['برای تکمیل فراییند سفارش باید از شروع باربری و پایان باربری گذشته باشد']);
                }
                if (!$order->is_paid) {
                    $t = new TransactionSaver();
                    $t->driver_user_id = $order->carrierUsers->where('parent_id', null)->first()->user->id ?? '';
                    $t->order_id = $order->id;
                    $t->user_id = $order->user->id;
                    $third = $order->thirdparty()->first()->user->id ?? null;
                    // if order from third party
                    if ($third) {
                        $t->third_user_id = $third;
                        if ($order->transaction_type == 1) {
                            // driver get Cache
                            $t->driverGetCash()->ThirdCommission();
                        } elseif ($order->transaction_type == 2) {
                            // nobaar get cash
                            $t->ThirdCommission()->nobaarGetCashDriver();

                        } elseif ($order->transaction_type == 3) {
                            // payment to third party
                            $t->thirdOnlineDriver()->thirdOnlineThird();

                        } elseif ($order->transaction_type == 4) {
                            // online payment to nobaar
                            $t->thirdOnlineDriver()->ThirdCommission();

                        }
                    } else {// if order from nobaar app
                        //updated form panel
                        // cash
                        $t->third_user_id = $third;
                        if ($order->transaction_type == 1) {
                            // driver get Cache
                            $t->driverGetCash();
                        } elseif ($order->transaction_type == 2) {
                            // nobaar get cash
                            $t->nobaarGetCashDriver();

                        } elseif ($order->transaction_type == 3) {
                            // payment to third party
                            return back()->with('alert', ['طرف سومی برای این سفارش تخصیص نیافته است ']);
                        }
                    }
                    $order->is_paid = 1;
                }
                $order->payment_status = 2;
            }
            if ($request->change_status == 4) {
                if (!$order->carrierUsers()->count())
                    return back()->with('alert', ['سفارش بدون راننده را نمیتواند به این استیت ببرید']);
                $order->start_time = date('Y-m-d H:i:s');
                $this->penalty($order);
            }
            if ($request->change_status == 5) {
                if (!$order->carrierUsers()->count())
                    return back()->with('alert', ['سفارش بدون راننده را نمیتواند به این استیت ببرید']);
                $order->end_time = date('Y-m-d H:i:s');

                $start_time = date_create_from_format('Y-m-d H:i:s', $order->start_time);
                $end_time = date_create_from_format('Y-m-d H:i:s', $order->end_time);
                $diff = $start_time->diff($end_time);
                if (((int)($diff->h) > 3) || ((int)($diff->h) == 3 && (int)($diff->i) >= 30)) {
                    $extra_hour = (int)($diff->h) - 3;
                    if ((int)($diff->i) >= 30) {
                        $extra_hour += 1;
                    }
                    $price = $order->price;

                    if ($order->carrier_id == 1) {

                        $price += $extra_hour * Price::where('title', 'ساعت اضافه نیسان')->first()->amount;

                    } elseif ($order->carrier_id == 2) {

                        $price += $extra_hour * Price::where('title', 'ساعت اضافه کامیون')->first()->amount;

                    } elseif ($order->carrier_id == 3) {

                        $price += $extra_hour * Price::where('title', 'ساعت اضافه وانت')->first()->amount;

                    }

                    $price += $order->moving_workers * ($extra_hour * Price::where('title', 'ساعت اضافه کارگر')->first()->amount);

                    $order->price = $price;
                }


            }
            $order->status = $request->change_status;
            $statusLog = new StatusLog();
            $statusLog->status = $request->change_status;
            $statusLog->order()->associate($id);
            $statusLog->save();
            event(new OrderUpdateLog($order, "تغییر وضعیت"));
            $order->save();
            return back();

        } else {

            event(new OrderUpdateEvent($order));
            $carrier = Carrier::find($request->car_id);
            // price
            $price = 0;
            $price += $carrier->price;
            if (intval($request->barbar_worker) > 0) {
                $price = $price + ($request->barbar_worker * Price::where('title', 'کارگر باربر')->first()->amount);
            }
            if (intval($request->chideman_worker) > 0) {
                $price = $price + ($request->chideman_worker * Price::where('title', 'کارگر بسته بندی')->first()->amount);
            }
            if (intval($request->origin_walking) > 10) {

                $price += ($request->origin_walking) * Price::where('title', 'پیاده روی')->first()->amount;
            }
            if (intval($request->destination_walking) > 10) {
                $price += ($request->destination_walking) * Price::where('title', 'پیاده روی')->first()->amount;

            }
            if (intval($request->origin_floor) >= 9 || intval($request->destination_floor) >= 9) {

                $price += Price::where('title', 'بیشتر از 9')->first()->amount * $request->barbar_worker;

            } elseif (intval($request->origin_floor) >= 4 || intval($request->destination_floor) >= 4) {

                $price += Price::where('title', 'بیشتر از 4')->first()->amount * $request->barbar_worker;
            }

            if ($request->has('vasayeleHazinedar')) {
                $vhObj = $request->vasayeleHazinedar;

                foreach ($vhObj as $key => $vh) {
                    $vhRow = HeavyThing::find($key);
                    $price += ($vhRow->price * $vh) * ($request->origin_floor + $request->destination_floor);
                }
            }
            $order->price = $price;
            $order->moving_workers = $request->barbar_worker;
            $order->packing_workers = $request->chideman_worker;
            if ($request->chideman_worker != 0 && $request->has('chideman_worker_gender')) {
                $order->gender = $request->chideman_worker_gender;
            }
            $order->origin_floor = $request->origin_floor;
            $order->dest_floor = $request->destination_floor;
            $order->origin_walking = $request->origin_walking;
            $order->dest_walking = $request->destination_walking;
            if ($request->has('platform')) {
                $order->platform = $request->platform;
            }
            if ($request->has('vasayeleHazinedar')) {
                $vhObj = $request->vasayeleHazinedar;
                $sync = [];
                foreach ($vhObj as $key => $vh)
                    $sync[$key] = ['count' => $vh];
                event(new OrderUpdateLog($order, 'ویرایش اقلام هزینه بر'));
                $order->heavyThings()->sync($sync);
            }
            if ($order->user->phone != '09338931751') {
                ProcessSendSms::dispatch([
                    'phone' => $order->user->phone,
                    'templateId' => "5424",
                    'parameterArray' => array(
                        array('Parameter' => "OrderId", 'ParameterValue' => $order->id),
                        array('Parameter' => "price", 'ParameterValue' => $order->price)

                    )
                ])->onQueue('sms');
            } else {
                ProcessSendSms::dispatch([
                    'phone' => $order->receiver_phone,
                    'templateId' => "5424",
                    'parameterArray' => array(
                        array('Parameter' => "OrderId", 'ParameterValue' => $order->id),
                        array('Parameter' => "price", 'ParameterValue' => $order->price)

                    )
                ])->onQueue('sms');
            }
            if (isset($order->carrierUsers()->where('parent_id', null)->first()->user->phone)) {
                $phone = $order->carrierUsers()->where('parent_id', null)->first()->user->phone;
                ProcessSendSms::dispatch([
                    'phone' => $phone,
                    'templateId' => "5424",
                    'parameterArray' => array(
                        array('Parameter' => "OrderId", 'ParameterValue' => $order->id),
                        array('Parameter' => "price", 'ParameterValue' => $order->price)

                    )
                ])->onQueue('sms');
            }
            event(new OrderUpdateLog($order, "ویرایش از پنل"));
            $order->save();
            return back();

        }

    }

    public function destroy($id)
    {
        Order::find($id)->delete();
        return back();
    }

    public function checkLocation(Request $request)
    {
        $lat = $request->lat;
        $long = $request->long;
        if (env('USE_POLYGON')) {

            //TODO it should be dynamic
            if (checkLocation($lat, $long, 1)) {
                $status = "200";
                $message = "درخواست شماداخل تهران است.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                $status = "406";
                $message = "درخواست شما خارج از تهران است.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://map.ir/reverse?lat=" . $lat . "&lon=" . $long);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'x-api-key: eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ3OTZjODA1ZWMxNTZiM2ZlM2NiMTlmZDEyZTdlYTJiZDhkMjY2Y2YyNzRiMTAwNmJmNjIxOTMzOGVhZDQ0MzAzZDIxMjc1Y2Y2MDJhNzU0In0.eyJhdWQiOiJteWF3ZXNvbWVhcHAiLCJqdGkiOiJkNzk2YzgwNWVjMTU2YjNmZTNjYjE5ZmQxMmU3ZWEyYmQ4ZDI2NmNmMjc0YjEwMDZiZjYyMTkzMzhlYWQ0NDMwM2QyMTI3NWNmNjAyYTc1NCIsImlhdCI6MTUzMzk3NDI1OSwibmJmIjoxNTMzOTc0MjU5LCJleHAiOjE1MzM5Nzc4NTksInN1YiI6IiIsInNjb3BlcyI6WyJiYXNpYyIsImVtYWlsIl19.uY0zL2IgIo2fznRqJpup_gW7G2EhwirrXkDhsWBlTyQbr5jsZ1EqjAnpRvBunE5QnjYyZeUNlrxppfwQulOQxASUgbmuVzFYtIwVRCuvpNWepciANIE2jHE7uOvRlJksuVX63ijf0EYh-V-oL4-28CYvIMzIQbjBai_kcJzf2I5CRsWPFNcTKIhuXJorZ_vuSd-im2yM684CQ36hZJbOTuhiJqBlO6OBM8eVTNrrof-9O4JjHRKFRtYYDEY-n7TYfvVYqdMmN0AHiWOdlNHx0__tFdHc0pYXyZJNgbT_pHy49615F9_49lzRDlhtJJtt6tZiIpTHoD0Be2iSxYmNbw'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = json_decode(curl_exec($ch));
            curl_close($ch);
//            dd($response);
            if ($response->county != "تهران") {
                $status = "406";
                $message = "درخواست شما خارج از تهران است.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                $status = "200";
                $message = "درخواست شماداخل تهران است.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }
        }

        return response()->json($final);
    }

    public function cancelOrder(Request $request)
    {
        if ($request->has('order_id')) {
            $order = Order::find($request->order_id);
            $order->status = 7;
            if ($order->carrier_user_id)
                $order->carrier_user_id = null;
            $order->save();
            $statusLog = new StatusLog();
            $statusLog->status = 7;
            $statusLog->order()->associate($request->order_id);
            $statusLog->save();

            $status = "200";
            $message = "درخواست با موفقیت غیر فعال شد";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "404";
            $message = "مقادیر خالی رها شده است،از پر بودن مقادیر اطمینان حاصل نمایید!";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function acceptOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($request->has('order_id')) {
            if ($request->has(['receiver_name', 'receiver_phone', 'endAddressLat'])) {

                //order comming from web app
                $startAddress = new Address();
                $startAddress->lat = $request->startAddressLat;
                $startAddress->long = $request->startAddressLong;
                $startAddress->description = $request->startAddress;
                $startAddress->type = 1;

                $startAddress->save();

                $endAddress = new Address();
                $endAddress->lat = $request->endAddressLat;
                $endAddress->long = $request->endAddressLong;
                $endAddress->description = $request->endAddress;
                $endAddress->type = 2;
                $endAddress->save();


                $order->origin_address_id = $startAddress->id;
                $order->dest_address_id = $endAddress->id;
                $order->receiver_name = $request->receiver_name;
                $order->receiver_phone = $request->receiver_phone;
                $order->moving_time = $request->time;
            }
            $order->status = 1;
            $order->save();
            //TODO place this this codes in event listener
            $statusLog = new StatusLog();
            $statusLog->status = 1;
            $statusLog->order()->associate($order->id);
            $statusLog->save();
            date_default_timezone_set("Asia/Tehran");

            $this->acceptOrderNotifications($request, $order);

            $status = "200";
            $message = "درخواست با موفقیت تایید شد";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => $order
            );

        } else {
            $status = "404";
            $message = "مقادیر خالی رها شده است،از پر بودن مقادیر اطمینان حاصل نمایید!";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function updateOrder(Request $request)
    {

        if ($request->has(['order_id', 'barbar_worker', 'chideman_worker', 'origin_walking', 'destination_walking', 'origin_floor', 'destination_floor'])) {
            $order = Order::find($request->order_id);
            event(new OrderUpdateEvent($order)); //order update event listener

            $carrier = Carrier::find($order->carrier_id);
            $price = 0;
            $price += $carrier->price;

            if($request->has('insurance') && $request->insurance > 0 ){
                $insurance = (int)($request->insurance * 150000);
                $order->insurance = $insurance;
                $order->price += $insurance;
            }
            if($request->has('asansor_origin') &&$request->has('asansor_dest') ){
                $order->asansor_origin = $request->asansor_origin;
                $order->asansor_dest = $request->asansor_dest;
            }

            if ($request->has('receiver_code')) {
                $order->receiver_code = $request->receiver_code;
            }

            if ($request->has('stop_inway') && $request->stop_inway > 0) {
                $order->stop_inway = (($request->barbar_worker - 1) * 150000 ) + 500000;
                $order->price += $order->stop_inway;
            }

            if (intval($request->barbar_worker) > 0) {
                $price = $price + ($request->barbar_worker * Price::where('title', 'کارگر باربر')->first()->amount);
            }

            if (intval($request->chideman_worker) > 0) {
                $price = $price + ($request->chideman_worker * Price::where('title', 'کارگر بسته بندی')->first()->amount);
            }

            if (intval($request->layout_worker) > 0) {
                $price = $price + ($request->layout_worker * Price::where('title', 'کارگر چیدمان')->first()->amount);
            }

            if (intval($request->origin_walking) > 10) {

                $price += ($request->origin_walking) * Price::where('title', 'پیاده روی')->first()->amount;
            }
            if ($request->has('tech_worker') && intval($request->tech_worker) > 0) {

                $price = $price + ($request->tech_worker * Price::where('title', 'کارگر فنی')->first()->amount);

            }
            if (intval($request->destination_walking) > 10) {
                $price += ($request->destination_walking) * Price::where('title', 'پیاده روی')->first()->amount;

            }
            if (intval($request->origin_floor) >= 9 || intval($request->destination_floor) >= 9) {

                $price += Price::where('title', 'بیشتر از 9')->first()->amount * $request->barbar_worker;

            } elseif (intval($request->origin_floor) >= 4 || intval($request->destination_floor) >= 4) {

                $price += Price::where('title', 'بیشتر از 4')->first()->amount * $request->barbar_worker;
            }

            if ($request->has('vasayeleHazinedar')) {
                $vhObj = json_decode($request->vasayeleHazinedar);
                foreach ($vhObj->data as $vh) {
                    $vhRow = HeavyThing::find($vh->id);

                    $price += $vhRow->price * $vh->count * ($request->origin_floor + $request->destination_floor);
                }
            }


            $order->price = $price;
            $order->moving_workers = $request->barbar_worker;
            $order->packing_workers = $request->chideman_worker;
            $order->origin_floor = $request->origin_floor;
            $order->dest_floor = $request->destination_floor;
            $order->origin_walking = $request->origin_walking;
            $order->dest_walking = $request->destination_walking;
            if ($request->has('tech_worker')) {
                $order->tech_workers = $request->tech_worker;
            }
            event(new OrderUpdateLog($order, "ویرایش سفارش"));
            if ($order->save()) {
                $status = 200;
                $message = "فاکتور با موفقیت آپدیت شد";


                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $price
                );

            } else {
                $status = 404;
                $message = "مشکلی در آپدیت فاکتور پیش آمده است";


                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }


            if ($request->has('vasayeleHazinedar')) {

                if ($order->heavyThings->count() > 0) {
                    $order->heavyThings()->detach();
                }
                $vhObj = json_decode($request->vasayeleHazinedar);
                foreach ($vhObj->data as $vh)
                    $order->heavyThings()->attach([$vh->id => ['count' => $vh->count]]);

            } else {

                if ($order->heavyThings->count() > 0) {
                    $order->heavyThings()->detach();
                }
            }

        } else {
            $status = 401;
            $message = "مقادیر خالی رها شده است";

            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);


    }

    public function getActiveOrder() // User's active orders
    {
        $user = JWTAuth::parseToken()->toUser();
        $order = $user->orders()->whereIn('status', [1, 2, 3, 4, 5, 6])->first();
        if (sizeof($order) == 0) {
            $status = "403";
            $message = "اطلاعاتی موجود نیست";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "200";
            $message = "اطلاعات با موفقیت ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => $order
            );
        }

        return response()->json($final);
    }

    public function allActiveOrder()
    {
        $user = JWTAuth::parseToken()->toUser();
        if ($user->carrierUsers()->count()) {
            $orders = [];
            if ($user->phone != "09338931751" && !$user->carrierUsers()->first()->is_provider) {
                $carrier_id = $user->carrierUsers->first()->carrier_id;
                if ($carrier_id == 1) {
                    $orders = Order::whereIn('carrier_id', [1, 3])->where('status', 1)->with('originAddress', 'destAddress', 'heavyThings')->get()->toArray();
                } elseif ($carrier_id == 2) {
                    $orders = Order::whereIn('carrier_id', [2, 5])->where('status', 1)->with('originAddress', 'destAddress', 'heavyThings')->get()->toArray();
                } else {

                    foreach ($user->carriers as $carrier)
                        $orders = array_merge($orders, $carrier->orders()->where('status', 1)->with('originAddress', 'destAddress', 'heavyThings')->get()->toArray());
                }
            } else {
                $orders = Order::where('status', 1)->with('originAddress', 'destAddress', 'heavyThings')->get()->toArray();
            }
            if (sizeof($orders) == 0) {
                $status = "403";
                $message = "سفری موجود نیست.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد.";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $orders
                );
            }
        } else {
            $status = "406";
            $message = "اطلاعات راننده ناقص است.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function allMyOrders()
    {

        $user = JWTAuth::parseToken()->toUser();
        if ($user->carrierUsers()->count()) {
            $orders = [];
            foreach ($user->carrierUsers as $carrierUser)
                $orders = array_merge($orders, $carrierUser->orders()->whereIn('status', [2, 3, 4, 5])->with('originAddress', 'destAddress', 'heavyThings')->get()->toArray());

            if (sizeof($orders) == 0) {
                $status = "403";
                $message = "سفری موجود نیست.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد.";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $orders
                );
            }
        } else {
            $status = "406";
            $message = "اطلاعات راننده ناقص است.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        echo json_encode($final);
    }

    public function getOrderStatus(Request $request)
    {
        if ($request->order_id != null) {
            $order = Order::find($request->order_id);

            if ($order->status == 1 || $order->status == 7) {
                $status = "200";
                $message = "اطلاعاتی موجود است";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => array(
                        "status" => $order->status
                    )
                );
            } else {

                $carrier_user = $order->carrierUsers()->where('parent_id', null)->first();

                $user = $carrier_user->user;

                $car = $carrier_user->carrier;

                $driver_array = array(
                    "driver_id" => $carrier_user->id,
                    "driver_name" => $user->first_name,
                    "driver_family" => $user->last_name,
                    "driver_mobile" => $user->phone,
                    "driver_pic" => $user->profile_pic,
                    "driver_car" => $car->name,
                    "driver_rank" => $carrier_user->rating
                );

                $data = array(
                    "status" => $order->status,
                    "driver" => array($driver_array),
                    "start_time" => $order->start_time,
                    "end_time" => $order->end_time
                );
                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد";

                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $data
                );
            }


        } else {
            $status = "404";
            $message = "اطلاعات ارسالی خالی است";
            $final = array(
                "status" => $status,
                "message" => $message);
        }
        return response()->json($final);
    }

    public function getExtraTime($order_id)
    {
        $order = Order::find($order_id);
        $start_time = date_create_from_format('Y-m-d H:i:s', $order->start_time);
        $end_time = date_create_from_format('Y-m-d H:i:s', $order->end_time);
        $diff = $start_time->diff($end_time);

        if (((int)($diff->h) > 3) || ((int)($diff->h) == 3 && (int)($diff->i) >= 30)) {
            $extra_hour = (int)($diff->h) - 3;
            if ((int)($diff->i) >= 30) {
                $extra_hour += 1;
            }

            $extra_car = array();
            $extra_worker = array();

            if ($order->carrier_id == 1) {
                array_push($extra_car, "قیمت ساعت اضافه نیسان");
                array_push($extra_car, $extra_hour);
                array_push($extra_car, Price::where('title', 'ساعت اضافه نیسان')->first()->amount);
            } elseif ($order->carrier_id == 2) {
                array_push($extra_car, "قیمت ساعت اضافه کامیون");
                array_push($extra_car, $extra_hour);
                array_push($extra_car, Price::where('title', 'ساعت اضافه کامیون')->first()->amount);
            } elseif ($order->carrier_id == 3) {
                array_push($extra_car, "قیمت ساعت اضافه وانت");
                array_push($extra_car, $extra_hour);
                array_push($extra_car, Price::where('title', 'ساعت اضافه وانت')->first()->amount);
            } elseif ($order->carrier_id == 4) {
                array_push($extra_car, "قیمت ساعت اضافه ماشین بار");
                array_push($extra_car, $extra_hour);
                array_push($extra_car, Price::where('title', 'ساعت اضافه ماشین بار')->first()->amount);
            } elseif ($order->carrier_id == 5) {
                array_push($extra_worker, "قیمت ساعت اضافه کارگر");
                array_push($extra_worker, $extra_hour);
                array_push($extra_worker, Price::where('title', 'ساعت اضافه کارگر خالی')->first()->amount);

                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "extra_worker" => $extra_worker
                );
            }

            if ($order->carrier_id != 5 && $order->moving_workers != 0) {
                array_push($extra_worker, "قیمت ساعت اضافه کارگر");
                array_push($extra_worker, $extra_hour);

                if ($order->origin_floor >= 9 || $order->dest_floor >= 9) {
                    array_push($extra_worker, Price::where('title', 'ساعت اضافه کارگر بیشتر از 9')->first()->amount);
                } elseif ($order->origin_floor >= 4 || $order->dest_floor >= 4) {
                    array_push($extra_worker, Price::where('title', 'ساعت اضافه کارگر بیشتر از 4')->first()->amount);
                } else {
                    array_push($extra_worker, Price::where('title', 'ساعت اضافه کارگر')->first()->amount);
                }

                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد";

                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "extra_car" => $extra_car,
                    "extra_worker" => $extra_worker
                );

            } elseif ($order->carrier_id != 5 && $order->moving_workers == 0) {
                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "extra_car" => $extra_car
                );
            }


        } else {
            $status = "403";
            $message = "عدم وجود ساعت اضافه";

            $final = array(
                "status" => $status,
                "message" => $message
            );
        }


        return response()->json($final);
    }

    public function getOrder($order_id)
    {
        try {
            $order = Order::where('id', $order_id)->with('originAddress', 'destAddress', 'heavyThings', 'carrier')->first();
            $discount_usage = DiscountUsage::where('order_id', $order_id)->where('status', 0)->first();
            $order->real_price = $order->price; // without discount
            if ($discount_usage != null) {

                if ($discount_usage->discount_id != null) {
                    $discount = Discount::find($discount_usage->discount_id);
                    if ($discount->type == 0) {
                        $order->price -= $discount->amount;
                    } else {
                        $order->price -= ($order->price * $discount->amount / 100);
                    }

                } else {
                    $discount = Price::where('title', 'کد معرف')->first();
                    if ($discount->type == 0) {
                        $order->price -= $discount->amount;
                    } else {
                        $order->price -= ($order->price * $discount->amount / 100);
                    }

                }

            }
            $prices = Price::all();
            $status = "200";
            $message = "اطلاعات با موفقیت ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => array($order, $prices)
            );
        } catch (ModelNotFoundException $exception) {
            $status = "403";
            $message = "اطلاعاتی موجود نیست";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }

    public function updateStatus(Request $request)
    {
        if ($request->has('driver_id', 'order_id', 'status')) {
            $order = Order::find($request->order_id);
            if (in_array($order->status, [2, 3, 4, 5, 6])) {
                Log::info("First cattier User: " . $order->carrierUsers->first()->id);
                Log::info("driver id: " . $request->get("driver_id"));
                if ($request->get("driver_id") != $order->carrierUsers->first()->id) {
                    Log::info("not access for user" . $request->get("driver_id"));
                    $status = "403";
                    $message = "این سفارش توسط فرد دیگری قبول شده است";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                    return response()->json($final);
                }

                date_default_timezone_set('Asia/Tehran');
                if ($request->status == 4) {
                    $this->penalty($order);
                    $order->start_time = date('Y-m-d H:i:s');
                    $order->save();

                }
                if ($request->status == 5) {

                    $order->end_time = date('Y-m-d H:i:s');
                    $order->save();

                    $start_time = date_create_from_format('Y-m-d H:i:s', $order->start_time);
                    $end_time = date_create_from_format('Y-m-d H:i:s', $order->end_time);
                    $diff = $start_time->diff($end_time);
                    if (((int)($diff->h) > 3) ||
                        ((int)($diff->h) == 3 && (int)($diff->i) >= 30)) {
                        $extra_hour = (int)($diff->h) - 3;
                        if ((int)($diff->i) >= 30) {
                            $extra_hour += 1;
                        }
                        $price = $order->price;

                        if ($order->carrier_id == 1) {
                            $price += $extra_hour * Price::where('title', 'ساعت اضافه نیسان')->first()->amount;
                        } elseif ($order->carrier_id == 2) {
                            $price += $extra_hour * Price::where('title', 'ساعت اضافه کامیون')->first()->amount;
                        } elseif ($order->carrier_id == 3) {
                            $price += $extra_hour * Price::where('title', 'ساعت اضافه وانت')->first()->amount;
                        } elseif ($order->carrier_id == 4) {
                            $price += $extra_hour * Price::where('title', 'ساعت اضافه ماشین بار')->first()->amount;
                        } elseif ($order->carrier_id == 5) {
                            $price += $extra_hour * $order->moving_workers * Price::where('title', 'ساعت اضافه کارگر خالی')->first()->amount;
                        }

                        if ($order->carrier_id != 5) {

                            if ($order->origin_floor >= 9 || $order->dest_floor >= 9) {
                                $price += $extra_hour * $order->moving_workers * Price::where('title', 'ساعت اضافه کارگر بیشتر از 9')->first()->amount;
                            } elseif ($order->origin_floor >= 4 || $order->dest_floor >= 4) {
                                $price += $extra_hour * $order->moving_workers * Price::where('title', 'ساعت اضافه کارگر بیشتر از 4')->first()->amount;
                            } else {
                                $price += $extra_hour * $order->moving_workers * Price::where('title', 'ساعت اضافه کارگر')->first()->amount;
                            }

                        }

                        $order->price = $price;
                        $order->save();
                    }
                }
                if ($request->status == 7) {
                    $order->carrierUser()->detach();
                }

                $order->status = $request->status;

                $data = $order->save();
                $statusLog = new StatusLog();
                $statusLog->status = $request->status;
                $statusLog->order()->associate($order->id);
                $statusLog->save();

                if ($data) {
                    $status = "201";
                    $message = "وضعیت درخواست با موفقیت آپدیت شد";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                } else {
                    $status = "405";
                    $message = "مشکلی در آپدیت کردن وضعیت رخ داده است. دوباره تلاش نمایید.";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }
            } else {
                if ($request->status == 2) {

                    $user = CarrierUser::find($request->driver_id);

                    $driver_orders= $user->orders;
                    $req_time = Carbon::parse($order->moving_time);
                    foreach ($driver_orders as $ord){
                        $order_time = Carbon::parse($ord->moving_time);
                        if($req_time->diffInHours($order_time) < 3 ){
                            $status = "408";
                            $message = "شما در محدوده این زمان سفارش دیگری پذیرفته اید";
                            $reject = array(
                                "status" => $status,
                                "message" => $message
                            );
                        }

                    }
                    /** @var Builder $user */
                    $rating = $user->comments()->average('rating');
                    $number_of_orders = $user->orders()->count();
                    $totalPrice = $user->orders()->whereIn('status', [2, 3, 4, 5])->sum('price');
                    $totalPrice += $order->price;
                    $credit = Transaction::where('user_id', $user->user_id)->where('status', 1)
                        ->orderBy('created_at', 'DESC')->get()->sum('amount');
                    if (in_array($order->user->phone, $GLOBALS['teamPhone']) && !in_array($user->user->phone, $GLOBALS['teamPhone'])) {
                        $message = "این سفارش تستی میباشد.";
                        $reject = array(
                            "status" => "401",
                            "message" => $message
                        );
                    } elseif ($credit < ($totalPrice * $user->commission) / 100) {
                        $status = "407";
                        $message = "برای قبول سفارش لازم است حساب خود را شارژ نمایید. ";
                        //$message = $usrq;
                        $reject = array(
                            "status" => $status,
                            "message" => $message
                        );
                    } elseif ($rating < 3 && $number_of_orders >= 5) {
                        $status = "407";
                        $message = "میانگین امتیاز شما از ۳ کمتر می باشد.برای قبول سفارش با نوبار تماس بگیرید.";
                        $reject = array(
                            "status" => $status,
                            "message" => $message
                        );
                    }
                    if (isset($reject)) {
                        return response()->json($reject);
                    }

                }
                $carrierUser = CarrierUser::find($request->driver_id);
                $lastOrder = $carrierUser->orders()->whereNotIn('status', [6, 7])->orderBy('id', 'DESC')->first();
                if ($lastOrder) {
                    date_default_timezone_set('Asia/Tehran');
                    $start = Carbon::now();
                    $end = Carbon::parse($lastOrder->created_at);
                    $hours = $end->diffInHours($start);
                }
                // if (!$lastOrder || $hours >= 4) {

                $order->status = $request->status;
                $data = $order->save();
                $statusLog = new StatusLog();
                $statusLog->status = $request->status;
                $statusLog->order()->associate($order->id);
                $statusLog->save();
                $order->carrierUsers()->attach($request->driver_id);

                if ($order->user->phone != '09338931751') {

                    ProcessSendSms::dispatch([
                        'phone' => $order->user->phone,
                        'templateId' => "3678",
                        'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' => $carrierUser->user->first_name . ' ' . $carrierUser->user->last_name))
                    ])->onQueue('sms');
                } else {
                    ProcessSendSms::dispatch([
                        'phone' => $order->receiver_phone,
                        'templateId' => "3678",
                        'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' => $carrierUser->user->first_name . ' ' . $carrierUser->user->last_name))
                    ])->onQueue('sms');
                }

                if ($data) {
                    $status = "200";
                    $message = "درخواست با موفقیت انتخاب و فعال شد";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                } else {
                    $status = "405";
                    $message = "مشکلی در فعال کردن وضعیت رخ داده است. دوباره تلاش نمایید.";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }
                // } else {
                //     $status = "406";
                //     $message = "از سفر قبلی که پذیرفته اید، باید حداقل ۴ ساعت بگذرد.";
                //     $final = array(
                //         "status" => $status,
                //         "message" => $message
                //     );
                // }
            }
        } else {
            $status = "404";
            $message = "مقادیر خالی رها شده است،از پر بودن مقادیر اطمینان حاصل نمایید!";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        if ($request->has('moving_carrier_id')) {
            $carriers = explode('.', $request->moving_carrier_id);
            array_pop($carriers);
            $order->carrierUsers()->attach($carriers);
        }
        return response()->json($final);
    }

    public function uploadSignature(Request $request)
    {
        if ($request->has('order_id', 'image')) {

            $request->image->store('img/signature');

            $order = Order::find($request->order_id);
            $order->signature = Storage::url('img/signature/' . $request->image->hashName());
            event(new OrderUpdateLog($order, 'دریافت امضا'));
            if ($order->save()) {
                $status = "200";
                $message = "آپلود انجام شد";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    'image' => Storage::url('img/signature/' . $request->image->hashName())
                );
            } else {
                $status = "407";
                $message = "مشکل در آپلود عکس دوباره تلاش نمایید.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }
        } else {
            $status = "404";
            $message = "مقادیر خالی رها شده است،از پر بودن مقادیر اطمینان حاصل نمایید!";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function changeDriver(Request $request)
    {
        list($carrier_user, $carrier) = explode(' ', $request->driver);
        $order = Order::find($request->order_id);
        $driver = CarrierUser::find($carrier_user);
        $order->carrier_id = $carrier;
        $order->carrierUsers()->sync($carrier_user);
        $phone = $request->phone;
        $order->status = 2;
        $order->save();
        $statusLog = new StatusLog();
        $statusLog->status = 2;
        $statusLog->order()->associate($request->order_id);
        $statusLog->save();
        $carrierUser = $order->carrierUsers()->where('parent_id', null)->first();
        if ($order->user->phone != '09338931751') {

            ProcessSendSms::dispatch([
                'phone' => $order->user->phone,
                'templateId' => "3678",
                'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' => $carrierUser->user->first_name . ' ' . $carrierUser->user->last_name))
            ])->onQueue('sms');
        } else {
            ProcessSendSms::dispatch([
                'phone' => $order->receiver_phone,
                'templateId' => "3678",
                'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' => $carrierUser->user->first_name . ' ' . $carrierUser->user->last_name))
            ])->onQueue('sms');
        }
        return back();
    }

    public function getUserOrders()
    {
        $user = JWTAuth::parseToken()->toUser();
        $orders = $user->orders()->with('originAddress', 'destAddress', 'heavyThings', 'carrier')->get();
        foreach ($orders as $key => $order) {
            switch ($order->status) {
                case 0:
                    $orders[$key]->status = 'جدید';
                    break;
                case 1:
                    $orders[$key]->status = 'تایید شده';
                    break;
                case 2:
                    $orders[$key]->status = 'راننده پیدا شد';
                    break;
                case 3:
                    $orders[$key]->status = 'عدم تطابق سفارش';
                    break;
                case 4:
                    $orders[$key]->status = 'بارگیری شروع شد';
                    break;
                case 5:
                    $orders[$key]->status = 'بارگیری به اتمام رسید';
                    break;
                case 6:
                    $orders[$key]->status = 'سفارش تکمیل شده است';
                    break;
                case 7:
                    $orders[$key]->status = 'سفارش لغو شده است';
                    break;
            }
        }

        $status = "200";
        $message = "اطلاعات با موفقیت ارسال شد.";
        $final = array(
            "status" => $status,
            "message" => $message,
            "data" => $orders
        );

        return response()->json($final);
    }

    public function getAllUserOrders()
    {

        $user = JWTAuth::parseToken()->toUser();
        $orders = $user->orders()->orderBy('updated_at', 'desc')->where(function ($q) {
            $q->where('status', 1)->orWhere('status', 2)->orWhere('status', 3)->orWhere('status', 4)->orWhere('status', 5);
        })->with('originAddress', 'destAddress', 'heavyThings', 'carrier')->get()->toArray();
        $prices = Price::all()->toArray();
        if (sizeof($orders) == 0) {
            $status = "403";
            $message = "سفری موجود نیست";

            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => array(
                    'prices' => $prices
                )
            );


        } else {
            $status = "200";
            $message = "اطلاعات با موفقیت ارسال شد";

            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => array(
                    'orders' => $orders,
                    'prices' => $prices
                )
            );
        }

        echo json_encode($final);
    }

    public function allUserOrders()
    {
        $user = JWTAuth::parseToken()->toUser();
        if ($user->orders()->count()) {
            $orders = [];
            $orders = array_merge($orders, $user->orders()->orderBy('moving_time', 'ASC')->with('originAddress', 'destAddress', 'heavyThings', 'carrierUsers.user', 'carrier')->get()->toArray());

            if (sizeof($orders) == 0) {
                $status = "403";
                $message = "سفری موجود نیست.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد.";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => $orders
                );
            }
        } else {
            $status = "406";
            $message = "اطلاعات کاربر ناقص است.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        echo json_encode($final);
    }

    public function seenAll()
    {
        Order::where('status', 0)->update(['seen' => 1]);

        return back();
    }

    public function manualInfo(Request $request, $id)
    {
        $order = Order::find($id);
        $observer = Observer::find($request->observer);
        $OrderDate = explode(' ', $order->moving_time);
        $order->opration_date = gmdate('Y-m-d h:i:s', time());
        if ($request->platform != '0') {
            $order->platform = $request->platform;
        }
        if ($request->observer != '0') {
            $order->observer_id = $request->observer;
            if (isset($observer->user->phone)) {
                ProcessSendSms::dispatch([
                    'phone' => $observer->user->phone,
                    'templateId' => "7418",
                    'parameterArray' => array(
                        array('Parameter' => "Orderid", 'ParameterValue' => $id),
                        array('Parameter' => "data", 'ParameterValue' => explode(' ', dateTojal($OrderDate[0]))[1] . ' ' . $OrderDate[1])
                    )
                ])->onQueue('sms');

                ProcessSendSms::dispatch([
                    'phone' => $order->user->phone,
                    'templateId' => "8028",
                    'parameterArray' => array(
                        array('Parameter' => "name", 'ParameterValue' => $order->user->first_name),
                        array('Parameter' => "observer", 'ParameterValue' => $observer->user->first_name . ' ' . $observer->user->last_name),
                        array('Parameter' => "phone", 'ParameterValue' => $observer->user->phone),
                    )
                ])->onQueue('sms');
            }
        }
        if (!empty($request->comment)) {
            if($order->description){
                $order->description = $request->comment . "<br>" . $order->description;
            }else{
                $order->description = $request->comment;
            }
        }
        if (!empty($request->observer_rate) && $request->observer_rate < 6 && $request->observer_rate >= 0) {
            $order->observer_rate = $request->observer_rate;
        }
        event(new OrderUpdateLog($order, 'ورود اطلاعات دستی اپراتور'));
        $order->save();
        return back();
    }

    public function export(Request $request)
    {

        $month = $request->month;
        $sep = "\t";
        $date = dateTojal(gmdate("Y-" . $month . "-d", time())) . '.xls';
        list(, $filename) = explode(' ', $date);
        $filename = str_replace(',', '-', $filename);

        header("Content-Type: application/xls; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");


        $orders = DB::table('orders')
            ->whereMonth('orders.created_at', $month)
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('carriers', 'carriers.id', '=', 'orders.carrier_id')
            ->select(
                'orders.id as شماره سفارش',
                'users.phone as تلفن',
                'users.first_name as نام',
                'users.last_name as نام خانوادگی',
                'users.age as سن',
                'users.financial as وضعیت مالی',
                'users.know_us as نحوه اشنایی',
                'users.keyword as کلمه جستجو شده',
                'orders.price as قیمت',
                'orders.receiver_name as نام گیرنده',
                'orders.receiver_phone as تلفن گیرنده',
                'orders.moving_workers as کارگر باربر',
                'orders.packing_workers as بسته بند',
                'orders.origin_floor as طبفه مبدا',
                'orders.dest_floor as طبقه مقصد',
                'orders.origin_walking as پیاده روی مبدا',
                'orders.dest_walking as پیاده روی مقصد',
                'orders.platform as سکو',
                'orders.description as توضیحات',
                'orders.observer_rate as امتیاز ناظر',
                'carriers.name as خدمت',
                'users.created_at as تاریخ ثبت نام',
                'orders.opration_date as تاریخ پشتیبانی',
                'orders.created_at as تاریخ ثبت',
                'orders.moving_time as تاریخ باربری'
            )
            ->get()->toArray();
        if (count($orders)) {
            @$dd = $orders[0];
            foreach ($dd as $key => $value) {
                printf($key . "\t");
            }
            print("\n");
            foreach ($orders as $key => $value) {
                $schema_insert = "";
                $count = 0;
                foreach ($value as $key => $value) {

                    if ($count > 21)
                        $schema_insert .= dateTojal($value) . $sep;
                    else
                        $schema_insert .= $value . $sep;
                    $count++;
                }

                $schema_insert = str_replace($sep . "$", "", $schema_insert);
                $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
                $schema_insert .= "\t";
                print(trim($schema_insert));
                print "\n";
            }
        } else {
            return '';
        }
    }

    public function notification()
    {
        $orders = Order::where('status', '!=', '0')->where('seen', '!=', '1')->take(1)->orderBy('id', 'desc')->get();
        return response()->json($orders);
    }

    public function selectWorkers(Request $request)
    {
        $order = Order::find($request->order_id);
        $carriers = explode('.', $request->moving_carrier_id);
        array_pop($carriers);
        $order->carrierUsers()->attach($carriers);
        $order->has_workers = 1;
        $order->save();

        $status = "200";
        $message = "اطلاعات با موفقیت ذخیره شد";
        $final = array(
            "status" => $status,
            "message" => $message
        );

        return response()->json($final);
    }

    public function updatePrice(Request $request)
    {
        $order = Order::find($request->id);
        event(new OrderUpdateEvent($order));
        $order->price = str_replace(',', '', $request->price);
        event(new OrderUpdateLog($order, 'ویرایش قیمت'));
        $order->save();

        if ($order->user->phone != '09338931751') {
            ProcessSendSms::dispatch([
                'phone' => $order->user->phone,
                'templateId' => "5424",
                'parameterArray' => array(
                    array('Parameter' => "OrderId", 'ParameterValue' => $order->id),
                    array('Parameter' => "price", 'ParameterValue' => $order->price)

                )
            ])->onQueue('sms');
        } else {
            ProcessSendSms::dispatch([
                'phone' => $order->receiver_phone,
                'templateId' => "5424",
                'parameterArray' => array(
                    array('Parameter' => "OrderId", 'ParameterValue' => $order->id),
                    array('Parameter' => "price", 'ParameterValue' => $order->price)

                )
            ])->onQueue('sms');
        }
        return back();
    }

    public function sourceAssign(Request $request)
    {
        if (!$request->exists('source'))
            return 'مشکل در ذخیره اطلاعات';
        $o = Order::find($request->id);
        $o->thirdparty_id = $request->source;
        event(new OrderUpdateLog($o, 'اضافه کردن شریک تجاری'));
        $o->save();
        return back();
    }

    public function getPrice(Request $request)
    {
        if ($request->has(['car_id'])) {
            $carrier = Carrier::find($request->car_id);
            // price
            $price = 0;
            if ($carrier->id != 5) {
                $price += $carrier->price;
                if (intval($request->barbar_worker) > 0) {
                    $price = $price + ($request->barbar_worker * Price::where('title', 'کارگر باربر')->first()->amount);
                }
                if (intval($request->chideman_worker) > 0) {
                    $price = $price + ($request->chideman_worker * Price::where('title', 'کارگر بسته بندی')->first()->amount);
                }
                if ($request->has('tech_worker') && intval($request->tech_worker) > 0) {
                    $price = $price + ($request->tech_worker * Price::where('title', 'کارگر فنی')->first()->amount);
                }
                if (intval($request->origin_walking) > 10) {

                    $price += ($request->origin_walking) * Price::where('title', 'پیاده روی')->first()->amount;
                }
                if (intval($request->destination_walking) > 10) {
                    $price += ($request->destination_walking) * Price::where('title', 'پیاده روی')->first()->amount;

                }
                if (intval($request->origin_floor) >= 9 || intval($request->destination_floor) >= 9) {
                    $price += Price::where('title', 'بیشتر از 9')->first()->amount * $request->barbar_worker;

                } elseif (intval($request->origin_floor) >= 4 || intval($request->destination_floor) >= 4) {
//                    dump($price);

                    $price += Price::where('title', 'بیشتر از 4')->first()->amount * $request->barbar_worker;
//                    dd($price);
                }

                if ($request->has('vasayeleHazinedar')) {
                    $vhObj = ($request->vasayeleHazinedar);
                    foreach ($vhObj as $vh) {
                        $vhRow = HeavyThing::find($vh['id']);
                        $price += $vhRow->price * $vh['count'] * ($request->origin_floor + $request->destination_floor);
                    }
                }
            } else {
                if (intval($request->barbar_worker) > 0) {
                    $price = $price + ($request->barbar_worker * $carrier->price);
                }
                if (intval($request->chideman_worker) > 0) {
                    $price = $price + ($request->chideman_worker * Price::where('title', 'کارگر بسته بندی')->first()->amount);
                }
                if ($request->has('tech_worker') && intval($request->tech_worker) > 0) {
                    $price = $price + ($request->tech_worker * Price::where('title', 'کارگر فنی')->first()->amount);
                }
            }


        }


        if ($request->has('discount_code')) {

            $bon = $request->discount_code;
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
                if ($discount->type == 0) {
                    $price = $price - $discount->amount;
                } else {
                    $price = $price - ($price * $discount->amount / 100);
                }
            }

        }
        if (isset($final)) {
            return response(['message' => $final['message']], $final['status']);
        }

        return response()->json($price);
    }

    public function tracked(Request $request, $id)
    {
        $order = Order::find($id);
        if ($request->status == 0) {
            $order->tracked = 0;
        } else {
            $order->tracked = Auth::user()->id;
        }
        $order->save();
        return back();
    }

    public function bill($id)
    {
        $data = [];
        $order = Order::find($id);
        $title = $order->carrier->name;
        $number = 1;
        $price = $order->carrier->price;
        $sum = $order->carrier->price;
        $bill = [
            'title' => $title,
            'price' => $price,
            'number' => $number,
            'sum' => $sum,
            'description' => '',
        ];
        array_push($data, $bill);
        if (intval($order->moving_workers) > 0) {
            $title = 'کارگر حمل اسباب';
            $price = Price::where('title', 'کارگر باربر')->first()->amount;
            $sum = $order->moving_workers * $price;
            $number = intval($order->moving_workers);
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);
        }
        if (intval($order->packing_workers) > 0) {
            $title = 'کارگر بسته بندی';
            $price = Price::where('title', 'کارگر بسته بندی')->first()->amount;
            $sum = $order->packing_workers * $price;
            $number = intval($order->packing_workers);
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);
        }
        if (intval($order->tech_workers) > 0) {
            $title = 'کارگر فنی';
            $price = Price::where('title', 'کارگر فنی')->first()->amount;
            $sum = $order->tech_workers * $price;
            $number = intval($order->tech_workers);
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);
        }
        if (intval($order->origin_walking) > 10) {
            $title = 'پیاده روی مبدا';
            $price = Price::where('title', 'پیاده روی')->first()->amount;
            $sum = $order->origin_walking * $price;
            $number = intval($order->origin_walking);
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);

        }
        if (intval($order->dest_walking) > 10) {
            $title = 'پیاده روی مقصد';
            $price = Price::where('title', 'پیاده روی')->first()->amount;
            $sum = $order->dest_walking * $price;
            $number = intval($order->dest_walking);
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);
        }
        if (intval($order->origin_floor) >= 9 || intval($order->dest_floor) >= 9) {
            $title = 'هزینه طبقات بیسشتر از ۸';
            $price = Price::where('title', 'بیشتر از 9')->first()->amount;
            $sum = $order->moving_workers * $price;
            $number = (intval($order->origin_floor) >= 9 ? intval($order->origin_floor) : '' || intval($order->dest_floor) >= 9 ? $order->dest_floor : '');
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);

        } elseif (intval($order->origin_floor) >= 4 || intval($order->dest_floor) >= 4) {
            $title = 'هزینه طبقات بیسشتر از ۳';
            $price = Price::where('title', 'بیشتر از 4')->first()->amount;
            $sum = $order->moving_workers * $price;
            $number = (intval($order->origin_floor) >= 4 ? intval($order->origin_floor) : '' || intval($order->dest_floor) >= 4 ? $order->dest_floor : '');
            $bill = [
                'title' => $title,
                'price' => $price,
                'number' => $number,
                'sum' => $sum,
                'description' => '',
            ];
            array_push($data, $bill);
        }
        $heavy = $order->heavyThings()->where('count', '>', 0)->get();
        if ($heavy->count() > 0) {
            foreach ($heavy as $val) {
                $title = $val->name;
                $price = $val->price;
                $number = $val->pivot->count;
//                dd(($val->price) * ($val->pivot->count));
                $sum = ($val->price) * ($val->pivot->count) * (abs($order->origin_floor) + abs($order->dest_floor));
                $bill = [
                    'title' => $title,
                    'price' => $price,
                    'number' => $number,
                    'sum' => $sum,
                    'description' => '',
                ];
                array_push($data, $bill);

            }
        }

        if ($order->start_time && $order->end_time) {
            $start_time = date_create_from_format('Y-m-d H:i:s', $order->start_time);
            $end_time = date_create_from_format('Y-m-d H:i:s', $order->end_time);
            $diff = $start_time->diff($end_time);
            if (((int)($diff->h) > 3) || ((int)($diff->h) == 3 && (int)($diff->i) >= 30)) {
                $extra_hour = (int)($diff->h) - 3;
                if ((int)($diff->i) >= 30) {
                    $extra_hour += 1;
                }
                $price = 0;

                if ($order->carrier_id == 1) {
                    $fi = Price::where('title', 'ساعت اضافه نیسان')->first()->amount;
                    $price += $extra_hour * $fi;
                } elseif ($order->carrier_id == 2) {
                    $fi = Price::where('title', 'ساعت اضافه کامیون')->first()->amount;
                    $price += $extra_hour * $fi;
                } elseif ($order->carrier_id == 3) {
                    $fi = Price::where('title', 'ساعت اضافه وانت')->first()->amount;
                    $price += $extra_hour * $fi;
                } elseif ($order->carrier_id == 4) {
                    $fi = Price::where('title', 'ساعت اضافه ماشین بار')->first()->amount;
                    $price += $extra_hour * $fi;
                } elseif ($order->carrier_id == 5) {
                    $fi = Price::where('title', 'ساعت اضافه کارگر خالی')->first()->amount;
                    $price += $extra_hour * $order->moving_workers * $fi;
                }
                $bill = [
                    'title' => 'ساعت اضافه ماشین',
                    'price' => $fi,
                    'number' => $extra_hour,
                    'sum' => $price,
                    'description' => '',
                ];
                array_push($data, $bill);

                if ($order->carrier_id != 5) {
                    $price = 0;
                    if ($order->origin_floor >= 9 || $order->dest_floor >= 9) {
                        $fi = Price::where('title', 'ساعت اضافه کارگر بیشتر از 9')->first()->amount;
                        $price += $extra_hour * $order->moving_workers * $fi;
                    } elseif ($order->origin_floor >= 4 || $order->dest_floor >= 4) {
                        $fi = Price::where('title', 'ساعت اضافه کارگر بیشتر از 4')->first()->amount;
                        $price += $extra_hour * $order->moving_workers * $fi;
                    } else {
                        $fi = Price::where('title', 'ساعت اضافه کارگر')->first()->amount;
                        $price += $extra_hour * $order->moving_workers * $fi;
                    }
                    $bill = [
                        'title' => 'ساعت اضافه کارگر',
                        'price' => $fi,
                        'number' => $order->moving_workers . 'x' . $extra_hour,
                        'sum' => $price,
                        'description' => '',
                    ];
                    array_push($data, $bill);
                }
            }
        }

        return response()->json($data);
    }

    public function transactionType(Request $request)
    {
        /*        if(Auth()->user()->roles()->first()->name != 'manager')
                    return back()->with('alert', ['انجام این عملیات برای شما ممکن نیست']);*/
        $order = Order::find($request->id);
        $order->transaction_type = $request->type;
        event(new OrderUpdateLog($order, "تغییر نوع برداخت"));
        $order->save();
        return back();
    }

    public function match(Request $request, $id)
    {
        $order = Order::find($id);
        $order->match = $request->match;
        event(new OrderUpdateLog($order, "تطبیق"));
        $order->save();
        return back();
    }

    private function penalty($order)
    {
        $distance = time() - strtotime($order->moving_time);
        $penaltyAmount = 100000;
        $start = Carbon::parse($order->moving_time);
        $now = Carbon::now();
        $diff = $now->diffInMinutes($start);
        if (($distance / 60) > 30) {
            $amount = $penaltyAmount * (int)($diff / 30);
            $trns = new TransactionSaver();
            $trns->order_id = $order->id;
            $trns->driver_user_id = $order->carrierUsers->where('parent_id', null)->first()->user->id;
            $trns->driverPenalty($amount);
            event(new OrderUpdateLog($order, 'جریمه تاخیر راننده'));
        }
    }

    /**
     * @param Request $request
     * @param $responseStart
     * @param $responseEnd
     * @return array
     */
    private function addressStore(Request $request, $responseStart, $responseEnd): array
    {
        $startAddress = new Address();
        $startAddress->lat = $request->startAddressLat;
        $startAddress->long = $request->startAddressLong;
        $startAddress->region = $responseStart->region;
        $startAddress->description = $request->startAddress;
        $startAddress->type = 1;

        $endAddress = new Address();
        $endAddress->lat = $request->endAddressLat;
        $endAddress->long = $request->endAddressLong;
        $endAddress->region = $responseEnd->region;
        $endAddress->description = $request->endAddress;
        $endAddress->type = 2;
        $startAddress->save();
        $endAddress->save();
        return array($startAddress, $endAddress);
    }

    public function sendOrder(Request $request)
    {
        $disabledCarriers = Carrier::where('status', 0)->get();
        if ($disabledCarriers->count()) { // reject disabled service
            foreach ($disabledCarriers as $val) {
                if ($request->car_id == $val->id) {

                    $status = "404";
                    $message = "به نظر میرسد اپ شما منقضی شده باشد لطفا بروزرسانی کنید";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                    return response()->json($final);
                }
            }
        }
        list($responseStart, $responseEnd) = $this->getLocationDetails($request);

        if (env('USE_POLYGON')) {
            if (!checkLocation($request->startAddressLat, $request->startAddressLong, 1) ||
                !checkLocation($request->endAddressLat, $request->endAddressLong, 1)) {
                $status = "406";
                $message = "درخواست شما خارج از تهران است.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
                return response()->json($final);
            }
        } else {
            if ($responseStart->county != "تهران" ||
                $responseEnd->county != "تهران") {
                $status = "406";
                $message = "درخواست شما خارج از تهران است.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

            return response()->json($final);
        }

        if ($request->has(['user_id', 'car_id', 'time']) && !isset($final)) {

            list($startAddress, $endAddress) = $this->addressStore($request, $responseStart, $responseEnd);
            $carrier = Carrier::find($request->car_id);
            // price
            $price = $this->orderPriceCalc($request, $carrier);
            $order = new Order();
            $order->user()->associate(JWTAuth::parseToken()->authenticate()->id);
            $order->carrier()->associate($carrier->id);
            $order->origin_address_id = $startAddress->id;
            $order->dest_address_id = $endAddress->id;
            $order->receiver_name = $request->receiver_name;
            $order->receiver_phone = $request->receiver_mobile;
            $order->moving_time = date('Y-m-d H:i:s', strtotime($request->time));
            $order->price = $price;
            $order->moving_workers = $request->barbar_worker;
            $order->packing_workers = $request->chideman_worker;
            $order->layout_worker = $request->layout_worker;
            $order->user_description = $request->user_description;


            if($request->has('insurance') && $request->insurance > 0 ){
                $insurance = (int)($request->insurance * 150000);
                $order->insurance = $insurance;
                $order->price += $insurance;
            }
            if($request->has('asansor_origin') &&$request->has('asansor_dest') ){
                $order->asansor_origin = $request->asansor_origin;
                $order->asansor_dest = $request->asansor_dest;
            }

            if ($request->chideman_worker != 0 && $request->has('chideman_worker_gender')) {
                $order->gender = $request->chideman_worker_gender;
            }

            if ($request->has('tech_worker')) {
                $order->tech_workers = $request->tech_worker;
            }

            if ($request->has('receiver_code')) {
                $order->receiver_code = $request->receiver_code;
            }
            $order->origin_floor = $request->origin_floor;
            $order->dest_floor = $request->destination_floor;
            $order->origin_walking = $request->origin_walking;
            $order->dest_walking = $request->destination_walking;

            if ($request->has('stop_inway') && $request->stop_inway > 0) {
                $order->stop_inway = (($request->barbar_worker - 1) * 150000 ) + 500000;
                $order->price += $order->stop_inway;
            }

            if ($request->has('platform')) {
                $order->platform = $request->platform;
            }
            $order->status = 0;
            if ($request->has('status') && $request->status == 1) {
                $order->status = 1;
                $this->acceptOrderNotifications($request, $order);
            }
            if ($order->save()) {
                $statusLog = new StatusLog();
                $statusLog->status = 0;
                $statusLog->order()->associate($order->id);
                $statusLog->save();
                $status = "200";
                $message = "درخواست شما با موفقیت ثبت شد.";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => array($order)
                );
            } else {
                $status = "401";
                $message = "مشکل در ذخیره درخواست، دوباره تلاش نمایید!";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

            if ($request->has('vasayeleHazinedar')) {
                $vhObj = json_decode($request->vasayeleHazinedar);
                foreach ($vhObj->data as $vh)
                    $order->heavyThings()->attach([$vh->id => ['count' => $vh->count]]);
            }


        } else {
            $status = "404";
            $message = "مقادیر خالی رها شده است،از پر بودن مقادیر اطمینان حاصل نمایید!";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }

    /**
     * @param Request $request
     * @param $carrier
     * @return array
     */
    private function orderPriceCalc(Request $request, $carrier)
    {
        $price = 0;
        if ($carrier->id != 5) {
            $price += $carrier->price;
            if (intval($request->barbar_worker) > 0) {
                $price = $price + ($request->barbar_worker * Price::where('title', 'کارگر باربر')->first()->amount);
            }
            if (intval($request->chideman_worker) > 0) {
                $price = $price + ($request->chideman_worker * Price::where('title', 'کارگر بسته بندی')->first()->amount);
            }

            if (intval($request->layout_worker) > 0) {
                $price = $price + ($request->layout_worker * Price::where('title', 'کارگر چیدمان')->first()->amount);
            }
            if ($request->has('tech_worker') && intval($request->tech_worker) > 0) {

                $price = $price + ($request->tech_worker * Price::where('title', 'کارگر فنی')->first()->amount);

            }
            if (intval($request->origin_walking) > 10) {

                $price += ($request->origin_walking) * Price::where('title', 'پیاده روی')->first()->amount;
            }
            if (intval($request->destination_walking) > 10) {
                $price += ($request->destination_walking) * Price::where('title', 'پیاده روی')->first()->amount;

            }
            if (intval($request->origin_floor) >= 9 || intval($request->destination_floor) >= 9) {

                $price += Price::where('title', 'بیشتر از 9')->first()->amount * $request->barbar_worker;

            } elseif (intval($request->origin_floor) >= 4 || intval($request->destination_floor) >= 4) {

                $price += Price::where('title', 'بیشتر از 4')->first()->amount * $request->barbar_worker;
            }

            $origin_asansor = $request->has('asansor_origin') && $request->asansor_origin == 1 ? 0 : 1;
            $dest_asansor = $request->has('asansor_dest') && $request->asansor_dest == 1 ? 0 : 1;

            if ($request->has('vasayeleHazinedar')) {
                $vhObj = json_decode($request->vasayeleHazinedar);
                foreach ($vhObj->data as $vh) {
                    $vhRow = HeavyThing::find($vh->id);

                    $price += $vhRow->price * $vh->count * (($request->origin_floor * $origin_asansor) + ($request->destination_floor * $dest_asansor));
                }
            }

        } else {
            if (intval($request->barbar_worker) > 0) {
                $price = $price + ($request->barbar_worker * $carrier->price);
            }
            if (intval($request->chideman_worker) > 0) {
                $price = $price + ($request->chideman_worker * Price::where('title', 'کارگر بسته بندی')->first()->amount);
            }
            if ($request->has('tech_worker') && intval($request->tech_worker) > 0) {

                $price = $price + ($request->tech_worker * Price::where('title', 'کارگر فنی')->first()->amount);

            }
        }
        return $price;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getLocationDetails(Request $request): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://map.ir/reverse?lat=" . $request->startAddressLat . "&lon=" . $request->startAddressLong);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-api-key: eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ3OTZjODA1ZWMxNTZiM2ZlM2NiMTlmZDEyZTdlYTJiZDhkMjY2Y2YyNzRiMTAwNmJmNjIxOTMzOGVhZDQ0MzAzZDIxMjc1Y2Y2MDJhNzU0In0.eyJhdWQiOiJteWF3ZXNvbWVhcHAiLCJqdGkiOiJkNzk2YzgwNWVjMTU2YjNmZTNjYjE5ZmQxMmU3ZWEyYmQ4ZDI2NmNmMjc0YjEwMDZiZjYyMTkzMzhlYWQ0NDMwM2QyMTI3NWNmNjAyYTc1NCIsImlhdCI6MTUzMzk3NDI1OSwibmJmIjoxNTMzOTc0MjU5LCJleHAiOjE1MzM5Nzc4NTksInN1YiI6IiIsInNjb3BlcyI6WyJiYXNpYyIsImVtYWlsIl19.uY0zL2IgIo2fznRqJpup_gW7G2EhwirrXkDhsWBlTyQbr5jsZ1EqjAnpRvBunE5QnjYyZeUNlrxppfwQulOQxASUgbmuVzFYtIwVRCuvpNWepciANIE2jHE7uOvRlJksuVX63ijf0EYh-V-oL4-28CYvIMzIQbjBai_kcJzf2I5CRsWPFNcTKIhuXJorZ_vuSd-im2yM684CQ36hZJbOTuhiJqBlO6OBM8eVTNrrof-9O4JjHRKFRtYYDEY-n7TYfvVYqdMmN0AHiWOdlNHx0__tFdHc0pYXyZJNgbT_pHy49615F9_49lzRDlhtJJtt6tZiIpTHoD0Be2iSxYmNbw'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $responseStart = json_decode(curl_exec($ch));
        curl_close($ch);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://map.ir/reverse?lat=" . $request->endAddressLat . "&lon=" . $request->endAddressLong);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-api-key: eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ3OTZjODA1ZWMxNTZiM2ZlM2NiMTlmZDEyZTdlYTJiZDhkMjY2Y2YyNzRiMTAwNmJmNjIxOTMzOGVhZDQ0MzAzZDIxMjc1Y2Y2MDJhNzU0In0.eyJhdWQiOiJteWF3ZXNvbWVhcHAiLCJqdGkiOiJkNzk2YzgwNWVjMTU2YjNmZTNjYjE5ZmQxMmU3ZWEyYmQ4ZDI2NmNmMjc0YjEwMDZiZjYyMTkzMzhlYWQ0NDMwM2QyMTI3NWNmNjAyYTc1NCIsImlhdCI6MTUzMzk3NDI1OSwibmJmIjoxNTMzOTc0MjU5LCJleHAiOjE1MzM5Nzc4NTksInN1YiI6IiIsInNjb3BlcyI6WyJiYXNpYyIsImVtYWlsIl19.uY0zL2IgIo2fznRqJpup_gW7G2EhwirrXkDhsWBlTyQbr5jsZ1EqjAnpRvBunE5QnjYyZeUNlrxppfwQulOQxASUgbmuVzFYtIwVRCuvpNWepciANIE2jHE7uOvRlJksuVX63ijf0EYh-V-oL4-28CYvIMzIQbjBai_kcJzf2I5CRsWPFNcTKIhuXJorZ_vuSd-im2yM684CQ36hZJbOTuhiJqBlO6OBM8eVTNrrof-9O4JjHRKFRtYYDEY-n7TYfvVYqdMmN0AHiWOdlNHx0__tFdHc0pYXyZJNgbT_pHy49615F9_49lzRDlhtJJtt6tZiIpTHoD0Be2iSxYmNbw'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $responseEnd = json_decode(curl_exec($ch));
        curl_close($ch);
        return array($responseStart, $responseEnd);
    }

    /**
     * @param Request $request
     * @param $user
     * @param $order
     */
    private function acceptOrderNotifications(Request $request, $order): void
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->phone != '09338931751') {
//        if ($user->phone != '09369248966') {
            ProcessSendSms::dispatch([
                'phone' => $user->phone,
                'templateId' => "4642",
                'parameterArray' => array(array('Parameter' => "OrderId", 'ParameterValue' => $request->order_id))
            ])->onQueue('sms');
            ProcessSendSms::dispatch([
                'phone' => "09338931751",
                'templateId' => "3208",
                'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $user->first_name . '_' . $user->last_name . '-' . $user->phone))
            ])->onQueue('sms');
            ProcessSendSms::dispatch([
                'phone' => "09122250704",
                'templateId' => "3208",
                'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $user->first_name . '_' . $user->last_name . '_' . $user->phone))
            ])->onQueue('sms');
            ProcessSendSms::dispatch([
                'phone' => "09123384853",
                'templateId' => "3208",
                'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $user->first_name . '_' . $user->last_name . '_' . $user->phone))
            ])->onQueue('sms');

        } else { //order comming from web app
            if ($request->has('receiver_phone')) {
                ProcessSendSms::dispatch([
                    'phone' => $request->receiver_phone,
                    'templateId' => "8350",
                    'parameterArray' => array(
                        array('Parameter' => "Username", 'ParameterValue' => $order->receiver_name),
                        array('Parameter' => "Orderid", 'ParameterValue' => $order->id),
                        array('Parameter' => "price", 'ParameterValue' => $order->price)
                    )
                ])->onQueue('sms');
            }
            ProcessSendSms::dispatch([
                'phone' => "09338931751",
                'templateId' => "3208",
                'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $order->receiver_name . '_' . $order->receiver_phone . "کاربرـسایت"))
            ])->onQueue('sms');
            ProcessSendSms::dispatch([
                'phone' => "09122250704",
                'templateId' => "3208",
                'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $order->receiver_name . '_' . $order->receiver_phone . "کاربر_سایت"))
            ])->onQueue('sms');
        }
        $carrierUsers = $order->carrier->carrierUsers;
        foreach ($carrierUsers as $carrierUser) {
            if (!in_array($order->user->phone, $GLOBALS['teamPhone']) && $order->user->status == 1) {
                ProcessNotification::dispatch([
                    'title' => '🚛سفارش جدید🚚',
                    'body' => '🚛درخواست جدید در نوبار ثبت شد🚚'
                ], $carrierUser->user)->onQueue('notification');
                if($carrierUser->user->status == 1){
                    ProcessSendSms::dispatch([
                        'phone' => $carrierUser->user->phone,
                        'templateId' => "1",
                        'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $carrierUser->user->first_name. '-'.$carrierUser->user->last_name))
                    ])->onQueue('sms');
                }


            }
        }
    }

    public function traficPrice(Request $request, $id)
    {
        $amount = str_replace(',', '', $request->price);
        $order  = Order::find($id);
        $order->trafic_price = $amount;
        $order->price = $order->price + $amount;
        $order->save();
        return back();
    }

    public function dateUpdate(Request $request, $id)
    {
        $order = Order::find($id);
            if ($request->date != null) {
                $date = gmdate('Y-m-d', (int)($request->date) / 1000) . ' ' . $request->clock;
                $order->moving_time = $date;
            }
            $order->save();
        event(new OrderUpdateEvent($order));
        return back();
    }

    public function gift(Request $request, $id)
    {
        $order = Order::find($id);
        $driver_user_id = $order->carrierUsers()->where("parent_id", null)->first()->user->id;
        $order->gift = $request->gift;
        $order->save();
        $t = new TransactionSaver;
        $t->driver_user_id = $driver_user_id;
        $t->order_id = $order->id;
        $t->driverGift( $request->gift);
        return back();
    }

    public function detach($id)
    {
        $user = JWTAuth::parseToken()->toUser();
//        dd($user->orders()->where('id',5662)->get());
        $user->carrierUsers()->first()->orders()->detach($id);
        $order = Order::find($id);
        $order->status = 1;
        $order->save();
        $carrierUsers = $order->carrier->carrierUsers;
        foreach ($carrierUsers as $carrierUser) {
            if (!in_array($order->user->phone, $GLOBALS['teamPhone']) && $order->user->status == 1) {
                ProcessNotification::dispatch([
                    'title' => '🚛سفارش جدید🚚',
                    'body' => '🚛درخواست جدید در نوبار ثبت شد🚚'
                ], $carrierUser->user)->onQueue('notification');
                if($carrierUser->user->status == 1){
                    ProcessSendSms::dispatch([
                        'phone' => $carrierUser->user->phone,
                        'templateId' => "1",
                        'parameterArray' => array(array('Parameter' => "UserId", 'ParameterValue' => $carrierUser->user->first_name. '-'.$carrierUser->user->last_name))
                    ])->onQueue('sms');
                }
            }
        }
        $status = "200";
        $message = "سفارش با مموفقیت لغو شد";
        $final = array(
            "status" => $status,
            "message" => $message
        );
        return response()->json($final);
    }
}
