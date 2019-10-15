@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <!-- Content Header (Page header) -->
        <section class="content-header nobaar-dark" style="display: flex;justify-content: space-between;">
            <h1>
                لیست سفارشات
            </h1>
                <form class="form-group" action="{{route('orderExport')}}">
                    <select class="" name="month" id="orderExport">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                    <button class="btn btn-info btn-flat" type="submit" >استخراج</button>
                </form>
        </section>
        <!-- Main content -->
        <section class="content index_items nobaar-dark">
            <div class="box nobaar-dark">
                <div class="box-header">

                        <form class="form-inline float-right order_search" action="{{ route('order.search') }}" method="get">
                            @csrf
                            <input autofocus tabindex="1" autocomplete="off" id="order_search" type="text" class="form-control" placeholder="ﺟﺴﺘﺠﻮی ﺳﻔﺎﺭﺵ">
                            <select name="field" id="field" class="form-controll">
                                <option value="id">شماره سفارش</option>
                                <option value="name">اسم کاربر</option>
                                <option value="phone">شماره کاربر</option>
                                <option value="receiver_phone">شماره گیرنده</option>
                                <option value="receiver_name">نام گیرنده</option>
                            </select>
                            <div id="order_users" class="hidden"></div>
                        </form>

                        <form class="form-inline float-left" action="{{ route('order.search') }}" method="get">
                        @csrf
                        <label for="start_date">از</label>

                        <input id="start_date" tabindex="2" autocomplete="off" class="form-control" placeholder="تاریخ">
                        <input type="hidden" name="start_date" id="observer-start">

                        <label for="end_date">تا</label>

                        <input  id="end_date" tabindex="3" autocomplete="off" class="form-control" placeholder="تاریخ">
                        <input type="hidden" id="observer-end" name="end_date">

                        <input type="submit" class="form-control btn btn-info" value="فیلتر">
                    </form>
                </div>

                <ul class="nav nav-tabs nobaar_tab">
                    <li><a class="coming" data-toggle="tab" href="#coming">در حال ثبت سفارش </a></li>
                    <li><a class="new" data-toggle="tab" href="#new">جدید</a></li>
                    <li><a class="confilict" data-toggle="tab" href="#confilict">نیازمند ویرایش</a></li>
                    <li><a class="accepted" data-toggle="tab" href="#accepted"> پذیرفته ﺷﺪﻩ</a></li>
                    <li class="active"><a class="today" data-toggle="tab" href="#today">امروز</a></li>
                    <li><a class="start_moving" data-toggle="tab" href="#start_moving"> ﺷﺮﻭﻉ ﺑﺎﺭﺑﺮی</a></li>
                    <li><a class="done_moving" data-toggle="tab" href="#done_moving">پایاﻥ ﺑﺎﺭﺑﺮی</a></li>
                    <li><a class="done" data-toggle="tab" href="#done">اﻧﺠﺎﻡ ﺷﺪﻩ </a></li>
                    <li><a class="cancell" data-toggle="tab" href="#cancell">ﻟﻐﻮ ﺷﺪﻩ </a></li>
                </ul>
                <div class="tab-content nobaar_indexes">
                    <div id="coming" class="tab-pane fade">
                        @include('admin.orders.coming')
                    </div>
                    <div id="new" class="tab-pane fade">
                        @include('admin.orders.new')
                    </div>
                    <div id="confilict" class="tab-pane fade">
                        @include('admin.orders.confilict')
                    </div>
                    <div id="accepted" class="tab-pane fade">
                        @include('admin.orders.accepted')
                    </div>
                    <div id="today" class="tab-pane fade in active">
                        @include('admin.orders.today')
                    </div>
                    <div id="start_moving" class="tab-pane fade">
                        @include('admin.orders.start_moving')
                    </div>
                    <div id="done_moving" class="tab-pane fade">
                        @include('admin.orders.done_moving')
                    </div>
                    <div id="done" class="tab-pane fade">
                        @include('admin.orders.done')
                    </div>
                    <div id="cancell" class="tab-pane fade">
                        @include('admin.orders.cancelled')
                    </div>
                </div>
            </div>
                <!-- /.box-body -->


        </section>
    </div>
    @stop
