<div class="row">
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('name', trans('global.taxes.fields.name').'*', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Name', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('name'))
            <p class="help-block">
                {{ $errors->first('name') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('rate', trans('global.taxes.fields.rate').'*', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::number('rate', old('rate'), ['class' => 'form-control', 'min'=>'0','step'=>'0.01','placeholder' => 'Rate', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('rate'))
            <p class="help-block">
                {{ $errors->first('rate') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('rate_type', trans('global.taxes.fields.rate-type').'', ['class' => 'control-label']) !!}
        {!! Form::select('rate_type', $enum_rate_type, old('rate_type'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('rate_type'))
            <p class="help-block">
                {{ $errors->first('rate_type') }}
            </p>
        @endif
    </div>
    </div>

    <div class="col-xs-8">
    <div class="form-group">
        <br>
        {!! Form::label('description', trans('global.taxes.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description','rows'=>4]) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
    </div>
</div>