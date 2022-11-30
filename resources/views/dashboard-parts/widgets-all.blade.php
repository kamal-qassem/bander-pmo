@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.dashboard-widgets.title')</h3>
    @can('widget_create')
    <p>
        <a href="{{ route('admin.home.dashboard-widgets-add') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan
    @can('widget_assign')
    <p>
        <div class="btn-group">
              @if( config('app.debug') )
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-check" aria-hidden="true"></i>&nbsp;@lang('global.dashboard-widgets.assign-widgets')&nbsp;<span class="caret"></span>
              </button>
              @endif
              <?php
              $roles = \App\Role::where('type', 'role')->where('status', 'active')->get();
              ?>
              <ul class="dropdown-menu">               
                @forelse( $roles as $role )
                    <li><a href="{{ route('admin.home.dashboard-widgets-assign', $role->id) }}" class="btn btn-success d-widgets"><i class="fa fa-plus"></i>&nbsp;{{$role->title}}</a></li>
                @empty
                @endforelse        
              </ul>
        </div> 
        <a href="{{ route('admin.home.dashboard-widgets') }}" class="btn btn-success"><i class="fa fa-list"></i>&nbsp;@lang('global.app_assigned_to')</a>
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable">
                <thead>
                    <tr>
                        <th>@lang('global.dashboard-widgets.fields.title')</th>
                        <th>@lang('global.dashboard-widgets.fields.status')</th>
                        <th>@lang('global.dashboard-widgets.fields.type')</th>                         
                        <th>&nbsp;</th>
                        
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
@include('dashboard-parts.widgets-all-scripts')
@endsection