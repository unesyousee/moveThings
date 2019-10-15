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
            <th>پلتفرم</th>
            <th>زمان</th>
            <th>وضعیت</th>
        </tr>
        </thead>
        <tbody>
        @foreach($cancelled_orders as $key=>$order)
            <tr>
                <td><a href="{{route('orders.show', $order->id)}}">{{$order->id}}</a></td>
                <td class="amount">{{$order->price}}</td>
               <td>
                    @if( ($order->user->phone ?? '') != '09338931751')
                        <a href="{{route('users.show', (int)$order->user['id'])}}">
                            {{$order->user->first_name ?? ''}} 
                            {{$order->user->last_name ?? ''}}</a>
                        @else
                       {{$order->receiver_name  ?? ''}}
                    @endif  

                </td>
                <td>{{$order->carrier->name}}</td>
                <td>
                    <a href="{{route('carrierUserShow', $order->carrierUsers()->where('parent_id', null)->first()->id ?? 1)}}">
                    {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->first_name) ? 
                    $order->carrierUsers()->where('parent_id', null)->first()->user->first_name : '' }}
                     {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->last_name) ? 
                     $order->carrierUsers()->where('parent_id', null)->first()->user->last_name:''}}
                     </a>
                </td>
                <td class="heavyItm">
                    @foreach($order->heavyThings as $heavyThing)
                        @if ($heavyThing->pivot->count)
                        <span class="heavy_img">
                            <img width="26" title="{{$heavyThing->name}}" src=" {{ $heavyThing->image ?? ''}}" alt="">
                            <span class="label label-success">{{($heavyThing->pivot->count > 1) ? $heavyThing->pivot->count : ''}}</span>
                        </span>
                        @endif
                    @endforeach
                </td>
                <td>{{$order->packing_workers}}</td>
                <td>{{$order->moving_workers}}</td>
                <td><a href="tel://{{ $order->user->phone ?? ''  != '09338931751' ?$order->user->phone ?? '': $order->receiver_phone ?? '' }}">{{ $order->user->phone  ?? '' != '09338931751' ?$order->user->phone ?? '' : $order->receiver_phone  ?? ''}}</a></td>
                <td>{{$order->platform}} </td>
                <td>{{dateTojal($order->moving_time ?? '') . ' '. dayOweek($order->moving_time ?? '')}} </td>
                <td>
                    <form action="{{route('orders.update',$order->id ?? '')}}" method="post" class="change_status">
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
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$cancelled_orders->links()}}
</div>
