<div class="row">
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('shortcode', trans('global.countries.fields.shortcode').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line"> 
        {!! Form::text('shortcode', old('shortcode'), ['class' => 'form-control required', 'placeholder' => '','required'=>'']) !!}
        <p class="help-block"></p>
        @if($errors->has('shortcode'))
            <p class="help-block">
                {{ $errors->first('shortcode') }}
            </p>
        @endif
    </div>
</div>
    </div>

    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('title', trans('global.countries.fields.title').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line"> 
        {!! Form::text('title', old('title'), ['class' => 'form-control required', 'placeholder' => '','required'=>'']) !!}
        <p class="help-block"></p>
        @if($errors->has('title'))
            <p class="help-block">
                {{ $errors->first('title') }}
            </p>
        @endif
    </div>
</div>
    </div>

    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('dialcode', trans('others.countries.dialcode').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line"> 
        {!! Form::text('dialcode', old('dialcode'), ['class' => 'form-control required', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('dialcode'))
            <p class="help-block">
                {{ $errors->first('dialcode') }}
            </p>
        @endif
    </div>
</div>
    </div>
</div>
