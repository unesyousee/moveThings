<?php

namespace App\Http\Controllers;

use App\ActivationCode;
use App\Jobs\ProcessSendSms;
use App\Order;
use App\User;
use App\CarrierUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class LoginController extends Controller
{

    public function authenticate(Request $request)
    {
        if ($request->mobile != null && $request->activeCode != null) {
            $activationCode = ActivationCode::where('phone', $request->mobile)->where('code', $request->activeCode)->latest()->first();
            if (!$activationCode) {
                $status = "403";
                $message = "کد فعال سازی ارسالی اشتباه است، دوباره تلاش نمایید";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                date_default_timezone_set('Asia/Tehran');
                $start = Carbon::now();
                $end = Carbon::parse($activationCode->created_at);
                $seconds = $end->diffInSeconds($start);
                if ($seconds > 90) {
                    $status = "405";
                    $message = "کد فعال سازی منقضی شده است، دوباره تلاش نمایید";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                } else {
                    //update user status to 1 : active
                    $user = User::where('phone', $request->mobile)->first();
                    $user->status = 1;
                    $user->save();

                    $activationCode->status = 1;
                    $activationCode->save();

                    $isFirst = $user->is_first;

                    if ($user->is_first == 1) {
                        $user->is_first = 0;
                        $user->save();
                    }

                    $order = $user->orders()->latest()->first();

                    $status = "200";
                    $message = "ثبت نام با موفقیت انجام شد.";
                    $final = array(
                        "status" => $status,
                        "message" => $message,
                        "data" => array(
                            "user_id" => $user->id,
                            "is_first" => $isFirst,
                            "name" => $user->first_name,
                            "family" => $user->last_name,
                            "moneyBag" => '0',
                            "haveTravel" => $user->is_first,
                            "order_id" => ($order != null) ? $order->id : 0,
                            'token' => JWTAuth::fromUser($user)
                        ));
                }
            }
        } else {
            $status = "404";
            $message = "مجددا تلاش نمایید";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }
    public function sendActivationCodeSMS(Request $request, $mobile)
    {

        if ($mobile != null) {
            try {
                $user = User::where('phone', $mobile)->firstOrFail();

                //send active code to mobile nummber with sms panel api
                $amount = $user->transactions()->sum('amount');

                if ($amount == null)
                    $amount = 0;

                $status = "402";
                $message = "شما قبلا ثبت نام شده اید،کد تایید برایتان ارسال شد!";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "user_cost" => $amount
                );
            } catch (ModelNotFoundException $exception) {
                $token = substr(rndTextNumeric(),14);

                $user = new User();
                $user->phone = $request->mobile;
                $user->share_code = $token;
                $user->status = 2;
                if ($user->save()) {
                    $status = "200";
                    $message = "کد تاببد برای شما ارسال شد";
                    $final = array(
                        "status" => $status,
                        "message" => $message,
                    );
                } else {
                    $status = "401";
                    $message = "مشکل در ذخیره اطلاعات، دوباره تلاش نمایید!";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }
            }
            $code = rand(1000, 9999);
            if(in_array($mobile, $GLOBALS['teamPhone'])){
                $code = 0000;
            }else{
                $code = rand(1000, 9999);
                ProcessSendSms::dispatch([
                    'phone' => $request->mobile,
                    'templateId' => "866",
                    'parameterArray' => array(array('Parameter' => "VerificationCode", 'ParameterValue' => $code))
                ])->onQueue('sms');
            }
            $activeCode = new ActivationCode();
            $activeCode->code = $code;
            $activeCode->phone = $request->mobile;
            $activeCode->save();

        } else {
            $status = "404";
            $message = "مجددا تلاش نمایید";
            $final = array(
                "status" => $status,
                "message" => $message);
        }

        return response()->json($final);
    }
    public function login(Request $request)
    {

        if($request->has('username','password')){

            $user = User::where('phone',$request->username)->where('password',md5($request->password))->first();
            if($user){
                $user_id = $user->id;

                $carrier = CarrierUser::where('user_id',$user->id)->first();

                $final_array = array(
                    "name" => $user->first_name,
                    "family" => $user->last_name,
                    "email" => $user->email,
                    "image" => $user->profile_pic,
                    "id" => $carrier->id,
                    "user_id" => $user_id,
                    'token' => JWTAuth::fromUser($user)
                );

                $status = "200";
                $message = "اطلاعات ارسال شد";

                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "driver_id" => array($final_array)
                );

            }else{
                $status = "405";
                $message = "شماره تلفن یا رمز عبور اشتباه است";

                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

        }else{
            $status = "404";
            $message = "مقادیر خالی رها شده است،از پر بودن مقادیر اطمینان حاصل نمایید!";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
        // if ($token = JWTAuth::attempt($request->only(['phone', 'password'])))
        // {
        //     $user = User::where('phone', $request->phone)->first();
        //     $status = "200";
        //     $message = "اطلاعات ارسال شد.";

        //     $final = array(
        //         "status" => $status,
        //         "message" => $message,
        //         "driver_id" => $user
        //     );

        //     return response()->json($final);
        // }
    }
}
