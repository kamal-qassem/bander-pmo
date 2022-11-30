@extends('layouts.auth')

@section('content')
<style>
    .lists>a {
         font-weight: bold;

    }

    ul{
      list-style-type: none;
      margin-top: 20px;
    }
    li {
      font-weight: bold;
      margin-top: 18px;


    }
    .login-user-details {
    	background-color: #d9edf7;
    	border-radius: 5px;
    	padding: 30px 30px;

    }
    .login-user-details a:hover{
    	     	  text-decoration: none;
    }

    .login-user-details li a.positive:hover{
                        background-color: white;
                        border: 1px solid #44a1ef;
                        color: #44a1ef;
                        /*box-shadow:11px 1px 12px 17px #e4f6fbbd;*/
              }

    .login-user-details li a{
               padding: 8px 12px;
               display: block;
               border-radius: 3px;
               margin-left: 70px;
               margin-right: 70px;
               text-align: center;
          }

    }

    @media (min-width: 768px) {
    .login-user-details li a {
        padding: 5px 10px;
        display: block;
        text-decoration: none;
        border-radius: 3px;
        cursor: pointer;

    }
    }

    @media (min-width: 768px) {
    .login-user-details li a.positive {
        border: 1px solid #44a1ef;
        background: #44a1ef;
        color: #fff;
        box-shadow:11px 1px 12px 17px #e4f6fbbd;
    }
    }



  </style>
<section class="login-block">
    <div class="container">
    <div class="row">



        <div class="col-md-6 login-sec">

            <h2 class="text-center">@lang('custom.app_sign_in')</h2>

            <?php
        $login_logo = getSetting('login_logo','login-settings');
        ?>
            <?php
              $login_logo_enable = getSetting('login_logo_enable','login-settings');
              if($login_logo_enable === 'Yes'){
            ?>

             @if($login_logo)
            <p class="single-line"><img src="{{ IMAGE_PATH_SETTINGS.$login_logo }}"  height="56" width="180"></p>
            @endif

              <?php
            } else {
             ?>

              <p class="single-line">{{ getSetting('site_title','site_settings') }}</p>

           <?php }  ?>

                         @if (count($errors) > 0)
                        <div class="alert alert-danger" style="font-weight: bold;">
                           <!--  <strong>@lang('quickadmin.qa_whoops')</strong> @lang('quickadmin.qa_there_were_problems_with_input'):
                            <br><br> -->
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (Session::has('message'))
                      <div class="alert alert-{{Session::get('status', 'info')}}">
                      &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{ Session::get('message') }}
                      </div>
                    @endif


            <form class="login-form"
             role="form"
            method="POST"
            action="{{ url('login') }}"
              >

               <input type="hidden"
                name="_token"
                value="{{ csrf_token() }}">

              <div class="form-group">
         <label for="exampleInputEmail1">@lang('global.app_email')</label>
          <input type="email"
          class="form-control"
          name="email"
          id="email"
          placeholder="Enter Email"
          value="{{ old('email') }}">



           </div>


             <div class="form-group">
             <label for="exampleInputPassword1">@lang('global.app_password')</label>
             <input type="password"
             class="form-control"
             name="password"
             id="password"
             placeholder="Enter Your Password">
           </div>


    <div class="form-check">
    <label class="form-check-label">
      <input type="checkbox" name="remember" class="form-check-input">
      <small>@lang('quickadmin.qa_remember_me')</small>
    </label>

    <button type="submit" class="btn btn-login float-right">@lang('global.app_login')</button>
  </div>

                  <div class="form-group">
                            <div class="exampleInputPassword1">
                                <a href="{{ route('auth.password.reset') }}" style="font-size: 14px;">@lang('global.app_forgot_password')</a>
                            </div>
                        </div>


        </form>

        </div>

        <div class="col-sm-6">


          <ul class="login-user-details list-unstyled">
            <li class="title" style="text-align: center; color: #44a1ef;"><b>Login As</b></li>
          <?php
          foreach( $roles as $role ) {
          ?>

               <li><a href="{{ route('direct.login', $role->id) }}" class="positive">{{$role->title}}&nbsp;{!!digi_get_help($role->description)!!}</a></li>

          <?php
          }
          ?>
          <li><a class="positive" href="https://wordpress-127011-898706.cloudwaysapps.com/" target="_blank" style="padding-bottom: -5px;">Documentation</a></li>
          </ul>


        </div>
      </div>
    </div>
   </div>
</div>
    <script>
      function fillDetails(email, password)
      {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
      }
    </script>

</section>
@endsection
