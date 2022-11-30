@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>
    
    {!! Form::model($order, ['method' => 'PUT', 'route' => ['admin.orders.update', $order->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('customer_id', trans('orders::global.orders.fields.customer').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('customer_id'))
                        <p class="help-block">
                            {{ $errors->first('customer_id') }}
                        </p>
                    @endif
                </div>
            
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('status', trans('orders::global.orders.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="col-xs-12 form-group">
                    @include('admin.common.add-products', array('products_return' => $order))
            </div>
            <div class="row">
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('billing_cycle_id', trans('orders::global.orders.fields.billing-cycle').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('billing_cycle_id', $billing_cycles, old('billing_cycle_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_cycle_id'))
                        <p class="help-block">
                            {{ $errors->first('billing_cycle_id') }}
                        </p>
                    @endif
                </div>
                @php
                $options = array(
                    'no' => 'No',
                    'yes' => 'Yes',
                );
                @endphp
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('generate_invoice', trans('orders::global.orders.fields.generate-invoice'), ['class' => 'control-label']) !!}
                    {!! Form::select('generate_invoice', $options, old('generate_invoice'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('generate_invoice'))
                        <p class="help-block">
                            {{ $errors->first('generate_invoice') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('send_email', trans('orders::global.orders.fields.send-email'), ['class' => 'control-label']) !!}
                    {!! Form::select('send_email', $options, old('send_email'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('send_email'))
                        <p class="help-block">
                            {{ $errors->first('send_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-{{COLUMNS}} form-group">
                    {!! Form::label('send_sms', trans('orders::global.orders.fields.send-sms'), ['class' => 'control-label']) !!}
                    {!! Form::select('send_sms', $options, old('send_sms'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('send_sms'))
                        <p class="help-block">
                            {{ $errors->first('send_sms') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

