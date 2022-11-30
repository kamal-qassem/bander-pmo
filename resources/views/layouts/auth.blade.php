<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head-auth')
</head>

<body class="page-header-fixed">

    <div style="margin-top: 10%;"></div>

<section class="login-block">
        @yield('content')
</section>

    <div class="scroll-to-top"
         style="display: none;">
        <i class="fa fa-arrow-up"></i>
    </div>

    @include('partials.javascripts-auth')

</body>
</html>
