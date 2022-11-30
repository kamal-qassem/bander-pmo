<div class="row">
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('name', trans('global.discounts.fields.name').'*', ['class' => 'control-label form-label']) !!}
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
        {!! Form::label('discount', trans('global.discounts.fields.discount').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::number('discount', old('discount'), ['class' => 'form-control','min'=>'0','step'=>'0.01', 'placeholder' => 'Discount']) !!}
        <p class="help-block"></p>
        @if($errors->has('discount'))
            <p class="help-block">
                {{ $errors->first('discount') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('discount_type', trans('global.discounts.fields.discount-type').'', ['class' => 'control-label']) !!}
        {!! Form::select('discount_type', $enum_discount_type, old('discount_type'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('discount_type'))
            <p class="help-block">
                {{ $errors->first('discount_type') }}
            </p>
        @endif
    </div>
    </div>

    <div class="col-xs-8">
    <div class="form-group">
        
        {!! Form::label('description', trans('global.discounts.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description','rows'=>'3']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
    </div>
</div>