<?php
 
namespace App\Http\Controllers;


use App\App;
use App\Jobs\ProcessSendSms;

class AppController extends Controller
{
    public function checkVersion($app_id)
    {
        $app = App::where('id', $app_id)->first()->toArray();

        $status = "200";

        $message = "بررسی بروزرسانی";

        $final = array(

            "status" => $status,

            "message" => $message,

            "data" => $app

        );

        return response()->json($final);
    }
    public function AppLink(Request $request){
        $phone = $request->phone;
        ProcessSendSms::dispatch([
           'phone' => $phone,
           'templateId' => '4182',
           'parameterArray' => array(
               array(
                   "Parameter" => "Android",
                   "ParameterValue" => 'bit.ly/nobaar-android'
               ),
               array(
                   "Parameter" => "Ios",
                   "ParameterValue" => 'bit.ly/nobaar-iphone'
               ),
               array(
                   "Parameter" => "Website",
                   "ParameterValue" => 'bit.ly/nobaar-site'
               )

           )
        ])->onQueue('sms');

        return redirect('https://nobaar.com/wordpress/');
    }
}