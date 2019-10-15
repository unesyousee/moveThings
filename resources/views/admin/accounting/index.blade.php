@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <!-- Content Header (Page header) -->
        <section class="content-header nobaar-dark">
            <h1>
                نوبار
            </h1>
        </section>
        <!-- Main content -->
        <section class="content nobaar-dark">
            <div class="box">
                <div class="box-header nobaar-dark">
                    <h3 class="box-title"> حسابداری</h3>
                </div>
                <ul class="nav nav-tabs nobaar-dark">
                    <li><a data-toggle="tab" href="#transaction">پراختها</a></li>
                    <li><a data-toggle="tab" href="#wallet">کیف پول</a></li>
                    <li class="active"><a data-toggle="tab" href="#drivers">راننده‌گان</a></li>
                </ul>

                <div class="tab-content nobaar-dark">
                    <div id="transaction" class="tab-pane fade">
                        @include('admin.accounting.transaction')
                    </div>
                    <div id="wallet" class="tab-pane fade">
                        @include('admin.accounting.wallet')
                    </div>
                    <div id="drivers" class="tab-pane fade active in">
                        @include('admin.accounting.drivers')
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </section>
    </div>
@stop