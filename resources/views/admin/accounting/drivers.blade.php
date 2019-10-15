<div class="box-body">
    <table class="nobaar-table">
        <thead style="text-align: center">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>موجودی</th>
            <th>تاریخ</th>
            <th>پرداخت</th>
            <th>تسویه</th>
        </tr>
        </thead>
        <tbody>

        @foreach($driver as $key=>$val)
            <tr>
                <td> {{++$key}} </td>
                <td>
                    <a href="{{route('carrierUserShow', $val->carrierUsers()->first()->id)}}">{{$val->first_name ?? ''}} {{ $val->last_name ?? '' }} </a>
                </td>
                <td class="amount"> {{$amount = $val->transactions->where('status', 1)->sum('amount') ?? ''}} </td>
                <td> {{ dateToJal($val->transactions->last()->updated_at ?? '')}} </td>
                <td>
                    <form class="form-inline" action="{{ route('driverCheckout')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="{{$val->id}}">
                            <input type="text" autocomplete="off" name="amount" class="amount priceNum form-control" placeholder="مبلغ">
                            <select name="description" id="filla">
                                <option value="13">پاداش راننده</option>
                                <option value="6">اصلاحیه</option>
                                <option value="14">جریمه خسارت بار</option>
                                <option value="10">واریز به کارت راننده</option>
                            </select>
                            <button type="submit" class="btn btn-success">پرداخت</button>
                        </div>
                    </form>
                </td>
                <td>
                    <form action="{{ route('driverCheckout')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="{{$val->id}}">
                            <input type="hidden" value="تسویه حساب" name="description">
                            <input type="hidden" autocomplete="off" name="amount"
                                   value="{{intval($amount)>0 ? '-' : ''}}{{abs($amount)}}">
                            <button type="submit" class="btn btn-danger">تسویه حساب</button>
                        </div>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $driver->links() }}
</div>
