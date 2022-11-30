
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ticketit Upgrade</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="{{ url('css/cdn-styles-css/bootstrap/3.3.5/bootstrap.min.css') }}">

    <!-- Custom styles for this template -->
    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <![endif]-->
</head>

<body>

<div class="container">
    <h1 style="text-align: center">{{ trans('ticketit::install.upgrade') }}</h1>
    <br>
    <div class="well small" style="border: 1px solid #ccc">
        @if(!empty($inactive_migrations))
            <b>{{ trans('ticketit::install.migrations-to-be-installed') }}</b>
            <ul>
                @foreach($inactive_migrations as $mig)
                    <li>{{ $mig }}</li>
                @endforeach
            </ul>
        @else
            <b>{{ trans('ticketit::install.all-tables-migrated') }}</b>
        @endif
    </div>
    <br>
    <div class="well small" style="border: 1px solid #ccc">
        @if(!empty($inactive_settings))
            <b>{{ trans('ticketit::install.settings-to-be-installed') }}</b>
            <ul>
                @foreach($inactive_settings as $slug => $value)
                    <li>{{ $slug }} => {!! is_array($value) ? print_r($value) : $value !!}</li>
                @endforeach
            </ul>
        @else
            <b>{{ trans('ticketit::install.all-settings-installed') }}</b>
        @endif
    </div>
    <br>
    <a href="/tickets-upgrade" class="btn btn-lg btn-primary btn-block" type="submit">
        {{ trans('ticketit::install.proceed') }}
    </a>

</div> <!-- /container -->
</body>
</html>
