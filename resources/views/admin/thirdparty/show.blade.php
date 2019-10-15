@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                مشخصات شریک تجاری
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box">
                <div class="box-header">
                    <h4>{{$thirdparty->user->first_name ?? ''}} {{ $thirdparty->user->last_name ?? ''}} {{ $thirdparty->user->id ?? ''}}</h4>
                    <button type="button" style="margin: 10px;" class="btn btn-info btn-lg new_user" data-toggle="modal"
                            data-target="#add_user_modal">ویرایش
                    </button>
                    <!-- Modal -->
                    <div id="add_user_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header" style="">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">ویرایش </h4>
                                </div>
                                <div class="modal-body">

                                    <div class="box box-primary">
                                        <!-- /.box-header -->
                                        <!-- form start -->
                                        <form role="form" action="/admin/users/{{$thirdparty->user->id}}" method="post">

                                            {{csrf_field()}}
                                            {{method_field('put')}}

                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label for="first_name"> نام<span
                                                            class="text-danger">*</span></label>
                                                    <input required autofocus type="text" name="first_name"
                                                           class="form-control" id="firsrt_name" placeholder="نام"
                                                           value="{{$thirdparty->user->first_name ?? ''}}">
                                                    <label for="last_name">نام خانوادگی<span
                                                            class="text-danger">*</span></label>
                                                    <input required type="text" name="last_name" class="form-control"
                                                           id="last_name" placeholder="نام خانوادگی"
                                                           value="{{$thirdparty->user->last_name ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">تلفن<span class="text-danger">*</span></label>
                                                <input required type="text" name="phone" class="form-control" id="phone"
                                                       placeholder="تلفن" value="{{$thirdparty->user->phone ?? ''}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="address">آدرس</label>
                                                <input required type="text" name="address" class="form-control"
                                                       id="address" placeholder="آدرس"
                                                       value="{{$thirdparty->user->address ?? ''}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="address">کمسیون</label>
                                                <input required type="text" name="commission" class="form-control"
                                                       id="address" placeholder="کمسیون"
                                                       value="{{$thirdparty->comission ?? ''}}">
                                            </div>
                                    </div>
                                    <!-- /.box-body -->

                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary submit_user">ارسال</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="box-body">

                <div class="container">
                    <p>شمراه تلفن : <strong>{{$thirdparty->user->phone}}</strong></p>
                    <p>آدرس : <strong>{{$thirdparty->user->address ?? 'فاقد آدرس'}}</strong></p>
                    <br>
                    <hr>

                </div>
                @php
                $transactions = $thirdparty->transactions()->orderBy('id', 'desc')->get()
                @endphp
                <table class="nobaar-table">
                    <caption>
                        <span>مالی</span>
                        <?php $sum = $transactions->sum('amount'); ?>
                        <h2 style="color: white; direction: ltr; font-size: 30px"><span class="amount">{{ $sum }}</span></h2>
                    </caption>
                    <tr>
                        <th>مبلغ:</th>
                        <th>تاریخ:</th>
                        <th>توضیحات:</th>
                        <th>متصدی:</th>
                        <th>مانده:</th>
                        <th>شماره سفارش:‌ :</th>
                    </tr>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td style="direction: ltr"><strong
                                        class="amount">{{ $transaction->amount }}</strong></td>
                            <td><strong> {{ dateTojal($transaction->created_at)}}</strong></td>
                            <td><strong>{{ $transaction->description }} </strong></td>
                            @php
                                $operator_id = $transaction->operator->id ?? null; $operator = $operator_id ?  \App\User::find($transaction->operator->id) : null;
                            @endphp
                            <td>
                                @if($operator_id)
                                    @if($operator->roles()->exists())
                                        <strong>
                                            {{ $transaction->operator->first_name ?? ''}} {{ $transaction->operator->last_name ?? '' }}
                                        </strong>
                                    @endif
                                @endif
                            </td>
                            <td class="amount">{{$sum}}<?php $sum -= $transaction->amount ?></td>
                            <td>
                                            <span>
                                                <a href="{{ route('orders.show', $transaction->order->id ?? '') }}">
                                                    {{$transaction->order->id ?? ''}}
                                                </a>
                                            </span>
                            </td>
                        </tr>
                    @endforeach
                </table>

                {{-- <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading display-flex space-between">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">سفارشها</a>
                            </h4>

                            <h4>{{ $thirdparty->orders->count() }}</h4>
                        </div>
                        <div id="collapse3" class="panel-collapse collapse">
                            <div class="panel-body">

                                @foreach( $thirdparty->orders as $order)
                                    @if( $order->user)
                                        <div class="qoute" style=" padding: 20px">
                                            <p>سفارش دهنده
                                                <strong>{{ $order->user->first_name ?? ''}} {{ $order->user->last_name ?? ''}}</strong>
                                            </p>
                                            @if( $order->user->phone != '09338931751')
                                                <p>شماره سفارش <a href="{{route('orders.show', $order->id)}}">
                                                        <strong>{{ $order->id}}</strong> </a></p>
                                                <p>قیمت <strong>{{ $order->price}}</strong></p>
                                                <hr>
                                            @else
                                                <p>شماره سفارش <a href="{{route('orders.show', $order->id)}}">
                                                        <strong>{{ $order->id}}</strong> </a></p>
                                                <p>سفارش دهنده <strong>کاربر سایت</strong></p>
                                                <p>قیمت <strong>{{ $order->price}}</strong></p>
                                                <hr>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>--}}
            </div>
        </section>
    </div>
@stop
