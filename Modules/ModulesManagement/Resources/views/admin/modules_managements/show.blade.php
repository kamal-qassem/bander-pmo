@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{ $modules_management->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.modules_managements.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'modules_management_edit',
            ], 
            [
                'route' => 'admin.modules_managements.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'modules_management_delete',
            ],
        ],
        'record' => $modules_management,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('modulesmanagement::global.modules-management.fields.name')</th>
                            <td field-key='name'>{{ $modules_management->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('modulesmanagement::global.modules-management.fields.slug')</th>
                            <td field-key='slug'>{{ $modules_management->slug }}</td>
                        </tr>
                        <tr>
                            <th>@lang('modulesmanagement::global.modules-management.fields.type')</th>
                            <td field-key='type'>{{ $modules_management->type }}</td>
                        </tr>
                        <tr>
                            <th>@lang('modulesmanagement::global.modules-management.fields.enabled')</th>
                            <td field-key='enabled'>{{ $modules_management->enabled }}</td>
                        </tr>
                        <tr>
                            <th>@lang('modulesmanagement::global.modules-management.fields.description')</th>
                            <td field-key='description'>{!! clean($modules_management->description) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.modules_managements.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

@stop
