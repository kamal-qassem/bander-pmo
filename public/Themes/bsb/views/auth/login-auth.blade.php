@extends('layouts.auth')

@section('content')
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);"><b>@lang('global.app_login')</b></a>
            <small>{{ ucfirst(config('app.name')) }}</small>
        </div>
        <div class="card">
            <div class="body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>@lang('global.app_whoops')</strong> @lang('global.app_there_were_problems_with_input'):
                        <br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="form-horizontal"
                          role="form"
                          method="POST"
                          action="{{ url('login') }}">
                        <input type="hidden"
                               name="_token"
                               value="{{ csrf_token() }}">
                    <div class="msg">Sign in to start your session</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input autocomplete="off" type="email"
                                       class="form-control"
                                       name="email"
                                       value="{{ old('email') }}" placeholder="@lang('global.app_email')" autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input autocomplete="off" type="password"
                                       class="form-control"
                                       name="password" placeholder="@lang('global.app_password')" required>                            
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            {!! NoCaptcha::display() !!}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-8 p-t-5">                            
                            <input type="checkbox"
                                           name="remember" id="rememberme" class="filled-in chk-col-pink"> 
                            <label for="rememberme">@lang('global.app_remember_me')</label>
                        </div>

                        <div class="col-xs-4">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">@lang('global.app_login')</button>
                        </div>
                    </div>

                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-4">
                            &nbsp;
                        </div>
                        <div class="col-xs-8 align-right">
                            <a href="{{ route('auth.password.reset') }}">@lang('global.app_forgot_password')</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection