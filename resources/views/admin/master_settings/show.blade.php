@extends('layouts.app')

@section('content')
     <h3 class="page-title">{{ $master_setting->module }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.master_settings.edit',
                'title' => trans('global.app_edit'),
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'master_setting_edit',
            ],
            [
                'route' => 'admin.master_settings.destroy',
                'title' => trans('global.app_delete'),
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'master_setting_delete',
            ],
        ],
        'record' => $master_setting,
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
                            <th>@lang('global.master-settings.fields.module')</th>
                            <td field-key='module'>{{ $master_setting->module }}</td>
                        </tr>
                          <tr>
                            <th>@lang('global.master-settings.fields.key')</th>
                            <td field-key='key'>{{ $master_setting->key }}</td>
                        </tr>
                        <tr>
                            <th>@lang('custom.settings.moduletype')</th>
                            <td field-key='moduletype'>{{ $master_setting->moduletype }}</td>
                        </tr>
                         <tr>
                            <th>@lang('custom.settings.status')</th>
                            <td field-key='status'>{{ $master_setting->status }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.master-settings.fields.description')</th>
                              <td field-key='description'>
                                {{    $master_setting->description  }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            {{-- <a href="{{ route('admin.master_settings.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div> --}}
@stop


