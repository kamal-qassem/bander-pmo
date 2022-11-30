<?php
$controller = getController('controller');
$method = getController('method');
?>
<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            @can('invoice_edit')
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
            @endcan

            @can('invoice_make_payment')
            <?php

            $payment_records_count = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', $invoice->id)->where('payment_status', 'Success')->count();

            if ( ! in_array( $controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' ) ) ) {
            $paid_amount = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', $invoice->id)->where('payment_status', 'Success')->sum('amount');
            $total_used = \App\CreditNoteCredit::where('invoice_id', $invoice->id)->sum('amount');

            $paid_amount += $total_used;
            
            if ( 'stripe' === request()->segment(6) && isCustomer() ) {
            ?>
            <a href="<?php echo url()->full(); ?>" class="btn btn-success sendBill" title="{{trans('custom.invoices.make-payment')}}">
                <span class="fa fa-credit-card"></span> {{trans('custom.invoices.make-payment')}}&nbsp;<span class="badge">{{$payment_records_count}}</span>
            </a>
            <?php
            } else {
            ?>
            <a href="#loadingModal" data-toggle="modal" data-remote="false" data-action="make-payment-pay" class="btn btn-success sendBill" title="{{trans('custom.invoices.make-payment')}}" data-invoice_id="{{$invoice->id}}" data-paid_amount="{{$paid_amount}}" data-payable_amount="{{$invoice->amount}}">
                <span class="fa fa-credit-card"></span> {{trans('custom.invoices.make-payment')}}&nbsp;
                <span class="badge">{{$payment_records_count}}</span>
            </a>
            <?php } 
            }
            ?>
            @endcan

            @can('invoice_email_access')
            @if ( ! in_array( $controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' ) ) )
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                @can('invoice_email_created')
                <?php
                $is_sent = $invoice->history()->where('comments', 'invoice-created')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="invoice-created-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.notification')}} @if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan

                @can('invoice_email_reminder')
                <?php
                $is_sent = $invoice->history()->where('comments', 'payment-reminder')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="payment-reminder-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.payment-reminder')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan

                @can('invoice_email_received')
                <?php
                $is_sent = $invoice->history()->where('comments', 'payment-received')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="payment-received-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.payment-received')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan
                
                <li role="separator" class="divider"></li>
                @can('invoice_email_overdue')
                <?php
                $is_sent = $invoice->history()->where('comments', 'payment-overdue')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="payment-overdue-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.payment-overdue')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan

                @can('invoice_email_refund')
                <?php
                $is_sent = $invoice->history()->where('comments', 'refund-proceeded')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="refund-proceeded-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.refund-proceeded')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan
              </ul>
            </div>
            @endif
            @endcan

            

            @can('invoice_sms_access')
            @if ( ! in_array( $controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' ) ) )
            <!-- SMS -->
            <div class="btn-group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.sms')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_sms_created')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'invoice-created')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="invoice-created-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.notification')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    <li role="separator" class="divider"></li>
                    @endcan
                    
                    @can('invoice_sms_reminder')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'payment-reminder')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="invoice-reminder-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.payment-reminder')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    @endcan
                    
                    @can('invoice_sms_received')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'payment-received')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="payment-received-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.payment-received')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    <li role="separator" class="divider"></li>
                    @endcan
                    
                    @can('invoice_sms_overdue')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'payment-overdue')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="payment-overdue-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.payment-overdue')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    @endcan
                    
                    @can('invoice_sms_refund')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'refund-proceeded')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="refund-processed-sms" data-invoice_id="{{$invoice->id}}">{{trans('custom.invoices.refund-proceeded')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                    @endcan
                </div>
            </div>
            @endif
            @endcan

            
            
            @can('invoice_changestatus_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_changestatus_paid')
                    <li><a class="dropdown-item" href="{{route('admin.invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'paid'])}}">{{trans('custom.invoices.paid')}}</a></li>
                    @endcan

                    @can('invoice_changestatus_due')
                    <li><a class="dropdown-item" href="{{route('admin.invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'unpaid'])}}">{{trans('custom.invoices.status-unpaid')}}</a></li>
                    @endcan

                    @can('invoice_changestatus_partial')
                    <li><a class="dropdown-item" href="{{route('admin.invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'partial'])}}">{{trans('custom.invoices.partial')}}</a></li>
                    @endcan

                    @can('invoice_changestatus_cancelled')
                    <li><a class="dropdown-item" href="{{route('admin.invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'cancelled'])}}">{{trans('custom.invoices.cancelled')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan

            @can('invoice_more_options')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-microchip" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.more')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_task_access')
                    <?php
                    $count = Modules\InvoiceAdditional\Entities\InvoiceTask::where('invoice_id', '=',$invoice->id )->count();
                    ?>
                    <li>
                        <a href="{{route('admin.invoice_tasks.index', $invoice->id)}}"  class="dropdown-item">
                        {{trans('custom.invoices.tasks')}}&nbsp;<span class="badge">{{$count}}</span>
                        </a></li>
                    @endcan
                    @can('invoice_reminder_access')
                    <?php
                    $count = Modules\InvoiceAdditional\Entities\InvoiceReminder::where('invoice_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.invoice_reminders.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.reminders')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                    @can('invoice_note_access')
                    <?php
                    $count = Modules\InvoiceAdditional\Entities\InvoiceNote::where('invoice_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.invoice_notes.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.notes')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                </div>
            </div>
            @endcan

            @can('invoice_preview')
           
            <a href="{{ route( 'admin.invoices.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" target="_blank"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
          
            @endcan

            @can('invoice_duplicate')
            <a href="{{ route( 'admin.invoices.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" onclick="return confirm(window.are_you_sure_duplicate)"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan

            @can('invoice_uploads')
            <a href="{{ route( 'admin.invoices.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>
            @endcan

            
             @can('invoice_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_pdf_view')
                     <li><a class="dropdown-item" href="{{route('admin.invoices.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('invoice_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.invoices.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan

                </div>
            </div>
            @endcan

            @can('invoice_print')
           <a href="{{route('admin.invoices.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            
            </div>
        </div>
</div>