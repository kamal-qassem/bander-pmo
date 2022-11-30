<?php
$direction = 'ltr';
if (\Cookie::get('direction')) {
    // die(\Cookie::get('direction'));
    $direction = \Cookie::get('direction');
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<title>{{getSetting('site_title', 'site_settings', trans('global.global_title'))}}</title>
<!-- Favicon-->
<link rel="icon" href="{{IMAGE_PATH_SETTINGS.getSetting('site_favicon', 'site_settings')}}" type="image/x-icon" />
<meta name="description" content="{{getSetting('meta_description', 'seo_settings')}}">
<meta name="keywords" content="{{getSetting('meta_keywords', 'seo_settings')}}">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

<link href="{{ url('css/cdn-styles-css/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

<!-- Bootstrap Core Css -->
<link href="{{ themes('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all">

<!-- Bootstrap Select Css -->
<link href="{{ themes('plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />

<link href="{{ themes('css/cdn-bsb-styles-css/jquery-ui.min.css') }}" rel="stylesheet" />
<link href="{{ themes('css/cdn-bsb-styles-css/dataTables.bootstrap.min.css') }}" rel="stylesheet" />
<link href="{{ themes('css/cdn-bsb-styles-css/responsive.bootstrap.min.css') }}" rel="stylesheet" />
<link href="{{ themes('css/cdn-bsb-styles-css/select.dataTables.min.css') }}" rel="stylesheet" />


<!-- Waves Effect Css -->
<link href="{{ themes('plugins/node-waves/waves.css') }}" rel="stylesheet" />

<!-- Animation Css -->
<link href="{{ themes('plugins/animate-css/animate.css') }}" rel="stylesheet" />

<!-- Bootstrap DatePicker Css -->
<!--<link href="{{ themes('plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" />-->
<link href="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>

<!-- Bootstrap Chosen Css -->
<link href="{{ themes('plugins/chosen/chosen.css') }}" rel="stylesheet" />

<!-- Bootstrap Tagsinput Css -->
<link href="{{ themes('plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">

<link href="{{ themes('plugins/sweetalert/sweetalert.css') }}" rel="stylesheet" />




<!-- Custom Css -->
<link href="{{ themes('css/style.css') }}" rel="stylesheet" type="text/css" media="all">
@if ( 'rtl' === $direction )
<!-- Custom RTL Css -->
<link href="{{ themes('css/style-rtl.css') }}" rel="stylesheet">
@endif

<!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->

<!-- cartpage links -->
<link href="{{ url('css/products-cart.css') }}" rel="stylesheet">
<link href="{{ url('css/cart-side-page.css') }}" rel="stylesheet">

<link href="{{ themes('css/themes/all-themes.css') }}" rel="stylesheet" type="text/css" media="all"/>
<link href="{{ url('css/stats-style.css') }}" rel="stylesheet">

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
</script>