   <head>


       <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
       <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
       <script src="{{ themes('plugins/jquery/jquery.min.js') }}"></script>
       <script src="{{ themes('js/cdn-js-files/jquery-ui.min.js') }}"></script>

       @include('partials.head-auth')
       <style>
           .divider-text {
               position: relative;
               text-align: center;
               margin-top: 15px;
               margin-bottom: 15px;
           }

           .divider-text span {
               padding: 7px;
               font-size: 12px;
               position: relative;
               z-index: 2;
           }
            .invalid input {
              border-color: #d93025;
              display: block;
            }
           .divider-text:after {
               content: "";
               position: absolute;
               width: 100%;
               border-bottom: 1px solid #ddd;
               top: 55%;
               left: 0;
               z-index: 1;
           }

           .btn-facebook {
               background-color: #405D9D;
               color: #fff;
           }

           #cont {
               background-image: url('images/1570427717-image4.jpg');
               background-repeat: repeat;
               background-color: #cccccc;
           }

           .btn-twitter {
               background-color: #42AEEC;
               color: #fff;
           }

           .checkbox:checked+svg .path-moving {
               -webkit-transition: stroke .4s, stroke-dasharray .4s, stroke-dashoffset .4s cubic-bezier(.3, .8, .6, 1.5);
               transition: stroke .4s, stroke-dasharray .4s, stroke-dashoffset .4s cubic-bezier(.3, .8, .6, 1.5);
               stroke-dasharray: 25 90;
               stroke-dashoffset: 0;
           }

           .path-moving,
           .path-back {
               fill: none;
               stroke: #1161ee;
               stroke-width: 3px;
               stroke-linecap: round;
               stroke-linejoin: round;
           }

           .path-moving {
               -webkit-transition: stroke .4s, stroke-dasharray .4s, stroke-dashoffset .4s;
               transition: stroke .4s, stroke-dasharray .4s, stroke-dashoffset .4s;
               stroke: #ffffff;
               stroke-dasharray: 110;
               stroke-dashoffset: -32;
           }

           .enregistrer {
               position: absolute;
               padding: 15% 13%;
               width: 74%;
               right: 0px;
               transition: all 0.7s;
           }

           .menu {
               position: relative;
               padding: 15% 13% 0 13%;
           }

           .active-section {
               position: absolute;
               right: 500px;
           }

           .remove-section {
               position: absolute;
               left: 500px;
           }

           .menu h2 {
               display: inline;
               margin: 20px;
               padding-bottom: 3px;
               border-bottom: 3px solid #1161ee;
           }

           a:not(.active) {
               cursor: default;
           }

           .menu .active h2 {
               border-bottom: 0 solid #1161ee;
               color: #AEAEAE;
               transition: color 0.5s ease-in;
           }

           .connexion {
               position: absolute;
               padding: 15% 13%;
               width: 74%;
               left: 0px;
               transition: all 0.7s;
           }

           .connexion h2 {
               display: inline;
               margin: 20px;
               padding-bottom: 3px;
               border-bottom: 2px solid #1161ee;
           }

           .connexion h4 {
               margin-bottom: 0;
               text-align: center;
               color: #ffffff;
               opacity: 0.3;
           }

           .connexion h4:hover {
               opacity: 0.8;
               transition: all 0.1s ease-in;
           }

           .container {
               display: block;
               position: relative;
               margin: auto;
               margin-top: 30px;
               box-shadow: 1px 5px 10px 1px #333;
               overflow: hidden;
               border: 1px solid green;
           }

           .imglogo {
               margin: auto;
               margin-bottom: 0;
               padding-bottom: 0;

           }

           .imglogo img {
               display: block;
               margin-left: auto;
               margin-right: auto;


           }

           .center {
               display: block;
               margin-left: auto;
               margin-right: auto;
           }

           body {
               background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
               background-size: 400% 400%;
               animation: gradient 50s ease infinite;
               height: 100vh;
               background-image: url('images/bg-main.jpg');
               background-repeat: repeat;
               background-color: transparent;

           }

           @keyframes gradient {
               0% {
                   background-position: 0% 50%;
               }

               50% {
                   background-position: 100% 50%;
               }

               100% {
                   background-position: 0% 50%;
               }
           }
       </style>

   </head>
   <body class="page-header-fixed" style="margin-top: 0px;">
       <div class="d-flex flex-column justify-content-center w-100 h-100" >
           <section class="login-block">
               <div class="container col-md-4">
                   <div class="imglogo">
                       <img src="{{ 'images\emptylogo.png' }}" height="200" width="170">
                       </p>
                   </div>
                   <div class="row col-md-12 center">
                       <div id="login" class="col-md-12 center" style="display:block">
                           @isset($msg)
                                    {{ $msg }}
                            @endisset

                           <article>
                               <h4 class="card-title mt-3 text-center">Log In Account</h4>
                               <p class="text-center">Start managing your project professionally and efficiently</p>
                               @if (count($errors) > 0)
                                   <div class="alert alert-danger">
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
                                   <div class="alert alert-{{ Session::get('status', 'info') }}">
                                       &nbsp;&nbsp;&nbsp;<a href="#" class="close" data-dismiss="alert"
                                           aria-label="close">&times;</a>
                                       {{ Session::get('message') }}
                                   </div>
                               @endif


                               <form class="login-form" role="form" method="POST" action="{{ url('login') }}">

                                   @csrf

                                   <div class="form-group">
                                       <div class="form-group input-group">
                                           <div class="input-group-prepend">
                                               <span class="input-group-text"> <i class="fa fa-envelope"></i>
                                               </span>
                                           </div>
                                           <input id="email" name="email" class="form-control"
                                               placeholder="Email address" type="email">
                                       </div> <!-- form-group// -->

                                   </div>

                                   <div class="form-group input-group ">
                                       <div class="input-group-prepend">
                                           <span class="input-group-text">
                                               <i class="fa fa-lock"></i> </span>
                                       </div>
                                       <input class="form-control" id="password" name="password" placeholder="password"
                                           type="password">
                                   </div> <!-- form-group// -->



                                   <div class="form-check">
                                       <label class="form-check-label">
                                           <input type="checkbox" name="remember" class="form-check-input">
                                           <small>@lang('quickadmin.qa_remember_me')</small>
                                       </label>
                                   </div>
                                   <br />
                                   <div class="form-group">
                                       <div class="exampleInputPassword1">
                                           <a href="{{ route('auth.password.reset') }}"
                                               style="font-size: 14px;">@lang('global.app_forgot_password')</a>
                                       </div>
                                   </div>

                                   <div class="form-group">
                                       <button type="submit" class="btn btn-primary btn-block"> Log In
                                       </button>
                                   </div> <!-- form-group// -->
                                   <p onclick="signUp()" class="text-center">don't Have an account? <a
                                           href="#">sign
                                           Up</a>
                                   </p>

                               </form>
                           </article>
                       </div>
                   </div>
                   <div class="row col-md-12 center">
                       <div id="signup" class="col-md-12 center" style="display:none">
                           <article class="card-body mx-auto" style="max-width: 400px;">
                               <h4 class="card-title mt-3 text-center">Create Account</h4>
                               <p class="text-center">Start managing your project professionally and efficiently</p>
                               {!! Form::open([
                                   'method' => 'POST',
                                   'id' => 'registration',
                                   'name' => 'registration',
                                   'route' => ['Register'],
                                   'files' => false,
                               ]) !!}
                               <div class="form-group input-group">
                                   <div class="input-group-prepend">
                                       <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                   </div>
                                   <input name="first_name" class="form-control" placeholder="first name" type="text"
                                       required>
                               </div>
                               <!--first name form-group// -->
                               <div class="form-group input-group">
                                   <div class="input-group-prepend">
                                       <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                   </div>
                                   <input name="last_name" class="form-control" placeholder="last name" type="text"
                                       required>
                               </div>
                               <!--last_name  form-group// -->

                               <div class="form-group input-group">
                                   <div class="input-group-prepend">
                                       <span class="input-group-text"> <i class="fa fa-envelope"></i>
                                       </span>
                                   </div>
                                   <input name="email" class="form-control" placeholder="Email address" type="email"
                                       required>
                               </div> <!-- form-group// -->
                                 <span  id="email-field"  name="email-field"  class="error password-error">
                                   <i class="bx bx-error-circle error-icon"></i>
                                   <p class="error-text" style="display:none">
                                       Please enter atleast 8 charatcer with number, symbol, small and
                                       capital letter.
                                   </p>
                               </span>
                               <div class="form-group input-group">
                                   <div class="input-group-prepend">
                                       <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
                                   </div>

                                   <input name="phone1" class="form-control" placeholder="Phone number"
                                       type="text">
                               </div> <!-- form-group// -->



                               <div class="form-group input-group">
                                   <div class="input-group-prepend">
                                       <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                   </div>
                                   <input name="password" class="form-control" placeholder="Create password"
                                       type="password">
                               </div> <!-- form-group// -->
                               <span id="create-password" name="create-password" class="error password-error">
                                   <i class="bx bx-error-circle error-icon"></i>
                                   <p class="error-text" style="display:none">
                                       Please enter atleast 8 charatcer with number, symbol, small and
                                       capital letter.
                                   </p>
                               </span>
                               <div class="form-group input-group">
                                   <div class="input-group-prepend">
                                       <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                   </div>
                                   <input name="cPassword" id="cPassword" class="form-control"
                                       placeholder="Repeat password" type="password">
                               </div>
                                <span  class="confirm-password error password-error ">
                                   <i class="bx bx-error-circle error-icon"></i>
                                   <p id="confirm-password" name="confirm-password" class="error-text" style="display:none" >
                                      
                                   </p>
                               </span>
                                <!-- form-group// -->
                               <div class="form-group">
                                   <button name="submit" id="submit" type="submit"
                                       class="btn btn-primary btn-block"> Create Account
                                   </button>
                               </div> <!-- form-group// -->
                               <p onclick="Login()" class="text-center">Have an account? <a href="#">Log In</a>
                               </p>


                               </form>

                           </article>
                       </div>
                   </div>
               </div>
           </section>

           <script>
               function signUp() {
                   var x = document.getElementById("signup");
                   var y = document.getElementById("login");
                   if (x.style.display === "none") {
                       x.style.display = "block";
                       y.style.display = "none"
                   } else {
                       x.style.display = "none";
                       y.style.display = "block"
                   }
               }

               function Login() {
                   var x = document.getElementById("login");
                   var y = document.getElementById("signup");
                   if (x.style.display === "none") {
                       x.style.display = "block";
                       y.style.display = "none"
                   } else {
                       x.style.display = "none";
                       y.style.display = "block"
                   }
               }
           </script>
           <div class="row">

               <div class="col-xs-6">
                   <div class="form-group">

                       @include('partials.javascripts-auth')

                   </div>
               </div>
           </div>
           <script>
               const form = document.querySelector("#registration"),
                   emailField = document.querySelector("#email-field"),
                   emailInput = document.querySelector("#email"),
                   passField = document.querySelector("#create-password"),
                   passInput = document.querySelector("#password"),
                   cPassField = document.querySelector("#confirm-password"),
                   cPassInput = document.querySelector("#cPassword");


               // Password Validation
               //function createPass() {
                 //  const passPattern =
               //        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

               //    if (!passInput.value.match(passPattern)) {
               //     alert()
                //       return passField.classList.add(
                //           "invalid"); //adding invalid class if password input value do not match with passPattern
                //   }
              //     passField.classList.remove("invalid"); //removing invalid class if password input value matched with passPattern
              // }

               // Confirm Password Validtion
               function confirmPass() {
                   if (passInput.value !== cPassInput.value || cPassInput.value === "") {
                    // alert("please chack password and confirmed passowrd");
                        cPassField.textContent ="wrong passowrd";
                        cPassField.style.display = "block";
                        cPassInput.
                       return cPassField.classList.add("invalid");

                   }
                    cPassField.style.display = "none";
                   cPassField.classList.remove("invalid");
               }

               // Calling Funtion on Form Sumbit
               form.addEventListener("submit", (e) => {
                   e.preventDefault(); //preventing form submitting
                  // checkEmail();
                  // createPass();
                   confirmPass();

                   //calling function on key up
                  // emailInput.addEventListener("keyup", checkEmail);
                  // passInput.addEventListener("keyup", createPass);
                   cPassInput.addEventListener("keyup", confirmPass);

                   if (
                       !emailField.classList.contains("invalid") &&
                       !passField.classList.contains("invalid") &&
                       !cPassField.classList.contains("invalid")
                   ) {
                       location.href = form.getAttribute("action");
                   }
               });
           </script>
   </body>

   </html>
