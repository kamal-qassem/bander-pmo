<script src="{{ url('adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<link href="{{ url('adminlte/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<script src="{{ url('js/cdn-js-files/bootstrap/3.4.0') }}/bootstrap.min.js"></script>



<div style="width:920px;margin-left:20px;margin-top: 5px;">
<a href="{{route('admin.credit_notes.show', $invoice->id)}}" class="btn btn-info" role="button">@lang('custom.credit_notes.app_back_to_credit_note')</a>
    
    <div class="ibox-content">
<div class="invoice" id="invoice_pdf">  
@include('admin.common.invoice-stylesheet')

<style>
    .footer {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    color: #333;
    background-color: white;
    text-align: center;
    }
    .pagebreak{
    clear:both;
    page-break-inside: avoid ;
   }
   .wrapper{
  
}

</style>


        <header><br/>
            <h1>{{trans('custom.credit_notes.title-caps')}}</h1>
            <address>
                <h4 class="red"><strong>{{trans('global.credit_notes.fields.credit-no') . $invoice->invoicenumberdisplay}}</strong></h4>
                @if(! empty($invoice->reference) )
                <h4 class="red"><strong>{{trans('quotes::custom.quotes.reference') . $invoice->reference}}</strong></h4>
                @endif

                   <?php
                
                $class = 'info'; // Un-paid, due
                $title = trans('custom.credit_notes.' . $invoice->paymentstatus);
                
                if ( 'paid' == $invoice->paymentstatus ) {
                    $class = 'success';
                }
                if ( 'partial' == $invoice->paymentstatus ) {
                    $class = 'info';
                }
                 if ( 'unpaid' == $invoice->paymentstatus ) {
                    $class = 'info';
                }

                
                ?>

                <h3 class="alert alert-{{$class}} alert-bg-p">{{ strtoupper($title) }}</h3>
            </address>
            <?php
                    $logo = getSetting('Credit-Note-Logo', 'credit-note-settings');
                    if ( empty( $logo ) ) {
                        $logo = getSetting('site_logo', 'site_settings');
                    }
                    ?>
            <table class="meta">
            <tr><td class="beta2"><img alt="" src="{{asset( 'uploads/settings/' . $logo )}}" height="56" width="180"></td></tr> 
                <tr><td class="beta3"></td></tr>
                <tr><td class="beta2"><strong>{{getSetting('Company_Name_On_Credit_Note', 'credit-note-settings', trans('global.global_title'))}}</strong></td></tr>
                <tr><td class="beta2">{{getSetting('Company-Address', 'credit-note-settings')}}</td></tr>
            </table>
            <address>
                <p><strong>{{trans('custom.credit_notes.invoice-to')}}</strong></p>
                 <?php
                     if ( ! empty( $invoice->customer->company->name )) {
                   ?>
                <p>{{$invoice->customer->company->name}}</p>

                <?php } ?>

                <p><strong>{{trans('custom.invoices.attn')}}</strong>&nbsp;{{$invoice->customer->name}}</p>

                <p>{{$invoice->address}}</p>
                
                <p><strong>{{trans('custom.common.phone')}}</strong> {{$invoice->customer->phone1}}</p>

                @if(! empty( $invoice->customer->email ) )
                <p><strong>{{trans('custom.common.email')}}</strong> {{$invoice->customer->email}}</p>
                @endif
                <br/>

                @if ( 'yes' === $invoice->show_delivery_address ) 
                <p><strong>{{trans('custom.invoices.ship-to')}}</strong></p>
                <p>{!! clean($invoice->delivery_address) !!}</p>
            </address>
            @endif
        </header>
        <article>
            <table class="balance pagebreak">
            
                <tr>
                    <th><span>{{trans('custom.credit_notes.credit-note-date')}}</span></th>
                    <td><span>{{ $invoice->invoice_date ? digiDate($invoice->invoice_date) : '' }}</span></td>
                </tr>
             
                <tr>
                    <th><span>{{trans('custom.credit_notes.total')}}</span></th>
                    <td><span>{{digiCurrency($invoice->amount, $invoice->currency_id)}}</span></td>
                </tr>
              

            <?php
                $total_paid = \App\CreditNotePayment::where('credit_note_id', '=', $invoice->id)->where('payment_status', '=', 'Success')->sum('amount');
                $total_used = \App\CreditNoteCredit::where('credit_note_id', '=', $invoice->id)->sum('amount');
                $amount_due = $invoice->amount - ( $total_paid + $total_used );
                ?>


             @if( ! empty( $total_paid ) )
                <tr>
                    <th><span>{{trans('custom.credit_notes.refund')}}</span></th>
                    <td>{{digiCurrency( $total_paid, $invoice->currency_id )}}</td>
                </tr>
                @endif

            @if( ! empty( $total_used ) )
                <tr>
                    <th><span>{{trans('custom.credit_notes.used')}}</span></th>
                    <td>{{digiCurrency( $total_used, $invoice->currency_id )}}</td>
                </tr>
                @endif    

                @if( ! empty( $amount_due ) )
                <tr>
                    <th><span>{{trans('custom.credit_notes.credits-remaining')}}</span></th>
                    <td><span>{{digiCurrency( $amount_due, $invoice->currency_id )}}</span></td>
                </tr>
                @endif
            </table>
            @if( isPluginActive('product') ) 
            <div class="wrapper">
            <table class="inventory invoice-items">
                <thead>
                    <tr>
                        <th><span>{{ trans('custom.products.item_name') }}</span></th>
                        <th>
                        @if( ! empty( $invoice->show_quantity_as ) )
                            <span>{{$invoice->show_quantity_as}}</span>
                        @else
                            <span>{{ trans('custom.products.quantity') }}</span>
                        @endif 

                        </th>
                        <th><span>{{ trans('custom.products.rate') }}</span></th>
                        <th><span>{{ trans('custom.products.tax_percent') }}</span></th>
                        <th><span>{{ trans('custom.products.tax') }}</span></th>
                        <th><span>{{ trans('custom.products.discount_percent') }}</span></th>
                        <th><span>{{ trans('custom.products.discount') }}</span></th>
                        <th><span>{{ trans('custom.products.amount') }}</span></th>
                    </tr>
                </thead>


                <?php
                $products = ! empty( $invoice->products ) ? json_decode( $invoice->products ) : array();
                

                if ( ! empty( $products ) ) {
                   
                    $product_names = ! empty( $products->product_name ) ? $products->product_name : [];
                    $total_tax = $products->total_tax;
                    $total_discount = $products->total_discount;
                    $products_amount = $products->products_amount;
                    $sub_total = $products->sub_total;
                    $grand_total = $products->grand_total;
                    
                    $product_qtys = $products->product_qty;
                    $product_prices = $products->product_price;

                    $product_taxs = $products->product_tax;
                    $tax_types = $products->tax_type;
                    $tax_values = $products->tax_value;

                    $product_discounts = $products->product_discount;
                    $discount_types = $products->discount_type;
                    $discount_values = $products->discount_value;

                    $product_subtotals = $products->product_subtotal;
                    $pids = $products->pid;
                    $units = $products->unit;
                    $hsns = $products->hsn;
                    $alerts = $products->alert;
                    $stock_quantitys = $products->stock_quantity;
                    $product_ids = $products->product_ids;
                    $product_descriptions = $products->product_description;
                    for( $i = 0; $i < count( $product_names ); $i++ ) {

                        $product_name = ! empty( $product_names[ $i ] ) ? $product_names[ $i ] : '';
                        if ( is_numeric( $product_name ) ) {
                            $product = \App\Product::where('id', '=', $product_name )->first();
                            if ( $product ) {
                                $product_name = $product->name;
                            }
                        }
                        $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '1';
                        $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
                        $product_amount = $product_qty * $product_price;

                        $product_tax = ! empty( $product_taxs[ $i ] ) ? $product_taxs[ $i ] : '0'; // Rate.
                        $product_tax_display = digiCurrency( $product_tax, $invoice->currency_id );

                        $tax_type = ! empty( $tax_types[ $i ] ) ? $tax_types[ $i ] : 'percent';
                        
                        if ( 'percent' === $tax_type ) {
                            $tax_value = ( $product_amount * $product_tax) / 100;
                            $product_tax_display = $product_tax . ' %';
                        } else {
                            $tax_value = $product_tax;
                        }


                        $product_discount = ! empty( $product_discounts[ $i ] ) ? $product_discounts[ $i ] : '0';
                        $product_discount_display = digiCurrency( $product_discount, $invoice->currency_id );
                        $discount_type = ! empty( $discount_types[ $i ] ) ? $discount_types[ $i ] : 'percent';
                        
                        if ( 'percent' === $discount_type ) {
                            $discount_value = ( $product_amount * $product_discount) / 100;
                            $product_discount_display = $product_discount . ' %';
                        } else {
                            $discount_value = $product_discount;
                        }



                        $amount = $product_amount + $tax_value - $discount_value;

                        $product_subtotal = ! empty( $product_subtotals[ $i ] ) ? $product_subtotals[ $i ] : '0';
                        $pid = ! empty( $pids[ $i ] ) ? $pids[ $i ] : '';
                        $unit = ! empty( $units[ $i ] ) ? $units[ $i ] : '';
                        $hsn = ! empty( $hsns[ $i ] ) ? $hsns[ $i ] : '';
                        $alert = ! empty( $alerts[ $i ] ) ? $alerts[ $i ] : '';
                        $stock_quantity = ! empty( $stock_quantitys[ $i ] ) ? $stock_quantitys[ $i ] : '';
                        $product_id = ! empty( $product_ids[ $i ] ) ? $product_ids[ $i ] : '';
                        $product_description = ! empty( $product_descriptions[ $i ] ) ? $product_descriptions[ $i ] : '';
                    ?>

                <tbody>
                    <tr class="product_row" data-rowid="{{$i}}" data-product_id="{{$pid}}">
                        <td><span>{{$product_name}}</span></td>
                        <td><span>{{$product_qty}}</span></td>
                        <td><span>{{digiCurrency( $product_amount, $invoice->currency_id )}}</span></td>
                        <td><span>{{$product_tax_display}}</span></td>
                        <td id="tax_value_display-{{$i}}"><span>{{digiCurrency($tax_value, $invoice->currency_id)}}</span></td>
                        <td id="discount_value_display-{{$i}}"><span>{{$product_discount_display}}</span></td>
                        <td><span>{{digiCurrency($discount_value, $invoice->currency_id)}}</span></td>
                        <td id="result-{{$i}}"><span>{{digiCurrency($amount, $invoice->currency_id)}}</span></td>
                    </tr>
                        <?php
                    }
                }else {
                    $total_tax = 0;
                    $total_discount = 0;
                    $total_products_amount = 0;
                    $sub_total = 0;
                    $grand_total = 0;
                }
                ?>
                </tbody>
            </table>
        </div>
            <table class="balance pagerbreak">
                <tr>
                    <th><span>@lang('custom.products.total_tax')</span></th>
                    <td><span class="tax">{{digiCurrency($total_tax, $invoice->currency_id)}}</span></td>
                </tr>
                <tr>
                    <th><span>@lang('custom.products.sub_total')</span></th>
                    <td><span class="sub-total">{{digiCurrency($sub_total, $invoice->currency_id)}}</span></td>
                </tr>
                <tr>
                    <th><span>@lang('custom.products.total_discount')</span></th>
                    <td><span class="total-discount">{{digiCurrency($total_discount, $invoice->currency_id)}}</span></td>
                </tr>
                <tr>
                    <th><span>@lang('custom.products.grand_total')</span></th>
                    <td><span class="amount">{{digiCurrency($grand_total, $invoice->currency_id)}}</span></td>
                </tr>
                <?php
                    $additionals = false;
                    if ( ! empty( $products->cart_tax ) && $products->cart_tax > 0 ) {
                        $additionals = true;
                    ?>                <tr>
                    <th><span>@lang('custom.products.additional-tax')</span></th>
                    <td><span>{{digiCurrency($products->cart_tax, $invoice->currency_id)}}</span></td>
                </tr>
                <?php } ?>

                 <?php
                    if ( ! empty( $products->cart_discount ) && $products->cart_discount > 0 ) {
                        $additionals = true;
                    ?>
                <tr>
                    <th><span>@lang('custom.products.additional-discount')</span></th>
                    <td><span>{{digiCurrency($products->cart_discount, $invoice->currency_id)}}</span></td>
                </tr>
                <?php } ?>

                 <?php
                    if ( true === $additionals ) {
                    ?>
                <tr>
                    <th><span>@lang('custom.products.amount-payable')</span></th>
                    <td><span>{{digiCurrency($products->amount_payable, $invoice->currency_id)}}</span></td>
                </tr>

                <?php } ?>

                <?php
                $total_paid = \App\CreditNotePayment::where('credit_note_id', '=', $invoice->id)->where('payment_status', '=', 'Success')->sum('amount');
                $total_used = \App\CreditNoteCredit::where('credit_note_id', '=', $invoice->id)->sum('amount');
                $amount_due = $invoice->amount - ( $total_paid + $total_used );
                ?>

             @if( ! empty( $total_paid ) )
                <tr>
                    <th><span>{{trans('custom.credit_notes.refund')}}</span></th>
                    <td>{{digiCurrency( $total_paid, $invoice->currency_id )}}</td>
                </tr>
                @endif

            @if( ! empty( $total_used ) )
                <tr>
                    <th><span>{{trans('custom.credit_notes.used')}}</span></th>
                    <td>{{digiCurrency( $total_used, $invoice->currency_id )}}</td>
                </tr>
                @endif
                
                  @if( ! empty( $amount_due ) )
                  <tr>
                    <th><span>{{trans('custom.credit_notes.credits-remaining')}}</span></th>
                    <td>{{digiCurrency($amount_due, $invoice->currency_id)}}</span></td>
                </tr>
                   @endif
            </table>
            @endif
        </article>
         <?php
    $enable_signature_part = getSetting('enable-signature-part', 'credit-note-settings');
    ?>
    @if ( 'Yes' === $enable_signature_part )
            <table class="meta pagebreak">
            <?php
            $authorized_person = getSetting('Authorized-Person', 'credit-note-settings');
            $authorized_sign = getSetting('Authorized-Person-Signature', 'credit-note-settings');
            $authorized_designation = getSetting('Authorized-Person-Designation', 'credit-note-settings');
            ?>
                <tr><td class="beta">@lang('custom.invoices.authorized-person')</td></tr>
            @if( ! empty( $authorized_sign ) )
                <tr><td class="beta"><img src="{{asset( 'uploads/settings/' . $authorized_sign )}}" width="120" height="40" alt=""></td></tr>
                @endif

                @if( ! empty( $authorized_person ) )
                <tr><td class="beta">({{$authorized_person}})</td></tr>
                @endif

                @if( ! empty( $authorized_designation ) )
                <tr><td class="beta">{{$authorized_designation}}</td></tr>
                @endif
            </table>
            @endif
            <address>

           <?php
            $payment = \App\CreditNotePayment::where('credit_note_id', '=', $invoice->id)->orderBy('id', 'desc')->first();
            $paymentmethod = trans('custom.invoices.no-payment');
            if ( $payment ) {
                $paymentmethod = trans('custom.invoices.' . $payment->paymentmethod);
            }
            ?>
                 <p>@lang('custom.invoices.payment-method')  {{$paymentmethod}}</p>
             </address>
             <article>
                <table class="beta4 pagebreak">

                     @if(! empty( $invoice->invoice_notes ) )
                     <tr><td class="beta4"><b>@lang('global.invoices.fields.client-notes')</b></td></tr>
                     <tr><td><code>{!! clean($invoice->invoice_notes) !!} </code></td></tr>
                     @endif
                     
                 </table>
                 <table class="beta4 pagebreak">
                    
                     @if(! empty( $invoice->admin_notes ) )
                     <tr><td class="beta4"><b>@lang('global.invoices.fields.admin-notes')</b></td></tr>
                     <tr><td><code>{!! clean($invoice->admin_notes) !!} </code></td></tr>
                     @endif

                     @if(! empty( $invoice->terms_conditions ) )
                     <tr><td class="beta4"><b>@lang('global.invoices.fields.terms-conditions')</b></td></tr>
                     <tr><td><code>{!! clean($invoice->terms_conditions) !!}</code></td></tr>
                     @endif
                 </table>
           </article>

        <?php
            $invoice_footer_enable = getSetting('credit-note-footer-enable', 'credit-note-settings');
            ?>  
            @if ( 'Yes' === $invoice_footer_enable )    
            <div class="footer">
            <hr style="color: lightgray; margin: 0px !important;"/>
            <p style="font-size: 12px; text-decoration: none; text-align:center; margin: 0px;">
            {{ getSetting('credit-note-footer', 'credit-note-settings') }}</p>
            </div>  
        @endif
      

    </div>
</div>

</div>

<script type="text/javascript">
function printItem( elem ) {
    var mywindow = window.open('', 'PRINT', 'height=400,width=600' );

    var url = '{{themes("plugins/bootstrap/css/bootstrap.css")}}';
    var themecss_url = '{{ themes("css/style.css") }}';
    mywindow.document.write('<html><head><title>' + document.title  + '</title>' );
    mywindow.document.write('<link href="' + url + '" rel="stylesheet" type="text/css" media="print">');
    mywindow.document.write('<link href="' + themecss_url + '" rel="stylesheet" type="text/css" media="print">');
    mywindow.document.write('</head><body >' );
    mywindow.document.write('<h1>' + document.title  + '</h1>' );
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>' );

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}
printItem( 'invoice_pdf' );
</script>