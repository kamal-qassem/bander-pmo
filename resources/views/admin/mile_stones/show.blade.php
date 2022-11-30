@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))
    
    <h3 class="page-title">@lang('global.mile-stones.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
           
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.mile-stones.fields.name')</th>
                            <td field-key='name'>{{ $mile_stone->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.mile-stones.fields.description')</th>
                            <td field-key='description'>{!! clean($mile_stone->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.mile-stones.fields.description-visible-to-customer')</th>
                            <td field-key='description_visible_to_customer'>{{ $mile_stone->description_visible_to_customer }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.mile-stones.fields.due-date')</th>
                            <td field-key='due_date'>{{ digiDate($mile_stone->due_date) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.mile-stones.fields.project')</th>
                            <td field-key='project'>{{ $mile_stone->project->title ?? '' }}</td>
                        </tr>
                     
                        <tr>
                            <th>@lang('global.mile-stones.fields.milestone-order')</th>
                            <td field-key='milestone_order'>{{ $mile_stone->milestone_order }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.mile_stones.index', $project->id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
