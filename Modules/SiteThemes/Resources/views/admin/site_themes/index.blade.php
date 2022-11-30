@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sitethemes::global.site-themes.title')</h3>
    @can('site_theme_create')
    <p>
        <a href="{{ route('admin.site_themes.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    {{--
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.site_themes.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')

            <span class="badge"> 
          
               {{\Modules\SiteThemes\Entities\SiteTheme::count()}}
                      </span>

            </a></li>
            @can('site_theme_delete')
            |
            <li><a href="{{ route('admin.site_themes.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

            <span class="badge"> 
           
               {{\Modules\SiteThemes\Entities\SiteTheme::onlyTrashed()->count()}}
           
            </span>

            </a></li>
            @endcan
        </ul>
    </p>
    --}}
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body">
            <table style="width:100%" class="table nowrap table-bordered table-striped ajaxTable">
                <thead>
                    <tr>
                        <th>@lang('sitethemes::global.site-themes.fields.title')</th>
                        <th>@lang('sitethemes::global.site-themes.fields.theme-title-key')</th>
                        <th>@lang('sitethemes::global.site-themes.fields.is-active')</th>
                        <th>@lang('sitethemes::global.site-themes.fields.settings')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>

        $(document).ready(function () {
            window.dtDefaultOptions.ajax = '{!! route('admin.site_themes.index') !!}';
            window.dtDefaultOptions.buttons = [];
            window.dtDefaultOptions.columns = [
                {data: 'title', name: 'title'},
                {data: 'theme_title_key', name: 'theme_title_key'},
                {data: 'is_active', name: 'is_active'},
                {data: 'settings_data', name: 'settings_data'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection