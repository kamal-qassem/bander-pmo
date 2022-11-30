@extends('install.install-layout')

@section('content')

<style type="text/css">

      @media(max-width: 767px){
      .col-md-8 {
      	margin-left: 28px;
      	margin-right: 28px;
      	margin-top: 15px;
      }
      .btn-success{
      	font-size: 17px;
        padding: 8px 10px 8px 10px;
        margin-bottom: 30px;
      }
   }

</style>

<div class="login-content installation-page" style="margin-top: 20px; background-color: #fafafa;">

		<div class="logo text-center"><img src="{{url('images/logo2.png')}}" alt="LaraOffice" height="100" width="300"></div>
		
<div class="container">
<div class="row">
	<div class="col-md-2"></div>
	<div class="col-md-8" style="border: 2px solid #d3cfcf; border-radius: 10px;">
		<div class="info">
		<h3 style="font-family: lato; font-size: 34px; font-weight: bold;">Before you proceed...</h3>
		<p style="font-size: 17px; margin-bottom: 18px; text-align: justify; margin-top: 15px;">
			Welcome to LaraOffice. Before getting started, we need some information on the database. You will need to know the following items before proceeding.</p>
<span style="font-size: 17px;">
<li>Database name</li>
<li>Database username</li>
<li>Database password</li>
<li>Database host</li>
</span>
<p style="margin-top: 15px; text-align: justify; font-size: 17px;">We’re going to use this information to update a .env file.	If for any reason this automatic file creation doesn’t work, don’t worry. All this does is fill in the database information to a configuration file called .env in root folder. You may also simply open .env which is located in root folder in a text editor, fill in your information, and save it.</p>

<p style="text-align: justify; font-size: 17px;">In all likelihood, these items were supplied to you by your Web Host. If you don’t have this information, then you will need to contact them before you can continue. If you’re all ready…</p>
		
	</div>
</div>
	<div class="col-md-2"></div>
	
</div>
</div>
		
<div class="text-center buttons" style="margin-top: 25px;">

<a class="btn button btn-success btn-lg" href="{{url('install-check-requiremetns')}}"><span style="font-family: lato; font-size: 21px;">Let’s go!</span></a>

</div>

	</div>

@stop