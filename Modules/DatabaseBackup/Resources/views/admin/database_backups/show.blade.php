@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.database-backup.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('databasebackup::global.database-backup.fields.name')</th>
                            <td field-key='name'>{{ $database_backup->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('databasebackup::global.database-backup.fields.storage-location')</th>
                            <td field-key='storage_location'>{{ $database_backup->storage_location }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.database_backups.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


