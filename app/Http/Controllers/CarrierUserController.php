<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/18/18
 * Time: 7:22 PM
 */

namespace App\Http\Controllers;


use App\Carrier;
use App\Order;
use App\CarrierUser;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CarrierUserController extends Controller
{
    public function workers()
    {
        $workers = CarrierUser::where('parent_id', '!=', null)->paginate(15);
        $drivers = CarrierUser::where('parent_id', null)->get();
        return view('admin.carriers.workers', compact('workers', 'drivers'));
    }

    public function show($id)
    {
        $carrier_user = CarrierUser::find($id);
        $orders = [];
        foreach ($carrier_user->orders as $key => $value) {
            $order = Order::find($value->id)->toArray();
            $orders[$key] = $order;
        }
        $orders = array_reverse($orders);
        // $orders = $carrier_user->orders()->orderBy("careated_at",'DESC')->get()->toArray();
        // dd($orders); 
        $transactions = Transaction::where('user_id', $carrier_user->user->id)->where('status', 1)->orderBy('created_at', 'DESC')->get();
        return view('admin.carriers.show', compact('carrier_user', 'transactions', 'orders'));
    }

    public function drivers()
    {
        $carriers = Carrier::all();
        $enable = CarrierUser::where("parent_id",null)->whereHas('user',function($q){
            $q->where('status',1);
        })->get();
        $disable = CarrierUser::where("parent_id",null)->whereHas('user',function($q){
            $q->where('status',0);
        })->get();
        return view('admin.carriers.drivers', compact('enable','disable', 'carriers'));
    }

    public function driverSearch(Request $request)
    {
        $carriers = Carrier::all();
        $drivers = CarrierUser::where('parent_id', null)
            ->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%');
            })->orWhereHas('user', function ($q) use ($request) {
                $q->where('last_name', 'like', '%' . $request->search . '%');
            })
            ->with('user')
            ->get();
        if (isset($request->json))
            return response()->json($drivers);
        return view('admin.carriers.search', compact('drivers', 'carriers'));
    }

    public function updateDriver(Request $request)
    {
        if ($request->has('status', 'driver_id')) {
            $user = JWTAuth::parseToken()->toUser();
            $carrierUser = $user->carrierUsers()->where('id', $request->driver_id)->first();
            if ($request->status == '1') {
                $carrierUser->status = 1;
                if ($carrierUser->save()) {
                    $status = "200";
                    $message = "وضعیت با موفقیت فعال شد";
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
            } else {
                $carrierUser->status = 0;
                if ($carrierUser->save()) {
                    $status = "200";
                    $message = "وضعیت با موفقیت غیر فعال شد";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                } else {
                    $status = "405";
                    $message = "مشکلی در غیر فعال کردن وضعیت رخ داده است. دوباره تلاش نمایید.";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }
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

    public function getOrderHistory(Request $request)
    {
        $carrierUser = CarrierUser::find($request->driver_id);
        $data = $carrierUser->orders()
            ->orderBy('updated_at', 'desc')
            ->with('originAddress', 'destAddress', 'heavyThings')
            ->get()
            ->toArray();
        if (sizeof($data) > 0) {
            $status = "200";
            $message = "اطلاعات ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => $data
            );
        } else {
            $status = "405";
            $message = "اطلاعاتی موجود نیست.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }

    //get all drivers ranks
    public function getDriverComments(Request $request)
    {

        $carrierUser = DB::table('comments')->join('carrier_user', 'comments.carrier_user_id', '=', 'carrier_user.id')->join('users', 'carrier_user.user_id', '=', 'users.id')->select(DB::raw('users.first_name as name,users.last_name as family,avg(comments.rating) as avg '))->where('users.status','1')->groupBy('carrier_user.id', 'users.first_name', 'users.last_name')->orderBy('avg', 'DESC')->get();

        $status = "200";
        $message = "اطلاعات با موفقیت ارسال شد";
        $final = array(
            "status" => $status,
            "message" => $message,
            "data" => $carrierUser
        );

        return response()->json($final);
    }

    public function getDriverTotalRating(Request $request)
    {
        $carrierUser = DB::table('comments')->join('carrier_user', 'comments.carrier_user_id', '=', 'carrier_user.id')->join('users', 'carrier_user.user_id', '=', 'users.id')->select(DB::raw('users.first_name as name,users.last_name as family,avg(comments.rating) as avg '))->groupBy('carrier_user.id', 'users.first_name', 'users.last_name')->orderBy('avg', 'DESC')->get();

        $avg_array = array();

        foreach ($carrierUser as $carrier) {
            if (!in_array($carrier->avg, $avg_array)) {
                array_push($avg_array, $carrier->avg);
            }
        }

        rsort($avg_array);

        $myCarrier = CarrierUser::find($request->driver_id)->comments()->avg('rating');

        $driver_rank = array_search($myCarrier, $avg_array) + 1;

        $status = "200";

        $final_array = array("avg" => $myCarrier, "rank" => $driver_rank);
        $message = "اطلاعات با موفقیت ارسال شد";
        $final = array(
            "status" => $status,
            "message" => $message,
            "data" => $final_array
        );

        return response()->json($final);
    }


    public function store(Request $request)
    {
        if (strpos($request->url(), 'worker') !== false)
            $carrier = Carrier::where('name', 'worker')->first();

        $user = new User();
        $user->first_name = $request->name;
        $user->last_name = $request->family;
        //$user->phone = $request->phone;

        //$request->image->store('img/profile');
        $user->profile_pic = Storage::url('img/profile/' . $request->image->hashName());


        if ($user->save()) {
            $carrierUser = new CarrierUser();
            $carrierUser->parent_id = $request->driver_id;
            $carrierUser->national_code = $request->national_id;

            $carrierUser->carrier_id = Carrier::where('name', 'کارگر')->first()->id;

            $carrierUser->user()->associate($user->id);
            $carrierUser->save();

            $status = "200";
            $message = "اطلاعات با موفقیت دخیره شد";

            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "407";
            $message = "مشکل در آپلود عکس دوباره تلاش نمایید.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }


    public function delete(Request $request)
    {
        $carrierUser = CarrierUser::find($request->worker_id);
        $user_id = $carrierUser->user_id;

        $carrierUser->delete();
        User::where("id", $user_id)->delete();

        $status = "200";
        $message = "با موفقیت حذف شد";
        $final = array(
            "status" => $status,
            "message" => $message
        );

        return response()->json($final);
    }

    public function removeWorker(Request $request)
    {
        $carrierUser = CarrierUser::find($request->worker_id);
        $user_id = $carrierUser->user_id;

        $carrierUser->delete();
        User::where("id", $user_id)->delete();


        return back();
    }

    public function addWorker(Request $request)
    {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        if ($request->has('image')) {
            $request->image->store('img/profile');
            //$user->profile_pic = "/images/carriers/".$request->image->hashName();;
            $user->profile_pic = Storage::url('img/profile/' . $request->image->hashName());
            //$file = $request->image;
            //$file->move(public_path('/images/carriers/'),$file->hashName());

        }

        if ($user->save()) {
            $carrierUser = new CarrierUser();
            $carrierUser->parent_id = $request->driver_id;
            $carrierUser->national_code = $request->national_id;

            $carrierUser->carrier_id = Carrier::where('name', 'کارگر')->first()->id;

            $carrierUser->user()->associate($user->id);
            $carrierUser->save();
        }
        return back();
    }

    public function getWorkerList(Request $request)
    {
        $carrierUser = CarrierUser::where('parent_id', $request->driver_id)->get();

        foreach ($carrierUser as $c) {
            $c = $c->user;
        }

        $status = "200";
        $message = "اطلاعات با موفقیت ارسال شد";
        $final = array(
            "status" => $status,
            "message" => $message,
            "data" => $carrierUser
        );

        return response()->json($final);
    }


    public function login(Request $request)
    {
        if ($token = JWTAuth::attempt($request->only(['phone', 'password']))) {
            $user = User::where('phone', $request->phone)->first();
            $status = "200";
            $message = "اطلاعات ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message,
                "driver_id" => $user
            );

            return response()->json($final);
        }
    }

    public function changePassword(Request $request)
    {

        $user = User::where('id', $request->user_id)->where('password', md5($request->old_pass))->first();


        if ($user->id ?? 0) {
            $user->password = md5($request->new_pass);
            $user->save();
            $status = "200";
            $message = "رمز عبور شما با موفقیت تغییر یافت";

            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "400";
            $message = "رمز عبور فعلی نادرست می باشد";

            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }

    public function updateWorker(Request $request, $id)
    {
        $carrierUser = CarrierUser::find($id);
        $user = User::find($carrierUser->user_id);
        $carrierUser->national_code = $request->national_code;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        if ($carrierUser->save()) {
            $user->save();
            return back();
        }
        return "مشکل در ذخیره اطلاعات";
    }

    public function SiteRegister(Request $request)

    {
        $valid = true;
        $inputs = ['first_name', 'last_name', 'phone', 'address', 'national_code'];
        foreach ($inputs as $value) {
            if ($request->$value == null) {
                $valid = false;
            }
        }
        if ($valid) {
            $chack = User::where('phone', $request->phone)->get();
            if (!count($chack)) {

                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->phone = $request->phone;
                $user->address = $request->address;
                if ($user->save()) {
                    $carrierUser = new CarrierUser();
                    $carrierUser->user_id = $user->id;
                    $carrierUser->national_code = $request->national_code;
                    $carrierUser->carrier_id = $request->carrier_id;
                    $carrierUser->status = 0;
                    if ($carrierUser->save()) {
                        return redirect('http://parhamsite.ir?requster=true');
                    }
                } else {
                    return '<h1> اختلال در ثبت اطلاعات</h1>';
                }
            } else {
                return redirect('http://parhamsite.ir?dublicate=true');
            }

        } else {
            return '<h1> اطلاعات به درستی وارد نشده است </h1>';
        }
        // $carrierUser->national_code = $request->national_code;
    }

    public function destroy($id)
    {
        $users = carrierUser::doesnthave('User')->get();
        foreach ($users as $key => $user) {
            $user->delete();
        }
        $usr = User::find($id)->delete();
        $carrierUser = carrierUser::where('user_id', $id)->delete();
        return back();
    }

    public function makeProvider(Request $request, $id)
    {
        $user = CarrierUser::find($id);
        $user->is_provider = !$user->is_provider;
        $user->save();
        return back();
    }
}
