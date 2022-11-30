<?php
$direction = 'ltr';
if (\Cookie::get('direction')) {
    // die(\Cookie::get('direction'));
    $direction = \Cookie::get('direction');
}

$lang = 'en';
if (\Cookie::get('language')) { 
    $lang = \Cookie::get('language');
}
?>
<!DOCTYPE html>
<html lang="{{$lang}}" dir="{{ $direction }}">

<head>
    @include('partials.head')
</head>
<?php
$themecolor = strtolower( str_replace( ' ', '-', getActiveTheme('skin', 'red') ) );
//$themecolor = 'red';
?>
<body class="theme-{{$themecolor}}" ng-app="academia">

    <span id="hdata"
      data-df="{{ config('app.date_format_moment') }}"
      data-curr="{{ getDefaultCurrency() }}"></span>
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING..." class="searchable-field">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div>
    <!-- #END# Search Bar -->
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

    <section class="content" <?php echo $style; ?>>
        <?php
        $parts = getController();
        // echo $parts['controller'] . '@' . $parts['action'];
        ?>
        <div class="col-md-12">{{ Breadcrumbs::render($parts['controller'] . '.' . $parts['action']) }}</div>
        <div class="container-fluid">
            

            @if(env('DEMO_MODE'))  
            <div class="col-md-12">
                <div class="alert alert-info demo-alert">
                &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>@lang('global.info')!</strong> CRUD @lang('global.operations_disabled')
                </div>
            </div>
            @endif

            @if ($errors->count() > 0 && ! in_array($parts['controller'], array( 'TicketsController', 'StatusesController', 'PrioritiesController', 'AgentsController', 'ConfigurationsController', 'CategoriesController', 'AdministratorsController' ) ))
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <ul class="list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if (Session::has('message'))
                <?php
                $message_type = getSetting('message_type', 'site_settings', 'onpage');
                if ( 'onpage' === $message_type ) {
                ?>
                <div class="col-md-12">
                    <div class="alert alert-{{Session::get('status', 'info')}}">
                        &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{ Session::get('message') }}
                    </div>
                </div>
            <?php } ?>
            @endif

            @yield('content')
        </div>
    </section>

    {!! Form::open(['route' => 'logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
<button type="submit">Logout</button>
{!! Form::close() !!}

    @include('partials.javascripts')

    {!!getSetting('google_analytics', 'seo_settings')!!}

</body>

</html>
