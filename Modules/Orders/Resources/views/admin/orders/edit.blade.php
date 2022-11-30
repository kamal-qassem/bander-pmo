@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>
    
    {!! Form::model($order, ['method' => 'PUT', 'route' => ['admin.orders.update', $order->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3">
                <div class="form-group">
                    {!! Form::label('customer_id', trans('orders::global.orders.fields.customer').'*', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('customer_create') )
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcustomer" data-selectedid="customer_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('orders::global.orders.fields.customer') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createcustomer" data-selectedid="customer_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('orders::global.orders.fields.customer') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('customer_id'))
                        <p class="help-block">
                            {{ $errors->first('customer_id') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    <?php
                    $currency_id = ! empty( old('currency_id_old') ) ? old('currency_id_old') : '';
                    if ( empty( $currency_id ) && ! empty( $order ) ) {
                        $currency_id = $order->currency_id;
                    }
                    ?>
                    {!! Form::label('currency_id', trans('global.invoices.fields.currency').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('currency_id', $currencies, old('currency_id', $currency_id), ['class' => 'form-control', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true','disabled' =>'disabled']) !!}
                    <input type="hidden" name="currency_id_old" id="currency_id_old" value="{{$currency_id}}">
                    <p class="help-block"></p>
                    @if($errors->has('currency_id'))
                        <p class="help-block">
                            {{ $errors->first('currency_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-3">
                <div class="form-group">
                    
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

                <div class="col-xs-3">
                <div class="form-group">
                    {!! Form::label('is_recurring', trans('global.expense.fields.is_recurring'), ['class' => 'control-label']) !!}
                    {!! Form::select('is_recurring', yesnooptions(), old('is_recurring'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('is_recurring'))
                        <p class="help-block">
                            {{ $errors->first('is_recurring') }}
                        </p>
                    @endif
                </div>
                </div>

                 <div class="col-xs-3">
                <div class="form-group">
                    {!! Form::label('billing_cycle_id', trans('orders::global.orders.fields.billing-cycle').'*', ['class' => 'control-label']) !!}
                    @if (Gate::allows('recurring_period_create'))
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createrecurringperiod" data-selectedid="billing_cycle_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('orders::global.orders.fields.billing-cycle') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createrecurringperiod" data-selectedid="billing_cycle_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('orders::global.orders.fields.billing-cycle') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('billing_cycle_id', $billing_cycles, old('billing_cycle_id'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_cycle_id'))
                        <p class="help-block">
                            {{ $errors->first('billing_cycle_id') }}
                        </p>
                    @endif
                </div>
                </div>

                @php
                $options = array(
                    'no' => 'No',
                    'yes' => 'Yes',
                );
                @endphp
                <div class="col-xs-3">
                <div class="form-group">
                    {!! Form::label('update_stock', trans('global.purchase-orders.fields.update-stock').'', ['class' => 'control-label']) !!}
                    @if( 'yes' === $order->stock_updated )
                        <span class="label label-success label-many">@lang('global.purchase-orders.fields.stock-updated')</span>
                    @else
                        <span class="label label-danger label-many">@lang('global.purchase-orders.fields.stock-not-updated')</span>
                    @endif
                    {!! Form::select('update_stock', $options, old('update_stock'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('update_stock'))
                        <p class="help-block">
                            {{ $errors->first('update_stock') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">                   
                    {!! Form::label('recurring_value', trans('global.recurring-invoices.fields.recurring_value').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('recurring_value', old('recurring_value', 1), ['class' => 'form-control number', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('recurring_value'))
                        <p class="help-block">
                            {{ $errors->first('recurring_value') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>
                
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">                   
                    {!! Form::label('recurring_type', trans('global.recurring-invoices.fields.recurring_type').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php
                    $recurring_types = array(
                        'day' => trans('custom.common.days'),
                        'week' => trans('custom.common.weeks'),
                        'month' => trans('custom.common.months'),
                        'year' => trans('custom.common.years'),
                    );
                    ?>
                    
                    {!! Form::select('recurring_type', $recurring_types, old('recurring_type'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'required' => '', 'id' => 'recurring_type']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('recurring_type'))
                        <p class="help-block">
                            {{ $errors->first('recurring_type') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>
                
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">                   
                    {!! Form::label('cycles', trans('global.recurring-invoices.fields.total_cycles').'', ['class' => 'control-label form-label']) !!}{!!digi_get_help(trans('global.recurring-invoices.total-cycles-help'), 'fa fa-question-circle')!!}
                    <div class="form-line">
                    {!! Form::text('cycles', old('cycles'), ['class' => 'form-control number', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cycles'))
                        <p class="help-block">
                            {{ $errors->first('cycles') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('prevent_overdue_reminders', trans('global.invoices.fields.prevent-overdue-reminders-order').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('prevent_overdue_reminders', yesnooptions(), old('prevent_overdue_reminders'), ['class' => 'form-control select2', 'required' => '']) !!}

                    <p class="help-block"></p>
                    @if($errors->has('prevent_overdue_reminders'))
                        <p class="help-block">
                            {{ $errors->first('prevent_overdue_reminders') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
            <div class="row">
            <div class="col-xs-12">            
                <div class="productsrow">
                @include('admin.common.add-products', array('products_return' => $order))
                </div>       
            </div>
        </div>
            
            
            <div class="row">
               
                @php
                $options = array(
                    'no' => 'No',
                    'yes' => 'Yes',
                );
                @endphp
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('generate_invoice', trans('orders::global.orders.fields.generate-invoice'), ['class' => 'control-label']) !!}
                    {!! Form::select('generate_invoice', $options, old('generate_invoice'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('generate_invoice'))
                        <p class="help-block">
                            {{ $errors->first('generate_invoice') }}
                        </p>
                    @endif
                </div>
                </div>
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('send_email', trans('orders::global.orders.fields.send-email'), ['class' => 'control-label']) !!}
                    {!! Form::select('send_email', $options, old('send_email'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('send_email'))
                        <p class="help-block">
                            {{ $errors->first('send_email') }}
                        </p>
                    @endif
                </div>
                </div>
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
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

                    <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                   
                    {!! Form::label('add_to_income', trans('others.orders.add-to-income'), ['class' => 'control-label']) !!}
                    {!! Form::select('add_to_income', $options, old('add_to_income'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('add_to_income'))
                        <p class="help-block">
                            {{ $errors->first('add_to_income') }}
                        </p>
                    @endif
                </div>
                </div>

            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}

    @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent
    @include('admin.common.scripts', array('products_return' => $order))
    @include('admin.common.modal-scripts')

    <script>
        $('#billing_cycle_id').change(function() {
            $.ajax({
                url: '{{url('admin/recurring-invoice/get-details')}}/' + $(this).val(),
                dataType: "json",
                method: 'get',
              
                success: function (data) {
         
         
                    $('#recurring_value').val( data.value );
                    $('#recurring_type').val( data.type ).trigger("change");
                }
            });
        });
    </script>
@endsection

