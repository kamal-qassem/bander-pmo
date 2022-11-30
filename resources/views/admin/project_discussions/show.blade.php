@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))
    
    <h3 class="page-title">@lang('global.project-discussions.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.project-discussions.fields.subject')</th>
                            <td field-key='subject'>{{ $topic->subject }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-discussions.fields.description')</th>
                            <td field-key='description'>{!! clean($topic->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-discussions.fields.visible-to-customer')</th>
                            <td field-key='show_to_customer'>{{ ucfirst( $topic->show_to_customer ) }}</td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.project-discussions.fields.project')</th>
                            <td field-key='project'>{{ $topic->project->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-discussions.fields.last-activity')</th>
                            <td field-key='color'>{{ ($topic->last_activity) ? digiDate( $topic->last_activity, true ) : '-' }}</td>
                        </tr>
                        
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.project_discussions.index', $project->id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
