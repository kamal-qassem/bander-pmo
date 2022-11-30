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


<body class="hold-transition skin-blue sidebar-mini">

<div id="app">
    <div id="wrapper">

    @include('partials.topbar')
    @include('partials.sidebar')

    <div class="content-wrapper">
        <event-hub></event-hub>
        <router-view></router-view>
    </div>

    </div>
</div>

{!! Form::open(['route' => 'auth.logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
<button type="submit">Logout</button>
{!! Form::close() !!}

@include('partialsvue.javascripts')
</body>
</html>
