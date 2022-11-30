@extends('install.install-layout')

@section('content')

<style type="text/css">

html, body {
  	background: white;
  	background-size:cover;
  	width: 100%;
  	height: auto; background-repeat:no-repeat; background-attachment:local;font-size: 90%;
	font-family: Helvetica,Arial,sans-serif;
	margin: 0 auto; 
}

.install {
	background: none repeat scroll 0 0 #fff;
    border-radius: 5px;
    margin: 65px auto 0;
    width: 32%;
}
 .input-group-addon {
 	padding: 7px;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #555;
    text-align: center !important;
    background-color: #15528c !important;
    border: 1px solid #ccc !important;
 }
.form-hed {
	font-size: 20px;
	color: #437ba3;
	text-shadow: 0 0px 0px rgba(0, 0, 0, 0.0) !important;
	text-align: left;
	border-bottom: 1px solid #ccc; 
	padding:25px 10px 10px; 
	margin-top:10px; 
	float:left; 
	width:93.5%;
}
.fa {
	color: white;
}
code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 15px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 20px 0;
	padding: 70px 10px;
}
.logo {
	padding:0px 10px; 
	align: center;
	height:48px;
	margin-top:35px;
	margin-bottom: 20px;
}

p.img-header {
	margin-bottom: -20px;
	padding: 0 13px 7px;
	background: #534051;
}
.prog {
    width:100%;
    height:50px;
    border:1px solid #269abc;
}
.filler {
    width:0%;
    height:50px;
    background-color:#31b0d5;
}
.active {
    color: #3bf02d;
}
.stage{
	color: #575757;
}
.done {
    color: #837b7b;
}
  .buttn
.sec1 {
	background-color: #575757;
}
 @media(max-width: 767px){
 	.sec1 {
 		width: 100%;
 		margin-left: 15px;
 	}
 	.info {
 		margin-left: 15px;
 		font-size: 26px;
 		margin-bottom: 15px;
 	}
 	.buttn {
 		  font-size: 18px;
 		  margin-bottom: 30px;
 		  padding: 5px 12px 5px 12px;
 		  margin-top: 10px;
 	}
 }
</style>

<div class="login-content installation-page" >

		<div class="logo text-center"><img src="{{url('images/logo3.png')}}" alt="Laraoffice" height="100" width="300"></div>
		
		@include('install.navigation')
		<div class="row">
			<div class="col-md-6 col-xs-offset-3">
		@include('errors.errors')
		</div>
	</div>
		{!! Form::open(array('route' => ['install.project'], 'method' => 'POST', 'name'=>'registrationForm ', 'novalidate'=>'', 'class'=>"loginform", 'id'=>"install_form")) !!}
	
<div class="row" >
	<div class="col-md-2"></div>
	<div class="col-md-8" style="background-color: #f6fbff; border: 2px solid #15528c17; border-radius: 7px; box-shadow: 3px 5px 6px -2px #eaeaea;">
	<div class="info">
		<h3 style="font-family: lato; margin-top: 30px; font-size: 29px;
		font-weight: bold;">Server Hosting Details</h3>
<p style="font-family: lato;font-size: 17px; margin-bottom: 10px;">Please enter server login details to install this system </p>
</div>
      <div class="sec1">  
		<div class="input-group" style="margin-top: 9px; margin-bottom: 9px;">
		<div class="input-group-addon"><i class="fa fa-server" aria-hidden="true" style="padding: 1px;"></i></div>
			{{ Form::text('host_name', $value = null , $attributes = array('class'=>'form-control',
				'placeholder' => 'Host Name',
				'ng-model'=>'host_name',
				'required'=> 'true',
				'ng-minlength' => '4',
				'id' => 'host_name',
			)) }}
			
		</div>
		<div class="input-group" style="margin-top: 9px; margin-bottom: 9px;">
		<div class="input-group-addon"><i class="fa fa-database" aria-hidden="true" style="padding-right: 2px; padding-left: 2px;"></i></div>
			{{ Form::text('database_name', $value = null , $attributes = array('class'=>'form-control',
				'placeholder' => 'Database Name',
				'ng-model'=>'database_name',
				'required'=> 'true',
				'ng-minlength' => '1',
			)) }}
			
		</div>

		<div class="input-group" style="margin-top: 9px; margin-bottom: 9px;">
		<div class="input-group-addon"><i class="fa fa-user" aria-hidden="true" style="padding-right: 3px; padding-left: 3px;"></i></div>

			{{ Form::text('user_name', $value = null , $attributes = array('class'=>'form-control',
				'placeholder' => 'Database Username',
				'ng-model'=>'user_name',
				'required'=> 'true',
				'ng-minlength' => '1',
				'id' => 'user_name',
			)) }}
			
		</div>

       <div class="input-group" style="margin-top: 9px; margin-bottom: 9px;">
		<div class="input-group-addon"><i class="fa fa-lock" aria-hidden="true" style="padding-right: 4px; padding-left: 3px"></i></div>
			{{ Form::password('password', $attributes = array('class'=>'form-control',
				'placeholder' => 'Database Password',
				'ng-model'=>'password',
				'id' => 'password',
			)) }}
			 
		</div>

		<div class="input-group" style="margin-top: 9px; margin-bottom: 9px;">
		<div class="input-group-addon"><i class="fa fa-dot-circle-o" aria-hidden="true" style="padding-right: 2px; padding-left: 2px;"></i></div>
			{{ Form::text('port_number', '3306' , $attributes = array('class'=>'form-control',
				'placeholder' => 'Port Number',
				'ng-model'=>'port_number',
				'required'=> 'true',
				'ng-minlength' => '1',
			)) }}
			
		</div>
	

	 <div class="input-group" style="margin-bottom: 50px;">
		
		<div class="input-group-addon"><i class="fa fa-bolt" aria-hidden="true" style="padding-right: 5px; padding-left: 4px; text-align: center;"></i></div>

		 	 <select class="form-control" name="sample_data"
		 	 ng-model="sample_data"
		 	 
		 	 required

		 	  >
  			 	<option value="no-data">Install With Empty Data</option>
  			 	<option value="data">Install With Data</option>
  			 	
  			 </select>
  			 
		</div>
		</div>
 
	</div>
	<div class="col-md-2"></div>
	
</div>
</div>
		
<div class="text-center buttons" style="margin-top: 40px; margin-bottom: 30px;">
<a href="{{route('install.index')}}" title="Instructions" class="btn button btn-info btn-lg buttn"><span style="font-family: lato; font-size: 21px;">Instructions</span></a>&nbsp;&nbsp;
<button type="button"  class="btn button btn-success btn-lg buttn" onclick="submitForm();" ><span style="font-family: lato; font-size: 21px;">Install</span></button>

</div>

		{!! Form::close() !!}

		<div class="loadingpage text-center" style="display: none;" id="after_display">
		 	
		 	<p>Installation in Progress. Dont refresh OR redirect the page. Please Wait...</p>

		 	<img width="200" src="{{url('images/loading-small.gif')}}">

		 	<code>
				<div id="prog" class="prog">
				    <div id="filler" class="filler"></div>
				</div>
				<p>Your website will be ready in next <span id="counter">120</span> seconds</p>
			</code>
		 </div>


	</div>

@stop

@section('footer_scripts')

<script src="{{ url('js/bootstrap-toggle.min.js') }}"></script>
 <script>
 	$().ready(function() {
 		$("#install_form").validate();
 	});
 	function submitForm() {
 		var host_name = $('#host_name').val();
 		if ( host_name == '' ) {
 			alert('Please enter host name');
 			$('#host_name').focus();
 			return false;
 		}
 		var database_name = $('#database_name').val();
 		if ( database_name == '' ) {
 			alert('Please enter database name');
 			$('#database_name').focus();
 			return false;
 		}
 		var user_name = $('#user_name').val();
 		if ( user_name == '' ) {
 			alert('Please enter user name');
 			$('#user_name').focus();
 			return false;
 		}
 		

 		$('#install_form').hide();
 		$('.buttons').hide();
 		$('#after_display').show();
 		$('#install_form').submit();

 		move();
 	}
 </script>

 <script>
function move() {
	var stepSize = 650;
	setTimeout((function() {
	    var filler 		= document.getElementById("filler"),
	    	prog 		= document.getElementById("prog"),
	        percentage 	= 0;
	    return function progress() {
	        filler.style.width = percentage + "%";
	        percentage +=1;
	        if (percentage <= 100) {
	        	if(percentage >= 70) {
	        		prog.style.border 		= "1px solid #4cae4c";
	        		filler.style.background = "#5cb85c";
	        	}
	            setTimeout(progress, stepSize);
	        }
	    }

	}()), stepSize);


	setInterval(function(){
		counter_val = parseInt(document.getElementById('counter').innerHTML);
		if(counter_val > 0)
			counter_val = counter_val - 1;
		document.getElementById('counter').innerHTML = counter_val;
	}, 1000);
}
</script>
@stop