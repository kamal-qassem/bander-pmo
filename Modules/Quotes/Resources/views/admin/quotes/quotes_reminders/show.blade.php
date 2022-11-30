@extends('layouts.app')

@section('content')
    @include('quotes::admin.quotes.invoice.invoice-menu', ['invoice' => $quote])
    
    <h3 class="page-title">@lang('global.quotes-reminders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.description')</th>
                            <td field-key='description'>{!! clean($quotes_reminder->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.date')</th>
                            <td field-key='date'>{{ $quotes_reminder->date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.isnotified')</th>
                            <td field-key='isnotified'>{{ $quotes_reminder->isnotified }}</td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.quote')</th>
                            <td field-key='quote'><a href ="{{route('admin.quotes.show', $quote->id)}}"> {{ $quotes_reminder->quote->id ?? '' }}</a></td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.reminder-to')</th>
                            <td field-key='reminder_to'>{{ $quotes_reminder->reminder_to->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.notify-by-email')</th>
                            <td field-key='notify_by_email'>{{ $quotes_reminder->notify_by_email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.quotes-reminders.fields.created-by')</th>
                            <td field-key='created_by'>{{ $quotes_reminder->created_by->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.quote_reminders.index', $quotes_reminder->quote_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
