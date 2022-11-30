@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.invoice-payments.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.invoice-payments.fields.invoice')</th>
                            <td field-key='invoice'>{{ $invoice_payment->invoice->invoice_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-payments.fields.date')</th>
                            <td field-key='date'>{{ $invoice_payment->date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-payments.fields.account')</th>
                            <td field-key='account'>{{ $invoice_payment->account->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-payments.fields.amount')</th>
                            <td field-key='amount'>{{ $invoice_payment->amount }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.invoice-payments.fields.transaction-id')</th>
                            <td field-key='transaction_id'>{{ $invoice_payment->transaction_id }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.credit_note_payments.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
