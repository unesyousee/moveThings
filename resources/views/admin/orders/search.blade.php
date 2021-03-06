@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                لیست سفارشات
            </h1>
        </section>
        <!-- Main content --> 
        <section class="content">
            <div class="box">
                <div class="box-header">
                    
                        @include('admin.orders.filter')
                </div>

                <div class="box-body">
                    <table id="example2" class="nobaar-table">
                        <thead>
                        <tr>
                            <th>سفارش</th>
                            <th>قیمت</th>
                            <th>کاربر</th>
                            <th>نوع خدمت</th>
                            <th>راننده</th>
                            <th>هزینه‌دار</th>
                            <th>بسته‌بند</th>
                            <th>باربر</th>
                            <th>تلفن</th>
                            <th>زمان</th>
                            <th>تخصیص</th>
                            <th>وضعیت</th>
                            <th>ویرایش</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $key=>$order)
                            <tr>
                                <td class="item_id"><?= $order->seen == 0 ? '<span class="label label-danger "> &#9734;</span>' : ''  ?><a href="{{ isset($order->id) ? route('orders.show', $order->id) : '#'}}">{{$order->id}}</a></td>
                                <td>{{$order->price}}</td>
                                <td>
                                    @if( ($order->user->phone ?? '') != '09338931751')
                                        <a href="{{route('users.show', (int)$order->user['id'])}}">
                                            {{$order->user->first_name ?? ''}} 
                                            {{$order->user->last_name ?? ''}}</a>
                                        @else
                                        کاربر سایت
                                    @endif  

                                </td>
                                <td>{{$order->carrier->name ?? ''}}</td>
                                <td>{{isset($order->carrierUsers()->where('parent_id', null)->first()->user->first_name) ? $order->carrierUsers()->where('parent_id', null)->first()->user->first_name:''}} {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->last_name) ? $order->carrierUsers()->where('parent_id', null)->first()->user->last_name:''}}</td>
                                <td class="heavyItm">
                                    @foreach($order->heavyThings as $heavyThing)
                                        @if ($heavyThing->pivot->count)
                                        <span class="heavy_img">
                                            <img width="26" src=" {{ $heavyThing->image ?? ''}}" alt="">
                                            <span class="label label-success">{{($heavyThing->pivot->count > 1) ? $heavyThing->pivot->count : ''}}</span>
                                        </span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{$order->packing_workers}}</td>
                                <td>{{$order->moving_workers}}</td>
                                <td>{{$order->receiver_phone}}</td>
                                <td>{{dateTojal($order->moving_time ?? '') . ' '. dayOweek($order->moving_time ?? '')}} </td>
                                <td>
                                    <form class="order_search" action="{{ route('change_driver') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <input type="hidden" name="phone" value="{{$order->user['phone']}}">
                                        <select class="form-control" name="driver" id="" onchange="this.form.submit()">
                                            <option value="" selected></option>
                                            @foreach($drivers as $driver)
                                                <option value="{{ $driver->id}} {{ $driver->carrier->id }}">{{ $driver->user->first_name  ?? ''}} {{$driver->user->last_name ?? ''}} {{$driver->carrier->name  ?? ''}}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{route('orders.update',$order->id)}}" method="post" class="change_status">
                                        {{ csrf_field() }}
                                        {{ method_field('put') }}
                                        <select class="form-control" name="change_status"onchange="this.form.submit();">
                                            <option value="1">جدید</option>
                                            <option selected value="">پذیرفت شده</option>
                                            <option value="3">نیازمند ویرایش</option>
                                            <option value="4">شروع باربری</option>
                                            <option value="5">پایان باربری</option>
                                            <option value="6">تکمیل فرایند</option>
                                            <option value="7">لغو</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#Modal{{$order->id}}"><i class="fa fa-pencil"></i></button>
                                    <div id="Modal{{$order->id}}" class="modal fade" role="dialog">
                                        <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">{{ $driver->user['first_name'] }} {{$driver->user['last_name'] }}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    {{--modal-body--}}
                                                    <form action="/admin/orders/{{$order->id}}" method="post">
                                                        @csrf
                                                        {{ method_field('put') }}
                                                        <div class="form-group">
                                                            <label for="packing_workers">تعداد کارگر بسته بند</label>
                                                            <input name="chideman_worker" type="text" class="form-control" id="packing_workers" value="{{ $order->packing_workers }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="barbar_worker">تعداد کارگر باربر</label>
                                                            <input name="barbar_worker" type="text" class="form-control" id="moving_workers" value="{{ $order->moving_workers }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="receiver_name">نام گیرنده</label>
                                                            <input name="receiver_name" type="text" class="form-control" id="receiver_name" value="{{ $order->receiver_name }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="receiver_phone">شماره گیرنده</label>
                                                            <input name="receiver_phone" type="text" class="form-control" id="receiver_phone" value="{{ $order->receiver_phone }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="origin_floor">طبقات مبدا</label>
                                                            <input name="origin_floor" type="text" class="form-control" id="origin_floor" value="{{ $order->origin_floor }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="dest_floor">طبقات مقصد</label>
                                                            <input name="destination_floor" type="text" class="form-control" id="dest_floor" value="{{ $order->dest_floor }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="origin_walking">پیاده روی مبدا</label>
                                                            <input name="origin_walking" type="text" class="form-control" id="origin_walking" value="{{ $order->origin_walking }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="dest_walking">پیاده روی مقصد</label>
                                                            <input name="destination_walking" type="text" class="form-control" id="dest_walking" value="{{ $order->dest_walking }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="dest_walking">وسایل هزینه دار</label>
                                                                <div class="range_sider_container">
                                                                @foreach( $heavyThings as $key=>$val)
                                                                    <p>
                                                                        @if(isset($order->heavyThings[$key]))
                                                                        <p style="margin-top: 20px">{{$order->heavyThings[$key]->name}}</p>
                                                                            <input name="vasayeleHazinedar[{{$order->heavyThings[$key]->id}}]" type="range" min="0" max="10" value="{{$order->heavyThings[$key]->pivot->count}}" class="slider" id="">
                                                                        @else
                                                                        <p style="margin-top: 20px">{{$val->name}}</p>
                                                                            <input name="vasayeleHazinedar[{{$val->id}}]" type="range" min="0" max="10" value="0" class="slider" id="">

                                                                        @endif
                                                                  </p>
                                                                  @endforeach
                                                                </div>
                                                        </div>
                                                        <div class="form-group" style="display: flex;">
                                                            <input class="order-data{{$key}}" value="{{ $order->moving_time }}"/>
                                                            <input style="display: none" name="date" class="datepicker-demo order-data-alt{{$key}}">
                                                            <div class="input-group clockpicker">
                                                                <input name="clock" type="text" class="form-control" value="09:30">
                                                            </div>
                                                            {{--<label for="moving_time">تعداد کارگر باربر</label>--}}
                                                            {{--<input name="moving_time" type="date" class="form-control" id="moving_time" value="{{ $order->moving_time }}">--}}
                                                        </div>
                                                        <input type="submit" value="ذخیره" class="btn btn-info btn-flat">
                                                    </form>
                                                    {{--/.modal-body--}}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
                <!-- /.box-body -->
            </div>

        </section>
    </div>
    @stop