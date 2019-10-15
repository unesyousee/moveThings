@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>
لیست کارگران
            </h1>


<div style="margin-top: 40px">
<button style="display: inline-block; margin: 0 auto; float: none;" type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#add_user_modal">افزودن</button>

<!-- Modal -->
    <div id="add_user_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">کاربر جدید</h4>
                </div>
                <div class="modal-body">

                <form autocomplete="off" role="form" action="{{ route('addWorker') }}" method="post" enctype="multipart/form-data">
                        @csrf
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <!-- form start -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="first_name"> نام<span class="text-danger">*</span></label>
                                    <input required="" autofocus="" type="text" name="first_name" class="form-control" id="firsrt_name" placeholder="نام">
                                    <label for="last_name">نام خانوادگی<span class="text-danger">*</span></label>
                                    <input required="" type="text" name="last_name" class="form-control" id="last_name" placeholder="نام خانوادگی">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">تلفن<span class="text-danger">*</span></label>
                                <input required="" type="text" name="phone" class="form-control" id="phone" placeholder="تلفن">
                            </div>
                            <div class="form-group">
                                <label for="address">آدرس</label>
                                <input required="" type="text" name="address" class="form-control" id="address" placeholder="آدرس">
                            </div>
                            <div class="form-group">
                                <label for="driver">راننده</label>
                                <select name="driver_id" id="driver">
                                    @foreach ($drivers as $driver)
                                        <option value="{{$driver->id ?? ""}}">{{ $driver->user->first_name ?? '' }} {{ $driver->user->last_name ?? '' }}</option>
                                    @endforeach
                                </select>
                             </div>
                            
                            <div class="form-group">
                                <label for="national_code">کد ملی</label>
                                <input required="" type="text" name="national_id" class="form-control" id="national_code" placeholder="کد ملی">
                            </div>
                            <div class="form-group">
                                <label for="file">عکس مدارک<span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" value="" id="file">
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

        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box-body">
                <table id="example2" class="nobaar-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام </th>
                        <th>تلفن </th>
                        <th>نام راننده</th>
                        <th>آدرس</th>
                        <th>کد ملی</th>
                        <th>تاریخ ثبت نام</th>
                        <th>امتیاز</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>

                        @foreach($workers as $key=>$carrier)
                            <tr>
                                <td><a href="{{ $carrier->user->profile_pic }}" data-lightbox="roadtrip"><img src="{{ $carrier->user->profile_pic }}" width="40"></a></td>
                                <td>{{ $carrier->user->first_name }} {{ $carrier->user->last_name }}</td>
                                <td>{{ $carrier->user->phone }} </td>
                                <td>{{ $carrier->parent->user->first_name ?? ''}} {{ $carrier->parent->user->last_name ?? ''}}</td>
                                 <td>{{ $carrier->user->address }}</td>
                               <td>{{ $carrier->national_code}}</td>
                               <td><?php

                                 $date = $carrier->user->created_at;
                                 if ($date){
                                 list($gy,$gm,$gd)=explode('-',$date);
                                    list($gd)=explode(' ',$gd);
                                    $j_date_array=gregorian_to_jalali($gy,$gm,$gd);
                                       echo $j_date_array[0]. ','. $j_date_array[1].','.$j_date_array[2];}
                                       else{
                                           echo 'بدون تاریخ';
                                       }
                                    ?>
                                 </td>
                               <td>{{ $carrier->rating}}</td>
                                <td>
                                    <form action="{{route('removeWorker',$carrier->id)}}" method="post" class="user_delete">
                                        {{ csrf_field() }}
                                        {{ method_field('delete') }}
                                        <label>
                                        <input type="hidden" name="id">
                                        <button name="worker_id" value="{{$carrier->id}}" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                        </label>
                                    </form>
                                    <label for="modal{{$carrier->id}}" class="edite_user">
                                        <button type="button" id="modal{{$carrier->id}}" class="btn btn-info btn-lg" data-toggle="modal" data-target="#edit_user_modal{{$carrier->id}}" style="display: none ;"></button>
                                        <div class="fa fa-pencil"></div>
                                    </label>
                                    <!-- Modal -->
                                    
                                    <div id="edit_user_modal{{ $carrier->id}}" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">{{$carrier->user->first_name}} {{$carrier->user->last_name}}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" action="{{route('updateWorker', $carrier->id)}}" method="post">
                                                        {{csrf_field()}}
                                                        {{method_field('put')}}
                                                        <input type="hidden" name="id" value="{{$carrier->id}}" >
                                                        <div class="box-body">
                                                            <div class="form-group">
                                                                <label for="first_name{{$carrier->user->id}}">نام</label>
                                                                <input value="{{$carrier->user->first_name}}" type="text" name="first_name" class="form-control" id="firsrt_name{{$carrier->user->id}}" placeholder="نام">
                                                                <label for="last_name{{$carrier->user->id}}">نام خانوادگی</label>
                                                                <input value="{{$carrier->user->last_name}}" type="text" name="last_name" class="form-control" id="last_name{{$carrier->user->id}}" placeholder="نام خانوادگی">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="address{{$carrier->user->id}}">آدرس</label>
                                                                <input value="{{$carrier->user->address}}" type="text"
                                                                 name="address" class="form-control" id="address{{$carrier->user->id}}" placeholder="آدرس">
                                                             </div>
                                                            
                                                            <div class="form-group">
                                                                <label for="phone{{$carrier->user->id}}">تلفن</label>
                                                                <input value="{{$carrier->user->phone}}" type="text"
                                                                 name="phone" class="form-control" id="phone{{$carrier->user->id}}" placeholder="تلفن">
                                                             </div>
                                                            <div class="form-group">
                                                                <label for="national_code{{$carrier->user->id}}">کد ملی</label>
                                                                <input value="{{$carrier->national_code}}" type="text"
                                                                 name="national_code" class="form-control" id="national_code{{$carrier->user->id}}" placeholder="کد ملی">
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$workers->links()}}
            </div>
            </div>

        </section>
    </div>
    @stop