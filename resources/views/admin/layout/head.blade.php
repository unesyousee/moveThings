<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Cache-Control" content="no-store" />
<title>داشبرد | کنترل پنل مدیریت</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="/dist/css/bootstrap-theme.css">
<link rel="stylesheet" href="/dist/css/lightbox.min.css">
<!-- Bootstrap rtl -->
<link rel="stylesheet" href="/dist/css/rtl.css">
<!-- persian Date Picker -->
<link rel="stylesheet" href="/dist/css/persian-datepicker.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="/bower_components/Ionicons/css/ionicons.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="/dist/css/AdminLTE.css">
<link rel="stylesheet" href="/dist/css/jquery-clockpicker.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="/dist/css/skins/_all-skins.min.css">
<script src="{{ asset('dist/js/Chart.min.js') }}"></script>
<!-- Morris chart -->
<link rel="stylesheet" href="/bower_components/morris.js/morris.css">
<!-- jvectormap -->
<link rel="stylesheet" href="/bower_components/jvectormap/jquery-jvectormap.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="/bower_components/bootstrap-daterangepicker/daterangepicker.css">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<link rel="stylesheet" href="/dist/css/persian-datepicker.min.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<!--<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>-->
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
@if(Route::currentRouteName() =="dashboard")
    <link href="/dist/css/leaflet.css" rel="stylesheet"/>
    <script src="/dist/js/leaflet.js"></script>
@endif
<!-- Google Font -->
{{--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">--}}

@if(Auth::user()->roles()->first()->name == "observer")
<style>
	.dropdown, .settings{
		display: none !important; 
	}
	li.dropdown.user.user-menu, .order_search{
		display: block !important; 
	}
</style>
@endif
<link rel="stylesheet" href="/dist/css/user.css?v=1.4">
