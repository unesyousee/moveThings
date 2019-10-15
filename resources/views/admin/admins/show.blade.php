@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                کاربر پنل
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box header_box">
                <div class="box-header">
                    <h4>{{$user->first_name ?? ''}} {{ $user->last_name ?? ''}} {{ $user->id ?? ''}}</h4>
                </div>
            </div>
            <div class="panel-body">
                @foreach($logs as $log)
                    <div class="row" style=" padding: 20px; @if($loop->iteration%2 == 1) background: #fff; @endif">
                        <div class="col-lg-12 table" style="display: table">
                            <p style="display: table-cell">کنترلر : <strong>{{ $log->controller  ?? '' }}</strong></p>
                            <p style="display: table-cell">متد : <strong>{{ $log->action }}</strong></p>
                            <p style="display: table-cell">آی‌پی : <strong>{{ $log->client_ip ?? ''   }}</strong></p>
                            <p style="display: table-cell">آدزس : <strong>{{ $log->url ?? ''  }}</strong></p>
                            <p style="display: table-cell">تاریخ:‌<strong> {{ dateTojal($log->created_at)}}</strong></p>
                        </div>
                        <div class="col-sm-12 col-xm-12" style="padding: 20px;">
                            <p class="col-sm-2" style="padding: 11px;"> درخواستها:</p>
                            <ul class="col-sm-10" style="direction: rtl; text-align: right">
                                @foreach($log->requests as $key => $val)

                                    @php
                                        if($key == '_token')
                                            continue;
                                    @endphp
                                    @if(is_array($val))
                                        <li>
                                            <ul>
                                                @foreach($val as $k=>$v)
                                                    <li class="col-lg-2"> {{ $k }} => {{ $v }}</li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>{{$key}} => {{$val}}</li>
                                    @endif

                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <br>
                @endforeach
            </div>
            {{$logs->links()}}
        </section>
    </div>
@stop
