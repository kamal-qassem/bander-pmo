@extends('layouts.app')

@section('content')
    @include('admin.invoices.invoice.invoice-menu', ['invoice' => $invoice])
    
    <h3 class="page-title">@lang('global.invoice-reminders.title')</h3>
    
    {!! Form::model($invoice_reminder, ['method' => 'PUT', 'route' => ['admin.invoice_reminders.update', $invoice_reminder->invoice_id, $invoice_reminder->id], 'class' => 'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('invoiceadditional::admin.invoice_reminders.form-elements')            
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