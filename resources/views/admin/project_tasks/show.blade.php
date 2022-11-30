@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project_task->project))

    <h3 class="page-title">@lang('global.project-tasks.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.project-tasks.fields.name')</th>
                            <td field-key='name'>{{ $project_task->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.description')</th>
                            <td field-key='description'>{!! clean($project_task->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.priority')</th>
                            <td field-key='priority'>{{ $project_task->task_priority->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.startdate')</th>
                            <td field-key='startdate'>{{ $project_task->startdate ? digiDate( $project_task->startdate ) : '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.duedate')</th>
                            <td field-key='duedate'>{{ $project_task->duedate ? digiDate( $project_task->duedate ) : '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.datefinished')</th>
                            <td field-key='datefinished'>{{ $project_task->datefinished ? digiDate( $project_task->datefinished ) : '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.status')</th>
                            <td field-key='status'>{{ $project_task->task_status->title }}</td>
                        </tr>
                      

                        @if( ! empty( $project_task->recurring_value ) )

                         <tr>
                            <th>@lang('global.project-tasks.fields.recurring-value')</th>
                            <td field-key='recurring_value'>{{ $project_task->recurring_value }}</td>
                        </tr>

                        <tr>
                            <th>@lang('global.project-tasks.fields.recurring-type')</th>
                            <td field-key='recurring_type'>{{ $project_task->recurring_type }}</td>
                        </tr>
                       
                        <tr>
                            <th>@lang('global.project-tasks.fields.cycles')</th>
                            <td field-key='cycles'>{{ $project_task->cycles }}</td>
                        </tr>
                        @endif

                        <tr>
                            <th>@lang('global.project-tasks.fields.total-cycles')</th>
                            <td field-key='total_cycles'>{{ $project_task->total_cycles }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.last-recurring-date')</th>
                            <td field-key='last_recurring_date'>{{ $project_task->last_recurring_date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.is-public')</th>
                            <td field-key='is_public'>{{ $project_task->is_public }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.billable')</th>
                            <td field-key='billable'>{{ $project_task->billable }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.billed')</th>
                            <td field-key='billed'>{{ $project_task->billed }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.project')</th>
                            <td field-key='project'>{{ $project_task->project->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.hourly-rate')</th>
                            <td field-key='hourly_rate'>{{ digiCurrency($project_task->hourly_rate) }}</td>
                        </tr>
                        
                    <tr>
                     <th>@lang('global.client-projects.fields.assigned-to')</th>
                     <td field-key='assigned_to'>
                     @foreach ($project_task->assigned_to as $singleAssignedTo)
                     <span class="label label-info label-many">{{ $singleAssignedTo->name }}</span>
                     @endforeach
                     </td>
                    </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.visible-to-client')</th>
                            <td field-key='visible_to_client'>{{ $project_task->visible_to_client }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.deadline-notified')</th>
                            <td field-key='deadline_notified'>{{ $project_task->deadline_notified }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.mile-stone')</th>
                            <td field-key='mile_stone'>{{ $project_task->mile_stone->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.attachments')</th>
                            <td field-key='attachments's> @foreach($project_task->getMedia('attachments') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tasks.fields.created-by')</th>
                            <td field-key='created_by'>{{ $project_task->created_by->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.project_tasks.index', $project_task->project_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop
