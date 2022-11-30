@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.time-entries.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.time-entries.fields.project')</th>
                            <td field-key='project'>{{ $time_entry->project->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.time-entries.fields.start-date')</th>
                            <td field-key='start_date'>{{ digiDate( $time_entry->start_date,true )}}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.time-entries.fields.end-date')</th>
                            <td field-key='end_date'>{{ digiDate( $time_entry->end_date , true ) }}</td>
                        </tr>

                        <tr>
                            <th>@lang('global.project-tasks.title')</th>
                            <td field-key='project_tasks'>{{ $time_entry->task->name }}</td>
                        </tr>

                        <tr>
                            <th>@lang('global.time-entries.fields.description')</th>
                            <td field-key='description'>{!! clean($time_entry->description) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.time_entries.index', $time_entry->project_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
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
                format: "{{ config('app.datetime_format_moment') }}",
                locale: "{{ App::getLocale() }}",
                sideBySide: true,
            });
            
        });
    </script>
            
@stop
