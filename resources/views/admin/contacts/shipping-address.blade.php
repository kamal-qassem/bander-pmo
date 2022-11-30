@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contacts.title_shipping_address')</h3>
    @if ( isCustomer() )
        @can('profile_edit')
        <p>
            <a href="{{ route('admin.contacts.profile.edit') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.contacts.update-profile')</a>        
        </p>
        @endcan
        @can('delivery_address_edit')
        <p>
            <a href="{{ route('admin.contacts.delivery-address.edit') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.contacts.update-delivery-address')</a>        
        </p>
        @endcan
    @endif
    
    {!! Form::model($contact, ['method' => 'POST', 'route' => ['admin.contacts.shipping-address.update', $contact->id], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                                            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('first_name', trans('global.contacts.fields.first-name').'*', ['class' => 'control-label']) !!}
                    <?php
                    $first_name = ! empty( $shipping_address['first_name'] ) ? $shipping_address['first_name'] : old('first_name');
                    ?>
                    {!! Form::text('first_name', $first_name, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('first_name'))
                        <p class="help-block">
                            {{ $errors->first('first_name') }}
                        </p>
                    @endif
                </div>
            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('last_name', trans('global.contacts.fields.last-name').'', ['class' => 'control-label']) !!}
                    <?php
                    $last_name = ! empty( $shipping_address['last_name'] ) ? $shipping_address['last_name'] : old('last_name');
                    ?>
                    {!! Form::text('last_name', $last_name, ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_name'))
                        <p class="help-block">
                            {{ $errors->first('last_name') }}
                        </p>
                    @endif
                </div> 
            </div>

            
            
           
            
            <div class="row">            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('address', trans('global.contacts.fields.address').'*', ['class' => 'control-label']) !!}
                    <?php
                    $address = ! empty( $shipping_address['address'] ) ? $shipping_address['address'] : old('address');
                    ?>
                    {!! Form::text('address', $address, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="help-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('city', trans('global.contacts.fields.city').'*', ['class' => 'control-label']) !!}
                    <?php
                    $city = ! empty( $shipping_address['city'] ) ? $shipping_address['city'] : old('city');
                    ?>
                    {!! Form::text('city', $city, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('city'))
                        <p class="help-block">
                            {{ $errors->first('city') }}
                        </p>
                    @endif
                </div>
            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('state_region', trans('global.contacts.fields.state-region').'*', ['class' => 'control-label']) !!}
                    <?php
                    $state_region = ! empty( $shipping_address['state_region'] ) ? $shipping_address['state_region'] : old('state_region');
                    ?>
                    {!! Form::text('state_region', $state_region, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('state_region'))
                        <p class="help-block">
                            {{ $errors->first('state_region') }}
                        </p>
                    @endif
                </div>
            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('zip_postal_code', trans('global.contacts.fields.zip-postal-code').'*', ['class' => 'control-label']) !!}
                    <?php
                    $zip_postal_code = ! empty( $shipping_address['zip_postal_code'] ) ? $shipping_address['zip_postal_code'] : old('zip_postal_code');
                    ?>
                    {!! Form::text('zip_postal_code', $zip_postal_code, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zip_postal_code'))
                        <p class="help-block">
                            {{ $errors->first('zip_postal_code') }}
                        </p>
                    @endif
                </div>
            
                
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('country_id', trans('global.contacts.fields.country').'*', ['class' => 'control-label']) !!}
                    <?php
                    $country_id = ! empty( $shipping_address['country_id'] ) ? $shipping_address['country_id'] : old('country_id');
                    ?>
                    {!! Form::select('country_id', $countries, $country_id, ['class' => 'form-control select2 show-tick', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('country_id'))
                        <p class="help-block">
                            {{ $errors->first('country_id') }}
                        </p>
                    @endif
                </div>
            </div>   
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop