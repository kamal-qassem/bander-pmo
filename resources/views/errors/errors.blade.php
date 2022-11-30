@if(! empty( $errors ) && count($errors) > 0 )
 <div class="alert alert-danger alert-dismissible">
 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<ul class="list-unstyled">
		@foreach($errors->all() as $error)
		<li>{{$error}}</li>
		@endforeach
	</ul>
</div>
@endif

@if (Session::has('message'))
<?php
$type = 'danger';
if(Session::get('status', 'info') == 'success')
	$type = 'success';
?> 
<div class="alert alert-<?php echo $type;?> alert-dismissible">
 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<ul class="list-unstyled">		
		<li>{{{ Session::get('message') }}}</li>
	</ul>
</div>
@endif
 
 