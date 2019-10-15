<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>وضعیت پرداخت</title>
    <style>
        .logo{
            display: block;
            margin: 30px auto;
            height: 110px;
        }
        .user-name{
            color: #3949ab;
            text-align: center;
            font-size: 2.2em;
            margin: 15px auto 30px;
        }
        .alert {
            text-align: center;
            font-size: 1.6em;
            margin: 52px auto 40px;
        }
        .succ-checkout{
            color: #00b000;
        }
        .alert.alert-warning{
            color: #b00000;
        }
        body{
            direction: rtl;
            background-image: url("https://app.nobaarapp.ir/storage/img/transaction/pattern.png");
        }
        .icon{
            display: block;
            margin: 0 auto;
            width: 33px;
        }
        .box{
            background: white;
            display: block;
            margin: 40px auto 0;
            border: 2px solid #c3c1c4;
            width: 75%;
        }
        h2 {
            text-align: center;
            background: #fff;
            color: #6a676b;
        }
        .checkout.amount {
            font-size: 22px;
            text-align: center;
            font-weight: 900;
            color: #00b000;
        }
        .box-wrapp {
            background: #dbffdb;
            padding: 20px 20px 50px;
        }
        .current-credit{
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: 900;
            color: #3949ab;
        }
        .return {
            display: block;
            padding: 10px 15px;
            margin: -21px auto 100px;
            background-color: #3040ab;
            color: white;
            text-decoration: none;
            font-weight: 900;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>


<div class="container header">
    <img class="logo" src="https://app.nobaarapp.ir/storage/img/transaction/logo.png" alt="نوبار">
    <h1 class="user-name">{{ $user->first_name. ' '. $user->last_name }} عزیز</h1>
    @if($status)
        <div class="alert succ-checkout">{{$message}}</div>
        <img src="https://app.nobaarapp.ir/storage/img/transaction/success.png" alt="success" class="icon">
    @else
        <div class="alert alert-warning">{{ $message }}</div>
        <img src="https://app.nobaarapp.ir/storage/img/transaction/wrong.png" alt="wrong" class="icon">
    @endif
</div>
@if($status != 0)
<div class="box">
    <h2>رسید پرداخت</h2>
    <div class="box-wrapp">

        <div class="checkout amount">{{ $amount }}<span>ریال</span></div>
        <div class="current-credit">
            <div class="label">اعتبار فعلی شما</div>
            <div class="curnet-amount">{{ $credit }} ریال</div>
        </div>

    </div>
</div>
@endif
<a href="{{ $url }}?transaction=true&pardakht=ok&price=2000" class="return">بازگشت به نوبـــار</a>
</body>
</html>
