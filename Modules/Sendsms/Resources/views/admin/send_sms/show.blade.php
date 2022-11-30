@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sendsms::global.send-sms.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('sendsms::global.send-sms.fields.send-to')</th>
                            <td field-key='send_to'>{{ $send_sm->send_to }}</td>
                        </tr>
                        <tr>
                            <th>@lang('sendsms::global.send-sms.fields.message')</th>
                            <td field-key='message'>{!! clean($send_sm->message) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('sendsms::global.send-sms.fields.gateway')</th>
                            <td field-key='gateway'>{{ $send_sm->gateway->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.send_sms.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


