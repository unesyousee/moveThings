<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSendSms;
use App\Order;
use App\User;
use App\Transaction;
//use Illuminate\Foundation\Auth\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function MongoDB\BSON\toJSON;
use SoapClient;
use JWTAuth;
use App\Discount;
use App\DiscountUsage;
use App\Price;

class TransactionController extends Controller
{

    public function nobaarAccounting(Request $request)
    {
        $transaction_type = [0 => 'کد تخفیف', 1 => 'کمسیون', 2 => 'شارز کیف پول', 3 => 'کسر از کیف پول', 4 => 'مبلغ سفارش', 5 => 'جریمه بابت تاخیر', 6 => 'اصلاحیه', 7 => 'پرداخت بانکی', 8 => 'دریافت نقدی', 9 => 'پرداخت نقدی', 10 => 'تسویه حساب',];
        $transactions = null;
        $from = $request->start ? gmdate('Y-m-d', $request->start / 1000) . ' 00:00:00' : now()->subDays(30)->toDateTimeString();
        $to = $request->finish ? gmdate('Y-m-d H:i:s', $request->finish / 1000) : now()->addDay()->toDateTimeString();
        $type = $request->type != null ? [$request->type] : [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        if ($request->method() == 'POST') {
            $transactions = Transaction::whereIn('transaction_type', $type)->whereBetween('created_at', [$from, $to])->get();
        }
        return view('admin/nobaarAcounting/index', compact('transactions', 'transaction_type'));
    }

    public function driverCheckout(Request $request)
    {
        $type = $request->description ?? 'تراکنش کنش از نوبار';
        $trans = new TransactionSaver();
        $trans->user_id = $request->user_id;
        $operator = Auth::user()->id;
        $amount = str_replace(',', '', $request->amount);
        $trans->chargeUserWallet($amount, $type);

        return back();
    }

    public function index(Request $request)
    {
        // $request->user()->authorizeRoles('manager');
        $user = User::find(269);
        $wallets = [];
        $users = User::doesntHave('carrierUsers')->get();
        foreach ($users as $key => $user) {
            $wallets[$key] = $user->transactions()->where("status", 1)->get();
        }
        $transactions = Transaction::where('status', 1)->paginate(15, ['*'], 'transactions');
        $driver = User::whereHas('carrierUsers', function ($query) {

            $query->where('parent_id', null);
        })->paginate(50, ['*'], 'driver');
        return view('admin.accounting.index', compact('transactions', 'driver', 'wallets'));
    }

    public function store(Request $request)
    {
        if ($request->has(['authority', 'amount'])) {
            $transaction = new Transaction();
            $transaction->authority = $request->authority;
            $transaction->amount = $request->amount;
            if ($request->has('order_id'))
                $transaction->order()->associate($request->order_id);
            $transaction->user()->associate(JWTAuth::parseToken()->toUser()->id);
            $transaction->save();
            $response = [
                'status' => '200',
                'message' => 'تراکنش با موفقیت ایجاد شد.'
            ];
        } else {
            $response = [
                'status' => '405',
                'message' => 'مقادیر خالی رها شده اند.'
            ];
        }
        return response()->json($response);
    }

    public function update(Request $request)
    {
        date_default_timezone_set("Asia/Tehran");
//        if ($request->has(['RefId', 'SaleReferenceId'])) {

        $transaction = Transaction::find($request->ResNum);
//            Log::info('Transaction: '.json_encode($request->all()));

        if ($transaction) {

            $client = new \SoapClient('https://verify.sep.ir/Payments/ReferencePayment.asmx?WSDL');
            $result = $client->VerifyTransaction($request->RefNum, $request->MID);
            if ($result > 0) {

                $transaction->status = 1;
                $transaction->save();

                $status = "200";
                $message = "تراکنش با موفقیت ثبت شد.";
                $final = array(
                    "status" => $status,
                    "message" => $message,
                    "result" => $result
                );
            } else {
                $status = "410";
                $message = "تراکنش تایید نشد مبلغ تا 72 ساعت آینده برگت داده می شود.";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

        } else {
            $status = "410";
            $message = "تراکنش همخوانی اطلاعاتی ندارد. مبلغ تا 72 ساعت آینده برگت داده می شود.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
//        } else {
//            $status = "405";
//            $message = "مقادیر خالی رها شده است.";
//            $final = array(
//                "status" => $status,
//                "message" => $message
//            );
//        }
        return response()->json($final);
    }

    public function redirect(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        date_default_timezone_set("Asia/Tehran");
        if ($request->has('type')) {
            if ($request->has('amount')) {
                $transaction = new Transaction();
                $amount = $request->amount;
                $transaction->amount = $amount;
                if ($request->has('order_id')) {
                    $transaction->order_id = $request->order_id;
                    $order = Order::find($request->order_id);
                    $amount -= $user->transactions()->where('status', 1)->sum('amount');
                    $transaction->amount = $amount;
                }

                if ($request->has('redirect_type')) {
                    $transaction->redirect_type = $request->redirect_type;
                }

                $transaction->status = 2;
                $transaction->description = 'پرداخت بانکی';
                $transaction->transaction_type = 7;
                $transaction->user()->associate(JWTAuth::parseToken()->authenticate()->id);
                if ($request->has('user_type'))
                    $transaction->user_type = $request->user_type;
                if ($transaction->save()) {
                    $client = new \SoapClient('https://sep.shaparak.ir/payments/initpayment.asmx?wsdl');
                    $result = $client->RequestToken('11342142', intval($transaction->id), intval($amount));
                    if ($result != '') {
                        $transaction->authority = $result;

                        if ($transaction->save()) {
                            $status = "200";
                            $message = "تراکنش با موفقیت ایجاد شد ارسال به درگاه بانک پذیرنده.";
                            $final = array(
                                "status" => $status,
                                "message" => $message,
                                "data" => array(
                                    "token" => urlencode($result),
                                )
                            );
                        } else {
                            $status = "407";
                            $message = "مجددا تلاش نمایید";
                            $final = array(
                                "status" => $status,
                                "message" => $message
                            );
                        }
                    } else {
                        $status = "408";
                        $message = "مشکل از طرف بانک پذیرنده، دوباره تلاش نمایید!";
                        $final = array(
                            "status" => $status,
                            "message" => $message
                        );
                    }
                } else {
                    $status = "410";
                    $message = "مجددا تلاش نمایید";
                    $final = array(
                        "status" => $status,
                        "message" => $message
                    );
                }

            } else {
                $status = "404";
                $message = "اطلاعات کامل ارسال نشده است دوباره تلاش نمایید!";
                $final = array(
                    "status" => $status,
                    "message" => $message
                );
            }

        } else {
            $status = "406";
            $message = "مشکل در تعیین نشدن نوع تراکنش";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function paymentCheck(Request $request)
    {
        $order_id = $request->order_id;
        $by_credit = $request->by_credit;
        $order = Order::find($order_id);
        $driver_user_id = $order->carrierUsers()->where('parent_id', null)->first()->user->id;
        $commission = $order->carrierUsers()->where('parent_id', null)->first()->commission;
        $user = JWTAuth::parseToken()->toUser();

        if ($order->status == 6) {
            $status = 200;
            $message = "این سفارش پرداخت شده است";
            $isEnough = True;
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => array(
                    "is_enough" => $isEnough
                )
            );
            return response()->json($final);
        }


        if ($by_credit == 1) {
            $amount = $user->transactions()->where('status', 1)->sum('amount');
            $new_price = $order->price;

            $discount_usage = DiscountUsage::where('order_id', $order_id)->where('status', 0)->first();
            if ($discount_usage != null) {
                if ($discount_usage->discount_id != null) {
                    $discount = Discount::find($discount_usage->discount_id);
                    if ($discount->type == 0) {
                        $new_price -= $discount->amount;
                    } else {
                        $new_price -= ($new_price * $discount->amount / 100);
                    }

                } else {
                    $discount = Price::where('title', 'کد معرف')->first();
                    if ($discount->type == 0) {
                        $new_price -= $discount->amount;
                    } else {
                        $new_price -= ($new_price * $discount->amount / 100);
                    }
                }
            }
            if ($amount >= $new_price)
                $isEnough = true;
            else
                $isEnough = false;
            $status = "200";
            if ($isEnough) {
                $discount_usage = DiscountUsage::where('order_id', $request->order_id)->where('status', 0)->first();
                if ($discount_usage != null) {
                    $discount_usage->status = 1;
                    $discount_usage->save();

                    if ($discount_usage->discount_id != null) {
                        $discount = Discount::find($discount_usage->discount_id);
                        $discount->usage_number += 1;
                        $discount->save();
                    }
                }
                $t = new TransactionSaver();
                $t->driver_user_id = $driver_user_id;
                $t->user_id = $user->id;
                $t->order_id = $request->order_id;
                $third = $order->thirdparty()->first() ?? null;
                if ($third) {
                    $t->third_user_id = $third->user->id;
                    $t->ThirdCommission()->driverOnlineOrder()->userOnlineOrder();
                } else {
                    $t->driverOnlineOrder()->userOnlineOrder();
                }

//                $t->driverOnlineOrder()->userOnlineOrder();
                /*$transaction = new Transaction();
                $transaction->amount = $order->price - $new_price;
                $transaction->status = 1;
                $transaction->user()->associate($user->id);
                $transaction->save();

                $transaction = new Transaction();
                $transaction->amount = -1 * $order->price;
                $transaction->status = 1;
                $transaction->user()->associate($user->id);
                $transaction->save();

                $price_commission = ($order->price) - ($order->price * $commission / 100);
                $transaction = new Transaction();
                $transaction->amount = $price_commission;
                $transaction->order_id = $request->order_id;
                $transaction->user_id = $driver_user_id;
                $transaction->status = 1;
                $transaction->save();*/

                $order->status = "6";
                $order->payment_status = "1";
                $order->transaction_type = 4;
                $order->save();
                $message = "از اعتبار شما با موفقیت کم شده.";
                ProcessSendSms::dispatch([
                    'phone' => $order->carrierUsers()->where('parent_id', null)->first()->user->phone,
                    'templateId' => '5822',
                    'parameterArray' => ['OrderId' => $order->id]
                ])->onQueue('sms');
            } else
                $message = "اعتبار کافی نیست.";

            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => array(
                    "is_enough" => $isEnough
                )
            );
        } else {


            $discount_usage = DiscountUsage::where('order_id', $request->order_id)->where('status', 0)->first();

            if ($discount_usage != null) {
                // order has discount
                $discount_usage->status = 1;
                $discount_usage->save();

                if ($discount_usage->discount_id != null) {
                    $discount = Discount::find($discount_usage->discount_id);
                    $discount->usage_number += 1;
                    $discount->save();

                    $transaction2 = new Transaction();
                    if ($discount->type == 0) {
                        $transaction2->amount = $discount->amount;
                    } else {
                        $transaction2->amount = $order->price * $discount->amount / 100;
                    }
                    $transaction2->order_id = $request->order_id;
                    $transaction2->user_id = $driver_user_id;
                    $transaction2->description = 'کد تخفیف';
                    $transaction2->status = 1;
                    $transaction2->save();
                } else {
                    $transaction2 = new Transaction();
                    $amount_discount = Price::where('title', 'کد معرف')->first();
                    if ($amount_discount->type == 0) {
                        $transaction2->amount = $amount_discount->amount;
                    } else {
                        $transaction2->amount = $order->price * $amount_discount->amount / 100;
                    }

                    $transaction2->order_id = $request->order_id;
                    $transaction2->user_id = $driver_user_id;
                    $transaction2->status = 1;
                    $transaction2->save();
                }
            }

            $transaction = new TransactionSaver();
            $transaction->order_id = $request->order_id;
            $transaction->user_id = $user->id;
            $transaction->driver_user_id = $driver_user_id;
            $third = $order->thirdparty()->first() ?? null;
            if ($third) {
                $transaction->third_user_id = $third->user->id;
                if ($order->transaction_type == 2) {
                    $transaction->ThirdCommission()->nobaarGetCashDriver();
                } elseif ($order->transaction_type == 3) {
                    // payment to third party
                    $transaction->thirdOnlineDriver()->thirdOnlineThird();

                } else {
                    $transaction->userCashOrder()->driverGetCash()->ThirdCommission();
                    $order->transaction_type = 1;
                }
            } else {
                if ($order->transaction_type == 2) {
                    $transaction->nobaarGetCashDriver();
                } else {
                    $transaction->userCashOrder()->driverGetCash();
                    $order->transaction_type = 1;
                }
            }
            /*
             $price_commission = -1 * ($order->price * $commission / 100);
             $transaction = new Transaction();
             $transaction->amount = $price_commission;
             $transaction->order_id = $request->order_id;
             $transaction->user_id = $driver_user_id;
             $transaction->status = 1;
             $transaction->save();*/

            $order->status = "6";
            $order->is_paid = 1;
            $order->payment_status = "2";
            $order->save();


            $status = "200";
            $message = "نقدی پرداخت کنید.";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        }
        return response()->json($final);
    }

    public function destroy($id)
    {
        Transaction::find($id)->delete();
        return back();
    }

    public function getTotal(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $amount = $user->transactions()->where('status', 1)->sum('amount');

        $status = "200";
        $message = "اطلاعات ارسال شد.";
        $final = array(
            "status" => $status,
            "message" => $message,
            "amount" => $amount
        );

        return response()->json($final);
    }

    public function driverInvoice($id)
    {
        $transaction = Transaction::where("order_id", $id)->get();
        $order = Order::find($id);

        if ($order->status == 6) {
            $worker_price = 0;

            if (intval($order->moving_workers) > 0) {
                $worker_price = $worker_price + ($order->moving_workers * Price::where('title', 'کارگر باربر')->first()->amount);
            }
            if (intval($order->packing_workers) > 0) {
                $worker_price = $worker_price + ($order->packing_workers * Price::where('title', 'کارگر بسته بندی')->first()->amount);
            }

            if (intval($order->layout_worker) > 0) {
                $worker_price = $worker_price + ($order->layout_worker * Price::where('title', 'کارگر چیدمان')->first()->amount);
            }
            if (intval($order->tech_worker) > 0) {
                $worker_price = $worker_price + ($order->tech_worker * Price::where('title', 'کارگر فنی')->first()->amount);
            }

            $gift = $worker_price + ($order->gift ?? 0);

            $Commission = 0;
            if ($order->carrier_id < 5)
                $Commission = intval(($order->price - $gift) / 10);

            $discount = $transaction->where("transaction_type", 0)->first()->amount ?? 0;

            $pure = $order->price - ($Commission);

            $detail = [
                ['title' => "مبلغ کل", "price" => $order->price],

                ['title' => "کمیسیون نوبار", "price" => $Commission],

                ['title' => "کد تخفیف", "price" => $discount],

                ['title' => "پاداش نوبار", "price" => $gift / 10],
            ];
            foreach ($detail as $key => $val)
                if ($val == 0)
                    unset($detail[$key]);
            $is_cash =
                !empty($transaction->where("online", 1)->first())
                    ? ["price" => abs($pure), "title" => " ریال به حساب شما واریز خواهد شد."]
                    : ["price" => $order->price - $discount, "title" => " ریال نقدا از مشتری دریافت نمودید"];

            $is_IPG = ($transaction->where([
                "ref_id","not","null"
            ])->count());
            $result = [
                "status" => 200,
                "message" => "اطلاعات با موفقیت انجام شد",
                "data" => [
                    "amount" => $pure,
                    "payment_type" => !$is_IPG? "نقدی": "اعتباری",//TODO put here in config
                    "detail" => $detail,
                    "total" => $order->price,
                    "date" => dateTojal($order->moving_time),
                    "user" => $order->phone != "09338931751" ? $order->receiver_name : $order->user->first_name . " " . $order->user->last_name,
                    "description" => "صافی راننده",// TODO put here in config
                ]
            ];
        } else {
            $result = [
                "status" => 404,
                "message" => "فاکتوری برای این سفارش صادر نشده است",
            ];
        }
        return response()->json($result);
    }

    public function getDriverTransactions(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $transactions = $user->transactions()->where('status', 1)->orderBy('id', 'DESC')->get();
        $amount = 0;
        /*        foreach ($transactions as $key => $p) {
                    if ($p["amount"] < 0) {
                        $transactions[$key]['transaction_is_deposit'] = 1;
        //                $transactions[$key]['description'] = "بدهی به نوبار بابت سفارش" . $p["order_id"];
                        $transactions[$key]['description'] = config("transaction.adaptor.types")[$p->transaction_type];
                        $amount += $p["amount"];
                    } else {
                        $transactions[$key]['description'] = config("transaction.adaptor.credit") . $p["order_id"];
                        $transactions[$key]['transaction_is_deposit'] = 0;
                        $amount += $p["amount"];
                    }
                }*/
        foreach ($transactions as $key => $p) {
            $transactions[$key]['transaction_is_deposit'] = $transactions[$key]["amount"] > 0 ? 0 : 1;
//                $transactions[$key]['description'] = "بدهی به نوبار بابت سفارش" . $p["order_id"];
            if ($p->transaction_type) {
                $transactions[$key]['description'] = config("transaction.types")[$p->transaction_type];
            }
            $amount += $p["amount"];
        }
        $amount -= $user->orders()->whereIn('status', [2, 3, 4, 5])->sum('price');

        $status = "200";
        $message = "اطلاعات ارسال شد.";
        $final = array(
            "status" => $status,
            "message" => $message,
            "data" => $transactions,
            "credit" => $amount
        );

        return response()->json($final);
    }

    public function settle(Request $request)
    {
        $transaction = Transaction::find($request->ResNum);

        if ($transaction->user_type == "2")
            $url = 'driver';
        else if ($transaction->order_id != null)
            $url = 'factor';
        else
            $url = 'main';


        if ($request->State != '' && $request->StateCode != '' && $request->State == 'OK') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => url('/api/update_transaction'),
                CURLOPT_USERAGENT => 'update_transaction',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array(
                    'RefNum' => $request->RefNum,
                    'MID' => $request->MID,
                    'ResNum' => $request->ResNum
                )
            ));

            $result = curl_exec($curl);
            curl_close($curl);

            $message = json_decode($result)->message;
            $status = 1;

        } else {
            $status = 0;
            if ($request->StateCode == -1)
                $message = 'پرداخت لغو شد';
            else
                $message = "مشکل در پارامتر های ارسالی از درگاه بانک. مبلغ تراکنش انجام شده تا 72 ساعت آینده به حسابتان برگشت داده خواهد شد.";
        }
        $amount = $transaction->amount;
        $user = $transaction->user;
        $credit = Transaction::where("user_id", $user->id)->where('status', 1)->sum('amount');
        if ($transaction->redirect_type == null)
            return view('settle', compact('url', 'message', 'status', 'amount', 'credit', 'user'));
        else {
            $url = config("payment.redirect." . $transaction->redirect_rype);
            return view('settle2', compact('url', 'message', 'status', 'amount', 'credit', 'user'));
        }
//        echo '<!DOCTYPE html> <html> <head> <meta charset="utf-8"> <meta name="viewport" content="width=device-width, initial-scale=1"> <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script> </head> <body> <div class="container"> <div class="row"> <div class="col-md-12"> <h4 class="text-center bold-font" style="margin-top:20px;color: #e03300">' . $message . '</h4> </div> <br/> <br/> <input class="form-control text-center" type="text" disabled value="' . $request->RefId . '" style="color: black; background: white; font-size: 1.5em;" class="readonly"> <br/> <br/> <br/> <img src="https://app.nobaarapp.ir/admin/assets/images/logo.png" sheight="300" style="display: block;margin: 0 auto;"> <a class="btn btn-success col-sm-l2 col-xs-l2" href ="nobaar://' . $url . '?transaction=true&pardakht=ok&price=2000" style="position: fixed;;bottom: 0; left: 0; right: 0">برگشت به اپلیکیشن</a> </div> </div> </body> </html>';
    }

    public function direct(Request $request)
    {
        echo "<form action='https://sep.shaparak.ir/payment.aspx' name='sepform' id='sepform' method='POST'>
                                            <input name='Token' type='hidden' value='" . $request->token . "'>
                                            <input name='RedirectURL' type='hidden' value='https://app.nobaarapp.ir/api/transaction/settle'>
                                        </form>";
        echo "<script>
        document.forms['sepform'].submit();
    </script>";
    }

    public function addToWallet(Request $request)
    {
        $t = new TransactionSaver();
        $t->user_id = $request->user_id;
        $amount = str_replace(',', '', $request->amount);
        $desc = $request->desc;
        $t->chargeUserWallet($amount, $desc);
        return back();
    }
}
