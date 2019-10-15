@extends('admin.layout/master')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>اسلایدر سایت</h1>
        </section>

        <section class="content">

            <form  autocomplete="off" role="form" action="{{route('post.store')}}" method="post" enctype="multipart/form-data"">
                {{csrf_field()}}
                <div class="box-body">
                    <div class="form-group">
                        <label for="title"> تیتر<span class="text-danger">*</span></label>
                        <input required autofocus type="text" name="title" class="form-control" id="title">
                    </div>
                </div>

                <div class="form-group">
                        <label for="abstract">خلاصه مطلب<span class="text-danger">*</span></label>
                        <input required type="text" name="abstract" class="form-control" id="abstract">
                </div>

                <div class="form-group">
                        <label for="author">نویسنده<span class="text-danger">*</span></label>
                        <input required type="text" value="مدیر سایت" name="author" class="form-control" id="author" rows="15">
                </div>

                <div class="form-group">
                        <label for="editor">متن کامل<span class="text-danger">*</span></label>
                        <textarea required name="body" id="editor"></textarea>
                </div>

                <div class="form-group">
                        <label for="first_image">عکس اول<span class="text-danger">*</span></label>
                        <input required type="file" name="first_image" class="form-control" id="first_image">
                </div>

                <div class="form-group">
                        <label for="second_image">عکس دوم (الزام نیست)<span class="text-danger">*</span></label>
                        <input type="file" name="second_image" class="form-control" id="second_image">
                </div>
                <!-- /.box-body -->

                    <button type="submit" class="btn btn-primary submit_user">ذخیره</button>
            </form>



        </section>
    </div>
    <script>
        initSample();
    </script>
@endsection
