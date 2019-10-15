<div class="box-body">
    <table class="nobaar-table ">
        <thead>
        <tr>
            <th>ﺳﻔﺎﺭﺵ</th>
            <th>قیمت</th>
            <th>کاﺭﺑﺮ</th>
            <th>تلفن</th>
            <th>ﺯﻣﺎﻥ</th>
            <th>گیرنده</th>
            <th>ﻧﻮﻉ ﺧﺪﻣﺎﺕ</th>
            <th>هزینه بر</th>
            <th>ﺑﺴﺘﻪ ﺑﻨﺪ</th>
            <th>ﺑﺎﺭﺑﺮ</th>
            <th>پیگیری</th>
        </tr>
        </thead>
        <tbody>
        @foreach($coming_orders as $order)
            <tr>
                <td class="item_id"><?= $order->seen == 0 ? '<span class="label label-danger "> &#9734;</span>' : ''  ?><a href="{{ isset($order->id) ? route('orders.show', $order->id) : '#'}}">{{$order->id}}</a></td>
                <td class="amount">{{$order->price}}</td>
                <td>
                    @if( ($order->user->phone ?? '') != '09338931751')
                        <a href="{{route('users.show', (int)$order->user['id'])}}">
                            {{$order->user->first_name ?? ''}}
                            {{$order->user->last_name ?? ''}}</a>
                        @else
                        کاربر سایت 
                    @endif

                </td>
                <td>{{ ($order->receiver_phone ?? '') != '09338931751' ? $order->receiver_phone : ''}}</td>
                <td>{{dateTojal($order->moving_time ?? '') . ' '. dayOweek($order->moving_time ?? '')}} </td>
                <td>{{$order->receiver_name ?? ''}}</td>
                <td>{{$order->carrier->name ?? ''}}</td>
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
                <td>{{$order->packing_workers ?? ''}}</td>
                <td>{{$order->moving_workers ?? ''}}</td>
                <td>
                    <form action="/admin/order/tracked/{{ $order->id }}">
                        <select name="status" id=""  onchange="this.form.submit()" data-ad-client234="{{$order->tracked}}">
                            <option {{ $a = $order->tracked == 0 ? 'selected' : '' }} value="0">نشده</option>
                            <option {{ $a =  $order->tracked >0 ? 'selected' : '' }} value="1">شده</option>
                        </select>
                    </form></td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
    </table>
    {{$coming_orders->links()}}
</div>
