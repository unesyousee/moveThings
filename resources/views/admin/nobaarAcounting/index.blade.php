@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper  nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>
                حسابداری نوبار
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box  nobaar-dark">
                <div class="box-header">
                    <form autocomplete="off" class="form-inline" action="{{--{{ route('transactionsFilter') }}--}}" method="post">
                        @csrf
                        <input autocomplete="off" id="start_date" class="form-control" placeholder="از">
                        <input type="hidden" name="start" id="observer-start">

                        <input autocomplete="off" id="end_date" class="form-control" placeholder="تا">
                        <input type="hidden" name="finish" id="observer-end">

                        <select name="type" id="type" class="form-control">
                            <option selected></option>
                            <option value="0">کد تخفیف</option>
                            <option value="1">کمسیون</option>
                            <option value="2">شارز کیف پول</option>
                            <option value="3">کسر از کیف پول</option>
                            <option value="4">مبلغ سفارش</option>
                            <option value="5">جریمه بابت تاخیر</option>
                            <option value="6">اصلاحیه</option>
                            <option value="7">پرداخت بانکی</option>
                            <option value="8">دریافت نقدی</option>
                            <option value="9">پرداخت نقدی</option>
                            <option value="9">پرداخت نقدی</option>
                            <option value="10">تسویه حساب</option>
                        </select>
                        <input type="submit" name="btn" class="btn-sm btn-info btn-flat" value="فیلتر">
                    </form>
                </div>
            </div>
            <div class="box-body">
                @if(!is_null($transactions))
                    <table id="example2" class="nobaar-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره تراکنش</th>
                            <th>قیمت</th>
                            <th>کاربر</th>
                            <th>توضیح</th>
                            <th>سفارش</th>
                            <th>تاریخ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $trs)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$trs->id}}</td>
                                <td>{{ $trs->amount }}</td>
                                <td><strong> {{ ($trs->user->first_name ?? ''). ' '. ($trs->user->last_name ?? '') }}</strong></td>
                                <td>{{ $trs->description }}</td>
                                <td><a href="{{ route("orders.show",($trs->order_id ?? '')) }}">{{$trs->order_id ?? ''}}</a></td>
                                <td>
                                    <div style="width: max-content;">{{ dateTojal($trs->created_at) }}</div>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>جمع کل</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="amount">{{ $transactions->sum('amount') }}</td>
                        </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </div>

@stop
