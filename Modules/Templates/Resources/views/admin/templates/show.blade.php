@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $template->subject }}
    @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.templates.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'template_edit',
            ], 
            [
                'route' => 'admin.templates.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'template_delete',
            ],
        ],
        'record' => $template,
        ] )
    </h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('templates::global.templates.fields.key')</th>
                            <td field-key='key'>{{ $template->key }}</td>
                        </tr>
                        <tr>
                            <th>@lang('templates::global.templates.fields.type')</th>
                            <td field-key='type'>{{ $template->type }}</td>
                        </tr>
                        <tr>
                            <th>@lang('templates::global.templates.fields.subject')</th>
                            <td field-key='subject'>{{ $template->subject }}</td>
                        </tr>
                        <tr>
                            <th>@lang('templates::global.templates.fields.from-email')</th>
                            <td field-key='from_email'>{{ $template->from_email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('templates::global.templates.fields.from-name')</th>
                            <td field-key='from_name'>{{ $template->from_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('templates::global.templates.fields.content')</th>
                            <td field-key='content'>{!! clean($template->content) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.templates.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    @include('admin.common.standard-ckeditor')
@stop
