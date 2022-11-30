@extends('layouts.app')

@section('content')
    @include('quotes::admin.quotes.invoice.invoice-menu', ['invoice' => $quote])

    <h3 class="page-title">@lang('global.quotes-reminders.title')</h3>
    
    {!! Form::model($quotes_reminder, ['method' => 'PUT', 'route' => ['admin.quote_reminders.update', $quotes_reminder->quote_id, $quotes_reminder->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('quotes::admin.quotes.quotes_reminders.form-fields', compact('enum_isnotified', 'enum_notify_by_email', 'quotes', 'reminder_tos', 'created_bies', 'quote'))            
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