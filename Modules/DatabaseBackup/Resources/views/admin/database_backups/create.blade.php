@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.database-backup.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.database_backups.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('databasebackup::global.database-backup.take-backup')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', trans('databasebackup::global.database-backup.fields.type').'*', ['class' => 'control-label']) !!}
                    
                    <?php
                    $backuptypes = array(
                        'database' => trans('databasebackup::global.database-backup.database'),
                        'files' => trans('databasebackup::global.database-backup.files'),
                        'both' => trans('databasebackup::global.database-backup.both'),
                    );
                    ?>
                    {!! Form::select('name', $backuptypes, old('name'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
            </div>
           
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

