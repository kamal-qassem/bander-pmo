@extends('layouts.app')

@section('content')
   <h3 class="page-title" style="margin-bottom: 12px !important;">@lang('global.dashboard-widgets.title')</h3>
	
	{!! Form::open(['method' => 'POST', 'route' => ['admin.home.dashboard-widgets-assign-store', $role_id ],'class'=>'formvalidation', 'id' => 'frmAccount']) !!}
	
	<div class="panel panel-default">
	    <div class="panel-heading">	       
	        	@lang('global.app_edit')
	    </div>
	    <div class="panel-body table-responsive">
	        <div class="row">
	        	
	        	<div class="col-xs-6">
			        <div class="form-group">
			        	@lang('global.dashboard-widgets.role') : <b>{{$role->title}}</b>
			        </div>
			    </div>
			    @can('contact_edit')
			    <div class="pull-right" style="margin-right: 15px;">   
			    <a href="{{ route('admin.home.dashboard-widgets-changeorder', $role->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_change_order')}}</a>
			    </div>
			    @endcan
	        	<div class="col-xs-12">
			       
			    	<table class="table table-bordered table-striped datatablewidgets">
		                <thead>
		                    <tr>
		                        <th style="text-align:center;"><input type="checkbox" id="select-all-widgets"></th>
		                        <th>@lang('global.dashboard-widgets.fields.title')</th>
		                        <th>@lang('global.dashboard-widgets.fields.type')</th>
		                        <th>@lang('global.dashboard-widgets.fields.order')</th>
		                        <th>@lang('global.dashboard-widgets.fields.columns')</th>                        
		                    </tr>
		                </thead>
		                <?php
			            
			            $widgets_all = \App\DashBoard::orderBy('title')->get();
			            $assigned = $role->role_widgets()->pluck('dash_board_id')->toArray();
			            $display_orders = $role->role_widgets()->pluck('dash_board_id', 'display_order')->toArray();
			            $display_columns = $role->role_widgets()->pluck('dash_board_id', 'display_columns')->toArray();
			            
		                $columns = [
		                	2 => 2, 
		                	4 => 4, 
		                	6 => 6, 
		                	8 => 8, 
		                	10 => 10, 
		                	12 => 12,
		                ];
		                ?>
			            @forelse( $widgets_all as $widget )
			            	<tr>
		                        <td style="text-align:center;">
		                        	<input type="checkbox" class="select-checkbox" name="widgets[{{$widget->id}}]" @if( in_array( $widget->id, $assigned) ) checked @endif/>
		                        </td>
		                        <td>{{$widget->title}}</td>
		                        <td>{{$widget->type}}</td>
		                        <td>
		                        	<?php
		                        	

		                        	$order = $widget->get_widget_field( $role->id, $widget->id);
		                        	$column = $widget->get_widget_field( $role->id, $widget->id, 'display_columns');
		                        	?>
		                        	{!! Form::number('order['.$widget->id.']', old('order', $order), ['class' => 'form-control', 'placeholder' => trans('global.dashboard-widgets.fields.order'), 'min' => 0]) !!}</td>
		                        <td>{!! Form::select('columns['.$widget->id.']', $columns, old('columns', $column), ['class' => 'form-control', 'required' => '']) !!}</td>
		                        
		                    </tr>
			            @empty
			            @endforelse
		            </table>
			    </div>

	        </div>	        
	    </div>
	</div>
	{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveAccount']) !!}
	{!! Form::close() !!}
	
@stop
@section('javascript') 
@include('dashboard-parts.widgets-assign-scripts') 
@endsection
