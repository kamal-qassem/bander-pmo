@extends('layouts.app')

@section('content')
    @include('contracts::admin.contracts.invoice.invoice-menu', ['invoice' => $contract])
    
    <h3 class="page-title">@lang('global.contracts-reminders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.description')</th>
                            <td field-key='description'>{!! clean($contracts_reminder->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.date')</th>
                            <td field-key='date'>{{ $contracts_reminder->date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.isnotified')</th>
                            <td field-key='isnotified'>{{ $contracts_reminder->isnotified }}</td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.contract')</th>
                            <td field-key='contract'><a href ="{{route('admin.contracts.show', $contract->id)}}"> {{ $contracts_reminder->contract->id ?? '' }}</a></td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.reminder-to')</th>
                            <td field-key='reminder_to'>{{ $contracts_reminder->reminder_to->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.notify-by-email')</th>
                            <td field-key='notify_by_email'>{{ $contracts_reminder->notify_by_email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contracts-reminders.fields.created-by')</th>
                            <td field-key='created_by'>{{ $contracts_reminder->created_by->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.contract_reminders.index', $contracts_reminder->contract_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
