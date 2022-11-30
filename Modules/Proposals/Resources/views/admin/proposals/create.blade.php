@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('proposals::custom.proposals.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.proposals.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        <?php
        if ( empty( $fetchaddress ) ) {
            $fetchaddress = 'no';
        }
        ?>
        <input type="hidden" name="fetchaddress" id="fetchaddress" value="{{$fetchaddress}}">
        <?php
        if ( empty( $selectedid ) ) {
            $selectedid = 'customer_id';
        }
        ?>

        <input type="hidden" name="selectedid" id="selectedid" value="{{$selectedid}}">
        <div class="panel-body">
            <div class="row">

           <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('customer_id', trans('global.proposals.fields.customer').'*', ['class' => 'control-label']) !!}
                     @if( 'button' === $addnew_type )
                    &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcustomer" data-selectedid="customer_id" data-redirect="{{route('admin.invoices.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.customer') )])}}">{{ trans('global.app_add_new') }}</button>
                    @else        
                    &nbsp;<a class="modalForm" data-action="createcustomer" data-selectedid="customer_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.customer') )])}}"><i class="fa fa-plus-square"></i></a>
                    @endif
                   

                    <select id='customer_id' name= "customer_id" class="form-control select2" placeholder='Please select'>
                        <option value="">{{trans('global.app_please_select')}}</option>
                         <optgroup label='Customers'>
                                
                          @foreach($customers as $id => $name)
                                 <option value='{{ $id }}'>{{ $name }}</option>
                             </optgroup>
                          @endforeach
                           <optgroup label='Leads'>
                          @foreach($leads as $id => $name)
                                 <option value='{{ $id }}'>{{ $name }}</option>
                             </optgroup>
                          @endforeach
                      
                    </select>
                </div>
            </div>
                 
         
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('currency_id', trans('global.proposals.fields.currency').'*', ['class' => 'control-label']) !!}
               <?php
                $currency_id = ! empty( old('currency_id_old') ) ? old('currency_id_old') : '';
                if ( empty( $currency_id ) && ! empty( $invoice ) ) {
                $currency_id = $invoice->currency_id;
                }
              ?>
                    {!! Form::select('currency_id', $currencies, old('currency_id',$currency_id), ['class' => 'form-control', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true','disabled' =>'']) !!}
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
                    {!! Form::label('status', trans('global.proposals.fields.status').'', ['class' => 'control-label']) !!}
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
                    @if( 'button' === $addnew_type )
                    &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="sale_agent" data-selectedid="sale_agent" data-redirect="{{route('admin.invoices.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.sale-agent') )])}}">{{ trans('global.app_add_new') }}</button>
                    @else        
                    &nbsp;<a class="modalForm" data-action="sale_agent" data-selectedid="sale_agent" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.sale-agent') )])}}"><i class="fa fa-plus-square"></i></a>
                    @endif
                    {!! Form::select('sale_agent', $sales_agent, old('sale_agent'), ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true', 'id' => 'sale_agent']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sale_agent'))
                        <p class="help-block">
                            {{ $errors->first('sale_agent') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-6">
                    <div class="form-group">
                       
                    {!! Form::label('address', trans('global.invoices.fields.address').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('address', old('address'), ['class' => 'form-control ', 'placeholder' => trans('global.invoices.selected-customer-address'), 'rows' => 4, 'required' => '']) !!}
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
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('show_delivery_address', trans('global.proposals.fields.show-delivery-address').'', ['class' => 'control-label']) !!}
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
                    {!! Form::label('title', trans('global.proposals.fields.title').'*', ['class' => 'control-label form-label']) !!}
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
                    {!! Form::label('proposal_prefix', trans('global.proposals.fields.proposal-prefix').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php
                    $proposal_prefix = getSetting( 'proposal-prefix', 'proposal-settings' );
                    ?>
                    {!! Form::text('proposal_prefix', old('proposal_prefix', $proposal_prefix), ['class' => 'form-control', 'placeholder' => trans('global.proposals.fields.proposal-prefix')]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('proposal_prefix'))
                        <p class="help-block">
                            {{ $errors->first('proposal_prefix') }}
                        </p>
                    @endif
                </div>
            </div>
            </div>
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('show_quantity_as', trans('global.proposals.fields.show-quantity-as').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php
                    $show_quantity_as = getSetting( 'show_quantity_as', 'proposal-settings' );
                    ?>
                    {!! Form::text('show_quantity_as', old('show_quantity_as', $show_quantity_as), ['class' => 'form-control', 'placeholder' => 'Show Quantity As']) !!}
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
                    {!! Form::label('invoice_no', trans('global.proposals.fields.proposal-no'), ['class' => 'control-label form-label']) !!}

                    <div class="form-line">
                    <?php
                    $invoice_no = getNextNumber('Proposal');

                    if ( ! empty( $invoice ) ) {
                        $invoice_no = $invoice->invoice_no;

                    }
                    ?>                    
                    {!! Form::text('invoice_no', old('invoice_no', $invoice_no), ['class' => 'form-control', 'placeholder' => 'Enter Proposal number']) !!}
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
                   
                    {!! Form::label('reference', trans('global.proposals.fields.reference').'', ['class' => 'control-label form-label']) !!}
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
                    {!! Form::label('invoice_date', trans('global.proposals.fields.proposal-date').'', ['class' => 'control-label form-label']) !!}
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
                    {!! Form::label('invoice_due_date', trans('global.proposals.fields.proposal-expiry-date').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php
                    $invoice_due_after = getSetting( 'proposal_due_after', 'proposal-settings', 2 );
                    ?>  
                    {!! Form::text('invoice_due_date', old('invoice_due_date', digiTodayDateAdd($invoice_due_after)), ['class' => 'form-control date', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_due_date'))
                        <p class="help-block">
                            {{ $errors->first('invoice_due_date') }}
                        </p>
                    @endif
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
                <div class="col-xs-12">
                    <div class="productsrow">
                        @include('admin.common.add-products')
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
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('invoice_notes', trans('global.proposals.fields.client-notes').'', ['class' => 'control-label']) !!}
                    <?php
                    $predefined_clientnote_invoice = getSetting( 'predefined_clientnote_proposal', 'proposal-settings' );
                    ?>
                    {!! Form::textarea('invoice_notes', old('invoice_notes', $predefined_clientnote_invoice), ['class' => 'form-control', 'placeholder' => '']) !!}
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
                    $predefined_adminnote_proposal = getSetting( 'predefined_adminnote_proposal', 'proposal-settings' );
                    ?>
                    {!! Form::textarea('admin_notes', old('admin_notes', $predefined_adminnote_proposal), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('admin_notes'))
                        <p class="help-block">
                            {{ $errors->first('admin_notes') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-12
                ">
                <div class="form-group">
                    {!! Form::label('terms_conditions', trans('global.invoices.fields.terms-conditions').'', ['class' => 'control-label']) !!}
                    <?php
                    $predefined_terms_invoice = getSetting( 'predefined_terms_proposal', 'proposal-settings' );
                    ?>
                    {!! Form::textarea('terms_conditions', old('terms_conditions', $predefined_terms_invoice), ['class' => 'form-control editor', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('terms_conditions'))
                        <p class="help-block">
                            {{ $errors->first('terms_conditions') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('tax_id', trans('global.proposals.fields.tax').'', ['class' => 'control-label']) !!}
                    @if( 'button' === $addnew_type )
                    &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createtax" data-selectedid="tax_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.tax') )])}}">{{ trans('global.app_add_new') }}</button>
                    @else        
                    &nbsp;<a class="modalForm" data-action="createtax" data-selectedid="tax_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.tax') )])}}"><i class="fa fa-plus-square"></i></a>
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
                    "before_tax" => "Tax Before Product Tax",
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
                       
                    {!! Form::label('discount_id', trans('global.proposals.fields.discount').'', ['class' => 'control-label']) !!}
                    @if( 'button' === $addnew_type )
                    &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="creatediscount" data-selectedid="discount_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.discount') )])}}">{{ trans('global.app_add_new') }}</button>
                    @else        
                    &nbsp;<a class="modalForm" data-action="creatediscount" data-selectedid="discount_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.proposals.fields.discount') )])}}"><i class="fa fa-plus-square"></i></a>
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
                "before_tax" => "Discount Before Product Tax",
            ];
            ?>
            <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                        
                    {!! Form::label('discount_format', trans('global.invoices.fields.discount_format').'', ['class' => 'control-label']) !!}
                    {!! Form::select('discount_format', $enum_discounts_format, '', ['class' => 'form-control select2']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                       
                    {!! Form::label('recurring_period_id', trans('proposals::custom.proposals.payment-terms').'', ['class' => 'control-label']) !!}
                    {!! Form::select('recurring_period_id', $recurring_periods, old('recurring_period_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('recurring_period_id'))
                        <p class="help-block">
                            {{ $errors->first('recurring_period_id') }}
                        </p>
                    @endif
                </div>
                </div>
          
                           
             
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect', 'name' => 'save']) !!}
    {!! Form::submit(trans('global.app_save_send'), ['class' => 'btn btn-success wave-effect', 'name' => 'savesend','value' => 'savesend']) !!}
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