@extends('layouts.app')

@section('content')
     <h3 class="page-title">{{ $project_billing_type->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.project_billing_types.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'project_billing_type_edit',
            ], 
            [
                'route' => 'admin.project_billing_types.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'project_billing_type_delete',
            ],
        ],
        'record' => $project_billing_type,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                
                </div>
            </div><!-- Nav tabs -->
    <?php
    $tabs = [
        'details_active' => 'active',
        'client_projects_active' => '',
    ];
    
    if ( ! empty( $list ) ) {
        foreach ($tabs as $key => $value) {
            $tabs[ $key ] = '';
            if ( substr( $key, 0, -7) == $list ) {
                $tabs[ $key ] = 'active';
            }
        }
    }
    ?>

<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="{{$tabs['details_active']}}"><a href="{{route('admin.project_billing_types.show',  $project_billing_type->id)}}">@lang('others.canvas.details')</a></li>    
@if( isPluginActive('client_project') )
<li role="presentation" class="{{$tabs['client_projects_active']}}"><a href="{{route('admin.project_billing_types.show', [ 'id' => $project_billing_type->id, 'list' => 'client_projects' ])}}">@lang('global.client-projects.title')</a></li>
@endif
</ul>

<!-- Tab panes -->
<div class="tab-content">
 
  <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

          <div class="pull-right">
            @can('project_billing_type_edit')
                <a href="{{ route('admin.project_billing_types.edit',[$project_billing_type->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
        </div> 

          <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.project-billing-types.fields.title')</th>
                            <td field-key='title'>{{ $project_billing_type->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-billing-types.fields.description')</th>
                            <td field-key='description'>{!! clean($project_billing_type->description) !!}</td>
                        </tr>
                    </table>  

    </div>

<div role="tabpanel" class="tab-pane {{$tabs['client_projects_active']}}" id="client_projects">
    @include('admin.client_projects.records-display')
</div>

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.project_billing_types.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 
    @if ( 'active' === $tabs['client_projects_active'] )
        @include('admin.client_projects.records-display-scripts', ['type' => 'project_billing_type', 'type_id' => $project_billing_type->id ])
    @endif
@endsection


