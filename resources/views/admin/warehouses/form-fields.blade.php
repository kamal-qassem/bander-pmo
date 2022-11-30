<div class="row">
    <div class="col-xs-12">
    <div class="form-group">
        {!! Form::label('name', trans('global.warehouses.fields.name').'*', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('name'))
            <p class="help-block">
                {{ $errors->first('name') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('address', trans('global.warehouses.fields.address').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('address', old('address'), ['class' => 'form-control ', 'placeholder' => '','rows'=>'3']) !!}
        <p class="help-block"></p>
        @if($errors->has('address'))
            <p class="help-block">
                {{ $errors->first('address') }}
            </p>
        @endif
    </div>
    </div>

    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('description', trans('global.warehouses.fields.description').'', ['class' => 'control-label']) !!}
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