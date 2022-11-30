
<div class="row">
    @if ( ! $project )
    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('project_id', trans('global.time-entries.fields.project').'*', ['class' => 'control-label']) !!}
        {!! Form::select('project_id', $projects, old('project_id'), ['class' => 'form-control select2', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('project_id'))
            <p class="help-block">
                {{ $errors->first('project_id') }}
            </p>
        @endif
    </div>
    </div>
    @else
    <input type="hidden" name="project_id" value="{{$project->id}}">
    @endif

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('start_date', trans('global.time-entries.fields.start-date').'*', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::text('start_date', old('start_date'), ['class' => 'form-control datetime', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('start_date'))
            <p class="help-block">
                {{ $errors->first('start_date') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('end_date', trans('global.time-entries.fields.end-date').'*', ['class' => 'control-label form-label']) !!}
     <div class="form-line">   
        {!! Form::text('end_date', old('end_date'), ['class' => 'form-control datetime', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('end_date'))
            <p class="help-block">
                {{ $errors->first('end_date') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('task_id', trans('global.project-tasks.title').'*', ['class' => 'control-label']) !!}{!!digi_get_help(trans('global.project-tasks.task-not-completed-only-htlp'), 'fa fa-question-circle')!!}
        {!! Form::select('task_id', $tasks, old('task_id'), ['class' => 'form-control select2', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('task_id'))
            <p class="help-block">
                {{ $errors->first('task_id') }}
            </p>
        @endif
    </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('completed_by_id', trans('global.project-tasks.completed-by').'*', ['class' => 'control-label']) !!}{!!digi_get_help(trans('global.project-tasks.completed-by-htlp'), 'fa fa-question-circle')!!}
        {!! Form::select('completed_by_id', $assignees, old('completed_by_id'), ['class' => 'form-control select2', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('completed_by_id'))
            <p class="help-block">
                {{ $errors->first('completed_by_id') }}
            </p>
        @endif
    </div>
    </div>

    <div class="col-xs-12">
    <div class="form-group">
        {!! Form::label('description', trans('global.time-entries.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
    </div>
</div>