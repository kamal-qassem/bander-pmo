<?php
$direction = 'ltr';
if (\Cookie::get('direction')) {
    
    $direction = \Cookie::get('direction');
}

$lang = 'en';
if (\Cookie::get('language')) { 
    $lang = \Cookie::get('language');
}

$theme = 'default';
if (\Cookie::get('theme')) { 
    $theme = \Cookie::get('theme');
}
$color_theme = getSettingTheme('theme_color', $theme, 'skin-blue');
?>
<!DOCTYPE html>
<html lang="{{$lang}}" dir="{{ $direction }}">

<head>
    @include('partials.head')
</head>

<body class="hold-transition {{$color_theme}} sidebar-mini" ng-app="academia">

<span id="hdata"
      data-df="{{ config('app.date_format_moment') }}"
      data-curr="{{ getDefaultCurrency() }}"></span>

<div id="wrapper">

@if( empty( $topbar ) )
    @include('partials.topbar')
@elseif ( 'yes' === $topbar )
    @include('partials.topbar')
@endif

<?php
$style = '';
$columns = 6;
?>
@if( empty( $sidebar ) )
    @include('partials.sidebar')
@elseif ( 'yes' === $sidebar )
    @include('partials.sidebar')
@else
<?php $style = ' style="margin-left:0px;"'; ?>
@endif

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" <?php echo $style; ?>>
        <!-- Main content -->
        <section class="content">
            @if(isset($siteTitle))
                <h3 class="page-title">
                    {{ $siteTitle }}
                </h3>
            @endif

            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-9">
                    <?php
                    $parts = getController();
                    if( env('APP_DEV') ) {
                        echo $parts['controller'] . '@' . $parts['action'] . ' ' . date('d-m-Y H:i:s');
                    }
                    ?>
                    {{ Breadcrumbs::render($parts['controller'] . '.' . $parts['action']) }}
                    @if(env('DEMO_MODE'))  
                    <div class="alert alert-info demo-alert col-md-12">
                    &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>@lang('global.info')!</strong> CRUD @lang('global.operations_disabled')
                    </div>
                    @endif

                    @if (Session::has('message'))
                        <?php
                        $message_type = getSetting('message_type', 'site_settings', 'onpage');
                        if ( 'onpage' === $message_type ) {
                        ?>
                        <div class="alert alert-{{Session::get('status', 'info')}}">
                            &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ Session::get('message') }}
                        </div>
                    <?php } ?>
                    @endif
                    @if ($errors->count() > 0 && ! in_array($parts['controller'], array( 'TicketsController', 'StatusesController', 'PrioritiesController', 'AgentsController', 'ConfigurationsController', 'CategoriesController', 'AdministratorsController' ) ))
                        <div class="alert alert-danger">
                            <ul class="list-unstyled">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')

                </div>
            </div>
        </section>
    </div>
</div>

{!! Form::open(['route' => 'auth.logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
<button type="submit">Logout</button>
{!! Form::close() !!}

@include('partials.javascripts')

{!!getSetting('google_analytics', 'seo_settings')!!}
</body>
</html>