<div class="row">
<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('name', trans('global.proposal-tasks.fields.name').'*', ['class' => 'control-label']) !!}
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('name'))
        <p class="help-block">
            {{ $errors->first('name') }}
        </p>
    @endif
</div>
</div>

<?php
$priorities = \Modules\DynamicOptions\Entities\DynamicOption::where('module', '=', 'proposals')->where('type', '=', 'priorities')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
?>
<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('priority_id', trans('global.proposal-tasks.fields.priority').'*', ['class' => 'control-label']) !!}
    {!! Form::select('priority_id', $priorities, old('priority_id'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('priority_id'))
        <p class="help-block">
            {{ $errors->first('priority_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('startdate', trans('global.proposal-tasks.fields.startdate').'*', ['class' => 'control-label']) !!}
      <?php
        $startdate = digiTodayDateAdd();
        if ( ! empty( $proposal_task ) ) {
            $startdate = ! empty( $proposal_task->startdate ) ? digiDate( $proposal_task->startdate ) : '';
        }
        ?>
    {!! Form::text('startdate', old('startdate', $startdate), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('startdate'))
        <p class="help-block">
            {{ $errors->first('startdate') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('duedate', trans('global.proposal-tasks.fields.duedate').'', ['class' => 'control-label']) !!}
     <?php
        $duedate = digiTodayDateAdd(2);
        if ( ! empty( $proposal_task ) ) {
            $duedate = ! empty( $proposal_task->duedate ) ? digiDate( $proposal_task->duedate ) : '';
        }
        ?>
    {!! Form::text('duedate', old('duedate', $duedate), ['class' => 'form-control date', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('duedate'))
        <p class="help-block">
            {{ $errors->first('duedate') }}
        </p>
    @endif
</div>
</div>

  <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('datefinished', trans('global.invoice-tasks.fields.datefinished').'', ['class' => 'control-label']) !!}
        <?php
        $datefinished = digiTodayDateAdd(4);
        if ( ! empty( $proposal_task ) ) {
            $datefinished = ! empty( $proposal_task->datefinished ) ? digiDate( $proposal_task->datefinished ) : '';
        }
        ?>
        {!! Form::text('datefinished', old('datefinished', $datefinished), ['class' => 'form-control date', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('datefinished'))
            <p class="help-block">
                {{ $errors->first('datefinished') }}
            </p>
        @endif
        </div>
    </div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('status_id', trans('global.proposal-tasks.fields.status').'', ['class' => 'control-label']) !!}
    <?php
    $statuses = \Modules\DynamicOptions\Entities\DynamicOption::where('module', '=', 'proposals')->where('type', '=', 'taskstatus')->get()->pluck('title', 'id')->prepend(trans('global.app_please_select'), '');
    ?>
    {!! Form::select('status_id', $statuses, old('status_id'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('status_id'))
        <p class="help-block">
            {{ $errors->first('status_id') }}
        </p>
    @endif
</div>
</div>


<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('billable', trans('global.proposal-tasks.fields.billable').'', ['class' => 'control-label']) !!}
    {!! Form::select('billable', $enum_billable, old('billable'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('billable'))
        <p class="help-block">
            {{ $errors->first('billable') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('recurring_id', trans('global.proposal-tasks.fields.recurring').'', ['class' => 'control-label']) !!}
    {!! Form::select('recurring_id', $recurrings, old('recurring_id'), ['class' => 'form-control select2', 'id' => 'recurring_period_id']) !!}
    <p class="help-block"></p>
    @if($errors->has('recurring_id'))
        <p class="help-block">
            {{ $errors->first('recurring_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('recurring_type', trans('global.proposal-tasks.fields.recurring-type').'', ['class' => 'control-label']) !!}
    {!! Form::select('recurring_type', $enum_recurring_type, old('recurring_type'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('recurring_type'))
        <p class="help-block">
            {{ $errors->first('recurring_type') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('recurring_value', trans('global.proposal-tasks.fields.recurring-value').'', ['class' => 'control-label']) !!}{!!digi_get_help(trans('global.recurring-invoices.recurring-value-help'), 'fa fa-question-circle')!!}
    <?php
    $recurring_value = 0;
    if ( ! empty( $proposal_task ) ) {
        $recurring_value = $proposal_task->recurring_value;
    }
    ?>
    {!! Form::number('recurring_value', old('recurring_value', $recurring_value), ['class' => 'form-control', 'placeholder' => '','min'=>'0','step'=>'.01']) !!}
    <p class="help-block"></p>
    @if($errors->has('recurring_value'))
        <p class="help-block">
            {{ $errors->first('recurring_value') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('cycles', trans('global.proposal-tasks.fields.cycles').'', ['class' => 'control-label','min'=>'0','step'=>'.01']) !!}{!!digi_get_help(trans('global.recurring-invoices.total-cycles-help'), 'fa fa-question-circle')!!}
    <?php
    $cycles = 0;
    if ( ! empty( $proposal_task ) ) {
        $cycles = $proposal_task->cycles;
    }
    ?>
    {!! Form::number('cycles', old('cycles', $cycles), ['class' => 'form-control', 'placeholder' => '','min'=>'0','step'=>'.01']) !!}
    <p class="help-block"></p>
    @if($errors->has('cycles'))
        <p class="help-block">
            {{ $errors->first('cycles') }}
        </p>
    @endif
</div>
</div>                



<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('visible_to_client', trans('global.proposal-tasks.fields.visible-to-client').'', ['class' => 'control-label']) !!}
    {!! Form::select('visible_to_client', $enum_visible_to_client, old('visible_to_client'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('visible_to_client'))
        <p class="help-block">
            {{ $errors->first('visible_to_client') }}
        </p>
    @endif
</div>
</div>
<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('attachments', trans('global.proposal-tasks.fields.attachments').'', ['class' => 'control-label']) !!}
        {!! Form::file('attachments[]', [
            'multiple',
            'class' => 'form-control file-upload',
            'data-url' => route('admin.media.upload'),
            'data-bucket' => 'attachments',
            'data-filekey' => 'attachments',
            'data-accept' => FILE_TYPES_GENERAL,
            ]) !!}
        <p class="help-block"></p>
        <div class="photo-block">
            <div class="form-group">
            <div class="progress-bar">&nbsp;</div>
            <div class="files-list">
                @if ( ! empty( $proposal_task ) )
                @foreach($proposal_task->getMedia('attachments') as $media)
                    <p class="form-group">
                        <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                        <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                        <input type="hidden" name="attachments_id[]" value="{{ $media->id }}">
                    </p>
                @endforeach
                @endif
                </div>
            </div>
        </div>
        @if($errors->has('attachments'))
            <p class="help-block">
                {{ $errors->first('attachments') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="row">

     <div class="col-xs-12">
    <div class="form-group">
    {!! Form::label('description', trans('global.proposal-tasks.fields.description').'', ['class' => 'control-label']) !!}
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
