@extends('layouts.app')

@section('content')
   <h3 class="page-title">@lang('global.dashboard-widgets.title')</h3>
	@if( $widget )
	{!! Form::model($widget, ['method' => 'POST', 'route' => ['admin.home.dashboard-widgets-store', $widget->id],'class'=>'formvalidation', 'id' => 'frmAccount']) !!}
	@else
	{!! Form::open(['method' => 'POST', 'route' => ['admin.home.dashboard-widgets-store'],'class'=>'formvalidation', 'id' => 'frmAccount']) !!}
	@endif

	<div class="panel panel-default">
	    <div class="panel-heading">
	        @if( $widget )
	        	@lang('global.app_edit')
	        @else
	        	@lang('global.app_create')
	        @endif
	    </div>


	    
	    <div class="panel-body">
	        <div class="row">
	            <div class="col-xs-{{COLUMNS}}">
	            <div class="form-group">
	                {!! Form::label('title', trans('global.dashboard-widgets.fields.title').'*', ['class' => 'control-label form-label']) !!}
	                <div class="form-line">
	                {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Name', 'required' => '']) !!}
	                <p class="help-block"></p>
	                @if($errors->has('title'))
	                    <p class="help-block">
	                        {{ $errors->first('title') }}
	                    </p>
	                @endif
	            </div>
	            </div>
	            </div>

	             <div class="col-xs-{{COLUMNS}}">
	            <div class="form-group">
	                {!! Form::label('slug', trans('global.dashboard-widgets.fields.slug').'', ['class' => 'control-label']) !!}
	                {!! Form::text('slug', old('slug'), ['class' => 'form-control', 'placeholder' => trans('global.dashboard-widgets.fields.slug')]) !!}
	                <p class="help-block"></p>
	                @if($errors->has('slug'))
	                    <p class="help-block">
	                        {{ $errors->first('slug') }}
	                    </p>
	                @endif
	            </div>
	            </div>

	            <div class="col-xs-{{COLUMNS}}">
	            <div class="form-group">
	                {!! Form::label('status', trans('global.dashboard-widgets.fields.status').'', ['class' => 'control-label form-label']) !!}
	                <div class="form-line">
	                
	                <?php
	                $statuses = [
	                	'active' => trans('global.dashboard-widgets.active'),
	                	'inactive' => trans('global.dashboard-widgets.inactive'),
	                ];
	                ?>
	                {!! Form::select('status', $statuses, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}
	                
	                <p class="help-block"></p>
	                @if($errors->has('status'))
	                    <p class="help-block">
	                        {{ $errors->first('status') }}
	                    </p>
	                @endif
	            </div>
	            </div>
	            </div>
	     
	       
	            <div class="col-xs-{{COLUMNS}}">
	            <div class="form-group">
	                {!! Form::label('type', trans('global.dashboard-widgets.fields.type').'', ['class' => 'control-label form-label']) !!}
	                <div class="form-line">
	                <?php
	                $statuses = [
	                	'numbers' => trans('global.dashboard-widgets.numbers'),
	                	'chart' => trans('global.dashboard-widgets.chart'),
	                	'list' => trans('global.dashboard-widgets.list'),
	                	'view' => trans('global.app_view'),
	                ];
	                ?>
	                {!! Form::select('type', $statuses, old('type'), ['class' => 'form-control select2', 'required' => '']) !!}
	                <p class="help-block"></p>
	                @if($errors->has('type'))
	                    <p class="help-block">
	                        {{ $errors->first('type') }}
	                    </p>
	                @endif
	            </div>
	            </div>
	            </div>

	            <div class="col-xs-{{COLUMNS}}">
	            <div class="form-group">
	                {!! Form::label('columns', trans('global.dashboard-widgets.fields.columns').'', ['class' => 'control-label form-label']) !!}
	                <div class="form-line">
	                <?php
	                $columns = [
	                	2 => 2, 
	                	4 => 4, 
	                	6 => 6, 
	                	8 => 8, 
	                	10 => 10, 
	                	12 => 12,
	                ];
	                ?>
	                {!! Form::select('columns', $columns, old('columns'), ['class' => 'form-control select2', 'required' => '']) !!}
	                <p class="help-block"></p>
	                @if($errors->has('columns'))
	                    <p class="help-block">
	                        {{ $errors->first('columns') }}
	                    </p>
	                @endif
	            </div>
	            </div>
	            </div>


	           
	      
	            
	        </div>
	        
	    </div>
	</div>
	<?php
	if ( empty( $is_ajax ) ) {
	    $is_ajax = 'no';
	}
	?>
	<input type="hidden" name="is_ajax" value="{{$is_ajax}}">
	<?php
	if ( empty( $selectedid ) ) {
	    $selectedid = 'account_id';
	}
	?>
	<input type="hidden" name="selectedid" value="{{$selectedid}}">
	{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveAccount']) !!}
	{!! Form::close() !!}

	@if ( 'yes' === $is_ajax )
	<script type="text/javascript">
	  $(".saveAccount").click(function(e){
	            e.preventDefault();

	            $.ajax({
	                url: "{{route('admin.accounts.store')}}",
	                type:'POST',
	                data: $( '#frmAccount' ).serializeArray(),
	                success: function(data) {
	                    if($.isEmptyObject(data.error)){
	                        notifyMe('success', data.success);
	                        $('#loadingModal').modal('hide');

	                        var value = data.record.id;
	                        var title = data.record.name;
	                        $('#' + data.record.selectedid).append('<option value="'+value+'" selected="selected">'+title+'</option>');                        
	                    }else{
	                        printErrorMsg(data.error);
	                    }
	                }
	            });
	  });

	  function printErrorMsg (msg) {
	      $("#message_bag").find("ul").html('');
	      $("#message_bag").css('display','block');
	      $("#message_bag").addClass('alert-danger');
	      $.each( msg, function( key, value ) {
	          $("#message_bag").find("ul").append('<li>'+value+'</li>');
	      });
	  }
	</script>
	@endif
@stop

