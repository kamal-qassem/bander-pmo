@extends('layouts.app')

@section('content')
    @include('proposals::admin.proposals.invoice.invoice-menu', ['invoice' => $proposal])
    
    <h3 class="page-title">@lang('global.proposals-reminders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.description')</th>
                            <td field-key='description'>{!! clean($proposals_reminder->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.date')</th>
                            <td field-key='date'>{{ $proposals_reminder->date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.isnotified')</th>
                            <td field-key='isnotified'>{{ $proposals_reminder->isnotified }}</td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.proposal')</th>
                            <td field-key='proposal'><a href ="{{route('admin.proposals.show', $proposal->id)}}"> {{ $proposals_reminder->proposal->id ?? '' }}</a></td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.reminder-to')</th>
                            <td field-key='reminder_to'>{{ $proposals_reminder->reminder_to->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.notify-by-email')</th>
                            <td field-key='notify_by_email'>{{ $proposals_reminder->notify_by_email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.proposals-reminders.fields.created-by')</th>
                            <td field-key='created_by'>{{ $proposals_reminder->created_by->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.proposal_reminders.index', $proposals_reminder->proposal_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
