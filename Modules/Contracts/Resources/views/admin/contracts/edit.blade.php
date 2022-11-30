@extends('layouts.app')

@section('content')
    <h3 class="page-subject">@lang('contracts::custom.contracts.title')</h3>
    
    {!! Form::model($recurring_invoice, ['method' => 'PUT', 'route' => ['admin.contracts.update', $recurring_invoice->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('customer_id', trans('global.recurring-invoices.fields.customer').'', ['class' => 'control-label']) !!}
                    @if( 'button' === $addnew_type )
                    &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcustomer" data-selectedid="customer_id" data-redirect="{{route('admin.invoices.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-subject="{{trans('global.add_new_title', ['subject' => strtolower( trans('global.contracts.fields.customer') )])}}">{{ trans('global.app_add_new') }}</button>
                    @else        
                    &nbsp;<a class="modalForm" data-action="createcustomer" data-selectedid="customer_id" data-toggle="tooltip" data-placement="bottom" data-original-subject="{{trans('global.add_new_title', ['subject' => strtolower( trans('global.contracts.fields.customer') )])}}"><i class="fa fa-plus-square"></i></a>
                    @endif
                    {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true','required'=>'']) !!}
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
                        {!! Form::label('currency_id', trans('global.recurring-invoices.fields.currency').'*', ['class' => 'control-label']) !!}
             <?php
                $currency_id = ! empty( old('currency_id_old') ) ? old('currency_id_old') : '';
                if ( empty( $currency_id ) && ! empty( $invoice ) ) {
                $currency_id = $invoice->currency_id;
                }
              ?>
                        {!! Form::select('currency_id', $currencies, old('currency_id',$currency_id), ['class' => 'form-control', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true','disabled' =>'']) !!}
                        <p class="help-block"></p>
                <input type="hidden" name="currency_id_old" id="currency_id_old" value="{{$currency_id}}">
                        @if($errors->has('currency_id'))
                            <p class="help-block">
                                {{ $errors->first('currency_id') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                        {!! Form::label('status', trans('global.recurring-invoices.fields.status').'', ['class' => 'control-label']) !!}
                        {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('status'))
                            <p class="help-block">
                                {{ $errors->first('status') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                {!! Form::label('visible_to_customer', trans('global.contracts.fields.visible-to-customer').'', ['class' => 'control-label']) !!}
                {!! Form::select('visible_to_customer', $enum_visible_to_customer, old('visible_to_customer'), ['class' => 'form-control select2']) !!}
                <p class="help-block"></p>
                @if($errors->has('visible_to_customer'))
                    <p class="help-block">
                        {{ $errors->first('visible_to_customer') }}
                    </p>
                @endif
                </div>
                
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('address', trans('global.recurring-invoices.fields.address').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('address', old('address'), ['class' => 'form-control ', 'placeholder' => 'Selected customer address', 'rows' => 4]) !!}
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
                       
                    {!! Form::label('delivery_address', trans('global.contracts.fields.show-delivery-address').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('delivery_address', old('delivery_address'), ['class' => 'form-control ', 'placeholder' => trans('global.invoices.selected-customer-delivery-address'), 'rows' => 4, 'id' => 'delivery_address']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('delivery_address'))
                        <p class="help-block">
                            {{ $errors->first('delivery_address') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('show_delivery_address', trans('global.contracts.fields.show-delivery-address').'', ['class' => 'control-label']) !!}
                    {!! Form::select('show_delivery_address', yesnooptions(), old('show_delivery_address'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('show_delivery_address'))
                        <p class="help-block">
                            {{ $errors->first('show_delivery_address') }}
                        </p>
                    @endif
                </div>
                </div>
                
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                   
                    {!! Form::label('subject', trans('global.contracts.fields.title').'', ['class' => 'control-label form-label']) !!}
                 <div class="form-line">
                    {!! Form::text('subject', old('subject'), ['class' => 'form-control', 'placeholder' => 'Subject','rows' => '4']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('subject'))
                        <p class="help-block">
                            {{ $errors->first('subject') }}
                        </p>
                    @endif
            </div>
                </div>
             </div>

          
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('invoice_prefix', trans('global.contracts.fields.contract-prefix').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('invoice_prefix', old('invoice_prefix'), ['class' => 'form-control', 'placeholder' => 'Invoice prefix']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_prefix'))
                        <p class="help-block">
                            {{ $errors->first('invoice_prefix') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('show_quantity_as', trans('global.recurring-invoices.fields.show-quantity-as').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('show_quantity_as', old('show_quantity_as'), ['class' => 'form-control', 'placeholder' => 'Show quantity as']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('show_quantity_as'))
                        <p class="help-block">
                            {{ $errors->first('show_quantity_as') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('invoice_no', trans('global.contracts.fields.contract-no'), ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('invoice_no', old('invoice_no'), ['class' => 'form-control', 'placeholder' => 'Enter contract number', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_no'))
                        <p class="help-block">
                            {{ $errors->first('invoice_no') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>
            
             
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('reference', trans('global.recurring-invoices.fields.reference').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('reference', old('reference'), ['class' => 'form-control', 'placeholder' => 'Reference']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('reference'))
                        <p class="help-block">
                            {{ $errors->first('reference') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>

                   
           
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                       
                    {!! Form::label('invoice_date', trans('global.contracts.fields.contract-date').'', ['class' => 'control-label']) !!}
                    <?php
                    $invoice_date = digiTodayDateAdd();
                    if ( ! empty( $recurring_invoice ) ) {
                    $invoice_date = ! empty( $recurring_invoice->invoice_date ) ?  digiDate($recurring_invoice->invoice_date)  : '';
                    }
                    ?>

                    <div class="form-line">
                    {!! Form::text('invoice_date', old('invoice_date', $invoice_date), ['class' => 'form-control date', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_date'))
                        <p class="help-block">
                            {{ $errors->first('invoice_date') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('invoice_due_date', trans('global.contracts.fields.contract-expiry-date').'', ['class' => 'control-label']) !!}
                    <div class="form-line">
                    <?php
                    $invoice_due_after = getSetting( 'contract_due_after', 'contract-settings', 2 );

                    $invoice_due_date = digiTodayDateAdd( $invoice_due_after );
                    if ( ! empty( $recurring_invoice ) ) {
                        $invoice_due_date = ! empty( $recurring_invoice->invoice_due_date ) ?  digiDate($recurring_invoice->invoice_due_date)  : '';
                    }
                    ?>
                    {!! Form::text('invoice_due_date', old('invoice_due_date', $invoice_due_date), ['class' => 'form-control date', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_due_date'))
                        <p class="help-block">
                            {{ $errors->first('invoice_due_date') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
                 <div class="col-xs-6">
                  <div class="form-group">
                     {!! Form::label('contract_value', trans('global.contracts.fields.contract_value').'*', ['class' => 'control-label form-label']) !!}
                     <div class="form-line">
                        {!! Form::number('amount', old('amount'), ['class' => 'form-control amount', 'placeholder' => trans('global.contracts.fields.contract_value'), 'required' => '', 'min'=>'0','step'=>'0.01','id' => 'contract_value']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('contract_value'))
                        <p class="help-block">
                           {{ $errors->first('contract_value') }}
                        </p>
                        @endif
                     </div>
                  </div>
                </div>
             

                   <div class="col-xs-6">
                  <div class="form-group">
                     {!! Form::label('contract_type_id', trans('global.contracts.fields.contract_type').'', ['class' => 'control-label']) !!}
                 
                     {!! Form::select('contract_type_id', $contract_types, old('contract_type_id'), ['class' => 'form-control select2', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true', 'subject' => trans('global.contracts.fields.contract_type')]) !!}
                     <p class="help-block"></p>
                     @if($errors->has('contract_type_id'))
                     <p class="help-block">
                        {{ $errors->first('contract_type_id') }}
                     </p>
                     @endif
                  </div>
               </div>
                <?php
                    $customer_id = old('customer_id');

                    if ( empty( $currency_id ) ) {
                        $currency_id = ! empty( $products_return->currency_id ) ? $products_return->currency_id : getDefaultCurrency('id');
                    }

                    $currency_code = getCurrency($currency_id, 'code');
                    $currency_symbol = getCurrency($currency_id, 'symbol');
                    ?>
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('invoice_notes', trans('global.contracts.fields.client-notes').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('invoice_notes', old('invoice_notes'), ['class' => 'form-control', 'placeholder' => 'Invoice notes']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_notes'))
                        <p class="help-block">
                            {{ $errors->first('invoice_notes') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('admin_notes', trans('global.invoices.fields.admin-notes').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('admin_notes', old('admin_notes'), ['class' => 'form-control', 'placeholder' => 'Admin notes']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('admin_notes'))
                        <p class="help-block">
                            {{ $errors->first('admin_notes') }}
                        </p>
                    @endif
                </div>
                </div>

                 <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('terms_conditions', trans('global.invoices.fields.terms-conditions').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('terms_conditions', old('terms_conditions'), ['class' => 'form-control editor', 'placeholder' => 'Terms and conditions']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('terms_conditions'))
                        <p class="help-block">
                            {{ $errors->first('terms_conditions') }}
                        </p>
                    @endif
                </div>
                </div>
   
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}

    @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent
    @include('admin.common.standard-ckeditor')
    @include('admin.common.scripts')
    @include('admin.common.modal-scripts')
    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop