@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>
                لیست سرویس ها
            </h1>
        </section>
        <section class="content">
            <div class="box-body">
                <table class="nobaar-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عکس</th>
                        <th>نام</th>
                        <th>مبلغ</th>
                        <th>وضعیت</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($carriers as $key=>$carrier)
                        <tr>
                            <td>{{++$key}}</td>
                            <td><img width="200" src="{{ $carrier->picture_enable }}" alt=""></td>
                            <td>{{ $carrier->name }}</td>
                            <td>{{ $carrier->price }}</td>
                            <td>
                                @if($carrier->status)
                                    <form action="{{route('service.update', $carrier->id)}}" method="post">
                                        {{ csrf_field() }}
                                        {{ method_field('put') }}
                                        <input type="hidden" value="{{$carrier->id}}" name="id">
                                        <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                    </form>
                                @else
                                    <form action="{{route('service.update', $carrier->id)}}" method="post">
                                        {{ csrf_field() }}
                                        {{ method_field('put') }}
                                        <input type="hidden" value="{{$carrier->id}}" name="id">
                                        <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                    </form>
                                @endif

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
