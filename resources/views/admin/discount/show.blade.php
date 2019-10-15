@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <!-- Content Header (Page header) -->
        <section class="content-header nobaar-dark">
            <h1>
               {{ $discount->code }}
            </h1>
        </section>

        <!-- Main content -->
        <section class="content"> <div class="box">
                <div class="box-header nobaar-dark">
                    <h4>دفعات استفاده</h4> <h2>{{ $discount->discountUsages->where('status',"1")->count()}}</h2>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="nobaar-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th> سفارش </th>
                        <th> وضعیت </th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($discount->discountUsages as $key=>$val)
                        <tr>
                            <td>{{++$key}}</td>
                            <td><a href="{{ route('orders.show',$val->order_id ?? 1)}}">{{$val->order_id ?? ''}}</a></td>
                            <td>
                                @if($val->status == 2)
                                    <span class="btn btn-danger">لغو</span>
                                @elseif($val->status == 1)
                                    <span class="btn btn-success">اعمال</span>
                                @elseif($val->status == 0)
                                    <span class="btn btn-warning">تست</span>
                                @endif

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
