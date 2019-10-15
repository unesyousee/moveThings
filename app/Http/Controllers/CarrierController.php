<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/12/18
 * Time: 8:55 PM
 */

namespace App\Http\Controllers;


use App\Carrier;
use App\CarrierUser;

class CarrierController extends Controller
{
    public function all()
    {
        $data = Carrier::all();
        if (sizeof($data) == 0) {
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
                "data" => $data
            );
        }
        return response()->json($final);
    }
}