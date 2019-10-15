@extends('admin.layout.master')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                هزینه ها
            </h1>

            <button type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#myModal">افزودن</button>

            <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">افزودن مورد جدید </h4>
                        </div>
                        <div class="modal-body">

                            <div class="box box-primary">
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form role="form" action="{{route('prices.store')}}" method="post">
                                    {{csrf_field()}}
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="title">عنوان<span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" id="title" placeholder="نام">
                                        </div>
                                        <div class="form-group">
                                            <label for="amount">قیمت<span class="text-danger">*</span></label>
                                            <input type="text" name="amount" class="form-control" id="amount" placeholder=" ">
                                        </div>

                                    </div>
                                    <!-- /.box-body -->

                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary submit_user">ثبت</button>
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

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">لیست هزینه ها </h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نام</th>
                                    <th>قیمت</th>
                                    <th>اخرین تغییر</th>
                                    <th>حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($prices as $key=>$price)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$price->title}}</td>
                                        <td>
                                            <form action="{{route('prices.update',$price->id)}}" method="post">
                                                {{csrf_field()}}
                                                {{method_field('put')}}
                                                <input type="text" name="amount" value="{{$price->amount}}">
                                            </form>
                                        </td>
                                        <td>{{$price->status}}</td>
                                        <td><form action="{{route('prices.destroy',$price->id)}}" method="post" class="user_delete">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <label>
                                                    <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                                </label>
                                            </form></td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>نام</th>
                                    <th>قیمت</th>
                                    <th>اخرین تغییر</th>
                                    <th>حذف</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>


    @stop