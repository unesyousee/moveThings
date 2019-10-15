@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <!-- Content Header (Page header) -->
        <section class="content-header nobaar-dark">
            <h1>
                لیست کد تخفیف
            </h1>
        </section>
        <!-- Main content -->
        <section class="content index_items nobaar-dark">
            <div class="box">
                <div class="box-header  nobaar-dark">
                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add_discount_modal2">افزودن تصادفی</button>
                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add_discount_modal">افزودن دستی</button>

                 <!-- Modal -->
                    <div id="add_discount_modal2" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">افزودن کد تخفیف</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="box box-primary">
                                        <!-- /.box-header -->
                                        <!-- form start -->
                                        <form  autocomplete="off" role="form" action="{{route('discount.store')}}" method="post">
                                            {{csrf_field()}}
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label for="number"> تعداد<span class="text-danger">*</span></label>
                                                    <input required autofocus type="number" name="number" class="form-control" id="number" placeholder="تعداد" value="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="expire"> انقضا<span class="text-danger">*</span></label>
                                                    <input required autofocus type="expire" name="expire" class="form-control" id="expire" placeholder="انقضا" value="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount"> قیمت<span class="text-danger">*</span></label>
                                                    <input required autofocus type="amount" name="amount" class="priceNum form-control" id="amount" placeholder="قیمت" value="5000">
                                                </div>
                                                <div class="form-group">
                                                    <label for="carrier"> نوع سرویس<span class="text-danger">*</span></label>
                                                    <select name="carrier" id="carrier">
                                                        @foreach ( $carriers as $carrier)
                                                            <option value="{{$carrier->id}}"> {{ $carrier->name }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->

                                            <div class="box-footer">
                                                <button type="submit" name="random" class="btn btn-primary submit_user">ارسال</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div id="add_discount_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">افزودن کد تخفیف</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="box box-primary">
                                        <!-- /.box-header -->
                                        <!-- form start -->
                                        <form  autocomplete="off" role="form" action="{{route('discount.store')}}" method="post">
                                            {{csrf_field()}}
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label for="code"> کد<span class="text-danger">*</span></label>
                                                    <input required autofocus type="text" name="code" class="form-control" id="code" placeholder="کد">
                                                </div>
                                                <div class="form-group">
                                                    <label for="expire"> انقضا<span class="text-danger">*</span></label>
                                                    <input required type="expire" name="expire" class="form-control" id="expire" placeholder="انقضا" value="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount"> قیمت<span class="text-danger">*</span></label>
                                                    <input required  type="amount" name="amount" class="priceNum form-control" id="amount" placeholder="قیمت" value="5000">
                                                </div>
                                                <div class="form-group">
                                                    <label for="limit"> محدیدوید استفاده<span class="text-danger">*</span></label>
                                                    <input required  type="number" name="limit" class="form-control" id="limit" placeholder="محدیدوید استفاده" value="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="carrier"> نوع سرویس<span class="text-danger">*</span></label>
                                                    <select name="carrier" id="carrier">
                                                        @foreach ( $carriers as $carrier)
                                                        <option value="{{$carrier->id}}"> {{ $carrier->name }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->

                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary submit_user">ارسال</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <form action="{{ route('discountMultiDestroy') }}" method="post  nobaar-dark">

                    <input type="submit" class="btn btn-danger flat fa fa-remove" style="float: left;" value="حذف">
                    @csrf
                    {{method_field('DELETE')}}
                    <table class="nobaar-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>قیمت</th>
                                <th>کد</th>
                                <th>انقضا</th>
                                <th>محدودیت استفاده</th>
                                <th>نوع سرویس</th>
                                <th>دفعات استفاده</th>
                                <th><input type="checkbox" id="discount_check_all"></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($discounts as $key=>$discount)
                            <tr>
                                <td><a href="{{ route('discount.show', $discount->id) }}">{{++$key}}</a> </td>
                                <td class="amount">{{$discount->amount ?? ''}}</td>
                                <td>{{$discount->code ?? ''}}</td>
                                <td>{{dateToJal($discount->expire_time ?? '')}}</td>
                                <td>{{$discount->Limitations ?? ''}}</td>
                                <td>{{$discount->carrier->name ?? ''}}</td>
                                <td>{{$discount->discountUsages->where('status',"1")->count()}}</td>
                                <td><input type="checkbox" name="multiDiscount[{{ $discount->id }}]"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <input type="submit" class="btn btn-danger flat fa fa-remove" style="float: left;" value="حذف">
                </form>
                </div>
                    <!-- /.box-body -->
            {{ $discounts->links() }}
        </section>
    </div>
    @stop
