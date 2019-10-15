@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                افزودن شریک تجاری
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box">
            </div>
            <div class="box-body">

                <div class="container">
                    <form autocomplete="off" role="form" action="{{route('thirdparty.store')}}" method="post">

                        <div class="box" style="background-color: white; padding: 20px;">
                            <!-- /.box-header -->
                            <!-- form start -->
                            {{csrf_field()}}

                            <div class="form-group">
                                <label for="first_name">نام</label>
                                <input  type="text" name="first_name" class="form-control" id="first_name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">نام خانوادگی</label>
                                <input type="text" name="last_name" class="form-control" id="last_name">
                            </div>
                            <div class="form-group">
                                <label for="commission">کمیسیون</label>
                                <input required type="number" value="10" name="commission" class="form-control" id="commission">
                            </div>
                            <div class="form-group">
                                <label for="phone">تلفن</label>
                                <input type="text" name="phone" class="form-control" id="phone">
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ارسال</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@stop
