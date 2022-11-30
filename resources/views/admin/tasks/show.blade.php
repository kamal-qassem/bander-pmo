@extends('layouts.app')

@section('content')
       <h3 class="page-title">{{ $task->name }}
        
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
   
        <div class="panel-body table-responsive">
            @if( Gate::allows('task_edit') || Gate::allows('task_delete'))
            <div class="pull-right">   
                @if( Gate::allows('task_edit') )
                    <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('task_delete'))
                    @include('admin.common.delete-link', ['record' => $task, 'routeName' => 'admin.tasks.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.tasks.fields.name')</th>
                            <td field-key='name'>{{ $task->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.tasks.fields.description')</th>
                            <td field-key='description'>{!! clean($task->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.tasks.fields.status')</th>
                            <td field-key='status'>{{ $task->status->name ?? '' }}</td>
                        </tr>
                     
                        <tr>
                            <th>@lang('global.tasks.fields.attachment')</th>
                            <td field-key='attachment'>@if($task->attachment)<a href="{{ route('admin.home.media-file-download', ['model' => 'Task', 'field' => 'attachment', 'record_id' => $task->id]) }}">@lang('others.download-file')</a>@endif</td>
                        </tr>
                        <tr>
                            <th>@lang('global.tasks.fields.start-date')</th>
                            <td field-key='due_date'>{{ $task->start_date ? digiDate($task->start_date) : '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.tasks.fields.due-date')</th>
                            <td field-key='due_date'>{{ $task->due_date ? digiDate($task->due_date) : '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.tasks.fields.user')</th>
                            <td field-key='user'>{{ $task->user->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.tasks.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop
