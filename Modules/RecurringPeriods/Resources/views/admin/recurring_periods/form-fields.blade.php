<div class="row">
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('title', trans('global.recurring-periods.fields.title').'*', ['class' => 'control-label']) !!}
        {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('title'))
            <p class="help-block">
                {{ $errors->first('title') }}
            </p>
        @endif
    </div>
    </div>
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('value', trans('global.recurring-periods.fields.value').'*', ['class' => 'control-label']) !!}
        {!! Form::number('value', old('value'), ['class' => 'form-control', 'placeholder' => '', 'required' => '','min'=>'1']) !!}
        <p class="help-block"></p>
        @if($errors->has('value'))
            <p class="help-block">
                {{ $errors->first('value') }}
            </p>
        @endif
    </div>
    </div>
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('type', trans('global.recurring-periods.fields.type').'*', ['class' => 'control-label']) !!}
        <?php
        $recurring_types = array(
            'day' => trans('custom.common.days'),
            'week' => trans('custom.common.weeks'),
            'month' => trans('custom.common.months'),
            'year' => trans('custom.common.years'),
        );
        ?>
        
        {!! Form::select('type', $recurring_types, old('type'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('type'))
            <p class="help-block">
                {{ $errors->first('type') }}
            </p>
        @endif
    </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('description', trans('global.recurring-periods.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => '','rows'=>'3']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
    </div>
</div>
