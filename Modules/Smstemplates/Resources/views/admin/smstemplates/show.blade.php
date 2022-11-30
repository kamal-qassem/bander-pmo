@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $smstemplate->title }}
     @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.smstemplates.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'smstemplate_edit',
            ], 
            [
                'route' => 'admin.smstemplates.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'smstemplate_delete',
            ],
        ],
        'record' => $smstemplate,
        ] )</h3>
        

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('smstemplates::global.smstemplates.fields.title')</th>
                            <td field-key='title'>{{ $smstemplate->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('smstemplates::global.smstemplates.fields.key')</th>
                            <td field-key='key'>{{ $smstemplate->key }}</td>
                        </tr>
                        <tr>
                            <th>@lang('smstemplates::global.smstemplates.fields.content')</th>
                            <td field-key='content'>{!! clean($smstemplate->content) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.smstemplates.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


