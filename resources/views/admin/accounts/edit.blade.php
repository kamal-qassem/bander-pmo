@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.accounts.title')</h3>
    
    {!! Form::model($account, ['method' => 'PUT', 'route' => ['admin.accounts.update', $account->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('name', trans('global.accounts.fields.name').'*', ['class' => 'control-label form-label']) !!}
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

                 <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('url', trans('global.accounts.fields.url').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('url', old('url'), ['class' => 'form-control', 'placeholder' => 'URL']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('url'))
                        <p class="help-block">
                            {{ $errors->first('url') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
           
              
           
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('initial_balance', trans('global.accounts.fields.initial-balance').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('initial_balance', old('initial_balance'), ['class' => 'form-control amount', 'placeholder' => 'Initial Balance']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('initial_balance'))
                        <p class="help-block">
                            {{ $errors->first('initial_balance') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
           
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('account_number', trans('global.accounts.fields.account-number').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('account_number', old('account_number'), ['class' => 'form-control', 'placeholder' => 'Account Number']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('account_number'))
                        <p class="help-block">
                            {{ $errors->first('account_number') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('contact_person', trans('global.accounts.fields.contact-person').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('contact_person', old('contact_person'), ['class' => 'form-control', 'placeholder' => 'Contact Person']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('contact_person'))
                        <p class="help-block">
                            {{ $errors->first('contact_person') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
         
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('phone', trans('global.accounts.fields.phone').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control number', 'placeholder' => 'Phone']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>

                  <div class="col-xs-8">
                <div class="form-group">
                    {!! Form::label('description', trans('global.accounts.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description' ,'rows'=>'4']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
                        </p>
                    @endif
                </div>
                </div>
        
               
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

