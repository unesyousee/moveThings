<?php
// test
namespace App\Http\Controllers;

use App\FcmRegisterId;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Jobs\ProcessNotification;
use App\Jobs\ProcessNotificationTopic;
use App\NotifMessage;
use App\Observer;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\CarrierUser;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use App\Price;
use App\Log;
use App\DiscountUsage;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UserController extends Controller
{
    public function storeCustomer(Request $request)
    {
        $data = $request->except("_token");
        $data = array_merge($data,[
            'share_code' => str_random(6)
        ]);
       $user =  User::create($data);
        return back();
    }
    public function index()
    {
        $today = User::whereDate('created_at', Carbon::today())->get();
        $users = User::doesntHave('carrierUsers')->doesntHave('thirdparty')->latest()->paginate(40);
        return view('admin/users/index', compact('users', 'today'));
    }

    public function boot(Request $request){

        $user = User::find($request->user_id);
        if (!is_null($user)) {
            $orders = $user->orders()->doesntHave('comment')->where('status',6)->orderBy('updated_at', 'desc')->first();
            if (!is_null($orders)){
                $unrated_order = $orders;
                $carrier_user = $orders->carrierUsers()->where('parent_id', null)->first();

                $user = $carrier_user->user;

                $car = $carrier_user->carrier;
                $unrated_order = array(
                    "order" => $orders->toArray(),
                    "driver_id" => $carrier_user->id,
                    "driver_name" => $user->first_name,
                    "driver_family" => $user->last_name,
                    "driver_pic" => $user->profile_pic,
                    "driver_car" => $car->name,
                );
            }
            else
                $unrated_order = null;
            $status = "200";
            $message = "کاربر وجود دارد";

            $final = array(
                'unrated_order' => $unrated_order,
                "status" => $status,
                "message" => $message
            );
            return response()->json($final);
        }else{
            $status = "401";
            $message = "کاربر وجود ندارد";

            $final = array(
                "status" => $status,
                "message" => $message
            );
            return response()->json($final);
        }
    }

    public function show($id)
    {
        $transactions = Transaction::where("user_id", $id)->where('status', 1)->get();
        $statuses = ['در حال سفارش ', 'جدید', 'پذیرفته شده', 'نیاز به ویرایش', 'شروع باربری', 'اتمام باربری', 'فرایند تکمیل شده', 'لغو شده'];
        $financial = ['ضعیف', 'متوسط', 'خوب', 'عالی'];
        $user = User::find($id);
        return view('admin/users/show', compact('user', 'financial', 'statuses', 'transactions'));
    }

    public function store(Request $request)
    {
        $id = User::whereRaw('id = (select max(`id`) from users)')->first();
        $id = (int)$id->id;
        $users = new User;
        $carrier_user = new CarrierUser();
        $users->id = (int)$id + 1;
        $users->first_name = $request->first_name;
        $users->last_name = $request->last_name;
        $users->address = $request->address;
        $users->password = $request->password;
        $users->phone = $request->phone;
        $users->status = 1;
        $users->password = md5($request->password);
        $users->email = $request->email;
        $carrier_user->national_code = $request->national_code;
        $carrier_user->user_id = $id + 1;
        $carrier_user->commission = $request->commission;
        $carrier_user->status = 1;
        $carrier_user->carrier_id = $request->carrier;
        $users->type = 2;
        $users->save();
        $carrier_user->save();
        return back();

    }

    public function search(Request $request)
    {
        $users = User::doesntHave('carrierUsers')->where('first_name', 'like', '%' . $request->search . '%')
            ->orWhere('last_name', 'like', '%' . $request->search . '%')
            ->orWhere('phone', 'like', '%' . $request->search . '%')
            ->orderBy('created_at', 'desc')->paginate(15);
        return response()->json(array(
            'msg' => $users
        ));
//        return view('admin/users/search', compact('users'));
    }

    public function update(Request $request, $id)
    {
        if ($request->disable) {
            $user = User::find($request->id);
            $user->status = 0;
            $user->disabled_at =  date('Y-m-d H:i:s');
            $user->save();
            return back();
        } else if ($request->enable) {
            $user = User::find($request->id);
            $user->status = 1;
            $user->save();
            return back();
        } else {
            $user = User::find($id);
            if ($request->has('national_code') && $request->national_code != null) {
                $user->carrierUsers()->first()->update(['national_code'=>$request->national_code]);
            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->email = $request->email;
            if ($request->password != null && $request->password == $request->confirm) {
                $user->password = md5($request->password);
            } elseif ($request->password != $request->confirm) {
                return 'رمز های وارد شده همخوانی ندارد.';
            }
            // $national->save();
            $user->save();
            return back();
        }
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return back();
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($request->has('introduce_code') && $request->introduce_code != "") {
            $share_code = User::where('id', '!=', $user->id)->where('share_code', $request->introduce_code)->first();
            if ($share_code == null) {
                $status = "401";
                $message = "کد معرف صحیح نمی باشد";

                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

            if (!isset($final)) {

                $dis_usage = DiscountUsage::where('user_id', $user->id)->where('share_code', "!=", null)->first();

                if ($dis_usage != null) {
                    $status = "401";
                    $message = "شما قبلا از امکان کد معرف استفاده کرده اید";

                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }
            }

            if (!isset($final)) {
                $dis_usage = new DiscountUsage;
                $dis_usage->share_code = $request->introduce_code;
                $dis_usage->user_id = $user->id;
                $dis_usage->status = 1;
                $dis_usage->save();

                $trans1 = new Transaction;
                $trans1->user_id = $user->id;
                $trans1->amount = Price::where('title', 'کد معرف معرفی شونده')->first()->amount;
                $trans1->status = 1;
                $trans1->save();

                $trans2 = new Transaction;
                $trans2->user_id = $share_code->id;
                $trans2->amount = Price::where('title', 'کد معرف معرفی کننده')->first()->amount;
                $trans2->status = 1;
                $trans2->save();

                if ($request->has('name') && $request->name != '')
                    $user->first_name = $request->name;
                if ($request->has('family') && $request->family != "")
                    $user->last_name = $request->family;
                if ($request->has('email') && $request->email != "")
                    $user->email = $request->email;

                $user->save();

                $status = "200";
                $message = "اطلاعات با موفقیت ثبت شد";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

        }

        if (!isset($final)) {
            if ($request->has('name') && $request->name != '')
                $user->past_first_name = $user->first_name;
                $user->first_name = $request->name;
            if ($request->has('family') && $request->family != "")
                $user->past_last_name = $user->last_name;
                $user->last_name = $request->family;
            if ($request->has('email') && $request->email != "")
                $user->email = $request->email;

            $user->save();

            $status = "200";
            $message = "پروفایل شما به صورت کامل تکمیل و تایید شد.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function getShareCode()
    {
        $user = JWTAuth::parseToken()->toUser();

        $status = "200";
        $message = "کد اشتراک برای شما ارسال شد";

        $final = array(
            "status" => $status,
            "message" => $message,
            "share_code" => $user->share_code
        );

        return response()->json($final);

    }

    public function uploadProfilePic(Request $request)
    {
        if ($request->has('image')) {

            $request->image->store('img/profile');

            $user = JWTAuth::parseToken()->toUser();
            $user->profile_pic = Storage::url('img/profile/' . $request->image->hashName());
            $user->save();
            $status = "200";
            $message = "آپلود انجام شد";
            $final = array(
                "status" => $status,
                "message" => $message,
                'image' => Storage::url('img/profile/' . $request->image->hashName())
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

    public function adminDestroy(Request $request, $id)
    {
        $user = User::find($request->user_id);
        if ($user->roles()->first()->id == 3) {
            $user->roles()->detach($id);
            $observer = Observer::where('user_id', $user->id)->first();
            $observer->status = 0;
            $observer->save();
            $user->status = 0;
            $user->save();
        } else {
            $user->roles()->detach($id);
            $user->delete($id);
        }


        return back();
    }

    public function addAdmin(Request $request)
    {
        $user = User::firstOrNew(['email' => $request->username]);
        if (isset($user->id)) {
            return back()->with('alert', ['کاربری با این نام کاربری قبلا ثبت نام شده است']);
        }
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $phone = $request->phone;
        $password = $request->password;
        $email = $request->username;
        $role = $request->role;
        $user = new User();
        $user->password = bcrypt($password);
        $user->first_name = $first_name;
        $user->phone = $phone;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->save();
        $user->roles()->attach($role);
        if ($request->role == 3) {
            $observer = new Observer();
            $observer->user_id = $user->id;
            $observer->save();
        }
        return back();
    }

    public function manualInfo(Request $request, $id)
    {
        $user = User::find($id);
        if (!empty($request->financial) && $request->financial < 5) {
            $user->financial = $request->financial;
        }
        if (!empty($request->know_us)) {
            $user->know_us = $request->know_us;
        }
        if (!empty($request->keyword)) {
            $user->keyword = $request->keyword;
        }
        if (!empty($request->age) && $request->age < 120) {
            $user->age = $request->age;
        }
        $user->save();
        return back();
    }

    public function setRegId(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        if($request->has('device_id')){
            $reg = FcmRegisterId::where("device_id",$request->device_id)->get();
            if($reg->count()){
                $reg->registration_id =  $request->registration_id;
                $reg->save();
                return response()->json(['status' => 200]);
            }
        }
        $reg = new FcmRegisterId();
        $reg->registration_id =  $request->registration_id;
        $reg->user_id = $user->id;
        $reg->save();
        return response()->json(['status' => 200]);


    }

    public function notify(Request $request)
    {
        ProcessNotification::dispatch([
            'title' => $request->title,
            'body' => $request->body,
        ], User::find($request->user_id))->onQueue('notification');

        return response()->json(['status' => 200]);
    }

    public function notifyTopic(Request $request)
    {
        ProcessNotificationTopic::dispatch([
            'title' => $request->title,
            'body' => $request->body,
        ], User::find($request->user_id), $request->topic)->onQueue('notification');

        return response()->json(['status' => 200]);
    }

    public function AllUserExel()
    {
        $month = 2;
        $sep = "\t";
        header("Content-Type: application/xls; charset=utf-8");
        header("Content-Disposition: attachment; filename=alluser.xls");
        header("Pragma: no-cache");
        header("Expires: 0");


        $carrier_user = DB::table('carrier_user')
            ->select(
                'user_id'
            )->get()->toArray();
        foreach ($carrier_user as $val) {
            $drivers[] = $val->user_id;
        }
        $users = DB::table('users')
            ->whereNotIn('id', $drivers)
            ->select(
                'users.id as شماره کاربری',
                'users.phone as تلفن',
                'users.first_name as نام',
                'users.last_name as نام خانوادگی',
                'users.email as ایمیل',
                'users.share_code as کد معرف',
                'users.age as سن',
                'users.financial as وضعیت مالی',
                'users.know_us as نحوه اشنایی',
                'users.keyword as کلمه جستجو شده',
                'users.created_at as تاریخ ثبت نام'
            )
            ->get()->toArray();
        if (count($users)) {
            @$dd = $users[0];
            foreach ($dd as $key => $value) {
                printf($key . "\t");
            }
            print("\n");
            foreach ($users as $key => $value) {
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

    public function admins()
    {
        $arr = collect([1,2,3]);
        $users = User::whereHas('roles')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.admins.index', compact('users'));
    }

    public function showAdmin($id)
    {
        $user = User::find($id);
        $logs = Log::where('user_id',$user->id)->orderBy('created_at', 'desc')->paginate(200);
//        dd($logs->first()->requests);
        return view('admin.admins.show', compact('user', 'logs'));
    }

    public function getUserProfile($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function getUserNotifs(){
        $user = JWTAuth::parseToken()->toUser();
        $carrier_user = CarrierUser::where('user_id', $user->id)->first();
        $topic = "user";
        if ($carrier_user != null){
            $topic = "driver";
        }

        $messages = NotifMessage::where(function($q) use ($user, $topic) {
            $q->where('user_id', $user->id)
                ->orWhere('topic', $topic);
        })->get();

        $final = array(
            "status" => "200",
            "message" => "اطلاعات با موفقیت ارسال شذ",
            "data" => $messages
        );

        return response()->json($final);
    }
}
