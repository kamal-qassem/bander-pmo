<div class="row">
    <div class="col-xs-4">
    <div class="form-group">
        {!! Form::label('title', trans('global.brands.fields.title').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::text('title', old('title'), ['class' => 'form-control','required'=>'', 'placeholder' => 'Enter your brand title']) !!}
        <p class="help-block"></p>
        @if($errors->has('title'))
            <p class="help-block">
                {{ $errors->first('title') }}
            </p>
        @endif
    </div>
    </div>
    </div>

    @if( empty( $is_ajax ) )
    <div class="col-xs-4">
    <div class="form-group">
       
        @if(! empty( $brand ) && file_exists(public_path() . '/thumb/' . $brand->icon))
            <a href="{{ asset(env('UPLOAD_PATH').'/'.$brand->icon) }}" target="_blank"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/'.$brand->icon) }}"></a>
        @endif
        {!! Form::label('icon', trans('global.brands.fields.icon').'', ['class' => 'control-label']) !!}
        {!! Form::file('icon', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}
        {!! Form::hidden('icon_max_size', 15) !!}
        {!! Form::hidden('icon_max_width', 4096) !!}
        {!! Form::hidden('icon_max_height', 4096) !!}
        <p class="help-block"></p>
        @if($errors->has('icon'))
            <p class="help-block">
                {{ $errors->first('icon') }}
            </p>
        @endif
    </div>
    </div>
    @endif

    <div class="col-xs-4">
        <div class="form-group">
        {!! Form::label('status', trans('global.brands.fields.status').'', ['class' => 'control-label']) !!}
        {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('status'))
            <p class="help-block">
                {{ $errors->first('status') }}
            </p>
        @endif
    </div>
    </div>
</div>