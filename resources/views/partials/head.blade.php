<?php
$color_theme = 'default';
if (\Cookie::get('color_theme')) { 
  $color_theme = \Cookie::get('color_theme');
}

$is_css = false;
if ( ! empty( $color_theme ) && substr($color_theme, -3) === 'css' ) {
  $is_css = true;
}
?>
<meta charset="utf-8">
<title>{{getSetting('site_title', 'site_settings', trans('global.global_title'))}}
</title>

<!-- Favicon-->
<link rel="icon" href="{{IMAGE_PATH_SETTINGS.getSetting('site_favicon', 'site_settings')}}" type="image/x-icon" />
<meta name="description" content="{{getSetting('meta_description', 'seo_settings')}}">
<meta name="keywords" content="{{getSetting('meta_keywords', 'seo_settings')}}">


<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Font Awesome -->
<link href="{{ url('css/cdn-styles-css/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

<!-- Ionicons -->
<link href="{{ url('css/cdn-styles-css/ionicons-2.0.1/css/ionicons.min.css') }}" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>

<![endif]-->

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">


<link href="{{ url('adminlte/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('adminlte/css') }}/select2.min.css"/>
<link href="{{ url('adminlte/css/AdminLTE.min.css') }}" rel="stylesheet">

<link rel="stylesheet" href="{{ url('adminlte/css/skins/_all-skins.min.css') }}">

<link href="{{ url('css/cdn-styles-css/jquery-ui.css') }}" rel="stylesheet">


<link href="{{ url('css/cdn-styles-css/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ url('css/cdn-styles-css/datatables/dataTables.min.css') }}" rel="stylesheet">

<link href="{{ url('css/cdn-styles-css/datatables/select.dataTables.min.css') }}" rel="stylesheet">

<link href="{{ url('css/cdn-styles-css/datatables/buttons.dataTables.min.css') }}" rel="stylesheet">


<!-- cartpage links -->
<link href="{{ url('css/products-cart.css') }}" rel="stylesheet">
<link href="{{ url('css/cart-side-page.css') }}" rel="stylesheet">

<link href="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>

<link href="{{ url('css/cdn-styles-css/bootstrap-notify.css') }}" rel="stylesheet">  

<link href="{{ url('css/sweetalert.css') }}" rel="stylesheet"/>


<link rel="stylesheet" type="text/css" href="{{asset('slick/slick.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('slick/slick-theme.css')}}"/>

<link href="{{ url('css/stats-style.css') }}" rel="stylesheet">

<link href="{{ url('css/custom.css') }}" rel="stylesheet">
@if( $color_theme != 'default' && $is_css )
  <link href="{{ url('css/' . $color_theme) }}" rel="stylesheet">
@endif

@if ( config('app.action_buttons_hover') )
<link href="{{ url('css/hover-buttons.css') }}" rel="stylesheet">
@endif

<script type="text/javascript">
  var baseurl = '{{ url('/') }}';
  var crsf_token = '_token';
  var crsf_hash = '{{ csrf_token() }}';

  var currency = '{{ getDefaultCurrency() }}';
  var currency_position = '{{ getCurrencyPosition() }}';
  <?php
  $toundsand_separator = App\Settings::getSetting('toundsand_separator', 'currency_settings');
  if ( empty( $toundsand_separator ) ) {
    $toundsand_separator = ',';
  }
  $decimal_separator = App\Settings::getSetting('decimal_separator', 'currency_settings');
  if ( empty( $toundsand_separator ) ) {
    $toundsand_separator = '.';
  }
 $decimals = App\Settings::getSetting('decimals', 'currency_settings');
  if ( empty( $decimals ) ) {
    $decimals = '2';
  }
  ?>
  var toundsand_separator = '{{ $toundsand_separator }}';
  var decimal_separator = '{{ $decimal_separator }}';
  var decimals = '{{ $decimals }}';
  var js_global = {};
  js_global["cartproducts"] = [];
  js_global["months_json"] = ["January","February","March","April","May","June","July","August","September","October","November","December"];
</script>


@yield('header_scripts')
