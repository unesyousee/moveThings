@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>
                مدیران پنل
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box-body">
                <table id="example2" class="nobaar-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>نقش</th>
                        <th>تلفن</th>
                        <th>تاریخ ثبت نام</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($users as $key=>$user)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><a href="{{route('showAdmin',$user->id)}}">{{ $user->first_name .' '. $user->last_name}}</a></td>
                            <td>{{ $user->roles->first()->name}}</td>
                            <td>{{ $user->phone}}</td>
                            <td>{{ dateTojal( $user->created_at)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$users    ->links()}}
            </div>
        </section>
    </div>

@stop
