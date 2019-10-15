@php
if(empty(auth()->user()->roles())){
    auth()->logout();
}
@endphp
<!DOCTYPE html>
<html>
<head>
    @include('admin.layout.head')
</head>
<body class="hold-transition skin-blue sidebar-mini">
@if(\Session::has('alert'))
    @component('admin.components.alert')
    @endcomponent
@endif
<div class="wrapper">

@include('admin.layout.header')

<!-- right side column. contains the logo and sidebar -->
@include('admin.layout.aside')

<!-- Content Wrapper. Contains page content -->

@yield('content')

<!-- /.content-wrapper -->
    @include('admin.layout.settings')
    @include('admin.layout.footer')
</div>
@include('admin.layout.scripts')

</body>
</html>
