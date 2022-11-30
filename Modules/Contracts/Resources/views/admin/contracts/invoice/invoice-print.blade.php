<link href="{{ url('adminlte/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<script src="{{ url('js/cdn-js-files/bootstrap/3.4.0') }}/bootstrap.min.js"></script>

<div style="width:920px;margin-left:20px;margin-top: 5px;">
<a href="{{route('admin.contracts.show', $invoice->id)}}" class="btn btn-info" role="button">@lang('custom.contracts.back-to-contracts')</a>

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
            <h1>{{trans('contracts::custom.contracts.title-single')}}</h1>
            <address>
                <h4 class="red"><strong>{{trans('contracts::custom.contracts.contract_no') . $invoice->invoicenumberdisplay}}</strong></h4>
                @if(! empty($invoice->reference) )
                <h4 class="red"><strong>{{trans('contracts::custom.contracts.reference') . $invoice->reference}}</strong></h4>
                @endif
                @if(! empty($invoice->subject) )
                <h3 class="red"><strong>{{$invoice->subject}}</strong></h3>
                @endif
               <h4 class="black"><strong>{{$invoice->contract_type->name}}</
                </strong></h4>

                  <?php
               
                $class = 'info'; // Un-paid, due
                $paymentstatus = $invoice->paymentstatus;
                if ( empty( $paymentstatus ) ) {
                    $paymentstatus = 'due';
                }
                $title = trans('custom.invoices.' . $paymentstatus);
                if ( 'accepted' == $paymentstatus ) {
                    $class = 'success';
                }
                if ( 'rejected' == $paymentstatus ) {
                    $class = 'danger';
                }
                
                if ( 'delivered' == $paymentstatus ) {  
                    $class = 'info';
                }
                if ( 'lost' == $paymentstatus ) {  
                    $class = 'danger';
                }
                if ( 'dead' == $paymentstatus ) {  
                    $class = 'danger';
                }


                ?>
                <h3 class="alert alert-{{$class}} alert-bg-p">{{strtoupper($title)}}</h3>
            </address>
            <?php
                    $logo = getSetting('Contract-Logo', 'contract-settings');
                    if ( empty( $logo ) ) {
                        $logo = getSetting('site_logo', 'site_settings');
                    }
                    ?>
            <table class="meta">
            <tr><td class="beta2"><img alt="" src="{{asset( 'uploads/settings/' . $logo )}}" height="56" width="180"></td></tr> 
                <tr><td class="beta3"></td></tr>
                <tr><td class="beta2"><strong>{{getSetting('Company_Name_On_Contract', 'contract-settings', trans('global.global_title'))}}</strong></td></tr>
                <tr><td class="beta2">{{getSetting('Company-Address', 'contract-settings')}}</td></tr>
            </table>
            <address>
                <p><strong>{{trans('contracts::custom.contracts.recipient')}}</strong></p>
                 <?php
                     if ( ! empty( $invoice->customer->company->name )) {
                   ?>
                <p>{{$invoice->customer->company->name}}</p>

                <?php } ?>

                <p><strong>{{trans('custom.invoices.attn')}}</strong>&nbsp;{{$invoice->customer->first_name . ' ' . $invoice->customer->last_name}}</p>

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
                    <th><span>{{trans('contracts::custom.contracts.contract-date')}}</span></th>
                    <td><span>{{$invoice->invoice_date ? digiDate($invoice->invoice_date) : ''}}</span></td>
                </tr>
                <tr>
                    <th><span>{{trans('global.contracts.fields.contract-expiry-date')}}</span></th>
                    <td><span>{{$invoice->invoice_due_date ? digiDate($invoice->invoice_due_date) : ''}}</span></td>
                </tr>
                 <tr>
                    <th><span>{{trans('global.contracts.fields.contract_type')}}</span></th>
                    <td><span>{{$invoice->contract_type->name}}</span></td>
                </tr>
                <tr>
                    <th><span>{{trans('global.contracts.fields.contract_value')}}</span></th>
                    <td><span>{{digiCurrency($invoice->amount,$invoice->currency_id)}}</span></td>
                </tr>
            </table>

        </article>
         <?php
    $enable_signature_part = getSetting('enable-signature-part', 'contract-settings');
    ?>
    @if ( 'Yes' === $enable_signature_part )
            <table class="meta pagebreak">
            <?php
            $authorized_person = getSetting('Authorized-Person', 'contract-settings');
            $authorized_sign = getSetting('Authorized-Person-Signature', 'contract-settings');
            $authorized_designation = getSetting('Authorized-Person-Designation', 'contract-settings');
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
    
             <article>
                <table class="beta4 pagebreak">
                 @if( $invoice->recurring_period_id )
                     <tr><td class="beta4"><b>@lang('contracts::custom.contracts.payment-terms') : </b></td></tr>
                     <tr><td><code>{{ $invoice->recurring_period->title }}</code></td></tr>
                     @endif
                 </table>
                 <table class="beta4 pagebreak">
                    
                    @if(! empty( $invoice->invoice_notes ) )
                     <tr><td class="beta4"><b>@lang('global.invoices.fields.client-notes')</b></td></tr>
                     <tr><td><code>{!! clean($invoice->invoice_notes) !!}</code></td></tr>
                     @endif

                        @if(! empty( $invoice->admin_notes ) )
                     <tr><td class="beta4"><b>@lang('global.invoices.fields.admin-notes')</b></td></tr>
                     <tr><td><code>{!! clean($invoice->admin_notes) !!}</code></td></tr>
                     @endif

                     @if(! empty( $invoice->terms_conditions ) )
                     <tr><td class="beta4"><b>@lang('global.invoices.fields.terms-conditions')</b></td></tr>
                     <tr><td><code>{!! clean($invoice->terms_conditions) !!}</code></td></tr>
                     @endif
                 </table>
           </article>
        
        <?php
        $invoice_footer_enable = getSetting('contract-footer-enable', 'contract-settings');
        ?>  
    @if ( 'Yes' === $invoice_footer_enable )    
    <div class="footer">
    <hr style="color: lightgray; margin: 0px !important;"/>
    <p style="font-size: 12px; text-decoration: none; text-align:center; margin: 0px;">
    {{ getSetting('contract-footer', 'contract-settings') }}</p>
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