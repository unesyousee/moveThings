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
            <div class="box-body">
                <div heighy="180" id="activityـrange"></div>
            </div>
            <input onclick="sendcoord()" type="button" value="ذخیره" class="btn btn-lg flat btn-success">
            <button title="برگشت" onclick="undo()" class="btn btn-lg flat btn-default"><i class="fa fa-undo"></i></button>
            <button title="پاک کردن همه" onclick="remove()" class="btn btn-lg flat btn-default"><i class="fa fa-trash"></i></button>
        </section>
    </div>
    <script>
        var coordinates = [];
        var polygon;
        var mymap = L.map('activityـrange').setView([35.6964895,51.0696315], 10);
            mymap.on('click', onMapClick);

            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(mymap);

        function onMapClick(e) {

            coordinates.push([e.latlng.lat, e.latlng.lng]);

            draw(coordinates)
        }
        function draw(coordinates){
            reset()
            polygon = L.polygon([coordinates]);
            mymap.addLayer(polygon)
        }
        function reset(){
            if (polygon){
                mymap.removeLayer(polygon)
            }
        }
        function remove() {
            if (polygon){
                mymap.removeLayer(polygon)
            }
            coordinates = []
        }
        function undo(){
            reset();
            coordinates.pop();
            draw(coordinates)
        }
        function sendcoord(){
            var q = confirm('در صورت موافقت محدوده قبلی پاک خواهد شد.')
            if(!q){
                return q;
            }
            var jsonString = JSON.stringify(coordinates);
            $.ajax({
                type: "POST",
                crossDomain: true,
                dataType: 'json',
                url: "{{route('coords')}}",
                data: {'_token': '{{csrf_token()}}', 'coordinates':coordinates},
                cache: false,

            }).done(function (data) {
                window.location.href = '{{ route('area.index') }}';

                window.location.replace('{{ route('area.index') }}');

            })
        }
    </script>
@stop
