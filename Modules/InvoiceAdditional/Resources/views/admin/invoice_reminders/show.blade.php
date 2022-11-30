@extends('layouts.app')

@section('content')
    @include('admin.invoices.invoice.invoice-menu', ['invoice' => $invoice])
    
    <h3 class="page-title">@lang('global.invoice-reminders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.invoice-reminders.fields.description')</th>
                            <td field-key='description'>{!! clean($invoice_reminder->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-reminders.fields.date')</th>
                            <td field-key='date'>{{ $invoice_reminder->date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-reminders.fields.isnotified')</th>
                            <td field-key='isnotified'>{{ $invoice_reminder->isnotified }}</td>
                        </tr>

                        <tr>
                            <th>@lang('global.invoice-reminders.fields.invoice')</th>
                            <td field-key='invoice'><a href ="{{route('admin.invoices.show', $invoice->id)}}"> {{ $invoice_reminder->invoice->id ?? '' }}</a></td>
                        </tr>

                        <tr>
                            <th>@lang('global.invoice-reminders.fields.reminder-to')</th>
                            <td field-key='reminder_to'>{{ $invoice_reminder->reminder_to->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-reminders.fields.notify-by-email')</th>
                            <td field-key='notify_by_email'>{{ $invoice_reminder->notify_by_email }}</td>
                        </tr>

                       <tr>
                            <th>@lang('global.invoice-reminders.fields.created-by')</th>
                            <td field-key='created_by'>{{ $invoice_reminder->created_by->name ?? '' }}</td>
                        </tr>
                        
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.invoice_reminders.index', $invoice_reminder->invoice_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
