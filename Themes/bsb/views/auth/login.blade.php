@extends('layouts.auth')

@section('content')

             <div class="login100-pic js-tilt" data-tilt>

                <?php
                $login_logo = getSetting('login_logo','login-settings');
                $login_logo_enable = getSetting('login_logo_enable','login-settings');
                if($login_logo_enable === 'Yes'){
                ?>
                    @if( $login_logo && file_exists(getSettingsPath() . $login_logo) )
                    <img src="{{ IMAGE_PATH_SETTINGS.$login_logo }}" style="margin-top:125px;">  
                    @endif
                

                  <?php
            } else {
             ?>

              <small><p style="margin-top:150px;">{{ getSetting('site_title', 'site_settings') }}</p></small>
                   


           <?php }  ?>
       </div>


                <form class="login100-form validate-form"
                           role="form"
                          method="POST"
                          action="{{ url('login') }}">

                         <input type="hidden"
                               name="_token"
                               value="{{ csrf_token() }}">

                    <span class="login100-form-title">

                        @lang('custom.app_sign_in')

                          <?php
                            if($login_logo_enable === 'Yes'){
                        ?>
                        @if($login_logo)
                        <small ><p>{{ getSetting('site_title', 'site_settings') }}</p></small>
                        @else
                        <small ><p></p></small>
                        @endif
                <?php } ?>
                    </span>

                           <div class = "col-sm-12"> 
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
            </div>

                            <p class="center"></p>
                   
                    <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">

                        <input class="input100" type="email" name="email" placeholder="Email"
                           value="{{ old('email') }}" placeholder="@lang('global.app_email')" >

                        <span class="focus-input100"></span>

                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>


                    <div class="wrap-input100 validate-input" data-validate = "Password is required">
                        <input class="input100" type="password" name="password" placeholder="@lang('global.app_password')">
                                <p class="help-block"></p>
                
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                       <div class="text-center p-t-136">
                        <a class="txt2" href="#">
                            
                            <input type="checkbox" name="remember" id="rememberme"> 

                            <label for="rememberme">@lang('global.app_remember_me')</label>

                        </a>
                    </div>
                    
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" type="submit">
                            @lang('global.app_login')
                        </button>
                    </div>



                    <div class="text-center p-t-12">
                       
                        <a class="txt2" href="{{ route('auth.password.reset') }}">
                            @lang('global.app_forgot_password')
                        </a>
                    </div>


                </form>
    

@endsection