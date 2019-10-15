@extends('admin.layout/master')
@section('content')



    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                نتیجه جستجو
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header">
                    <form action="{{ route('user.search') }}" method="get" class="form-group search_user">
                        @csrf
                        <input id="user_search" type="text" name="search" class="form-control" placeholder="جستجوی کاربر">
                        <div id="msg"></div>
                    </form>


                    <button type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#add_user_modal">کاربر جدید</button>

                    <!-- Modal -->
                    <div id="add_user_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">کاربر جدید</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="box box-primary">
                                        <!-- /.box-header -->
                                        <!-- form start -->
                                        <form  autocomplete="off" role="form" action="{{route('users.store')}}" method="post">
                                            {{csrf_field()}}
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label for="first_name"> نام<span class="text-danger">*</span></label>
                                                    <input autofocus type="text" name="first_name" class="form-control" id="firsrt_name" placeholder="نام">
                                                    <label for="last_name">نام خانوادگی<span class="text-danger">*</span></label>
                                                    <input type="text" name="last_name" class="form-control" id="last_name" placeholder="نام خانوادگی">
                                                </div>
                                                <div class="form-group">
                                                    <label for="role">نقش<span class="text-danger">*</span></label>
                                                    <select type="text" name="role" class="form-control" id="role" >
                                                        <option value="1">مدیر سیستم</option>
                                                        <option selected value="2">کاربر</option>
                                                        <option value="3">راننده</option>
                                                        <option value="4">کارگر</option>
                                                    </select>
                                                </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="phone">تلفن<span class="text-danger">*</span></label>
                                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="تلفن">
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">آدرس</label>
                                                    <input type="text" name="address" class="form-control" id="address" placeholder="آدرس">
                                                </div>
                                                <div class="form-group">
                                                    <label for="password"> رمز<span class="text-danger">*</span></label>
                                                    <input  type="password" name="password" class="form-control" value="" id="password" placeholder="رمز">
                                                    <input  type="password" name="confirm" class="form-control" value="" id="confirm" placeholder="تکرار رمز">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email"> ایمیل<span class="text-danger">*</span></label>
                                                    <input name="email" type="email" class="form-control" id="email" placeholder="ایمیل" value="" required>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->

                                            <div class="box-footer">
                                                <button type="submit" class="btn btn-primary submit_user">ارسال</button>
                                            </div>
                                        </form>
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">لغو عملیات</button>
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>تلفن همراه</th>
                            <th>ایمیل</th>
                            <th>آدرس</th>
                            <th>کد اشتراک</th>
                            <th>تاریخ ثبت نام</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($users as $key=>$user)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{$user->first_name}} {{$user->last_name}}</td>
                                <td>{{$user->phone}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->address}}</td>
                                <td>{{$user->share_code}}</td>
                                <td>
                                    <?php

                                    $date =$user->created_at;
                                    if ($date){
                                        $time= isset(explode(' ',$date)[1])? explode(' ',$date)[1] : '';
                                        list($gy,$gm,$gd)=explode('-',$date);
                                        list($gd)=explode(' ',$gd);
                                        $j_date_array=gregorian_to_jalali($gy,$gm,$gd);
                                        echo $j_date_array[0]. ','. $j_date_array[1].','.$j_date_array[2]. ' '. $time;}
                                    else{
                                        echo 'بدون تاریخ';
                                    }
                                    ?>
                                </td>
                                <td>
                                    @if($user->status)
                                        <form action="{{route('users.update', $user->id)}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input type="hidden" value="{{$user->id}}" name="id">
                                            <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                        </form>
                                    @else
                                        <form action="{{route('users.update', $user->id)}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input type="hidden" value="{{$user->id}}" name="id">
                                            <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                        </form>
                                    @endif

                                </td>
                                <td>
                                    <form action="/admin/users/{{$user->id}}" method="post" class="user_delete">
                                        {{ csrf_field() }}
                                        {{ method_field('delete') }}
                                        <label>
                                        <input type="hidden" name="id">
                                        <button name="delete" value="{{$user->id}}" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                        </label>
                                    </form>
                                    <label for="modal1" class="edite_user">
                                        <button type="button" id="modal1" class="btn btn-info btn-lg" data-toggle="modal" data-target="#edit_user_modal{{$user->id}}" style="display: none;"></button>
                                        <div class="fa fa-pencil"></div>
                                    </label>
                                    <!-- Modal -->
                                    <div id="edit_user_modal{{$user->id}}" class="modal fade" role="dialog">
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
                                                                <input value="{{$user->first_name}}" type="text" name="first_name" class="form-control" id="firsrt_name{{$user->id}}" placeholder="نام">
                                                                <label for="last_name{{$user->id}}">نام خانوادگی</label>
                                                                <input value="{{$user->last_name}}" type="text" name="last_name" class="form-control" id="last_name{{$user->id}}" placeholder="نام خانوادگی">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="phone{{$user->id}}">تلفن</label>
                                                                <input value="{{$user->phone}}"  type="text" name="phone" class="form-control" id="phone{{$user->id}}" placeholder="تلفن">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="address{{$user->id}}">آدرس</label>
                                                                <input value="{{$user->address}}" type="text" name="address" class="form-control" id="address{{$user->id}}" placeholder="آدرس">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="password{{$user->id}}">رمز</label>
                                                                <input  type="password" name="password" class="form-control" id="password{{$user->id}}" placeholder="خالی رها کنید تا تغییر نکند">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="email{{$user->id}}">ایمیل</label>
                                                                <input name="email" value="{{$user->email}}" type="email" class="form-control" id="email{{$user->id}}" placeholder="ایمیل">
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
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>تلفن همراه</th>
                            <th>ایمیل</th>
                            <th>آدرس</th>
                            <th>کد اشتراک</th>
                            <th>تاریخ ثبت نام</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- /.box-body -->
            {{$users->links()}}
        </section>
    </div>

        <!-- /.content -->
    </div>

@endsection
