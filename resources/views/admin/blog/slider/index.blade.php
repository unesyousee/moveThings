@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                اسلایدر سایت
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">




            <div class="box header_box">
                <div class="box-header">
                </div>

                <button type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#add_slider_modal">جدید</button>

                <!-- Modal -->
                <div id="add_slider_modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">اسلایدر جدید</h4>
                            </div>
                            <div class="modal-body">

                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <!-- form start -->
                                    <form  autocomplete="off" role="form" action="{{route('slider.store')}}" method="post" enctype="multipart/form-data"">
                                        {{csrf_field()}}
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label for="title"> تیتر<span class="text-danger">*</span></label>
                                                <input required autofocus type="text" name="title" class="form-control" id="title">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                                <label for="abstract">خلاصه مطلب<span class="text-danger">*</span></label>
                                                <input required type="text" name="abstract" class="form-control" id="abstract">
                                        </div>
                                        <div class="form-group">
                                                <label for="link">لینک<span class="text-danger">*</span></label>
                                                <input required type="text" name="link" class="form-control" id="link" value="#" placeholder="http://nobaar.com/blog">
                                        </div>
                                        <div class="form-group">
                                                <label for="image">عکس<span class="text-danger">*</span></label>
                                                <input required type="file" name="image" class="form-control" id="image">
                                        </div>
                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>






                <div class="box-body">
                	<div style="direction: ltr;">
                	</div>
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عکس</th>
                            <th>تیتر</th>
                            <th>خلاصه مطلب</th>
                            <th>لینک</th>
                            <th>وضعیت</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($sliders as $key=>$slider)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>
                                	<a href="{{$slider->link}}">
	                                	<img height="60" src="{{ $slider->image }}" alt="">
	                                </a>
                                </td>
                                <td>
                                	{{ $slider->title }}
                                </td>
                                <td>
                                	{{ $slider->abstract }}
                                </td>
                                <td>
                                	{{ $slider->link }}
                                </td>
                                <td>
                                    @if($slider->status)
                                        <form action="{{route('slider.update', $slider->id)}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input type="hidden" value="{{$slider->id}}" name="id">
                                            <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                        </form>
                                    @else
                                        <form action="{{route('slider.update', $slider->id)}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input type="hidden" value="{{$slider->id}}" name="id">
                                            <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                        </form>
                                    @endif

                                </td>
                                <td>
                                    <form action="/blog/slider/{{$slider->id}}" method="post" class="user_delete">
                                        {{ csrf_field() }}
                                        {{ method_field('delete') }}
                                        <label>
                                        <input type="hidden" name="id">
                                        <button name="delete" value="{{$slider->id}}" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                        </label>
                                    </form>
                                    <label for="modal{{$slider->id}}" class="edite_user">
                                        <button type="button" id="modal{{$slider->id}}" class="btn btn-info btn-lg" data-toggle="modal" data-target="#edit_user_modal{{$slider->id}}" style="display: none ;"></button>
                                        <div class="fa fa-pencil"></div>
                                    </label>
                                    <!-- Modal -->
                                    
                                    <div id="edit_user_modal{{$slider->id}}" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">{{$slider->title}}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" action="/blog/slider/{{$slider->id}}" method="post">
                                                        {{csrf_field()}}
                                                        {{method_field('put')}}
                                                        <div class="box-body">
                                                            <div class="form-group">
                                                                <label for="title{{$slider->id}}">تیتر</label>
                                                                <input value="{{$slider->title}}" type="text" name="title" class="form-control" id="firsrt_name{{$slider->id}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="abstract{{$slider->id}}">خلاصه مطلب</label>
                                                                <input value="{{$slider->abstract}}" type="text" name="abstract" class="form-control" id="abstract{{$slider->id}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="link{{$slider->id}}">لینک</label>
                                                                <input value="{{$slider->link}}" type="text" name="link" class="form-control" id="link{{$slider->id}}">
                                                            </div>
                                                            <!-- <div class="form-group">
                                                                <label for="phone{{$slider->id}}">تلفن</label>
                                                                <input value="{{$slider->phone}}"  type="text" name="phone" class="form-control" id="phone{{$slider->id}}" placeholder="تلفن">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="address{{$slider->id}}">آدرس</label>
                                                                <input value="{{$slider->address}}" type="text" name="address" class="form-control" id="address{{$slider->id}}" placeholder="آدرس">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="password{{$slider->id}}">رمز</label>
                                                                <input autocomplete="off" type="password" name="password" class="form-control" id="password{{$slider->id}}" placeholder="خالی رها کنید تا تغییر نکند">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="email{{$slider->id}}">ایمیل</label>
                                                                <input autocomplete="off" name="email" value="{{$slider->email}}" type="email" class="form-control" id="email{{$slider->id}}" placeholder="ایمیل">
                                                            </div> -->
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
                    </table>
                </div>
            {{$sliders->links()}}
        </section>
    <!-- /.box-body -->
    </div>


        <!-- /.content -->
    </div>

@endsection
