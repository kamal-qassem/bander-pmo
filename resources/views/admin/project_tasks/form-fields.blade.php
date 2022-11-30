<div class="row">
    <div class="col-xs-{{COLUMNS}}">
        <div class="form-group">
        {!! Form::label('name', trans('global.project-tasks.fields.name').'*', ['class' => 'control-label']) !!}
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('name'))
            <p class="help-block">
                {{ $errors->first('name') }}
            </p>
        @endif
        </div>
    </div>


    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('priority', trans('global.project-tasks.fields.priority').'*', ['class' => 'control-label']) !!}
       {!! Form::select('priority', $priorities ,old('priority'), ['class' => 'form-control select2', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('priority'))
            <p class="help-block">
                {{ $errors->first('priority') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('startdate', trans('global.project-tasks.fields.startdate').'*', ['class' => 'control-label']) !!}
        <?php
        $startdate = ! empty($project_task->startdate) ? digiDate( $project_task->startdate ) : '';
        ?>
        {!! Form::text('startdate', old('startdate', $startdate), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('startdate'))
            <p class="help-block">
                {{ $errors->first('startdate') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('duedate', trans('global.project-tasks.fields.duedate').'', ['class' => 'control-label']) !!}
        <?php
        $duedate = ! empty($project_task->duedate) ? digiDate( $project_task->duedate ) : '';
        ?>
        {!! Form::text('duedate', old('duedate', $duedate), ['class' => 'form-control date', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('duedate'))
            <p class="help-block">
                {{ $errors->first('duedate') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
         <?php
        $datefinished = ! empty($project_task->datefinished) ? digiDate( $project_task->datefinished ) : '';
        ?>
        {!! Form::label('datefinished', trans('global.project-tasks.fields.datefinished').'', ['class' => 'control-label']) !!}
        {!! Form::text('datefinished', old('datefinished',$datefinished), ['class' => 'form-control date', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('datefinished'))
            <p class="help-block">
                {{ $errors->first('datefinished') }}
            </p>
        @endif
    </div>


    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('status', trans('global.project-tasks.fields.status').'', ['class' => 'control-label']) !!}
        {!! Form::select('status', $statuses, old('status'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('status'))
            <p class="help-block">
                {{ $errors->first('status') }}
            </p>
        @endif
    </div>

    

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('billable', trans('global.project-tasks.fields.billable').'', ['class' => 'control-label']) !!}
        {!! Form::select('billable', $enum_billable, old('billable'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('billable'))
            <p class="help-block">
                {{ $errors->first('billable') }}
            </p>
        @endif
    </div>

    <!--<div class="col-xs-{{COLUMNS}}">-->
    <!--    {!! Form::label('recurring_id', trans('global.project-tasks.fields.recurring').'', ['class' => 'control-label']) !!}-->
    <!--    {!! Form::select('recurring_id', $recurrings, old('recurring_id'), ['class' => 'form-control select2', 'id' => 'recurring_period_id']) !!}-->
    <!--    <p class="help-block"></p>-->
    <!--    @if($errors->has('recurring_id'))-->
    <!--        <p class="help-block">-->
    <!--            {{ $errors->first('recurring_id') }}-->
    <!--        </p>-->
    <!--    @endif-->
    <!--</div>-->

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('recurring_value', trans('global.project-tasks.fields.recurring-value').'', ['class' => 'control-label']) !!}
        {!! Form::number('recurring_value', old('recurring_value'), ['class' => 'form-control', 'placeholder' => '','min'=>'0']) !!}
        <p class="help-block"></p>
        @if($errors->has('recurring_value'))
            <p class="help-block">
                {{ $errors->first('recurring_value') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('recurring_type', trans('global.project-tasks.fields.recurring-type').'', ['class' => 'control-label']) !!}
        {!! Form::select('recurring_type', $enum_recurring_type, old('recurring_type'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('recurring_type'))
            <p class="help-block">
                {{ $errors->first('recurring_type') }}
            </p>
        @endif
    </div>

    

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('cycles', trans('global.project-tasks.fields.cycles').'', ['class' => 'control-label']) !!}
        {!! Form::number('cycles', old('cycles'), ['class' => 'form-control', 'placeholder' => '','min'=>'0']) !!}
        <p class="help-block"></p>
        @if($errors->has('cycles'))
            <p class="help-block">
                {{ $errors->first('cycles') }}
            </p>
        @endif
    </div>
    

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('billed', trans('global.project-tasks.fields.billed').'', ['class' => 'control-label']) !!}
        {!! Form::select('billed', $enum_billed, old('billed'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('billed'))
            <p class="help-block">
                {{ $errors->first('billed') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('hourly_rate', trans('global.project-tasks.fields.hourly-rate').'', ['class' => 'control-label']) !!}
        {!! Form::number('hourly_rate', old('hourly_rate'), ['class' => 'form-control', 'placeholder' => '','min'=>'0']) !!}
        <p class="help-block"></p>
        @if($errors->has('hourly_rate'))
            <p class="help-block">
                {{ $errors->first('hourly_rate') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('visible_to_client', trans('global.project-tasks.fields.visible-to-client').'', ['class' => 'control-label']) !!}
        {!! Form::select('visible_to_client', $enum_visible_to_client, old('visible_to_client'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('visible_to_client'))
            <p class="help-block">
                {{ $errors->first('visible_to_client') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('milestone', trans('global.project-tasks.fields.milestone').'', ['class' => 'control-label']) !!}
        {!! Form::select('milestone', $mile_stones, old('milestone'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('milestone'))
            <p class="help-block">
                {{ $errors->first('milestone') }}
            </p>
        @endif
    </div>
</div>
    <div class="row">
    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('assigned_to', trans('global.client-projects.fields.assigned-to').'', ['class' => 'control-label']) !!}
        <button type="button" class="btn btn-primary btn-xs" id="selectbtn-assigned_to">
            {{ trans('global.app_select_all') }}
        </button>
        <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-assigned_to">
            {{ trans('global.app_deselect_all') }}
        </button>
        {!! Form::select('assigned_to[]', $users, old('assigned_to'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-assigned_to' ]) !!}
        <p class="help-block"></p>
        @if($errors->has('assigned_to'))
            <p class="help-block">
                {{ $errors->first('assigned_to') }}
            </p>
        @endif
    </div>
    </div>

    <div class="col-xs-6">
        {!! Form::label('attachments', trans('global.project-tasks.fields.attachments').'', ['class' => 'control-label']) !!}
        {!! Form::file('attachments[]', [
            'multiple',
            'class' => 'form-control file-upload',
            'data-url' => route('admin.media.upload'),
            'data-bucket' => 'attachments',
            'data-filekey' => 'attachments',
            'data-accept' => FILE_TYPES_GENERAL,
            ]) !!}
        <p class="help-block">{{trans('others.global_file_types_general')}}</p>
        <div class="photo-block">
            <div class="progress-bar">&nbsp;</div>
            <div class="files-list">
                @if ( ! empty($project_task) )
                    @foreach($project_task->getMedia('attachments') as $media)
                        <p class="form-group">
                            <a href="{{ $media->getUrl() }}" target="_blank">{{ $media->name }} ({{ $media->size }} KB)</a>
                            <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                            <input type="hidden" name="attachments_id[]" value="{{ $media->id }}">
                        </p>
                    @endforeach
                @endif
            </div>
        </div>
        @if($errors->has('attachments'))
            <p class="help-block">
                {{ $errors->first('attachments') }}
            </p>
        @endif
    </div>
</div>

    
 <div class="row">
    <div class="col-xs-12">
        {!! Form::label('description', trans('global.project-tasks.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>


</div>