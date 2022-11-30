<div class="row">
    <div class="form-group">
    <div class="col-xs-4">
        {!! Form::label('date', trans('global.proposals-reminders.fields.date').'*', ['class' => 'control-label']) !!}
        {!! Form::text('date', old('date'), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('date'))
            <p class="help-block">
                {{ $errors->first('date') }}
            </p>
        @endif
        </div>
    </div>

        <?php
    $isnotified = 'no';
    if ( ! empty( $proposals_reminder ) ) {
        $isnotified = $proposals_reminder->isnotified;
    }
    ?>
    <input type="hidden" name="isnotified" value="{{$isnotified}}">


    <div class="form-group">
    <div class="col-xs-4">
        {!! Form::label('reminder_to_id', trans('global.proposals-reminders.fields.reminder-to').'*', ['class' => 'control-label']) !!}
        {!! Form::select('reminder_to_id', $reminder_tos, old('reminder_to_id'), ['class' => 'form-control select2', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('reminder_to_id'))
            <p class="help-block">
                {{ $errors->first('reminder_to_id') }}
            </p>
        @endif
        </div>
    </div>

    <div class="form-group">
    <div class="col-xs-4">
        {!! Form::label('notify_by_email', trans('global.proposals-reminders.fields.notify-by-email').'', ['class' => 'control-label']) !!}
        {!! Form::select('notify_by_email', $enum_notify_by_email, old('notify_by_email'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('notify_by_email'))
            <p class="help-block">
                {{ $errors->first('notify_by_email') }}
            </p>
        @endif
        </div>
    </div>

</div>
 <div class="row">
    <div class="form-group">
    <div class="col-xs-6">
        {!! Form::label('description', trans('global.proposals-reminders.fields.description').'*', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Type your text here...','rows'=>'3', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
        </div>
    </div>
</div>