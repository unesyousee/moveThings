<div class="box-body">
    <table id="example2" class="nobaar-table">
        <thead>
        <tr>
            <th>سفارش</th>
            <th>قیمت</th>
            <th>کاربر</th>
            <th>راننده</th>
            <th>تلفن</th>
            <th>زمان</th>
            <th>مدت باربری</th>
            <th>منبع</th>
            <th>وضعیت پرداخت</th>
            <th>نظر</th>
            <th>امتیاز</th>
            <th>وضعیت</th>
        </tr>
        </thead>
        <tbody>
        @foreach($all_done_orders as $key=>$order)
            <tr>
                <td class="item_id"><?= $order->seen == 0 ? '<span class="label label-danger "> &#9734;</span>' : ''  ?><a href="{{ isset($order->id) ? route('orders.show', $order->id) : '#'}}">{{$order->id}}</a></td>
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
                <td>
                    <a href="{{route('carrierUserShow', $order->carrierUsers()->where('parent_id', null)->first()->id ?? 1)}}">
                    {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->first_name) ?
                    $order->carrierUsers()->where('parent_id', null)->first()->user->first_name : '' }}
                     {{isset($order->carrierUsers()->where('parent_id', null)->first()->user->last_name) ?
                     $order->carrierUsers()->where('parent_id', null)->first()->user->last_name:''}}
                     </a>
                </td>
                <td><a href="tel://{{ $order->user->phone  != '09338931751' ?$order->user->phone: $order->receiver_phone }}">{{ $order->user->phone  != '09338931751' ?$order->user->phone: $order->receiver_phone }}</a></td>
                <td>{{dateTojal($order->moving_time ?? '') . ' '. dayOweek($order->moving_time ?? '')}} </td>
                <td>
                    {{ gmdate('H:i:s' , strtotime($order->end_time) -  strtotime($order->start_time))}}
                 </td>
                <td>
                    <form action="{{ route('sourceAssign') }}" method="post">
                        @csrf
                        <input type="hidden" value="{{ $order->id }}" name="id">
                        <select name="source" id="" onchange="this.form.submit();">
                            <option {{ $order->thirdparty_id== '' ? 'selected': '' }} value=""></option>
                            @foreach($thirdparty as $val)
                                <option {{ $order->thirdparty_id== $val->id ? 'selected': '' }} value="{{$val->id}}">{{$val->user->first_name ?? '' .' '. $val->user->last_name ?? ' '}}</option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td>
                    <?php $arr = ['نقدی به راننده', 'نقدی به نوبار', 'پرداخت به طرف سوم', 'اعتباری به نوبار',] ?>
                    @if($order->transaction_type)
                        {{$arr[$order->transaction_type-1]}}
                    @endif
                </td>
                 <td>
                    {{ $order->comment->text ?? ''}}
                 </td>
                 <td>
                    {{ $order->comment->rating ?? ''}}
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
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$all_done_orders->links()}}
</div>
