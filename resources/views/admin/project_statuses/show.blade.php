@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $project_status->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.project_statuses.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'project_status_edit',
            ], 
            [
                'route' => 'admin.project_statuses.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'project_status_delete',
            ],
        ],
        'record' => $project_status,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
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

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                 
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
 
 <li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   

</ul>

<!-- Tab panes -->
<div class="tab-content">

   <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

         <div class="pull-right">
            @can('project_status_edit')
                <a href="{{ route('admin.project_statuses.edit',[$project_status->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
        </div>   

       <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.project-statuses.fields.name')</th>
                            <td field-key='name'>{{ $project_status->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-statuses.fields.description')</th>
                            <td field-key='description'>{!! 
                                clean($project_status->description) !!}</td>
                        </tr>
                    </table>

    </div>
    


</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.project_statuses.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


