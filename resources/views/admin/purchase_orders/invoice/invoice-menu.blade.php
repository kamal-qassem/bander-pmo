<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            @can('purchase_order_edit')
            <a href="{{ route('admin.purchase_orders.edit', $invoice->id) }}" class="btn btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('custom.purchase_orders.edit')}}</a>
            @endcan

            @can('purchase_order_make_payment')
            <?php
                $payment_records_count = \App\PurchaseOrderPayment::where('purchase_order_id', '=', $invoice->id)->where('payment_status', 'Success')->count();
            ?>
            <a href="#loadingModal" data-toggle="modal" data-remote="false" data-action="make-payment-pay" class="btn btn-success sendBill" title="{{trans('custom.invoices.make-payment')}}" data-invoice_id="{{$invoice->id}}">
                <span class="fa fa-credit-card"></span> {{trans('custom.invoices.make-payment')}}&nbsp;
                <span class="badge">{{$payment_records_count}}</span>
            </a>
            @endcan


            @can('purchase_order_email_access')
            @if( ! empty( $invoice->customer->email ) )
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>

              <ul class="dropdown-menu">
                <?php
                $is_sent = $invoice->history()->where('comments', 'purchase-order-created')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="purchase-order-created-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.purchase_orders.purchase-order-created')}} @if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>                
              </ul>
            </div>
            @endif
            @endcan

            
            @can('purchase_order_sms_access')
            @if( ! empty( $invoice->customer->phone1 ) )
            <!-- SMS -->
            <div class="btn-group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.sms')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('purchase_order_sms_created')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'order-created')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="order-created-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.purchase_orders.send-quote')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    @endcan

                    @can('purchase_order_sms_accepted')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'order-created')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="order-accepted-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.purchase_orders.quote-accepted')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    @endcan
                    
                    @can('purchase_order_sms_cancelled')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'order-cancelled')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="order-cancelled-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.purchase_orders.quote-cancelled')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    @endcan
                </div>
            </div>
            @endif
            @endcan

            
            
            @can('purchase_order_changestatus_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_changestatus_paid')
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'paid'])}}">{{trans('custom.purchase_orders.paid')}}</a></li>
                    @endcan

                    @can('invoice_changestatus_due')
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'due'])}}">{{trans('custom.invoices.unpaid')}}</a></li>
                    @endcan

                    @can('invoice_changestatus_partial')
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'partial'])}}">{{trans('custom.purchase_orders.partial')}}</a></li>
                    @endcan
                    {{--
                    @can('invoice_changestatus_hold')
                    <li><a class="dropdown-item markas" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'on-hold'])}}">{{trans('custom.purchase_orders.on-hold')}}</a></li>
                    @endcan                  
                    
                    @can('invoice_changestatus_rejected')
                    <li><a class="dropdown-item markas" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'rejected'])}}">{{trans('custom.purchase_orders.rejected')}}</a></li>
                    @endcan
                    --}}

                    @can('invoice_changestatus_cancelled')
                    <li><a class="dropdown-item markas" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'cancelled'])}}">{{trans('custom.purchase_orders.cancelled')}}</a></li>
                    @endcan                 
                </div>
            </div>
            @endcan
                        
             @can('purchase_order_preview')
            <a href="{{ route( 'admin.purchase_orders.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-primary" target="_blank"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan

             @can('purchase_order_duplicate')
            <a href="{{ route( 'admin.purchase_orders.duplicate', [ 'slug' => $invoice->slug ] ) }}" data-route="{{ route( 'admin.purchase_orders.duplicate', [ 'slug' => $invoice->slug ] ) }}" data-custommessage="{{trans('custom.common.are-you-sure-duplicate')}}" class="btn btn-info confirmbootbox"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan

            @can('purchase_order_uploads')
            <a href="{{ route( 'admin.purchase_orders.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>
            @endcan

            @can('purchase_order_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>

                </div>
            </div>
            @endcan

            @can('purchase_order_print')
            <a href="{{route('admin.purchase_orders.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm"  title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            </div>
        </div>
</div>