@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                لیست پیام ها
            </h1>


            <div style="margin-top: 40px">

                <!-- Modal -->
                <div id="add_user_modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </section>
        <!-- Main content -->
        <section class="content">
            <div class="box-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام </th>
                        <th>تلفن </th>
                        <th>نام راننده</th>
                        <th>آدرس</th>
                        <th>کد ملی</th>
                        <th>تاریخ ثبت نام</th>
                        <th>امتیاز</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>

                    {{--@foreach($workers as $key=>$carrier)--}}
                        {{--<tr>--}}
                            {{--<td><a href="{{ $carrier->user->profile_pic }}" data-lightbox="roadtrip"><img src="{{ $carrier->user->profile_pic }}" width="40"></a></td>--}}
                            {{--<td>{{ $carrier->user->first_name }} {{ $carrier->user->last_name }}</td>--}}
                            {{--<td>{{ $carrier->user->phone }} </td>--}}
                            {{--<td>{{ $carrier->parent->user->first_name ?? ''}} {{ $carrier->parent->user->last_name ?? ''}}</td>--}}
                            {{--<td>{{ $carrier->user->address }}</td>--}}
                            {{--<td>{{ $carrier->national_code}}</td>--}}
                            {{--<td><?php--}}

                                {{--$date = $carrier->user->created_at;--}}
                                {{--if ($date){--}}
                                    {{--list($gy,$gm,$gd)=explode('-',$date);--}}
                                    {{--list($gd)=explode(' ',$gd);--}}
                                    {{--$j_date_array=gregorian_to_jalali($gy,$gm,$gd);--}}
                                    {{--echo $j_date_array[0]. ','. $j_date_array[1].','.$j_date_array[2];}--}}
                                {{--else{--}}
                                    {{--echo 'بدون تاریخ';--}}
                                {{--}--}}
                                {{--?>--}}
                            {{--</td>--}}
                            {{--<td>{{ $carrier->rating}}</td>--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                    {{--@endforeach--}}
                    </tbody>
                </table>
                {{--{{$workers->links()}}--}}
            </div>
    </div>

    </section>
    </div>
@stop
