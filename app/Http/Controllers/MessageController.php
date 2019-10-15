<?php

namespace App\Http\Controllers;


use App\Message;
use Illuminate\Http\Request;
use JWTAuth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $message = new Message();
        $message->user()->associate($user->id);
        $message->title = $request->title;
        $message->body = $request->body;

        if ($message->save()) {
            $status = "200";
            $message = "پیام شما با موفقیت ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "401";
            $message = "در ارسال پیام مشکلی پیش آمده دوباره تلاش نمایید.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }

        return response()->json($final);
    }
}