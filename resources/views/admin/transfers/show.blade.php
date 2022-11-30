@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.transfers.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.transfers.fields.from')</th>
                            <td field-key='from'>{{ $transfer->from->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.transfers.fields.to')</th>
                            <td field-key='to'>{{ $transfer->to->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.transfers.fields.date')</th>
                            <td field-key='date'>{{ $transfer->date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.transfers.fields.amount')</th>
                            <td field-key='amount'>{{ digiCurrency($transfer->amount,$transfer->currency_id) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.transfers.fields.ref-no')</th>
                            <td field-key='ref_no'>{{ $transfer->ref_no }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.transfers.fields.payment-method')</th>
                            <td field-key='payment_method'>{{ $transfer->payment_method->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.transfers.fields.description')</th>
                            <td field-key='description'>{!! clean($transfer->description) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.transfers.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
