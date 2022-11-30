@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.sms-gateways.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.sms-gateways.fields.name')</th>
                            <td field-key='name'>{{ $sms_gateway->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.sms-gateways.fields.key')</th>
                            <td field-key='key'>{{ $sms_gateway->key }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.sms-gateways.fields.description')</th>
                            <td field-key='description'>{!! clean($sms_gateway->description) !!}</td>
                        </tr>
                    </table>
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    
<li role="presentation" class="active"><a href="#send_sms" aria-controls="send_sms" role="tab" data-toggle="tab">Send SMS</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
<div role="tabpanel" class="tab-pane active" id="send_sms">
<table class="table table-bordered table-striped {{ count($send_sms) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.send-sms.fields.send-to')</th>
                        <th>@lang('global.send-sms.fields.message')</th>
                        <th>@lang('global.send-sms.fields.gateway')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
        </tr>
    </thead>

    <tbody>
        @if (count($send_sms) > 0)
            @foreach ($send_sms as $send_sm)
                <tr data-entry-id="{{ $send_sm->id }}">
                    <td field-key='send_to'>{{ $send_sm->send_to }}</td>
                                <td field-key='message'>{!! clean($send_sm->message) !!}</td>
                                <td field-key='gateway'>{{ $send_sm->gateway->name ?? '' }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.send_sms.restore', $send_sm->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.send_sms.perma_del', $send_sm->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td>
                                    @can('send_sm_view')
                                    <a href="{{ route('admin.send_sms.show',[$send_sm->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('send_sm_edit')
                                    <a href="{{ route('admin.send_sms.edit',[$send_sm->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('send_sm_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.send_sms.destroy', $send_sm->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.sms_gateways.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


