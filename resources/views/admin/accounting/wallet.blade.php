<div class="box-body">
    <table class="nobaar-table">
        <thead style="text-align: center">
        <tr>
            <th>#</th>
            <th>کاربر</th>
            <th>مبلغ</th>
        </tr>
        </thead>
        <tbody>
            <?php $amounts = 0; $count=0; ?>
       @foreach($wallets as $key=>$wallet)
            @if($wallet->sum('amount')>0 && $amounts += $wallet->sum('amount') )
                <tr>
                    <td>{{++$count}}</td>
                    <td>
                        <a href="{{ route('users.show', $wallet[0]->user->id) }}">

                            {{$wallet[0]->user->first_name ?? ''}} {{ $wallet[0]->user->last_name ?? ''}} 

                        </a>
                    </td>
                    <td class="amount">{{$wallet->sum('amount')}}</td>
                </tr>
            @endif
        @endforeach
        <tr><td></td></tr>
        <tr>
            <td>جمع</td>
            <td>{{$count}}</td>
            <td class="amount">{{$amounts}}</td>
        </tr>
        </tbody>
    </table>
</div>
