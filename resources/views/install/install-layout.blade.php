<!DOCTYPE html>
<html lang="en" >

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Business Tips" content="BUSINESS STARTUP">


    <link href="{{ url('adminlte/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
    href="{{ url('adminlte/css') }}/select2.min.css"/>
    <link href="{{ url('adminlte/css/AdminLTE.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
<link href="{{ url('css/cdn-styles-css/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

    <link href="{{ url('css/install.css') }}" rel="stylesheet">

    @yield('header_scripts')
</head>

<body class="login-screen" ng-app="vehicle_booking" >
    <!-- PRELOADER -->
   <!-- <div id="preloader">
        <div id="status">
            <div class="mul8circ1"></div>
            <div class="mul8circ2"></div>
        </div>
    </div>-->
    <!-- /PRELOADER -->

@yield('content')
	
       <!-- /#wrapper -->
		<!-- jQuery -->
    <script src="{{ url('js/cdn-js-files/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ url('adminlte/js') }}/bootstrap.min.js"></script>
    <script src="{{ url('adminlte/js') }}/select2.full.min.js"></script>
    <script src="{{ url('js/cdn-js-files/jquery.validate.js') }}"></script>
    <script src="{{ url('js/sweetalert-dev.js') }}"></script>
		@include('errors.formMessages')
		@yield('footer_scripts')
</body>

</html>