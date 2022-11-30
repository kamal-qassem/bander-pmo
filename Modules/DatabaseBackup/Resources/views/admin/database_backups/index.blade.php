@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">
        @if ( 'database' === $type )
            @lang('databasebackup::global.database-backup.database-backup')
        @elseif( 'files' === $type )
            @lang('databasebackup::global.database-backup.files-backup')
        @else
            @lang('databasebackup::global.database-backup.database-files-backup')
        @endif
        </h3>
    @can('database_backup_create')
    <p>
        <a href="{{ route('admin.database_backups.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('databasebackup::global.database-backup.take-backup')</a>

        <a href="{{ route('admin.databasebackups.index', 'database') }}" class="btn @if ( 'database' === $type ) active @else btn-info @endif"><i class="fa fa-database"></i>&nbsp;@lang('databasebackup::global.database-backup.database-backup')</a>

        <a href="{{ route('admin.databasebackups.index', 'files') }}" class="btn @if ( 'files' === $type ) active @else btn-warning @endif"><i class="fa fa-files-o"></i>&nbsp;@lang('databasebackup::global.database-backup.files-backup')</a>

        <a href="{{ route('admin.database_backups.index') }}" class="btn @if ( 'both' === $type ) active @else btn-danger @endif"><i class="fa fa-database"></i>&nbsp;<i class="fa fa-files-o"></i>&nbsp;@lang('databasebackup::global.database-backup.database-files-backup')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('database_backup_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('database_backup_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('databasebackup::global.database-backup.fields.name')</th>
                        <th>@lang('databasebackup::global.database-backup.fields.storage-location')</th>
                        <th>@lang('databasebackup::global.database-backup.fields.size')</th>
                        <th>@lang('databasebackup::global.database-backup.fields.created')</th>
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
        @can('database_backup_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.database_backups.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptions.buttons = [];
            window.dtDefaultOptions.ajax = '{!! route('admin.database_backups.index') !!}?show_deleted={{ request('show_deleted') }}&type={{$type}}';
            window.dtDefaultOptions.columns = [@can('database_backup_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'file_name', name: 'name'},
                {data: 'file_path', name: 'storage_location'},
                {data: 'file_size', name: 'size'},
                {data: 'last_modified', name: 'created'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection