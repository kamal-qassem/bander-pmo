<div class="row">
    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('name', trans('global.contact-companies.fields.name').'*', ['class' => 'control-label form-label']) !!}
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

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('email', trans('global.contact-companies.fields.email').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('email'))
            <p class="help-block">
                {{ $errors->first('email') }}
            </p>
        @endif
    </div>
</div>
    </div>
	
	<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('country', trans('global.contacts.fields.country').'', ['class' => 'control-label']) !!}
        {!! Form::select('country_id', $countries, old('country_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
        <p class="help-block"></p>
        @if($errors->has('country_id'))
            <p class="help-block">
                {{ $errors->first('country_id') }}
            </p>
        @endif
    </div>
    </div>
	
	<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('website', trans('global.companies.fields.url').'', ['class' => 'control-label form-label']) !!}
        <div class="form-line">
        {!! Form::text('website', old('website'), ['class' => 'form-control', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('website'))
            <p class="help-block">
                {{ $errors->first('website') }}
            </p>
        @endif
    </div>
</div>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('address', trans('global.contact-companies.fields.address').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('address', old('address'), ['class' => 'form-control', 'placeholder' => '', 'rows' => 4]) !!}
        <p class="help-block"></p>
        @if($errors->has('address'))
            <p class="help-block">
                {{ $errors->first('address') }}
            </p>
        @endif
    </div>
</div>

    
</div>