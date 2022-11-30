@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))
    
    <h3 class="page-title">@lang('global.time-entries.title')</h3>
    
    {!! Form::model($time_entry, ['method' => 'PUT', 'route' => ['admin.time_entries.update', $time_entry->project_id, $time_entry->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
             @include('admin.time_entries.form-elements', compact('time_entry', 'project', 'projects', 'tasks', 'assignees'))            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    
     @include('admin.common.standard-ckeditor')

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.datetime').datetimepicker({
                format: "{{ getCurrentDateFormat('moment') }}",
                locale: "{{ App::getLocale() }}",
                sideBySide: true,
            });
            
        });
    </script>
            
@stop