<div class="box-body">
    <table class="nobaar-table">
        <thead style="text-align: center">
        <tr>
            <th>#</th>
            <th>کاربر</th>
            <th>کد تخقیق</th>
            <th>(٪)تخفیف</th>
            <th>مبلغ</th>
            <th>سفارش</th>
            <th>تاریخ</th>
        </tr>
        </thead>
        <tbody>

        @foreach($transactions as $key=>$transaction)
            <tr>
                <td>{{++$key}}</td>
                <td><a href="{{isset($transaction->user->id) ? route('users.show',$transaction->user->id) : '#'}}"> {{$transaction->user['first_name'] . ' ' .$transaction->user['last_name']}}</a></td>
                <td>{{$transaction->discountUsage->share_code ?? "" }}</td>
                <td>{{$transaction->discountUsage->discount->amount ?? ''}}</td>
                <td style="direction: ltr">{{$transaction->amount}}</td>
                <td><a href="{{ isset($transaction->order->id) ? route('orders.show', $transaction->order->id) : '#'}}">{{$transaction->order->id ?? ""}}</a></td>
                <td>{{$transaction->created_at}}</td>

            </tr>
        @endforeach

        </tbody>
        <tfoot style="text-align: center">
        <tr>
            <th>#</th>
            <th>کاربر</th>
            <th>کد تخقیق</th>
            <th>(٪)تخفیف</th>
            <th>مبلغ</th>
            <th>سفارش</th>
            <th>تاریخ</th>
        </tr>
        </tfoot>
    </table>
    {{$transactions->links()}}
</div>