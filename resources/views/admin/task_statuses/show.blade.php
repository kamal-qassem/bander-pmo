@extends('layouts.app')

@section('content')
      <h3 class="page-title">{{ $task_status->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.task_statuses.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'task_status_edit',
            ], 
            [
                'route' => 'admin.task_statuses.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'task_status_delete',
            ],
        ],
        'record' => $task_status,
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
            'tasks_active' => '',
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
<li role="presentation" class="{{$tabs['tasks_active']}}"><a href="{{route('admin.task_statuses.show', [ 'status_id' => $task_status->id, 'list' => 'tasks' ])}}" title="@lang('global.tasks.title')">@lang('global.tasks.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

<div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

        <div class="pull-right">
            @can('task_status_edit')
                <a href="{{ route('admin.task_statuses.edit',[$task_status->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
         </div> 

     <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.task-statuses.fields.name')</th>
                            <td field-key='name'>{{ $task_status->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.task-statuses.fields.color')</th>
                             <?php
                                   $color =  $task_status->color;
                                    $color =   ucfirst( str_replace('-', ' ', $color) );   
                                ?>
                            <td field-key='color'>{{ $color }}</td>
                        </tr>
                    </table>

    </div>
    @if ( 'active' === $tabs['tasks_active'] )
        <div role="tabpanel" class="tab-pane {{$tabs['tasks_active']}}" id="tasks">
    @include('admin.tasks.records-display')
    @endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.task_statuses.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['tasks_active'] )
        @include('admin.tasks.records-display-scripts', [ 'type' => 'status', 'type_id' => $task_status->id ])
     @endif
@endsection


