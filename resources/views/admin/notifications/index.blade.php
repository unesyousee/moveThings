@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <!-- Content Header (Page header) -->
        <section class="content-header nobaar-dark">
            <h1>
                ارسال اعلان برای کاربران
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <button type="button" class="btn btn-info btn-lg center " data-toggle="modal" data-target="#user">
                        کاربران
                    </button>
                    <button type="button" class="btn btn-info btn-lg center " data-toggle="modal" data-target="#driver">
                        رانندگان
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div id="user" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ارسال اعلان به کاربران</h4>
                        </div>
                        <div class="modal-body">
                            <form autocomplete="off" role="form" action="{{route('usersNotifications')}}" method="post">

                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <!-- form start -->
                                    {{csrf_field()}}

                                    <div class="form-group">
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
            <div id="driver" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ارسال اعلان به رانندگان</h4>
                        </div>
                        <div class="modal-body">
                            <form autocomplete="off" role="form" action="{{route('driversNotifications')}}" method="post">

                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <!-- form start -->
                                    {{csrf_field()}}

                                    <div class="form-group">
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


        </section>
        <!-- /.box-body -->
    </div>


@endsection
