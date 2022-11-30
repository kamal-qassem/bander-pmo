@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
@include('admin.client_projects.operations.menu', array('client_project' => $project))
<?php
$operation = ! empty( $operation ) ? $operation : 'create';
?>
    @if( 'edit' === $operation )
    {!! Form::model($invoice, ['method' => 'POST', 'route' => ['admin.client_projects.invoice-project-store', $project->id, $invoice->id],'class'=>'formvalidation']) !!}
    @else
    {!! Form::open(['method' => 'POST', 'route' => ['admin.client_projects.invoice-project-store', $project->id],'class'=>'formvalidation']) !!}
    @endif
    <input type="hidden" name="operation" value="{{$operation}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.client-projects.invoice-project')
        </div>
        <div class="panel-body">
            <div class="row">
            	<div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('customer_id', trans('global.invoices.fields.client').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
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
                    {!! Form::label('currency_id', trans('global.invoices.fields.currency').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('currency_id', $currencies, old('currency_id'), ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('currency_id'))
                        <p class="help-block">
                            {{ $errors->first('currency_id') }}
                        </p>
                    @endif
                    
                </div>
            </div>

              <div class="col-xs-{{COLUMNS}} ">
                    <div class="form-group">
                    {!! Form::label('status', trans('global.invoices.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}

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
                    {!! Form::select('sale_agent', $sale_agents, old('sale_agent'), ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
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
                    <?php
                    $address = $project->client->fulladdress;
                    ?>
                    {!! Form::textarea('address', old('address', $address), ['class' => 'form-control ', 'placeholder' => trans('global.invoices.selected-customer-address'), 'rows' => 4, 'required' => '']) !!}
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
                        <?php
                        $address = $project->client->fullbillingaddress;
                        ?>
                        {!! Form::textarea('delivery_address', old('delivery_address', $address), ['class' => 'form-control ', 'placeholder' => trans('global.invoices.selected-customer-delivery-address'), 'rows' => 4, 'id' => 'delivery_address']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('delivery_address'))
                            <p class="help-block">
                                {{ $errors->first('delivery_address') }}
                            </p>
                        @endif
                    </div>
                </div>

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
                    {!! Form::label('allowed_paymodes', trans('global.invoices.fields.allowed-paymodes').'*', ['class' => 'control-label']) !!}
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
                       
                    {!! Form::label('title', trans('global.invoices.fields.title') . '*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'required' => '','placeholder' => 'Title']) !!}
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
                    {!! Form::label('invoice_prefix', trans('global.invoices.fields.invoice-prefix').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php
                    $invoice_prefix = getSetting( 'invoice-prefix', 'invoice-settings' );
                    ?>
                    {!! Form::text('invoice_prefix', old('invoice_prefix', $invoice_prefix), ['class' => 'form-control', 'placeholder' => 'Invoice Prefix']) !!}
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
                    {!! Form::label('show_quantity_as', trans('global.invoices.fields.show-quantity-as').'', ['class' => 'control-label form-label']) !!}
                <?php
                $show_quantity_as = getSetting( 'show_quantity_as', 'invoice-settings' );
                if ( ! empty( $invoice ) ) {
                $show_quantity_as = $invoice->show_quantity_as;
                }
                ?>
                    <div class="form-line">
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
                    {!! Form::label('invoice_no', trans('global.invoices.fields.invoice-no'), ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    <?php $invoice_no = getNextNumber(); ?>
                    {!! Form::text('invoice_no', old('invoice_no', $invoice_no), ['class' => 'form-control', 'placeholder' => trans('custom.invoices.keep-it-blank')]) !!}
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
                    {!! Form::label('reference', trans('global.invoices.fields.reference').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('reference', old('reference'), ['class' => 'form-control', 'placeholder' => 'reference']) !!}
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
                    {!! Form::label('invoice_date', trans('global.invoices.fields.invoice-date').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('invoice_date', old('invoice_date', digiTodayDateAdd()), ['class' => 'form-control date', 'placeholder' => '']) !!}
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
                    {!! Form::label('invoice_due_date', trans('global.invoices.fields.invoice-due-date').'', ['class' => 'control-label form-label']) !!}

            <?php
                $invoice_due_after = getSetting( 'invoice_due_after', 'invoice-settings');
                $invoice_due_date = ! empty($invoice->invoice_due_date) ? digiDate( $invoice->invoice_due_date ) : digiTodayDateAdd($invoice_due_after);
            ?>

                    <div class="form-line">
                    {!! Form::text('invoice_due_date', old('invoice_due_date', $invoice_due_date ), ['class' => 'form-control date', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('invoice_due_date'))
                        <p class="help-block">
                            {{ $errors->first('invoice_due_date') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('tax_id', trans('global.invoices.fields.tax').'', ['class' => 'control-label']) !!}
                    {!! Form::select('tax_id', $taxes, old('tax_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('tax_id'))
                        <p class="help-block">
                            {{ $errors->first('tax_id') }}
                        </p>
                    @endif
                </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('tax_format', trans('global.invoices.fields.tax_format').'', ['class' => 'control-label']) !!}
                    {!! Form::select('tax_format', $enum_tax_format, '', ['class' => 'form-control select2']) !!}
                </div>
            </div>
        
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    
                    {!! Form::label('discount_id', trans('global.invoices.fields.discount').'', ['class' => 'control-label']) !!}
                    {!! Form::select('discount_id', $discounts, old('discount_id'), ['class' => 'form-control select2','data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('discount_id'))
                        <p class="help-block">
                            {{ $errors->first('discount_id') }}
                        </p>
                    @endif
                </div>
            </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    
                    {!! Form::label('discount_format', trans('global.invoices.fields.discount_format').'', ['class' => 'control-label']) !!}
                    {!! Form::select('discount_format', $enum_discounts_format, '', ['class' => 'form-control select2', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
                </div>
            </div>

        <div class="col-xs-12 form-group">
            <div class="form-group productsrow">
                @if ( ! empty( $invoice ) )
                    @include('admin.common.add-products', ['products_return' => $invoice])
                @else
                    @include('admin.common.add-products')
                @endif
            </div>
        </div>
        
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('invoice_notes', trans('global.invoices.fields.invoice-notes').'', ['class' => 'control-label']) !!}
                    <?php
                    $predefined_clientnote_invoice = getSetting( 'predefined_clientnote_invoice', 'invoice-settings' );
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
                    $predefined_adminnote_invoice = getSetting( 'predefined_adminnote_invoice', 'invoice-settings' );
                    ?>
                    {!! Form::textarea('admin_notes', old('admin_notes', $predefined_adminnote_invoice), ['class' => 'form-control', 'placeholder' => '']) !!}
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
                    <?php
                    $predefined_terms_invoice = getSetting( 'predefined_terms_invoice', 'invoice-settings' );
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
            </div>
        </div>
    </div>
    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::submit(trans('custom.common.save-manage'), ['class' => 'btn btn-info wave-effect', 'name' => 'btnsavemanage', 'value' => 'savemanage']) !!}
    {!! Form::submit(trans('custom.common.save-send'), ['class' => 'btn btn-success wave-effect', 'name' => 'btnsavesend', 'value' => 'savesend']) !!}
    {!! Form::close() !!}

    @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

    @include('admin.common.scripts', ['products_return' => ['currency_id' => $project->currency_id]])
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
    </script>            
@stop