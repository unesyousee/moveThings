@extends('admin.layout/master')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                اسلایدر سایت
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">




            <div class="box header_box">
                <div class="box-header">
                </div>

                <a href="{{ route('post.create') }}" class="btn btn-info btn-lg new_user" data-target="#add_post_modal">جدید</a>

            </div>






                <div class="box-body">
                	<div style="direction: ltr;">
                	</div>
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عکس</th>
                            <th>تیتر</th>
                            <th>خلاصه مطلب</th>
                            <th>نویسنده</th>
                            <th>وضعیت</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($posts as $key=>$post)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>
                                	<a href="{{$post->link}}">
	                                	<img height="60" src="{{ $post->first_image }}" alt="">
	                                </a>
                                </td>
                                <td>
                                	{{ $post->title }}
                                </td>
                                <td>
                                    {{ $post->abstract }}
                                </td>
                                <td>
                                	{{ $post->author }}
                                </td>
                                <td>
                                    @if($post->status)
                                        <form action="{{route('post.update', $post->id)}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input type="hidden" value="{{$post->id}}" name="id">
                                            <input type="submit" class="btn btn-success" name="disable" value="فعال">
                                        </form>
                                    @else
                                        <form action="{{route('post.update', $post->id)}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input type="hidden" value="{{$post->id}}" name="id">
                                            <input type="submit" class="btn btn-danger" name="enable" value="غیرفعال">
                                        </form>
                                    @endif

                                </td>
                                <td>
                                    <form action="/blog/post/{{$post->id}}" method="post" class="user_delete">
                                        {{ csrf_field() }}
                                        {{ method_field('delete') }}
                                        <label>
                                        <input type="hidden" name="id">
                                        <button name="delete" value="{{$post->id}}" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                        </label>
                                    </form>
                                    <label for="modal{{$post->id}}" class="edite_user">
                                        <button type="button" id="modal{{$post->id}}" class="btn btn-info btn-lg" data-toggle="modal" data-target="#edit_user_modal{{$post->id}}" style="display: none ;"></button>
                                        <div class="fa fa-pencil"></div>
                                    </label>
                                    <!-- Modal -->
                                    
                                    <div id="edit_user_modal{{$post->id}}" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">{{$post->title}}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" action="/blog/post/{{$post->id}}" method="post">
                                                        {{csrf_field()}}
                                                        {{method_field('put')}}
                                                        <div class="box-body">
                                                            <div class="form-group">
                                                                <label for="title{{$post->id}}">تیتر</label>
                                                                <input value="{{$post->title}}" type="text" name="title" class="form-control" id="firsrt_name{{$post->id}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="abstract{{$post->id}}">خلاصه مطلب</label>
                                                                <input value="{{$post->abstract}}" type="text" name="abstract" class="form-control" id="abstract{{$post->id}}">
                                                            </div>
                                                            <div class="form-group">
                                                                    <label for="author">نویسنده<span class="text-danger">*</span></label>
                                                                    <input required type="text" value="{{$post->author}}" name="author" class="form-control" id="author" rows="15">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="body{{$post->id}}"> متن کامل</label>
                                                                <textarea name="body" rows="15" class="form-control" id="body{{$post->id}}"> {{$post->body}} </textarea>
                                                            </div>
\
                                                        </div>
                                                        <!-- /.box-body -->

                                                        <div class="box-footer">
                                                            <button type="submit" class="btn btn-primary">ارسال</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            {{$posts->links()}}
        </section>
    <!-- /.box-body -->
    </div>


        <!-- /.content -->
    </div>

@endsection
