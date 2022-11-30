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
    @include('partialsvue.head')
</head>

<?php
$themecolor = strtolower( str_replace( ' ', '-', getActiveTheme('skin', 'red') ) );
?>
<body class="theme-{{$themecolor}}" ng-app="academia">

<div id="app">
    <div id="wrapper">

    @include('partials.topbar')
    @include('partials.sidebar')


    @if(env('DEMO_MODE'))  
    <div class="alert alert-info demo-alert">
    &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>@lang('global.info')!</strong> CRUD @lang('global.operations_disabled')
    </div>
    @endif
    <event-hub></event-hub>
    <router-view></router-view>

    </div>
</div>

{!! Form::open(['route' => 'auth.logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
<button type="submit">Logout</button>
{!! Form::close() !!}

@include('partialsvue.javascripts')

{!!getSetting('google_analytics', 'seo_settings')!!}
</body>
</html>