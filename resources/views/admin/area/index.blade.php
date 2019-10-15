@extends('admin.layout/master')
@section('content')
    <link href="/dist/css/leaflet.css" rel="stylesheet"/>
    <script src="/dist/js/leaflet.js"></script>
    <div class="content-wrapper nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>محدوده فعالیت</h1>
        </section>
        <!-- Main content -->
        <section class="content">

            <div class="box">
                <div class="box-header">
                    <a class="btn btn-info btn-lg new_user" href="{{ route('area.create') }}">تغییر محدوده </a>
                </div>
            </div>
            <div class="box-body">
                <div heighy="180" id="activityـrange"></div>
            </div>

        </section>
    </div>
    <script>
    $(document).ready(function () {

        var mymap = L.map('activityـrange').setView([35.6964895,51.0696315], 10);
        mymap.on('click', onMapClick);

        L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox.streets',
            accessToken: 'your.mapbox.access.token'
        }).addTo(mymap);
        var polygon = L.polygon([
            @foreach($coords as $coord)
                [{{ $coord->lat }},{{ $coord->long }}],
            @endforeach
        ]).addTo(mymap);
    });
    function onMapClick(e) {
        console.log(e.latlng.lat,',', e.latlng.lng);
    }


    </script>
@stop
