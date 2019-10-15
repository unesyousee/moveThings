@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                مشخصات راننده
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box">
                <div class="box-header">
                    <h4>{{$carrier_user->user->first_name ?? ''}} {{ $carrier_user->user->last_name ?? ''}} {{ $carrier_user->user->id ?? ''}}</h4>


                    <button type="button" style="margin: 10px;" class="btn btn-info btn-lg new_user" data-toggle="modal"
                            data-target="#add_user_modal">ویرایش
                    </button>
                    <button type="button" style="margin: 10px;" class="btn btn-info btn-lg new_user" data-toggle="modal"
                            data-target="#upload">آپلود مدارک
                    </button>


                    <button type="button" style="margin: 10px;" class="btn btn-info btn-lg" data-toggle="modal"
                            data-target="#notif">ارسال اعلان
                    </button>
                    <!-- Modal -->


                </div>
            </div>
            <div class="box-body">

                <div class="container-fluid">
                    <div class="row">
                        <p class="col-lg-3">شمراه تلفن : <strong>{{$carrier_user->user->phone}}</strong></p>
                        <p class="col-lg-3">خدمات : <strong>{{$carrier_user->carrier->name}}</strong></p>
                        <p class="col-lg-3">آدرس : <strong>{{$carrier_user->user->address ?? 'فاقد آدرس'}}</strong></p>
                        <p class="col-lg-3">کل سفارشات : <strong>{{$carrier_user->orders->where('status',6)->count() }}</strong></p>
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading display-flex space-between">

                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" class="collapsed" aria-expanded="false">مدارک</a>
                                    </h4>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse" aria-expanded="true" style="">
                                    <div class="panel-body">
                                        @foreach($carrier_user->user->files as $file)
                                            <div class="image col-lg-3">
                                                <a href="{{$file->path}}" data-lightbox="roadtrip"><img src="{{$file->path}}" alt=""></a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--dsddsdwsds--}}
                        <div class="col-lg-8">
                            <table class="nobaar-table">
                                <caption>
                                    <span>مالی</span>
                                    <?php $sum = $transactions->sum('amount'); ?>
                                    <span class="amount">{{ $sum }}</span>
                                </caption>
                                <tr>
                                    <th>مبلغ:</th>
                                    <th>تاریخ:</th>
                                    <th>توضیحات:</th>
                                    <th>متصدی:</th>
                                    <th>مانده:</th>
                                    <th>شماره سفارش:‌ :</th>
                                </tr>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td style="direction: ltr"><strong
                                                    class="amount">{{ $transaction->amount }}</strong></td>
                                        <td><strong> {{ dateTojal($transaction->created_at)}}</strong></td>
                                        <td><strong>{{ $transaction->description }} </strong></td>
                                        @php
                                            $operator_id = $transaction->operator->id ?? null; $operator = $operator_id ?  \App\User::find($transaction->operator->id) : null;
                                        @endphp
                                        <td>
                                            @if($operator_id)
                                                @if($operator->roles()->exists())
                                                    <strong>
                                                        {{ $transaction->operator->first_name ?? ''}} {{ $transaction->operator->last_name ?? '' }}
                                                    </strong>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="amount">{{$sum}}<?php $sum -= $transaction->amount ?></td>
                                        <td>
                                            <span>
                                                <a href="{{ route('orders.show', $transaction->order->id ?? '') }}">
                                                    {{$transaction->order->id ?? ''}}
                                                </a>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <table class="nobaar-table">
                                <caption>
                                    <span>نظرات</span>
                                    @if($carrier_user->comments->sum('rating') > 0)
                                        <span>{{ round($carrier_user->comments->sum('rating')/ $carrier_user->comments->count(), 2)  }}</span>
                                    @else
                                        <span>{{0}}</span>

                                    @endif
                                </caption>
                                <thead>
                                <tr>
                                    <th>کاربر :</th>
                                    <th>امتیاز</th>
                                    <th>متن</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($carrier_user->comments()->orderBy('id','desc')->get() as $comment)
                                    <tr>
                                        <td>
                                            ‌{{ $comment->user->first_name ?? '' }} {{ $comment->user->last_name ?? ''}}</td>
                                        <td> {{ $comment->rating }}</td>
                                        <td>{{ $comment->text ?? '' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div id="notif" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ارسال اعلان </h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" role="form" action="{{route('userNotifications')}}" method="post">

                        <div class="box box-primary">
                            <!-- /.box-header -->
                            <!-- form start -->
                            {{csrf_field()}}

                            <div class="form-group">
                                <input type="hidden" name="id" value="{{ $carrier_user->user['id'] }}">
                                <label for="phone">عنوان</label>
                                <input required type="text" name="title" class="form-control" id="title">
                            </div>
                            <div class="form-group">
                                <label for="address">متن</label>
                                <input required type="text" name="body" class="form-control" id="body">
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
    <div id="upload" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">آپلود مدارک</h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" action="{{route('uploadFile')}}" method="post" enctype="multipart/form-data" class="form-inline">


                        <div class="box box-primary">
                            <!-- /.box-header -->
                            <!-- form start -->
                            {{csrf_field()}}

                            <div class="form-group">
                                <input type="hidden" name="user_id" value="{{ $carrier_user->user['id'] }}">
                                <label for="file">عکس</label>
                                <input required name="file" type="file" class="form-control" id="file">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
                        </div>
                    </form>

                        <!-- /.box-body -->

                    </form>
                </div>
            </div>
        </div>

    </div>
    <div id="add_user_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ویرایش </h4>
                </div>
                <div class="modal-body">

                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="/admin/users/{{$carrier_user->user->id}}"
                              method="post">

                            {{csrf_field()}}
                            {{method_field('put')}}

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="first_name"> نام<span
                                                class="text-danger">*</span></label>
                                    <input required autofocus type="text" name="first_name"
                                           class="form-control" id="firsrt_name" placeholder="نام"
                                           value="{{$carrier_user->user->first_name}}">
                                    <label for="last_name">نام خانوادگی<span
                                                class="text-danger">*</span></label>
                                    <input required type="text" name="last_name" class="form-control"
                                           id="last_name" placeholder="نام خانوادگی"
                                           value="{{$carrier_user->user->last_name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">تلفن<span class="text-danger">*</span></label>
                                <input required type="text" name="phone" class="form-control" id="phone"
                                       placeholder="تلفن" value="{{$carrier_user->user->phone}}">
                            </div>
                            <div class="form-group">
                                <label for="address">آدرس</label>
                                <input required type="text" name="address" class="form-control"
                                       id="address" placeholder="آدرس"
                                       value="{{$carrier_user->user->address}}">
                            </div>
                            <div class="form-group">
                                <label for="address">کمسیون</label>
                                <input required type="text" name="commission" class="form-control"
                                       id="address" placeholder="کمسیون"
                                       value="{{$carrier_user->commission}}">
                            </div>
                            <div class="form-group">
                                <label for="national_code">کد ملی</label>
                                <input type="text" name="national_code" class="form-control"
                                       id="national_code" placeholder="کد ملی"
                                       value="{{$carrier_user->national_code}}">
                            </div>
                            <div class="form-group">
                                <label for="password"> رمز<span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" value=""
                                       id="password" placeholder="رمز">
                                <input type="password" name="confirm" class="form-control" value=""
                                       id="confirm" placeholder="تکرار رمز">
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
@stop
