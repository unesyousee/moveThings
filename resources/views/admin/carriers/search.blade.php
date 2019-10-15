@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                لیست راننده ها
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box">
                <div class="box-header">
                    <form action="{{ route('driver.search') }}" method="get">
                        @csrf
                        <input autofocus autocomplete="off" id="driver_search" type="text" name="search" class="form-control" placeholder="جستجوی سفارش">
                        <div id="driver_result" class="hidden"></div>
                    </form>
                </div>

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
                                                <input required autofocus type="text" name="first_name" class="form-control" id="firsrt_name" placeholder="نام">
                                                <label for="last_name">نام خانوادگی<span class="text-danger">*</span></label>
                                                <input required type="text" name="last_name" class="form-control" id="last_name" placeholder="نام خانوادگی">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">تلفن<span class="text-danger">*</span></label>
                                            <input required type="text" name="phone" class="form-control" id="phone" placeholder="تلفن">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">آدرس</label>
                                            <input required type="text" name="address" class="form-control" id="address" placeholder="آدرس">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">کمسیون</label>
                                            <input required type="text" name="commission" class="form-control" id="address" placeholder="کمسیون">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">وسیله</label>
                                            <select name="carrier" id="" class="form-control">
                                                @foreach($carriers as $carrier)
                                                <option value="{{ $carrier->id }}">{{ $carrier->name}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">کد ملی</label>
                                            <input required type="text" name="national_code" class="form-control" id="address" placeholder="کد ملی">
                                        </div>
                                        <div class="form-group">
                                            <label for="password"> رمز<span class="text-danger">*</span></label>
                                            <input  required type="password" name="password" class="form-control" value="" id="password" placeholder="رمز">
                                            <input  required type="password" name="confirm" class="form-control" value="" id="confirm" placeholder="تکرار رمز">
                                        </div>
                                        <div class="form-group">
                                            <label for="email"> ایمیل<span class="text-danger">*</span></label>
                                            <input required name="email" type="email" class="form-control" id="email" placeholder="ایمیل" value="" required>
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
            <div class="box-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>تلفن</th>
                        <th>امتیاز</th>
                        <th>ماشین</th>
                        <th>کد ملی</th>
                        <th>آدرس</th>
                        <th>تاریخ ثبت نام</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($drivers as $key=>$driver)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><a href="{{route('carrierUserShow', $driver['id'])}}"> {{ $driver->user['first_name'] . ' ' . $driver->user['last_name']}} </a></td>
                            <td>{{ $driver->user['phone']}}</td>
                            <td>{{ $driver->rating }} </td>
                            <td>{{ $driver->carrier['name']}}</td>
                            <td>{{ $driver->national_code}}</td>
                            <td>{{ $driver->user['address']}}</td>
                            <td>
                                <?php
                                $redate= strtotime($driver->user['created_at']);
                                if ($redate){
                                $coned_date = date( 'Y-m-d H:i:s', $redate );
                                list($gy,$gm,$gd)=explode('-',$coned_date);
                                list($gd)=explode(' ',$gd);
                                $j_date_array=gregorian_to_jalali($gy,$gm,$gd);
                                echo $j_date_array[0]. ','. $j_date_array[1].','.$j_date_array[2];
                                }else{
                                    echo 'بدون تاریخ';
                                }
                                ?>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>نام راننده</th>
                        <th>امتیاز</th>
                        <th>کد ملی</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>

@stop