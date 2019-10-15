<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/18/18
 * Time: 7:32 PM
 */

namespace App\Http\Controllers;


use App\Comment;
use App\Order;
use Illuminate\Http\Request;
use JWTAuth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        if ($request->has(['driver_id', 'rank', 'comment', 'order_id'])) {
            $comment = new Comment();
            $comment->text = $request->comment;
            $comment->rating = $request->rank;
            $comment->carrierUser()->associate($request->driver_id);
            $comment->user()->associate(JWTAuth::parseToken()->toUser()->id);
            $comment->order_id = $request->order_id;
            $comment->save();

            $status = "200";
            $message = "امتیاز با موفقیت ثبت شد.";
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

    public function checkOrderComment(Request $request)
    {

        $order = Order::find($request->order_id);
        if ($order->status < 5) {
            $status = "401";
            $message = "لطفا منتظر بمانید تا سفارش تمام شود";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } elseif ($order->status == 7) {
            $status = "401";
            $message = "سفارش شما لغو شده است";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {

            $comment = Comment::where('order_id', $order->id)->first();
            if ($comment != null) {
                $status = "401";
                $message = "شما قبلا نظر خود برای این سفارش را ثبت کرده اید";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            } else {
                $carrier_user = $order->carrierUsers()->where('parent_id', null)->first();

                $user = $carrier_user->user;

                $car = $carrier_user->carrier;
                $driver_array = array(
                    "driver_id" => $carrier_user->id,
                    "driver_name" => $user->first_name,
                    "driver_family" => $user->last_name,
                    "driver_pic" => $user->profile_pic,
                    "driver_car" => $car->name,
                );
                $status = "200";
                $message = "اطلاعات با موفقیت ارسال شد";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "data" => array($driver_array)
                );
            }

        }

        return response()->json($final);
    }

    public function addOrderComment(Request $request)
    {
        $comment = Comment::where('order_id', $request->order_id)->first();

        if (!$comment) {
            $comment = new Comment();
        }

        $comment->rating = $request->rating;
        $comment->order_id = $request->order_id;
        $comment->carrier_user_id = $request->carrieruser;
        $comment->text = $request->comment_body;
        $comment->user_id = $request->user_id;
        $comment->save();
        return back();
    }
}
