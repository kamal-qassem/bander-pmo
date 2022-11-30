@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.recurring-invoices.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.recurring_invoices.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('customer_id', trans('global.recurring-invoices.fields.customer').'', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('customer_create') )
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcustomer" data-selectedid="customer_id" data-redirect="{{route('admin.quotes.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.recurring-invoices.fields.customer') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createcustomer" data-selectedid="customer_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.recurring-invoices.fields.customer') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
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
                    {!! Form::select('currency_id', $currencies, old('currency_id',$currency_id), ['class' => 'form-control', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true','disabled'=>'']) !!}
                <input type="hidden" name="currency_id_old" id="currency_id_old" value="{{$currency_id}}">
                    <p class="help-block"></p>
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
                    <div class="form-group">
                    {!! Form::label('sale_agent', trans('global.invoices.fields.sale-agent').'*', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('customer_create') )
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="sale_agent" data-selectedid="sale_agent" data-redirect="{{route('admin.invoices.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.invoices.fields.sale-agent') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="sale_agent" data-selectedid="sale_agent" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.invoices.fields.sale-agent') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('sale_agent', $sales_agent, old('sale_agent'), ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sale_agent'))
                        <p class="help-block">
                            {{ $errors->first('sale_agent') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>

                <div class="row">
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
                        {!! Form::label('delivery_address', trans('global.invoices.fields.delivery-address').'', ['class' => 'control-label']) !!}
                        {!! Form::textarea('delivery_address', old('delivery_address'), ['class' => 'form-control ', 'placeholder' => trans('global.invoices.selected-customer-delivery-address'), 'rows' => 4, 'id' => 'delivery_address']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('delivery_address'))
                            <p class="help-block">
                                {{ $errors->first('delivery_address') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
				
               <div class="row"> 
				<div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">                    
                    {!! Form::label('recurring_period_id', trans('global.recurring-invoices.fields.recurring-period'), ['class' => 'control-label']) !!}
                    {!! Form::select('recurring_period_id', $recurring_periods, old('recurring_period_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('recurring_period_id'))
                        <p class="help-block">
                            {{ $errors->first('recurring_period_id') }}
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
                    {!! Form::text('cycles', old('cycles', 0), ['class' => 'form-control number', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cycles'))
                        <p class="help-block">
                            {{ $errors->first('cycles') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>
            </div>

                <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                    {!! Form::label('prevent_overdue_reminders', trans('global.invoices.fields.prevent-overdue-reminders').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('prevent_overdue_reminders', yesnooptions(), old('prevent_overdue_reminders'), ['class' => 'form-control select2', 'required' => '']) !!}

                    <p class="help-block"></p>
                    @if($errors->has('prevent_overdue_reminders'))
                        <p class="help-block">
                            {{ $errors->first('prevent_overdue_reminders') }}
                        </p>
                    @endif
                </div>
                </div>
            
                

                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('allowed_paymodes',trans('global.invoices.fields.allowed-paymodes').'*', ['class' => 'control-label']) !!}
                    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-allowed_paymodes">
                        {{ trans('global.app_select_all') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-allowed_paymodes">
                        {{ trans('global.app_deselect_all') }}
                    </button>
                    <?php
                    $paymodes = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'id');
                    ?>
                    {!! Form::select('allowed_paymodes[]', $paymodes, old('allowed_paymodes'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-allowed_paymodes' , 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('allowed_paymodes'))
                        <p class="help-block">
                            {{ $errors->first('allowed_paymodes') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>


                <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('show_delivery_address', trans('global.invoices.fields.show-delivery-address').'', ['class' => 'control-label']) !!}
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
                    {!! Form::label('title', trans('global.recurring-invoices.fields.title'), ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Title']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>
           
                
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('invoice_prefix', trans('global.recurring-invoices.fields.invoice-prefix').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php
                    $recurring_invoice_prefix = getSetting( 'invoice-prefix', 'invoice-settings' );
                    ?>
                    {!! Form::text('invoice_prefix', old('invoice_prefix', $recurring_invoice_prefix), ['class' => 'form-control', 'placeholder' => 'Invoice Prefix']) !!}
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
                    <?php
                    $show_quantity_as = getSetting( 'show_quantity_as', 'invoice-settings' );
                    ?>
                    <div class="form-line">
                    {!! Form::text('show_quantity_as', old('show_quantity_as',$show_quantity_as), ['class' => 'form-control', 'placeholder' => 'Show Quantity As']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('show_quantity_as'))
                        <p class="help-block">
                            {{ $errors->first('show_quantity_as') }}
                        </p>
                    @endif
                </div>
            </div>
            </div>
        </div>
                
                <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('invoice_no', trans('global.recurring-invoices.fields.invoice-no'), ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                  <?php
                    $invoice_no = getNextNumber();
                    if ( ! empty( $invoice ) ) {
                        $invoice_no = $invoice->invoice_no;
                    }
                    ?>
                    {!! Form::text('invoice_no', old('invoice_no',$invoice_no), ['class' => 'form-control', 'placeholder' => 'Enter recurring invoice number']) !!}
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
                    {!! Form::text('reference', old('reference'), ['class' => 'form-control', 'placeholder' => '']) !!}
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
                    {!! Form::label('invoice_date', trans('global.recurring-invoices.fields.invoice-date').'', ['class' => 'control-label form-label']) !!}
                     <?php
                    $invoice_date = digiTodayDateAdd();
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
                    {!! Form::label('invoice_due_date', trans('global.recurring-invoices.fields.invoice-due-date').'', ['class' => 'control-label form-label']) !!}
                <?php
                $invoice_due_after = getSetting( 'invoice_due_after', 'invoice-settings');
                $invoice_due_date = ! empty($invoice->invoice_due_date) ? digiDate( $invoice->invoice_due_date ) : digiTodayDateAdd($invoice_due_after);
                ?>
                    <div class="form-line">
                    {!! Form::text('invoice_due_date', old('invoice_due_date',$invoice_due_date ), ['class' => 'form-control date', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_due_date'))
                        <p class="help-block">
                            {{ $errors->first('invoice_due_date') }}
                        </p>
                    @endif
                </div>
            </div>
                </div>
            </div>
                @if( isPluginActive('product') ) 
                <?php
                $enable_products_slider = getSetting( 'enable_products_slider', 'site_settings' );
                if ( 'yes' === $enable_products_slider ) {
                    ?>
                    <div class="col-xs-12">
                        <div class="form-group productsslider" style="display: none;">
                        @include('admin.common.products-slider', compact('products_return'))
                        </div>
                        <span id="productsslider_loader" style="display: block;">
                            <img src="{{asset('images/loading-small.gif')}}"/>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <div class="row">
                <div class="form-group">
                <div class="col-xs-12">
                    <div class="productsrow">
                        @include('admin.common.add-products')
                    </div>
                </div>
            </div>
        </div>
        @else

            <div class="col-xs-8">
            <div class="form-group">      
            {!! Form::label('amount', trans('global.invoices.fields.amount') . '*', ['class' => 'control-label']) !!}
            <div class="form-line">
            {!! Form::number('amount', old('amount'), ['class' => 'form-control', 'placeholder' => 'amount', 'step' => '0.01', 'required' => true]) !!}
            <p class="help-block"></p>
            @if($errors->has('amount'))
                <p class="help-block">
                    {{ $errors->first('amount') }}
                </p>
            @endif
                </div>
                </div>
                </div>
                @endif
                <div class="row"> 
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('invoice_notes', trans('global.invoices.fields.client-notes').'', ['class' => 'control-label']) !!}
                     <?php
                    $predefined_clientnote_invoice = getSetting( 'predefined_clientnote_invoice', 'invoice-settings' );
                    ?>
                    {!! Form::textarea('invoice_notes', old('invoice_notes',$predefined_clientnote_invoice), ['class' => 'form-control', 'placeholder' => '']) !!}
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
                    <?php
                    $predefined_adminnote_invoice = getSetting( 'predefined_adminnote_invoice', 'invoice-settings' );
                    ?>
                    {!! Form::textarea('admin_notes', old('admin_notes',$predefined_adminnote_invoice), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('admin_notes'))
                        <p class="help-block">
                            {{ $errors->first('admin_notes') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
                 <div class="row">
                 <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('terms_conditions', trans('global.invoices.fields.terms-conditions').'', ['class' => 'control-label']) !!}
                      <?php
                    $predefined_terms_invoice = getSetting( 'predefined_terms_invoice', 'invoice-settings' );
                    ?>
                    {!! Form::textarea('terms_conditions', old('terms_conditions',$predefined_terms_invoice), ['class' => 'form-control editor', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('terms_conditions'))
                        <p class="help-block">
                            {{ $errors->first('terms_conditions') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
                
                <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('tax_id', trans('global.recurring-invoices.fields.tax').'', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('tax_create') )
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createtax" data-selectedid="tax_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.recurring-invoices.fields.tax') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createtax" data-selectedid="tax_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.recurring-invoices.fields.tax') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('tax_id', $taxes, old('tax_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('tax_id'))
                        <p class="help-block">
                            {{ $errors->first('tax_id') }}
                        </p>
                    @endif
                </div>
                </div>
				
				<?php
				$enum_tax_format = [
					"after_tax" => "Tax After Product Tax", 
					"before_tax" => "Tax Before Product TAX",
				];
				?>
				<div class="col-xs-{{COLUMNS}}">
						<div class="form-group">
						{!! Form::label('tax_format', trans('global.invoices.fields.tax_format').'', ['class' => 'control-label']) !!}
						{!! Form::select('tax_format', $enum_tax_format, '', ['class' => 'form-control select2']) !!}
					</div>
				</div>
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    
                    {!! Form::label('discount_id', trans('global.recurring-invoices.fields.discount').'', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('discount_create') )
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="creatediscount" data-selectedid="discount_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.recurring-invoices.fields.discount') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm"  data-action="creatediscount" data-selectedid="discount_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.recurring-invoices.fields.discount') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('discount_id', $discounts, old('discount_id'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('discount_id'))
                        <p class="help-block">
                            {{ $errors->first('discount_id') }}
                        </p>
                    @endif
                </div>
                </div>
				
				<?php
				$enum_discounts_format = [
					"after_tax" => "Discount After Product Tax",
					"before_tax" => "Discount Before Product TAX",
				];
				?>
				<div class="col-xs-{{COLUMNS}}">
						<div class="form-group">
                        
						{!! Form::label('discount_format', trans('global.invoices.fields.discount_format').'', ['class' => 'control-label']) !!}
						{!! Form::select('discount_format', $enum_discounts_format, '', ['class' => 'form-control select2']) !!}
					</div>
				</div>
            </div>
            
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
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

	
	<script>
        $("#selectbtn-allowed_paymodes").click(function(){
            $("#selectall-allowed_paymodes > option").prop("selected","selected");
            $("#selectall-allowed_paymodes").trigger("change");
        });
        $("#deselectbtn-allowed_paymodes").click(function(){
            $("#selectall-allowed_paymodes > option").prop("selected","");
            $("#selectall-allowed_paymodes").trigger("change");
        });
		
		$('#recurring_period_id').change(function() {
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

            
@stop