<title>{{ getSetting('site_title', 'site_settings', trans('global.global_title')) }}</title>

<!-- Favicon-->
<link rel="icon" href="{{IMAGE_PATH_SETTINGS.getSetting('site_favicon', 'site_settings')}}" type="image/x-icon" />

<link rel="stylesheet" type="text/css" href="{{ url('adminlte/Default-login/vendor/bootstrap/css/bootstrap.min.css') }}">
<!--===============================================================================================-->
 
<link rel="stylesheet" type="text/css" href="{{ url('adminlte/Default-login/css/default-main.css') }}">

<link href="{{ url('css/cdn-styles-css/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
<!--===============================================================================================-->

@yield('header_scripts')