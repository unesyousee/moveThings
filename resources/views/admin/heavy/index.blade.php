@extends('admin.layout.master')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                وسایل هزینه دار
            </h1>



        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">لیست وسایل هزینه دار</h3>
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
                                @foreach($things as $key=>$thing)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$thing->name}}</td>
                                        <td>{{$thing->price}} </td>
                                        <td>{{$thing->updated_at}} </td>
                                        <td><form action="{{route('heavythings.destroy',$thing->id)}}" method="post" class="user_delete">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <label>
                                                    <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                                </label>
                                            </form>
                                        </td>
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