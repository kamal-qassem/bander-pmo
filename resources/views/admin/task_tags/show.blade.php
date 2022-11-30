@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.task-tags.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                   
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
 
 <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   
<li role="presentation" class=""><a href="#tasks" aria-controls="tasks" role="tab" data-toggle="tab">@lang('global.tasks.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

 <div role="tabpanel" class="tab-pane active" id="details">

       <div class="pull-right">
            @can('task_tag_edit')
                <a href="{{ route('admin.task_tags.edit',[$task_tag->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
         </div> 

       <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.task-tags.fields.name')</th>
                            <td field-key='name'>{{ $task_tag->name }}</td>
                        </tr>
                    </table>

    </div>   
    
<div role="tabpanel" class="tab-pane" id="tasks">
<table class="table table-bordered table-striped {{ count($tasks) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.tasks.fields.name')</th>
                        <th>@lang('global.tasks.fields.description')</th>
                        <th>@lang('global.tasks.fields.status')</th>
                        <th>@lang('global.tasks.fields.tag')</th>
                        <th>@lang('global.tasks.fields.attachment')</th>
                        <th>@lang('global.tasks.fields.start-date')</th>
                        <th>@lang('global.tasks.fields.due-date')</th>
                        <th>@lang('global.tasks.fields.user')</th>
                                                <th>&nbsp;</th>

        </tr>
    </thead>

    <tbody>
        @if (count($tasks) > 0)
            @foreach ($tasks as $task)
                <tr data-entry-id="{{ $task->id }}">
                    <td field-key='name'>{{ $task->name }}</td>
                                <td field-key='description'>{!! clean($task->description) !!}</td>
                                <td field-key='status'>{{ $task->status->name ?? '' }}</td>
                                <td field-key='tag'>
                                    @foreach ($task->tag as $singleTag)
                                        <span class="label label-info label-many">{{ $singleTag->name }}</span>
                                    @endforeach
                                </td>
                                <td field-key='attachment'>@if($task->attachment)<a href="{{ asset(env('UPLOAD_PATH').'/' . $task->attachment) }}" target="_blank">View file</a>@endif</td>
                                <td field-key='due_date'>{{ digiDate($task->start_date) }}</td>
                                <td field-key='due_date'>{{ digiDate($task->due_date) }}</td>
                                <td field-key='user'>{{ $task->user->name ?? '' }}</td>
                            <td>
                                @can('task_view')
                                <a href="{{ route('admin.tasks.show',[$task->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                @endcan
                                @can('task_edit')
                                <a href="{{ route('admin.tasks.edit',[$task->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                @endcan
                                @can('task_delete')
                                {!! Form::open(array(
                                'style' => 'display: inline-block;',
                                'method' => 'DELETE',
                                'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                'route' => ['admin.tasks.destroy', $task->id])) !!}
                                {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!}
                                @endcan
                            </td>

                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.task_tags.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


