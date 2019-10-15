@extends('admin.layout.master')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                مشخصات کاربر
            </h1>
            <button type="button" id="modal{{$user->id}}" class="btn btn-info btn-lg" data-toggle="modal"
                    data-target="#edit_user_modal">ویرایش
            </button>
            <button type="button"  class="btn btn-info btn-lg" data-toggle="modal" data-target="#wallet">افزایش اعتبار</button>
            <button type="button"  class="btn btn-info btn-lg" data-toggle="modal" data-target="#notif">ارسال اعلان </button>
                <br>
                <br>
                <br>
            <table>
                <tr>
                    <td>شماره کاربری</td>
                    <td>{{ $user->id}}</td>
                </tr>
                <tr>
                    <td>نام</td>
                    <td>{{ $user->first_name ?? ''}}</td>
                </tr>
                <tr>
                    <td>نام خانوادگی</td>
                    <td>{{ $user->last_name ?? ''}}</td>
                </tr>
                <tr>
                    <td>ایمیل</td>
                    <td>{{ $user->email ?? ''}}</td>
                </tr>
                <tr>
                    <td>اخرین زمان آنلاین</td>
                    <td>@php try{
                    echo dateTojal($user->alives()->orderBy("id","desc")->firstOrFail()->created_at) ??'';
                    }catch (\Exception $exception){
                     echo "نام معلوم";
                     } @endphp</td>
                </tr>
                <tr>
                    <td>تلفن</td>
                    <td>{{ $user->phone ?? ''}}</td>
                </tr>
                <tr>
                    <td>ادرس</td>
                    <td>{{ $user->address ?? ''}}</td>
                </tr>
                <tr>
                    <td>اعتبار کیف پول</td>
                    <td class="amount">{{ $transactions->sum('amount')}}</td>
                </tr>
                <tr>
                    <td>سن</td>
                    <td>{{ $user->age ?? ''}}</td>
                </tr>
                <tr>
                    <td>کد معرف</td>
                    <td>{{ $user->share_code ?? ''}}</td>
                </tr>
                <tr>
                    <td>تاریخ ثبت نام</td>
                    <td>{{ $user->created_at ?? ''}}</td>
                </tr>
                <tr>
                    <td>وضعیت مالی</td>
                    <td>{{ $user->financial ? $financial[$user->financial] : 'نامشخص'}}</td>
                </tr>
                <tr>
                    <td>نحوه آشنایی</td>
                    <td>{{ $user->know_us ?? ''}}</td>
                </tr>
                <tr>
                    <td>کلمه جستجو شده</td>
                    <td>{{ $user->keyword ?? ''}}</td>
                </tr>
            </table>


            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading display-flex space-between">

                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">اطلاعات تکمیلی</a>
                        </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body">
                            <form class="form-inline" method="post" action="{{route('manual_user_info', $user->id)}}">
                                @csrf
                                <label for="platform">وضعیت مالی: </label>
                                <div class="form-group">
                                    <select name="financial" id="platform" style="background: white; color: black;">
                                        <option value="3">عالی</option>
                                        <option value="2">خوب</option>
                                        <option value="1">متوسط</option>
                                        <option value="0">ضعیف</option>
                                    </select>
                                </div>
                                <label for="know_us">نحوه آشنایی: </label>
                                <div class="form-group">
                                    <select name="know_us" id="">
                                        <option value="نامعلوم">نامعلوم</option>
                                        <option value="گوگل">گوگل</option>
                                        <option value="اینستا گرام">اینستا گرام</option>
                                        <option value="تلگرام">تلگرام</option>
                                        <option value="آشنایان">آشنایان</option>
                                        <option value="کافه بازار">کافه بازار</option>
                                        <option value="سیب اپ">سیب اپ</option>
                                        <option value="مشتری قبلی">مشتری قبلی</option>
                                    </select>
                                </div>
                                <label for="keyword">کلمه جستجو شده: </label>
                                <div class="form-group">
                                    <input type="text" id="keyword" name="keyword" class="form-control"
                                           placeholder="کلمه جستجو شده">
                                </div>
                                <label for="age">سن: </label>
                                <div class="form-group">
                                    <input type="number" id="age" name="age" class="form-control" placeholder="سن">
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="ذخیره" name="btn"
                                           class="form-control btn- btn-flat btn-info">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="panel-group" id="accordion2">
                <div class="panel panel-default">
                    <div class="panel-heading display-flex space-between">

                        <h4 class="panel-title" style="display: flex; justify-content: space-between; width: 100%">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse3">تاریخچه سفارشات</a>
                            <p>{{$user->orders->count()}}</p>
                        </h4>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="">
                                @foreach ($user->orders as $order)
                                    <p>شماره سفارش: {{ $order->id }}</p>
                                    <p>قیمت: {{ $order->price }}</p>
                                    <p>نوع ماشین: {{ $order->carrier->name }}</p>
                                    <p>وضعیت: <span
                                            class="btn btn-info btn-flat"> {{ $statuses[$order->status] }}</span></p>
                                    <br><br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="edit_user_modal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{$user->first_name}} {{$user->last_name}}</h4>
                        </div>
                        <div class="modal-body">
                            <form role="form" action="/admin/users/{{$user->id}}" method="post">
                                {{csrf_field()}}
                                {{method_field('put')}}
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="first_name{{$user->id}}">نام</label>
                                        <input value="{{$user->first_name}}" type="text" name="first_name"
                                               class="form-control" id="firsrt_name{{$user->id}}" placeholder="نام">
                                        <label for="last_name{{$user->id}}">نام خانوادگی</label>
                                        <input value="{{$user->last_name}}" type="text" name="last_name"
                                               class="form-control" id="last_name{{$user->id}}"
                                               placeholder="نام خانوادگی">
                                    </div>

                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">ارسال</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
                        </div>
                    </div>

                </div>
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
                                            <input type="hidden" name="id" value="{{ $user->id }}">
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

            <div id="wallet" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{$user->first_name}} {{$user->last_name}}</h4>
                        </div>
                        <div class="modal-body">
                            <form role="form" action="{{ route('addToWallet') }}" method="post">
                                {{csrf_field()}}
                                <div class="box-body">
                                    <div class="form-group">
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <label for="phone{{$user->id}}">مبلغ(به ریال )</label>
                                        <input type="text" placeholder="مبلغ" class="priceNum form-control" name="amount" style="direction: ltr; text-align: right">
                                        <input type="text" name="desc" placeholder="این در اپ مشتری نشان داده خواهد شد" class="form-control" >
                                    </div>
                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">اعمال</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>



@stop
