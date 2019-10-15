@extends('admin.layout.master')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h4>
                نوبار
            </h4>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> خانه</a></li>
                <li class="active">داشبرد</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $newCounter}}</h3>

                            <p>سفارش انجام نشده</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('orders.index')}}" class="small-box-footer">اطلاعات بیشتر <i
                                class="fa fa-arrow-circle-left"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>
                                {{round($commentsAverage,2)}}
                            </h3>

                            <p>میانگین امتیازات</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">اطلاعات بیشتر <i class="fa fa-arrow-circle-left"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{$userCount}}</h3>

                            <p>کاربران ثبت شده</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{route('users.index')}}" class="small-box-footer">اطلاعات بیشتر <i
                                class="fa fa-arrow-circle-left"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="/admin/orders?today=true">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{$orderForToday}}</h3>
                                <p>سفارش برای امروز </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <span class="small-box-footer">اطلاعات بیشتر <i class="fa fa-arrow-circle-left"></i></span>
                        </div>
                    </a>
                </div>
                <!-- ./col -->
            </div>
            <!-- Main row -->
            <div class="row">
                <!-- right col -->
                <section class="col-lg-7 connectedSortable">
                    <!-- Custom tabs (Charts with tabs)-->
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs pull-left">
                            <li class="active"><a href="#srevenue-chart" data-toggle="tab">سفارشات ماهیانه</a></li>
                            <li><a href="#sales-chart" data-toggle="tab">سفارشات روزانه</a></li>
                            <li><a href="#comment-chart" data-toggle="tab">امتیازات</a></li>
                            {{--<li><a href="#regions-chart" data-toggle="tab">مناطق</a></li>--}}
                            <li class="pull-right header"><i class="fa fa-inbox"></i>چارت</li>
                        </ul>
                        <div class="tab-content no-padding">
                            <!-- Morris chart - Sales -->
                            <div class="chart tab-pane active" id="srevenue-chart"
                                 style="position: relative; height: 422px;">
                                @include('admin.dashboard.lastmonth')
                            </div>
                            <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 422px;">
                                @include('admin.dashboard.lastday')
                            </div>
                            <div class="chart tab-pane" id="comment-chart" style="position: relative; height: 422px;">
                                @include('admin.dashboard.comments')
                            </div>
                            {{--<div class="chart tab-pane" id="regions-chart" style="position: relative; height: 422px;">
                                @include('admin.dashboard.regions')
                            </div>--}}
                        </div>
                    </div>
                    <!-- /.nav-tabs-custom -->

                    <!-- solid sales graph -->
                    <div class="box box-solid bg-teal-gradient">
                        <div class="box-header">
                            <i class="fa fa-th"></i>

                            <h3 class="box-title">فیلتر سفارشات</h3>

                            <div class="box-tools pull-left">
                                <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i
                                        class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body border-radius-none">
                            @include('admin.dashboard.filtered')
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.right col -->
                <!-- left col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-5 connectedSortable">

                    <!-- Map box -->
                    <div class="box box-solid bg-light-blue-gradient">
                        <div class="box-header">
                            <!-- tools box -->
                            <div class="pull-left box-tools">
                                <button type="button" class="btn btn-primary btn-sm daterange pull-left"
                                        data-toggle="tooltip"
                                        title="Date range">
                                    <i class="fa fa-calendar"></i></button>
                                <button type="button" class="btn btn-primary btn-sm pull-left" data-widget="collapse"
                                        data-toggle="tooltip" title="Collapse" style="margin-left: 5px;">
                                    <i class="fa fa-minus"></i></button>
                            </div>
                            <!-- /. tools -->

                            <i class="fa fa-map-marker"></i>

                            <h3 class="box-title">
                                ۵۰ سفارش اخیر
                            </h3>
                        </div>
                        <div class="box-body">
                            <div id="world-map" style="height: 250px; width: 100%;">
                                <div id="osm-map"></div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box -->

                    <div class="box box-success">
                        <div class="box-header">
                            <i class="fa fa-comments-o"></i>

                            <h3 class="box-title">اخرین نظرات</h3>

                            <div class="box-tools pull-left" data-toggle="tooltip" title="وضعیت">
                                <div class="btn-group" data-toggle="btn-toggle">
                                    <button type="button" class="btn btn-default btn-sm active"><i
                                            class="fa fa-square text-green"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fa fa-square text-red"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="box-body chat" id="chat-box">
                            <!-- chat item -->
                            @foreach ($lastComments as $comment)
                                <div class="item">
                                    <p class="message">
                                        <a href="{{route('users.show',$comment->user->id ?? '')}}" class="name">
                                            <small class="text-muted pull-left"><i
                                                    class="fa fa-clock-o"></i> {{dateToJal($comment->created_at)}}
                                            </small>
                                            {{ $comment->user->first_name ?? ""}} {{ $comment->user->last_name ?? ''}}
                                        </a>
                                        <span>{{ $comment->text ?? ''}}</span>
                                    </p>
                                </div>
                        @endforeach
                        <!-- /.item -->
                        </div>
                    </div>
                </section>
                <!-- left col -->
                <!-- left col (We are only adding the ID to make the widgets sortable)-->
                <!-- left col -->
            </div>
            <!-- /.row (main row) -->

        </section>
        <!-- /.content -->
    </div>
    <script>

        function randomColor() {
            r = Math.floor(Math.random() * 255);
            g = Math.floor(Math.random() * 255);
            b = Math.floor(Math.random() * 255);
            return 'rgb(' + r + ',' + g + ',' + b + ')';
        }


        var distIcon = L.icon({
            iconUrl: '/dist/css/images/destination.png',
            shadowUrl: '/dist/css/images/marker-shadow.png',

            iconSize: [38,], // size of the icon
            shadowSize: [50, 64], // size of the shadow
            iconAnchor: [20, 65], // point of the icon which will correspond to marker's location
            shadowAnchor: [5, 80],  // the same for the shadow
            popupAnchor: [-3, -66]

        });
        var originIcon = L.icon({
            iconUrl: '/dist/css/images/origin.png',
            shadowUrl: '/dist/css/images/marker-shadow.png',

            iconSize: [38,], // size of the icon
            shadowSize: [50, 64], // size of the shadow
            iconAnchor: [20, 65], // point of the icon which will correspond to marker's location
            shadowAnchor: [5, 80],  // the same for the shadow
            popupAnchor: [-3, -66]
        });

        var element = document.getElementById('osm-map');

        // Height has to be set. You can do this in CSS too.
        element.style = 'height:402px;';

        // Create Leaflet map on map element.
        var map = L.map(element);

        // Add OSM tile leayer to the LeafletactiveTab map.
        L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);

        @foreach ($address as $key=>$addr)

        // Target's GPS coordinates.
        var target{{$key}} = L.latLng('{{$addr->originAddress->lat}}', '{{$addr->originAddress->long}}');

        map.setView(target{{$key}} , 11);

        L.marker(target{{$key}}, {icon: originIcon}).addTo(map)
            .bindPopup('{{$addr->user->first_name ?? ''}} {{$addr->user->last_name ?? ''}} <br> سفارش: {{$addr->id}} <br> قیمت: {{$addr->price}}')
            .openPopup();
        var target{{$key}} = L.latLng('{{$addr->destAddress->lat}}', '{{$addr->destAddress->long}}');

        map.setView(target{{$key}} , 11);

        L.marker(target{{$key}}, {icon: distIcon}).addTo(map)
            .bindPopup('{{$addr->user->first_name ?? ''}} {{$addr->user->last_name ?? ''}} <br> سفارش: {{$addr->id}} <br> قیمت: {{$addr->price}}')
            .openPopup();


        // add line from disination to origin
        var pointA = new L.LatLng({{$addr->destAddress->lat}}, {{$addr->destAddress->long}});
        var pointB = new L.LatLng({{$addr->originAddress->lat}}, {{$addr->originAddress->long}});
        var pointList = [pointA, pointB];

        var firstpolyline = new L.Polyline(pointList, {
            color: randomColor(),
            weight: 3,
            opacity: 1,
            smoothFactor: 1
        });
        firstpolyline.addTo(map);


        @endforeach
        map.panTo(new L.LatLng(35.705499, 51.3812733));

        function onMapClick(e) {
            console.log(e.latlng.lat, ',', e.latlng.lng);
        }

        map.on('click', onMapClick);
    </script>



@stop
