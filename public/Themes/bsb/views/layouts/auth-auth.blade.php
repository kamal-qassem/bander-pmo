<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body>

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
        @yield('content')
    </div>
    </div>
</div>

    <div class="scroll-to-top"
         style="display: none;">
        <i class="fa fa-arrow-up"></i>
    </div>

    @include('partials.javascripts')

</body>
</html>