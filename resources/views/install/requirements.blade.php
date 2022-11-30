@extends('install.install-layout')

@section('content')
<style type="text/css">

html, body {
  	background: white;
  	background-size:cover;
  	width: 100%;
  	height: auto; background-repeat:no-repeat; background-attachment:local;  font-size: 90%;
	font-family: Helvetica,Arial,sans-serif;
	margin: 0 auto; 
}

.install {
	background: none repeat scroll 0 0 #fff;
    border-radius: 5px;
    margin: 65px auto 0;
    width: 32%;
}
.logo {
	margin-top: 25px;
}
.active {
    color: #3bf02d;
}
.stage {
	color: #171717;
}
.done {
    color: #fafafa;
}
.table {
	margin-top: 15px;
}
 .button {
 	margin-bottom: 40px;
 	margin-top: 15px;
 }
 @media(max-width: 767px){
 	  .table {
           width: 100%;
 	  }
 	  .logo {
 	  	margin-top: 20px;
 	  	margin-bottom: -15px;
 	  }
 	  .button {
           font-size: 17px;
           padding: 6px 14px 6px 14px;
           margin-bottom: 30px;
 	  }
 }

</style>

<div class="login-content installation-page" >

		<div class="logo text-center"><img src="{{url('images/logo3.png')}}" alt="" height="100" width="300"></div>
		@include('install.navigation')
		
		@include('errors.errors')
		{!! Form::open(array('route' => ['install.requirements'], 'method' => 'POST', 'name'=>'registrationForm ', 'novalidate'=>'', 'class'=>"loginform", 'id'=>"install_form")) !!}
<div class="row" >
<?php $isInstallable = 1; ?>
    <div class="container">
	 <div class="col-md-12">
	 <table class="table" style="align: center; width: 100%; background-color: #f1f1f1; border: 2px solid #98acac;">
  <thead>
    <tr>
       
      <th style="font-family: lato; font-size: 17px;">Requirement</th>
      <th style="font-family: lato; font-size: 17px;">Status</th>
      
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">PHP Version >= 7.1.3 </th>
      
      <td>
	      	@if (version_compare(phpversion(), '7.1.3', '>='))  
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
      
    </tr>

    <tr>
      <th scope="row">.env Writable</th>
      
      <td>
	      	@if ( is_writable( base_path() . '/.env' ) )
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">PDO PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('PDO'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">libxml PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('libxml'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">Ctype PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('Ctype'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">JSON PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('JSON'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">BCMath PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('BCMath'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

  
    <tr>
      <th scope="row">max_execution_time</th>
      
      <td>
	      	@if(ini_get('max_execution_time'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>


      <tr>
      <th scope="row">Tokenizer PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('tokenizer'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">XML PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('xml'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>

    <tr>
      <th scope="row">GD Library</th>
      
      <td>
	      	@if(extension_loaded('gd'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>


    
    <tr>
      <th scope="row">fileinfo</th>
      
      <td>
	      	@if(extension_loaded('fileinfo')) 
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>
    <tr>
      <th scope="row">OpenSSL PHP Extension</th>
      
      <td>
	      	@if (extension_loaded('openssl')) 
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>
    <tr>
      <th scope="row">Mbstring PHP Extension</th>
      
      <td>
	      	@if(extension_loaded('mbstring'))
	      		<i class="fa fa-check text-success" aria-hidden="true"></i>
	      	@else
		      	<i class="fa fa-times text-danger" aria-hidden="true"></i>
		      	<?php $isInstallable = 0; ?>
	      	@endif
      </td>
    </tr>
   
  
  </tbody>
</table>
	 	
	 </div>
	</div>
	</div>
	
</div>
		
		

		

		
			@if($isInstallable)
		
			<div class="text-center buttons">
<a href="{{route('install.index')}}" title="Instructions" class="btn button btn-info btn-lg"><span style="font-family: lato; font-size: 21px;">Instructions</span></a>&nbsp;&nbsp;
				<button type="button"  class="btn button btn-success btn-lg" 

				ng-disabled='!registrationForm.$valid' onclick="submitForm();" ><span style="font-family: lato; font-size: 21px;">Next</span></button>

			</div>
			@else
			<p class="text-danger">Note: Please install/enable the above requirements to continue... </p>
			@endif

		{!! Form::close() !!}
		

		 <div class="loadingpage text-center" style="display: none;" id="after_display">
		 	
		 	<p>Please Wait...</p>

		 	<img width="200" src="{{url('images/loading-small.gif')}}">
		 </div>

	</div>

@stop

@section('footer_scripts')
<script src="{{ url('js/bootstrap-toggle.min.js') }}"></script>
 <script>
 	function submitForm() {
 		$('#install_form').hide();
 		$('.buttons').hide();
 		$('#after_display').show();
 		$('#install_form').submit();
 	}
 </script>
@stop