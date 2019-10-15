<div class="box-body">
    <table id="example2" class="nobaar-table">
        <thead>
        <tr>
            <th>سفارش</th>

            <th>کاربر</th>
            <th>خدمت</th>
            <th>تلفن</th>
            <th>زمان</th>
            <th>گیرنده</th>
            <th>هزینه دار</th>
            <th>بسته بند</th>
            <th>باربر</th>
            <th>قیمت</th>
            <th>تخصیص</th>
            <th>وضعیت</th>
            <th>ویرایش</th>
        </tr>
        </thead>
        <tbody>
        @foreach($new_orders as $key=>$order)
            <tr>
                <td class="item_id"><?= $order->seen == 0 ? '<span class="label label-danger "> &#9734;</span>' : ''  ?><a href="{{ isset($order->id) ? route('orders.show', $order->id) : '#'}}">{{$order->id}}</a></td>
                <td>
                    @if( ($order->user->phone ?? '') != '09338931751')
                        <a href="{{route('users.show', (int)$order->user['id'])}}">
                            {{$order->user->first_name ?? ''}} 
                            {{$order->user->last_name ?? ''}}</a>
                        @else
                        {{$order->receiver_name  ?? ''}}
                    @endif  
                </td>
                <td>{{$order->carrier->name ?? ''}}</td>
                <td><a href="tel://{{ $order->user->phone  != '09338931751' ?$order->user->phone: $order->receiver_phone }}">{{ $order->user->phone  != '09338931751' ?$order->user->phone: $order->receiver_phone }}</a></td>
                <td>{{dateTojal($order->moving_time ?? '') . ' '. dayOweek($order->moving_time ?? '')}} </td>
                <td>{{$order->receiver_name ?? ''}}</td>
                <td class="heavyItm">
                    @foreach($order->heavyThings as $heavyThing)
                        @if ($heavyThing->pivot->count)
                        <span class="heavy_img">
                            <img title="{{$heavyThing->name}}" width="26" src=" {{ $heavyThing->image ?? ''}}" alt="">
                            <span class="label label-success">{{($heavyThing->pivot->count > 1) ? $heavyThing->pivot->count : ''}}</span>
                        </span>
                        @endif
                    @endforeach
                </td>
                <td>{{$order->packing_workers}}</td>
                <td>{{$order->moving_workers}}</td>
                <td class="amount">{{$order->price}}</td>
                <td>
                    <form action="{{ route('change_driver') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="phone" value="{{$order->user->phone ?? ''}}">
                        <select class="form-control" name="driver" id="" onchange="this.form.submit()">
                            <option value="" selected></option>
                            @foreach($drivers as $driver)
                            @if($driver->carrier->name == $order->carrier->name && $driver->user->status == 1)
                                <option data-chert="{{$driver->carrier->name}} {{$order->carrier->name}}" value="{{ $driver->id ?? ''}} {{ $driver->carrier->id  ?? ''}}">{{ $driver->user->first_name ?? '' }} {{$driver->user->last_name ?? ''}} {{$driver->carrier->name ?? '' }}</option>
                            @endif
                            @endforeach
                        </select>
                    </form>
                </td>
                <td>
                    <form action="{{route('orders.update',$order->id)}}" method="post" class="change_status">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <select class="form-control" name="change_status" onchange="this.form.submit();">
                            <option selected disabled value="1">جدید</option>
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
                                    <h3 class="modal-title">{{ $order->user['first_name'] }} {{$order->user['last_name'] }}</h3>
                                </div>
                                <div class="modal-body">
                                    {{--modal-body--}}
                                    <form action="/admin/orders/{{$order->id}}" method="post">
                                        @csrf
                                        {{ method_field('put') }}
                                        <input type="hidden" name="car_id" value="{{$order->carrier->id ?? ''}}">
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
                                                        <span class="value">{{$order->heavyThings[$key]->pivot->count}}</span>
                                                            <input name="vasayeleHazinedar[{{$order->heavyThings[$key]->id}}]" type="range" min="0" max="10" value="{{$order->heavyThings[$key]->pivot->count}}" class="slider" id="">
                                                        @else
                                                        <p style="margin-top: 20px">{{$val->name}}</p>
                                                        <span class="value">0</span>
                                                            <input name="vasayeleHazinedar[{{$val->id}}]" type="range" min="0" max="10" value="0" class="slider" id="">

                                                        @endif
                                                  @endforeach
                                                </div>
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
            @endforeach
        </tbody>
    </table>
    {{$new_orders->render()}}
        <script>
            setTimeout( function () {
    @foreach($new_orders as $key=>$order)
        $('#order-data{{$order->id}}').persianDatepicker({
            observer: true,
            altFormat: 'u',
            initialValue: false,
            minute : true,
            format: 'YYYY-MM-DD',
            altField: '#observe-alt{{$order->id}}'
        });
        @endforeach
    },2000)
        </script>
</div>
