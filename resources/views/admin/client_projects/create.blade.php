@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.client-projects.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.client_projects.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            @include('admin.client_projects.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}

    @include('admin.common.modal-loading-submit')
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
            
    <script>
        $("#selectbtn-assigned_to").click(function(){
            $("#selectall-assigned_to > option").prop("selected","selected");
            $("#selectall-assigned_to").trigger("change");
        });
        $("#deselectbtn-assigned_to").click(function(){
            $("#selectall-assigned_to > option").prop("selected","");
            $("#selectall-assigned_to").trigger("change");
        });
    </script>

    <script>
        $("#selectbtn-project_tabs").click(function(){
            $("#selectall-project_tabs > option").prop("selected","selected");
            $("#selectall-project_tabs").trigger("change");
        });
        $("#deselectbtn-project_tabs").click(function(){
            $("#selectall-project_tabs > option").prop("selected","");
            $("#selectall-project_tabs").trigger("change");
        });
    </script>
    <script type="text/javascript">
        $("#billing_type_id").change(function(){
            var billing_type_id = $(this).val();
            if( {{PROJECT_BILLING_TYPE_FIXED_PRICE}} == billing_type_id ) {
                $('#budget_div').show();
                $('#hourly_rate_div').hide();
                $('#project_rate_per_hour_div').hide();
            }
            if( {{PROJECT_BILLING_TYPE_PROJECT_HOURS}} == billing_type_id ) {
                $('#budget_div').hide();
                $('#hourly_rate_div').hide();
                $('#project_rate_per_hour_div').show();
            }
            if( {{PROJECT_BILLING_TYPE_TASK_HOURS}} == billing_type_id ) {
                $('#budget_div').hide();
                $('#hourly_rate_div').show();
                $('#project_rate_per_hour_div').hide();
            }
        });
    </script>

    @include('admin.common.modal-scripts')
@stop