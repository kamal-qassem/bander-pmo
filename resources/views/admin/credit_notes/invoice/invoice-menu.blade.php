<?php
$controller = getController('controller');
$method = getController('method');
?>
<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            @can('credit_note_edit')
            <a href="{{ route('admin.credit_notes.edit', $invoice->id) }}" class="btn btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('quotes::custom.quotes.edit')}}</a>
            @endcan

            @can('credit_note_make_payment')
			<?php
            $refund_records = \App\CreditNotePayment::where('credit_note_id', '=', $invoice->id)->where('payment_status', '=', 'Success')->count();

            if ( ! in_array( $controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' ) ) ) {
			//$paid_amount = \App\CreditNotePayment::where('credit_note_id', '=', $invoice->id)->where('payment_status', '=', 'Success')->sum('amount');
            $paid_amount = getCache('credit_note_total_paid', 'credit_note_total_paid_' . $invoice->id, 0);
			$total_used = \App\CreditNoteCredit::where('credit_note_id', '=', $invoice->id)->sum('amount');

            $paid_amount = $paid_amount + $total_used;
			// echo request()->segment(6);
			if ( 'stripe' === request()->segment(6) && isCustomer() ) {
			?>
			<a href="<?php echo url()->full(); ?>" class="btn btn-success sendBill" title="{{trans('custom.credit_notes.make-payment')}}">
                <span class="fa fa-credit-card"></span> {{trans('custom.credit_notes.make-refund')}}&nbsp;<span class="badge">{{$refund_records}}</span>
            </a>
			<?php
			} else {
			?>
            <a href="#loadingModal" data-toggle="modal" data-remote="false" data-action="make-payment-pay" class="btn btn-success sendBill" title="{{trans('custom.credit_notes.make-payment')}}" data-invoice_id="{{$invoice->id}}" data-paid_amount="{{$paid_amount}}" data-payable_amount="{{$invoice->amount}}">
                <span class="fa fa-credit-card"></span> {{trans('custom.credit_notes.make-refund')}}&nbsp;<span class="badge">{{$refund_records}}</span>
            </a>
			<?php } 
            }
            ?>
            @endcan


 
   
            <!-- invoice -->

 @can('credit_note_apply_to_invoice')           
<!-- Trigger the modal with a button -->
@if( isPluginActive('invoice') )
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">@lang('custom.credit_notes.apply-to-invoice')</button>
@endif
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:800px;">

    {!! Form::open(['method' => 'POST', 'route' => ['admin.credit_notes.apply-to-invoice'],'class'=>'formvalidation', 'id' => 'frmapplytoinvoice']) !!}
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{trans('global.credit_notes.fields.credit-no') . $invoice->invoicenumberdisplay}}</h4>
      </div>

      <div class="alert message_bag" style="display:none">
        <ul></ul>
    </div>
    
      <div class="modal-body">
        <p>
    <div class="row">        
  <div class="panel-body table-responsive">
            
            <table class="table table-bordered table-striped">
                <thead>
                   <tr>
                    <th><span class="bold">@lang('global.invoices.fields.invoice-no')</span></th>
                    <th><span class="bold">@lang('global.invoices.fields.invoice-date')</span></th>
                    <th><span class="bold">@lang('custom.credit_notes.invoice-amount')</span></th>
                    <th><span class="bold">@lang('custom.credit_notes.invoice-due')</span></th>
                    <th><span class="bold">@lang('custom.credit_notes.invoice-amount-credit')</span></th>
                </tr>
            </thead>

            <?php
            $total_credits_applied = 0;
            ?>
            @foreach( $credit_invoices as $credit_invoice)
            <?php
            $total_paid = getCache('invoice_total_paid', 'invoice_total_paid_' . $credit_invoice->id, 0);
            // $applied_credits = DB::table('credit_note_credits')->where('invoice_id', $credit_invoice->id)->sum('amount');
            $applied_credits = getCache('applied_credits_invoice', 'applied_credits_invoice_' . $credit_invoice->id, 0);
            $amount_due = $credit_invoice->amount - ( $total_paid + $applied_credits );

            $can_display = true;
            if (function_exists('bccomp')) {
                $decimals = \App\Settings::getSetting('decimals', 'currency_settings');
                if ( empty( $decimals ) ) {
                    $decimals = 2;
                }
                if (bccomp($credit_invoice->amount, ($total_paid + $applied_credits), $decimals) === 0) {
                    $can_display = false;
                }
            } else {
                if ($credit_invoice->amount == ($total_paid + $applied_credits) ) {
                    $can_display = false;
                }
            }
            if ( ! $can_display ) {
               $already_applied_credits = DB::table('credit_note_credits')->where('invoice_id', $credit_invoice->id)->first();
               if ( $already_applied_credits ) {
                $can_display = true;
               }
            }
            ?>
            @if( $can_display )            
                <tr>
                    <td><a href="{{ route('admin.invoices.show', $credit_invoice->id) }}" target="_blank">{{trans('global.invoices.fields.invoice-no') . $credit_invoice->invoicenumberdisplay}}</a></td>
                    <td>{{ $credit_invoice->invoice_date ? digiDate( $credit_invoice->invoice_date ) : ''}}</td>
                    <td>{{ digiCurrency( $credit_invoice->amount, $credit_invoice->currency_id ) }}</td>
                    <td>{{digiCurrency($amount_due, $credit_invoice->currency_id)}}</td>
                    <td>
                        <?php
                        // Let us show if the credits are from this credit note!
                        //$applied_credits = DB::table('credit_note_credits')->where('invoice_id', $credit_invoice->id)->where('credit_note_id', $invoice->id)->sum('amount');
                        $applied_credits = appliedCreditsInvoice( $credit_invoice->id, $invoice->id );
                        ?>
                        <input type="number" name="amount[{{$credit_invoice->id}}]" class="form-control appliedamount" id="amount_{{$credit_invoice->id}}" value="0" aria-invalid="false" min="0.01">
                    </td>
                    <?php
                    $total_credits_applied += $applied_credits;
                    ?>
                </tr>            
            @endif
            @endforeach
            </table>
            <input type="hidden" name="credit_note_id" id="credit_note_id" value="{{$invoice->id}}">
        
    </div>




<div class="col-md-6 col-md-offset-6" style="margin-top:50px;">
    <div class="text-right">
        <?php
        $total_paid = \App\CreditNotePayment::where('credit_note_id', $invoice->id)->where('payment_status', 'Success')->sum('amount');
        $total_used = \App\CreditNoteCredit::where('credit_note_id', '=', $invoice->id)->sum('amount');
        $amount_due = $invoice->amount - ( $total_paid + $total_used );
        ?>
        <table class="table">
            <tbody>
                <tr>
                    <td class="bold">@lang('custom.credit_notes.total-credits')</td>
                    <td class="amount-to-credit">
                        {{digiCurrency($invoice->amount, $invoice->currency_id)}}
                        <input type="hidden" name="total_credits" id="total_credits" value="{{$amount_due}}">
                    </td>
                </tr>

                <tr>
                    <td class="bold">@lang('custom.credit_notes.invoice-amount-credit')</td>
                    <td class="amount-to-credit"><span id="amount_to_credit">{{digiCurrency(0, $invoice->currency_id)}}</span></td>
                </tr>
                
                <tr>
                    <td class="bold">@lang('custom.credit_notes.credits-remaining')</td>
                    <td class="credit-note-balance-due"><span id="remaining_credits">{{digiCurrency($amount_due, $invoice->currency_id)}}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>

        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('custom.credit_notes.messages.close')</button>
        <button type="button" class="btn btn-info" id="applytoinvoice">@lang('custom.credit_notes.apply')</button>
      </div>
    </div>
{!! Form::close() !!}
  </div>
</div>
@endcan


            @can('invoice_email_access')            
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                @can('invoice_email_created')
                <?php
                $is_sent = $invoice->history()->where('comments', 'credit-note-created')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="credit-note-created-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.credit_notes.notification')}} @if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan

                @can('invoice_email_refund')
                <?php
                $is_sent = $invoice->history()->where('comments', 'refund-proceeded')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="credit-note-refund-proceeded-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.credit_notes.refund-proceeded')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan
              </ul>
            </div>
            
            @endcan
            
            @can('credit_note_change_status_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('credit_note_change_status_paid')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.changestatus', [ 'id' => $invoice->id, 'status' => 'Open'])}}">{{trans('custom.credit_notes.open')}}</a></li>
                    @endcan

                    @can('credit_note_change_status_paid')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.changestatus', [ 'id' => $invoice->id, 'status' => 'Closed'])}}">{{trans('custom.credit_notes.close')}}</a></li>
                    @endcan

                   
                </div>
            </div>
            @endcan

         

            @can('credit_note_preview')
           
            <a href="{{ route( 'admin.credit_notes.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" target="_blank"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
          
            @endcan


            @can('credit_note_uploads')
            <a href="{{ route( 'admin.credit_notes.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.credit_notes.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.credit_notes.upload-documents')}}
            </a>
            @endcan

            
             @can('credit_note_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('credit_note_pdf_view')
                     <li><a class="dropdown-item" href="{{route('admin.credit_notes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('credit_note_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan

                </div>
            </div>
            @endcan

            @can('credit_note_print')
           <a href="{{route('admin.credit_notes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan

           
           
            
            </div>
        </div>
</div>

