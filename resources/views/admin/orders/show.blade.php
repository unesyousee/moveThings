@extends('admin.layout.master')
@section('content')
    <style>


    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>مشخصات سفارش</h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-4">
                    <table class="nobaar-table">
                        <caption>مشخصات سفارش</caption>
                        <thead>
                        <tr>
                            <th><h1>عنوان</h1></th>
                            <th><h1>مقدار</h1></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>شماره</td>
                            <td>{{$order->id ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>کاربر</td>
                            <td> {{$order->user->first_name ?? ''}} {{$order->user->last_name ?? ''}} <a
                                        href="tel://{{$order->user->phone ?? ''}}">{{$order->user->phone ?? ''}}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>گیرنده</td>
                            <td>
                                {{$order->receiver_name ?? ''}}
                                <a href="tel://{{$order->receiver_phone}}">{{$order->receiver_phone}}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>طرح ترافیک</td>
                            <td>
                                <form action="{{ route("traficPrice", $order->id) }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="text" name="price" class="amount priceNum "> <span class="amount">{{$order->trafic_price}}</span><p class="disabled">بعد از وارد کردن enter را فشار دهید</p>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>بیمه</td>
                            <td>
                                {{ $order->insurance ?? '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>وضعیت</td>
                            <td>
                                <form action="{{route('orders.update',$order->id)}}" method="post"
                                      class="change_status inline-form">
                                    {{ csrf_field() }}
                                    {{ method_field('put') }}
                                    <label>{{ $statuses[$order->status] }}</label>
                                    <select class="form-control inline" name="change_status"
                                            onchange="this.form.submit();"
                                            style="width: 200px">
                                        <option value=""></option>
                                        <option value="1">جدید</option>
                                        <option value="3">نیازمند ویرایش</option>
                                        <option value="4">شروع باربری</option>
                                        <option value="5">پایان باربری</option>
                                        <option value="6">تکمیل فرایند</option>
                                        <option value="7">لغو</option>
                                    </select>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>هزینه دار</td>
                            <td>
                                @foreach($order->heavyThings as $heavyThing)
                                    @if ($heavyThing->pivot->count)
                                        <span class="heavy_show">
                                                <img src=" {{ $heavyThing->image ?? ''}}"
                                                     alt="{{ $heavyThing->name }}" title="{{ $heavyThing->name }}">
                                                <span class="label label-success">{{($heavyThing->pivot->count > 1) ? $heavyThing->pivot->count : ''}}</span>
                                            </span>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td>قیمت</td>
                            <td>
                                <span class="amount">{{$order->price}} </span>
                                <span><button type="button" class="btn btn-danger" data-toggle="modal"
                                              data-target="#price">ویرایش</button></span>
                            </td>
                        </tr>
                        <tr>
                            <td>کد تخفیف طرف سوم</td>
                            <td>
                                <span><button type="button" class="btn btn-danger" data-toggle="modal"
                                              data-target="#thirdDiscount">وارد کنید</button></span>
                            </td>
                        </tr>
                        <tr>
                            <td>پیگیر</td>
                            <td>
                                @if($order->tracked != 0)
                                    <h3 style="display: inline-block; padding:  0 30px"><a
                                                href="/admin/showadmins/{{$order->tracker->id}}">{{$order->tracker->first_name . ' ' . $order->tracker->last_name}}</a>
                                    </h3></p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>توضیح کاربر</td>
                            <td>{{$order->user_description}}</td>
                        </tr>
                        <tr>
                            <td>راننده</td>
                            <td>
                                <a href="{{route('carrierUserShow', $order->carrierUsers()->where('parent_id', null)->first()->id ?? 1)}}">
                                    {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->first_name) ?
                                    $order->carrierUsers()->where('parent_id', null)->first()->user->first_name : '' }}
                                    {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->last_name) ?
                                    $order->carrierUsers()->where('parent_id', null)->first()->user->last_name:''}}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>کارگرها</td>
                            <td>
                                @php
                                    $workers = $order->carrierUsers->where('parent_id', "!=" , null)
                                @endphp
                                @if($workers)
                                    @foreach($workers as $worker)
                                        <span> {{ $worker->user->first_name . ' '. $worker->user->last_name  }} </span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>کارگر فنی</td>
                            <td>{{ $order->tech_workers}}</td>
                        </tr>
                        <tr>
                            <td>نوع خدمت</td>
                            <td>{{$order->carrier->name}}</td>
                        </tr>
                        <tr>
                            <td>توقف در مسیر</td>
                            <td>{{$order->stop_inway}}</td>
                        </tr>
                        <tr>
                            <td>باربر</td>
                            <td>{{$order->moving_workers ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>بسته بند</td>
                            <td>{{$order->packing_workers ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>تعداد طبفات مبدا:</td>
                            <td>{{$order->origin_floor ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>تعداد طبفات مقصد</td>
                            <td>{{$order->dest_floor ?? ''}}</td>
                        </tr>
                        <tr>
                            <td> فاصله در مبدا</td>
                            <td>{{$order->origin_walking ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>فاصله در مقصد</td>
                            <td>{{$order->dest_walking ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>زمان جابجایی</td>
                            <td>
                                <p>{{dateTojal($order->moving_time ?? '')}}</p>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#date">ویرایش</button>
                            </td>
                        </tr>
                        <tr>
                            <td>پاداش</td>
                            <td>
                                <p>{{ $order->gift }}</p>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#gift">ویرایش</button>
                            </td>
                        </tr>
                        <tr>
                            <td>زمان بارگیری</td>
                            <td>{{dateTojal($order->statusLogs->where('status','4')->first()->created_at ?? '')}}</td>
                        </tr>
                        <tr>
                            <td>زمان رسیدن بار</td>
                            <td>{{dateTojal($order->statusLogs->where('status','5')->first()->created_at ?? '')}}</td>
                        </tr>
                        <tr>
                            <td>زمان ثبت</td>
                            <td>{{dateTojal($order->created_at ?? '')}}</td>
                        </tr>
                        <tr>
                            <td>مبدا</td>
                            <td><a target="_blank"
                                   href="https://map.ir/lat/{{$order->originAddress['lat'] ?? ''}}/lng/{{$order->originAddress['long'] ?? ''}}/z/16">{{$order->originAddress['description'] ?? ''}}</a> {{$order->originAddress['region']}}
                            </td>
                        </tr>
                        <tr>
                            <td>مقصد</td>
                            <td><a target="_blank"
                                   href="https://map.ir/lat/{{$order->destAddress['lat'] ?? ''}}/lng/{{$order->destAddress['long'] ?? ''}}/z/16">{{$order->destAddress['description'] ?? ''}}</a> {{$order->originAddress['region']}}
                            </td>
                        </tr>
                        <tr>
                            <td>تاریخ پشتیبانی</td>
                            <td>{{dateTojal($order->opration_date ?? '')}}</td>
                        </tr>
                        <tr>
                            <td>توضیحات پشتیبان</td>
                            <td>{{$order->description ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>پلتفرم</td>
                            <td>{{$order->platform ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>مدت باربری</td>
                            <td>
                                @if($order->satus > 4 && $order->status <7)
                                    @php
                                        $start = ($order->statusLogs->where('status','4')->first()->created_at ?? '');
                                        $end = ($order->statusLogs->where('status','5')->first()->created_at ?? '');
                                    @endphp
                                    {{ gmdate('H:i:s', strtotime($end) - strtotime($start) ) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>وضعیت پرداخت</td>
                            <td>
                                <?php $arr = ['نقدی به راننده', 'نقدی به نوبار', 'پرداخت به طرف سوم', 'اعتباری به نوبار',] ?>
                                @if($order->transaction_type)
                                    {{$arr[($order->transaction_type-1)]}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>منبع</td>
                            <td> منبع: <a
                                        href="{{ route('thirdparty.show',$order->thirdparty->id ?? "") }}">{{$order->thirdparty->user->first_name ?? ''}} {{$order->thirdparty->user->last_name ?? ''}}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>نظر کاربر</td>
                            <td>{{$order->comment->text ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>امتیاز کاربر</td>
                            <td>{{$order->comment->rating ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>ناظر</td>
                            <td>@if($order->observer_id == -1) بدون
                                ناظر @else {{$order->observer->user->first_name ?? ''}} {{ $order->observer->user->last_name ?? '' }} @endif</td>
                        </tr>
                        <tr>
                            <td> امتیاز ناظر</td>
                            <td>{{$order->observer_rate ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>کد تخفیف</td>
                            <td>
                                <a href="{{route('discount.show',$order->discountUsages()->first()->discount->id ?? '')}}">{{$order->discountUsages()->first()->discount->code ?? ''}}</a>
                            </td>
                        </tr>
                        <tr id="order-signature">
                            <td>امضا</td>
                            <td><img src="{{$order->signature}}"></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8">
                    <table class="nobaar-table">
                        <caption>
                            <span>مالی</span>
                        </caption>
                        <tr>
                            <th>مبلغ</th>
                            <th>کاربر</th>
                            <th>تاریخ</th>
                            <th>توضیحات</th>
                            <th>متصدی</th>
                            <th>وضعیت</th>
                        </tr>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td style="direction: ltr"><strong
                                            class="amount">{{ $transaction->amount }}</strong></td>
                                <td>
                                    <strong> {{ $transaction->user->first_name. ' '. $transaction->user->last_name }}</strong>
                                </td>
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
                                <td>
                                    <button class="btn btn-{!! $transaction->status == 1 ? 'success">موفق': 'danger">ناموفق' !!}"></button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="col-lg-8">
                    <table class="nobaar-table">
                        <thead>
                        <tr>
                            <th><h1>زمان</h1></th>
                            <th><h1>وضعیت</h1></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ dateTojal($log->created_at)}}</td>
                                <td>{{  $statuses[$log->status]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8">
                    <table class="nobaar-table">
                        <caption>تغییرات</caption>
                        <thead>
                        <tr>
                            <th><h1>نوع</h1></th>
                            <th><h1>از</h1></th>
                            <th><h1>به</h1></th>
                            <th><h1>کاربر</h1></th>
                            <th><h1>تاریخ</h1></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->events as $event)
                            <tr>
                                <td class="amount">{{$event->title}}</td>
                                @php($changes =  (array)json_decode($event->changes))
                                @if(sizeof($changes ))
                                    <td class="amount">{{ $changes[0][0] ?? ''}}</td>
                                    <td class="amount">{{  $changes[0][1]  ?? '' }}</td>
                                    <td class="amount">{{($event->user->first_name ?? '') .' '. ($event->user->last_name ?? '') }}</td>
                                    <td>{{ dateTojal($event->created_at ?? '')}}</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-{{ empty($order->comment) ? '4' : '8'}}">
                    <form class="form-app" method="post"
                          action="{{route('manualInfo', $order->id)}}">
                        @csrf
                        <div class="form-group">
                            <label for="platform">نحوه درخواست: </label>
                            <select class="form-control" name="platform" id="platform"
                                    style="background: white; color: black;">
                                <option value="0"></option>
                                <option
                                        value="android" {{$order->platform == 'android' ? 'selected' : ''}}>
                                    اندروید
                                </option>
                                <option value="ios" {{$order->platform == 'ios' ? 'selected' : ''}}>
                                    آیفون
                                </option>
                                <option value="web" {{$order->platform == 'web' ? 'selected' : ''}}>
                                    سایت
                                </option>
                                <option value="phone" {{$order->platform == 'phone' ? 'selected' : ''}}>
                                    تلفنی
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="platform">ناظر: </label>
                            <select class="form-control" name="observer" id="observer"
                                    style="background: white; color: black;">
                                <option value="0"></option>
                                <option value="-1"> بدون ناظر</option>
                                @foreach ($observers as $obs)
                                    @if($obs->status == 0)
                                        @continue
                                    @endif
                                    <option
                                            value="{{$obs->id}}">{{$obs->user->first_name . ' ' . $obs->user->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="observer_rate">امتیاز ناظر: </label>
                            <input type="number" min="0" max="5" id="observer_rate" value=""
                                   name="observer_rate" class="form-control" placeholder="امتیاز ناظر">
                        </div>
                        <div class="form-group">
                            <label for="rg-from">توضیحات پشتیبانی: </label>
                            <input type="text" id="rg-from" value="" name="comment" class="form-control"
                                   placeholder="توضیحات پشتیبانی">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="ذخیره" name="btn"
                                   class="form-control btn- btn-flat btn-success">
                        </div>
                    </form>
                </div>
                <div class="col-lg-4">
                    @if(empty($order->comment))
                        <form method="post" class="form-app" action="{{ route('addOrderComment') }}">
                            @csrf
                            <input type="hidden" name="carrieruser"
                                   value="{{  $order->carrierUsers()->where('parent_id', null)->first()->id ?? '' }}">
                            <input type="hidden" name="order_id" value="{{  $order->id }}">
                            <input type="hidden" name="user_id"
                                   value="{{  \Illuminate\Support\Facades\Auth::user()->id }}">
                            <div class="form-group">
                                <label for="comment_body">متن نظر</label>
                                <input type="text" name="comment_body" class="form-control" value="خالی"
                                       id="comment_body">
                            </div>
                            <div class="form-group">
                                <label for="rating">امتیاز</label>
                                <select name="rating" id="rating" class="form-control">
                                    <option value="1" {{ ($order->comment->rating ?? '') == 1 ? 'selected' : '' }}>1
                                    </option>
                                    <option value="2" {{ ($order->comment->rating ?? '') == 2 ? 'selected' : '' }}>2
                                    </option>
                                    <option value="3" {{ ($order->comment->rating ?? '') == 3 ? 'selected' : '' }}>3
                                    </option>
                                    <option value="4" {{ ($order->comment->rating ?? '') == 4 ? 'selected' : '' }}>4
                                    </option>
                                    <option value="5" {{ ($order->comment->rating ?? '') == 5 ? 'selected' : '' }}>5
                                    </option>
                                </select>
                            </div>

                            <input type="submit" value="ذخیره" name="btn"
                                   class="form-control btn- btn-flat btn-success">
                        </form>
                    @endif
                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>


    <div id="thirdDiscount" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">کد تخفیف طرف سوم</h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" role="form" action="{{route('thirdDiscount')}}" method="post">
                        <div class="box box-primary">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label for="phone">قیمت(ریال)</label>
                                <input type="hidden" name="id" value="{{$order->id}}">
                                <input required type="text" name="price" class="form-control priceNum">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <div id="price" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">اصلاح قیمت</h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" role="form" action="{{route('updatePrice')}}" method="post">

                        <div class="box box-primary">
                            <!-- /.box-header -->
                            <!-- form start -->
                            {{csrf_field()}}

                            <div class="form-group">
                                <label for="phone">قیمت(ریال)</label>
                                <input type="hidden" name="id" value="{{$order->id}}">
                                <input required type="text" name="price" class="form-control priceNum" id="price">
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    <div id="date" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ویرایش تاریخ</h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" role="form" action="{{route('dateUpdate', $order->id)}}" method="post">
                        @csrf
                        {{ method_field('patch') }}
                        <div class="form-group">
                            <div class="input-group ">
                                <label for="order-data{{$order->id}}">تاریخ</label>
                                <input required id="order-data{{$order->id}}"/>
                            </div>
                            <input type="hidden" name="date" id="observe-alt{{$order->id}}">
                            <div class="input-group clockpicker">
                                <label for="clock">زمان</label>
                                <input name="clock" id="clock" type="text" required class="form-control" value="">
                            </div>
                            {{--<label for="moving_time">تعداد کارگر باربر</label>--}}
                            {{--<input name="moving_time" type="date" class="form-control" id="moving_time" value="{{ $order->moving_time }}">--}}
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    <div id="gift" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ویرایش تاریخ</h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" role="form" action="{{route('gift', $order->id)}}" method="get">
                        @csrf
                        <div class="form-group">
                            <div class="input-group ">
                                <label for="fd{{$order->id}}">مبلغ</label>
                                <input type="number" required name="gift" id="fd{{$order->id}}"/>
                            </div>
                            {{--<label for="moving_time">تعداد کارگر باربر</label>--}}
                            {{--<input name="moving_time" type="date" class="form-control" id="moving_time" value="{{ $order->moving_time }}">--}}
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
        setTimeout( function () {
            $('#order-data{{$order->id}}').persianDatepicker({
                observer: true,
                altFormat: 'u',
                initialValue: false,
                minute : true,
                format: 'YYYY-MM-DD',
                altField: '#observe-alt{{$order->id}}'
            });
        },2000)
    </script>

@stop