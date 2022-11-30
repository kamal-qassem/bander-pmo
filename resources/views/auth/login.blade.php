   <!DOCTYPE html>
   <html lang="en">

   <head>

   
     {{-- <script src="{{ url('LoginAssests/js/jquery3.3.1.js')}}"></script>
     <script src="{{  url('LoginAssests/js/popper.min.js')}}"></script>
     <script src="{{  url('LoginAssests/js/bootstrap.min.js')}}"></script>
      <script src="{{ url('LoginAssests/js/main.js')}}"></script> --}}

     
        @include('partials.head-auth')  

   </head>

  <body class="login-block">
<div >
    <div class="overlay">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12"></div>
                <div class="col-lg-6 col-md-12">
                    <div class="loginForm ms-lg-5"> 
                        <div class="signIn" id="signIn">
                            <div class=" pt-2">
                                <img src="LoginAssests/img/logo.png" alt="">
                                <h2 class = "mt-3">Creat Account</h2>
                                <p class = "p1 mt-2">Start Managing Your Project Professionally And Efficiently</p>
                                <div class="text-start">
                                   @isset($msg)
                                        {{ $msg }}
                                    @endisset
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
                                  {!! Form::open([
                                   'method' => 'POST',
                                   'id' => 'registration',
                                   'name' => 'registration',
                                   'route' => ['Register'],
                                   'files' => false,
                               ]) !!}
                                @csrf
                                        <div class="mb-3">
                                            <label  class="form-label">First Name</label>
                                            <input name="first_name" type="text" class="form-control" placeholder="Enter Your first Name">
                                          </div>
                                          <div class="mb-3">
                                            <label  class="form-label">Last Name</label>
                                            <input   name="last_name" type="text" class="form-control" placeholder="Enter Your last Name">
                                          </div>
                                        <div class="mb-3">
                                          <label for="exampleInputEmail1" class="form-label">Email address</label>
                                          <input  name="email" type="email" class="form-control" placeholder="Enter Your email">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Phone Number</label>
                                            <input name="phone1" type="text" class="form-control" placeholder="Enter Your phone number">
                                        </div>
                                        <div class="mb-3">
                                          <label for="exampleInputPassword1" class="form-label">Create Password</label>
                                          <input name="password" id = "pswd1" type="password" class="form-control" placeholder="Enter Your password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleInputPassword1" class="form-label">Repeat Password</label>
                                            <input id = "pswd2" type="password" class="form-control" placeholder="Repeat password">
                                            <span id = "message2" style="color:red"> </span> <br><br>  
        
                                        </div>
                                    
                                        <div class="d-grid gap-2 mx-auto">
                                            <button type="submit" class="btn btn-primary">Creat Account</button>					     
                                        </div>
        
                                    </form>
                                </div>
                            </div>
                            <!-- Create Account button, for mobile only -->
                            <div class="hideBtn signUpBtn-mobile textStyle">
                                <p>Don't have an account?
                                    <span class="hoverColor">Create Account</span>
                                </p>
                            </div>
                            <!-- Adding Create account toggle button -->
                            <div class="toggleBtn signUpBtn">
                                <p class = "textAboveBtn  text-center mt-3">Have an account ?<button class="btn2"> Log in </button></p>   
                            </div>
                            
                
                        </div>                   
                        <!-- Left section of responsive registration form -->
                        <div class="formDiv" id="signUp">
                            <div class=" pt-2">
                                <img src="LoginAssests/img/logo.png" alt="">
                                <h2 class = "mt-3">Log In Account</h2>
                                <p class = "p1 mt-2">Start Managing Your Project Professionally And Efficiently</p>
                                {{-- <div class = "mt-3">
                                    {!! Form::open([
                                   'method' => 'POST',
                                   'id' => 'registration',
                                   'name' => 'registration',
                                   'route' => ['Register'],
                                   'files' => false,
                               ]) !!} --}}
                         <form  class = "mt-3"  role="form" method="POST" action="{{ url('login') }}">
                         @csrf
                                  
                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Email address</label>
                                            <input id="email" name="email" type="email" class="form-control" placeholder="Enter Your Email">
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleInputPassword1" class="form-label">Password</label>
                                            <input  id="password" name="password" type="password" class="form-control" placeholder="Enter Your Password">
                                        </div>
        
                                        <div class="resetSection clearfix">
                                            <div class="mb-3 form-check float-start">
                                                <input name="remember" type="checkbox" class="form-check-input" id="exampleCheck1">
                                                <label class="form-check-label mt-1" for="exampleCheck1">Remember  Me </label>
                                                </div>
                                                <a class="float-end"  href="">Forget Password?</a>
                                            </div>
                                    
                                        <div class="d-grid gap-2 mx-auto">
                                            <button type="submit" class="btn btn-primary">log In</button>					     
                                        </div>
        
                                    </form>
                                </div>
                            </div>
                      
                        <!-- Adding SignIn toggle button -->
                            <div class="toggleBtn loginBtn">
                                <p class = "text-center mt-3">Dont have an account ? <button  class="btn2" href=""> sign up</button></p>

                            </div>
                    </div>
                         
                    <!---------- Let's add code for Signin form -------->
                  
                </div>
            </div>

        </div>
    </div>
    </div>
</div>



    <!-- Linking jQuery file with HTML -->
 
	<script src="LoginAssests/js/jquery3.3.1.js"></script>
	<script src="LoginAssests/js/popper.min.js"></script>
	<script src="LoginAssests/js/bootstrap.min.js"></script>
	<script src="LoginAssests/js/main.js"></script>
</body>
   </html>
