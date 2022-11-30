@extends('layouts.app')

@section('content')
<div id="page-wrapper">
			<div class="container-fluid">
												
				<!-- /.row -->
				<div class="panel panel-custom col-lg-12">
					
					<div class="panel-body packages">
				
					{!! Form::open(array('url' => route('admin.site_themes.updatesettings', $record->slug), 'method' => 'POST', 
						'novalidate'=>'','name'=>'formSettings ', 'files'=>'true')) !!}
						<div class="row"> 
						<ul class="list-group">
						@if(count($settings_data))

						@foreach($settings_data as $key=>$value)
						<?php 
							$type_name = 'text';

							if($value->type == 'number' || $value->type == 'email' || $value->type=='password')
								$type_name = 'text';
							else
								$type_name = $value->type;
						?>
						
						@include(
									'admin.general_settings.sub-list-views.'.$type_name.'-type', 
									array('key'=>$key, 'value'=>$value)
								)
						  @endforeach

						  @else
							  <li class="list-group-item">{{ getPhrase('no_settings_available')}}</li>
						  @endif
						</ul>

						</div>



					@if(count($settings_data))
					<div class="form-group pull-right" >
					<button class="btn btn-success" ng-disabled='!formTopics.$valid'
					>{{ getPhrase('update') }}</button>
					</div>
					@endif

						
							{!! Form::close() !!}
					</div>

				</div>

			</div>

			<!-- /.container-fluid -->
		</div>


@endsection
 

@section('javascripts')
  
 
  

@stop
