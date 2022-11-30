@extends('layouts.app')

@section('content')
    @include('contracts::admin.contracts.invoice.invoice-menu', ['invoice' => $contract])

    <h3 class="page-title">@lang('global.contracts-reminders.title')</h3>
    
    {!! Form::model($contracts_reminder, ['method' => 'PUT', 'route' => ['admin.contract_reminders.update', $contracts_reminder->contract_id, $contracts_reminder->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('contracts::admin.contracts.contracts_reminders.form-fields', compact('enum_isnotified', 'enum_notify_by_email', 'contracts', 'reminder_tos', 'created_bies', 'contract'))            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
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
                minDate: 'now'
            });
            
        });
    </script>
            
@stop