@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper  nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>
                لیست راننده ها
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box  nobaar-dark">
                <div class="box-header">
                    <form action="{{ route('driver.search') }}" method="get">
                        @csrf
                        <input autofocus autocomplete="off" id="driver_search" type="text" class="form-control" placeholder="جستجوی سفارش">
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
                                            <label for="national_code">کد ملی</label>
                                            <input required type="text" name="national_code" class="form-control" id="national_code" placeholder="کد ملی">
                                        </div>
                                        <div class="form-group">
                                            <label for="password"> رمز<span class="text-danger">*</span></label>
                                            <input  autocomplete="off" required type="password" name="password" class="form-control" value="" id="password" placeholder="رمز">
                                            <input  autocomplete="off" required type="password" name="confirm" class="form-control" value="" id="confirm" placeholder="تکرار رمز">
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
                <ul class="nav nav-tabs nobaar_tab">
                    <li class="active"><a data-toggle="tab" href="#kamion">کامیون</a></li>
                    <li><a data-toggle="tab" href="#neisan">نیسان</a></li>
                    <li><a data-toggle="tab" href="#disable">غیر فعال</a></li>
                </ul>

                <div class="tab-content nobaar_indexes">

                    <div id="kamion" class="tab-pane fade in active">

                        <table id="example2" class="nobaar-table">
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
                                <th>حذف</th>
                                <th>وضعیت</th>
                                <th>سرویس دهنده</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($enable->where('carrier_id', 2) as $key=>$driver)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{route('carrierUserShow', $driver['id'])}}"> {{ $driver->user['first_name'] . ' ' . $driver->user['last_name']}} </a></td>
                                    <td>{{ $driver->user['phone']}}</td>
                                    <td>{{ round($driver->comments()->average('rating'),2) }} </td>
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
                                    <td>
                                        <form action="/admin/carrieruser/{{ $driver->user['id'] }}" method="post" class="user_delete">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <label>
                                                <input type="hidden" value="{{$driver->user['id']}}" name="id">
                                                <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        @if($driver->user->status)
                                            <form action="{{route('users.update', $driver->user->id)}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('put') }}
                                                <input type="hidden" value="{{$driver->user->id}}" name="id">
                                                <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                            </form>
                                        @else
                                            <form action="{{route('users.update', $driver->user->id)}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('put') }}
                                                <input type="hidden" value="{{$driver->user->id}}" name="id">
                                                <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        @if($driver->is_provider)
                                            <form action="{{route('makeProvider', $driver->id)}}" method="post">
                                                {{ csrf_field() }}
                                                <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                            </form>
                                        @else
                                            <form action="{{route('makeProvider', $driver->id)}}" method="post">
                                                {{ csrf_field() }}
                                                <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div id="neisan" class="tab-pane">

                        <table id="example2" class="nobaar-table">
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
                                <th>حذف</th>
                                <th>وضعیت</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($enable->where('carrier_id', 1) as $key=>$driver)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{route('carrierUserShow', $driver['id'])}}"> {{ $driver->user['first_name'] . ' ' . $driver->user['last_name']}} </a></td>
                                    <td>{{ $driver->user['phone']}}</td>
                                    <td>{{ round($driver->comments()->average('rating'),2) }} </td>
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
                                    <td>
                                        <form action="/admin/carrieruser/{{ $driver->user['id'] }}" method="post" class="user_delete">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <label>
                                                <input type="hidden" value="{{$driver->user['id']}}" name="id">
                                                <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        @if($driver->user->status)
                                            <form action="{{route('users.update', $driver->user->id)}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('put') }}
                                                <input type="hidden" value="{{$driver->user->id}}" name="id">
                                                <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                            </form>
                                        @else
                                            <form action="{{route('users.update', $driver->user->id)}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('put') }}
                                                <input type="hidden" value="{{$driver->user->id}}" name="id">
                                                <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                            </form>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="disable" class="tab-pane fade">

                        <table id="example2" class="nobaar-table">
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
                                <th>تاریخ غیرفعال شدن</th>
                                <th>حذف</th>
                                <th>وضعیت</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($disable as $key=>$driver)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td><a href="{{route('carrierUserShow', $driver['id'])}}"> {{ $driver->user['first_name'] . ' ' . $driver->user['last_name']}} </a></td>
                                    <td>{{ $driver->user['phone']}}</td>
                                    <td>{{ round($driver->comments()->average('rating'),2) }} </td>
                                    <td>{{ $driver->carrier['name']}}</td>
                                    <td>{{ $driver->national_code}}</td>
                                    <td>{{ $driver->user['address']}}</td>
                                    <td>
                                        {{ dateTojal($driver->user['created_at']) }}
                                    </td>
                                    <td>
                                        {{ dateTojal($driver->user['disabled_at']) }}
                                    </td>
                                    <td>
                                        <form action="/admin/carrieruser/{{ $driver->user['id'] }}" method="post" class="user_delete">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <label>
                                                <input type="hidden" value="{{$driver->user['id']}}" name="id">
                                                <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        @if($driver->user->status)
                                            <form action="{{route('users.update', $driver->user->id)}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('put') }}
                                                <input type="hidden" value="{{$driver->user->id}}" name="id">
                                                <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                            </form>
                                        @else
                                            <form action="{{route('users.update', $driver->user->id)}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('put') }}
                                                <input type="hidden" value="{{$driver->user->id}}" name="id">
                                                <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                            </form>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

@stop
