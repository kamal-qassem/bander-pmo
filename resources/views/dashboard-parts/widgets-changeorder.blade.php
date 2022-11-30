@extends('layouts.app')

@section('content')
   <h3 class="page-title">@lang('global.dashboard-widgets.title')</h3>
	
	{!! Form::open(['method' => 'POST', 'route' => ['admin.home.dashboard-widgets-changeorder-store', $role_id ],'class'=>'formvalidation', 'id' => 'frmAccount']) !!}
	

	<div class="panel panel-default">
	    <div class="panel-heading">	       
	        	@lang('global.app_edit')
	    </div>

	    <div class="col-xs-6">
	        <div class="form-group">
	        	@lang('global.dashboard-widgets.role') : <b>{{$role->title}}</b>
	        </div>
	    </div>
	    @can('contact_edit')
	    <div class="pull-right" style="margin-right: 15px;">   
	    <a href="{{ route('admin.home.dashboard-widgets-assign', $role_id) }}" class="btn btn-xs btn-primary"><i class="fa fa-list-alt"></i>&nbsp;@lang('global.dashboard-widgets.title')</a>
	    </div>
	    @endcan

	    <div class="panel-body table-responsive">
	        <div class="row">
	        	
	        	<ul id="sortable" style="margin-right: 15px; margin-left: -10px;">
				  @forelse( $widgets as $widget )
				  <li class="ui-state-default">{{$widget->title}} - ( @lang('global.dashboard-widgets.fields.type') : {{ucfirst($widget->type)}} @lang('global.dashboard-widgets.fields.columns') : {{ucfirst($widget->columns)}} )
				  	<input type="hidden" name="order[{{$widget->id}}]" value="{{$widget->display_order}}">
				  </li>
				  @empty
				  @endforelse
				</ul>
	        </div>	        
	    </div>
	</div>
	{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveAccount']) !!}
	{!! Form::close() !!}
@stop
@section('javascript') 
<script src="{{ url('js/cdn-js-files/jquery-ui.min.js') }}"></script>
@include('dashboard-parts.widgets-changeorder-scripts')
@endsection

