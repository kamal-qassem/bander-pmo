<?php
$controller = getController('controller');
$method = getController('method');
?>

<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            @can('quote_edit')
            <a href="{{ route('admin.quotes.edit', $invoice->id) }}" class="btn btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('quotes::custom.quotes.edit')}}</a>
            @endcan

            @can('quote_email_access')
             @if ( ! in_array( $controller, array( 'QuoteTasksController', 'QuotesRemindersController', 'QuotesNotesController' ) ) )
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                @can('quote_email_created')
                <?php
                $is_sent = $invoice->history()->where('comments', 'quote-created')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="quote-created-ema" data-invoice_id="{{$invoice->id}}">{{trans('quotes::custom.quotes.quote-created')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan
              </ul>
            </div>
            @endif
            @endcan

            
            @can('quote_sms_access')
             @if ( ! in_array( $controller, array( 'QuoteTasksController', 'QuotesRemindersController', 'QuotesNotesController' ) ) )
            <!-- SMS -->
            <div class="btn-group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.sms')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('quote_sms_created')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'quote-created')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="quote-created-sms" data-invoice_id="{{$invoice->id}}">{{trans('quotes::custom.quotes.send-quote')}}</a></li>
                    @endcan

                    @can('quote_sms_accepted')
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="quote-accepted-sms" data-invoice_id="{{$invoice->id}}">{{trans('quotes::custom.quotes.quote-accepted')}}</a></li>
                    @endcan
                    @can('quote_sms_cancelled')
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="quote-cancelled-sms" data-invoice_id="{{$invoice->id}}">{{trans('quotes::custom.quotes.quote-cancelled')}}</a></li>
                    @endcan
                </div>

            </div>
            @endif
            @endcan            
            
            @can('quote_changestatus_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('quote_changestatus_delivered')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'delivered'])}}">{{trans('quotes::custom.quotes.delivered')}}</a></li>
                    @endcan
                    
                    @can('quote_changestatus_onhold')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'on-hold'])}}">{{trans('quotes::custom.quotes.on-hold')}}</a></li>
                    @endcan
                    
                    @can('quote_changestatus_accepted')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'accepted'])}}">{{trans('quotes::custom.quotes.accepted')}}</a></li>
                    @endcan
                    
                    @can('quote_changestatus_rejected')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'rejected'])}}">{{trans('quotes::custom.quotes.rejected')}}</a></li>
                    @endcan
                    
                    @can('quote_changestatus_lost')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'lost'])}}">{{trans('quotes::custom.quotes.lost')}}</a></li>
                    @endcan
                    
                    @can('quote_changestatus_dead')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'dead'])}}">{{trans('quotes::custom.quotes.dead')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan

            @if( empty( $invoice->invoice_id ) )
                @can('quote_convertinvoice')
                 @if ( ! in_array( $controller, array( 'QuoteTasksController', 'QuotesRemindersController', 'QuotesNotesController' ) ) )
                <div class="btn-group ">
                    @if( isPluginActive('invoice') )
                    <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;{{trans('quotes::custom.quotes.convert-to-invoice')}}&nbsp;<span class="caret"></span>
                    </button>
                    @endif
                    <div class="dropdown-menu">
                        <?php
                        $invoice_url = '';
                        if ( ! empty( $invoice->invoice_id ) ) {
                            $invoice_url = route('admin.invoices.show', [$invoice->invoice_id]);
                        }
                        ?>
                        @can('quote_convertinvoice')
                        <li><a href="javascript:void(0);" data-url="{{route('admin.quotes.convertinvoice', ['slug' => $invoice->slug, 'type' => 'convertsavedraft'])}}" class="dropdown-item markas convertQuote" title="{{trans('quotes::custom.quotes.convert-to-invoice')}}" data-quote_id="{{$invoice->id}}" data-slug="{{$invoice->slug}}" data-invoice_id="{{$invoice->invoice_id}}" data-invoice_url="{{$invoice_url}}">{{trans('quotes::custom.quotes.convert-to-invoice-save-draft')}}</a></li>
                        @endcan
                        
                        @can('quote_convertinvoice')
                        <li><a href="javascript:void(0);" data-url="{{route('admin.quotes.convertinvoice', ['slug' => $invoice->slug, 'type' => 'convert'])}}" class="dropdown-item markas convertQuote" title="{{trans('quotes::custom.quotes.convert-to-invoice')}}" data-quote_id="{{$invoice->id}}" data-slug="{{$invoice->slug}}" data-invoice_id="{{$invoice->invoice_id}}" data-invoice_url="{{$invoice_url}}">{{trans('quotes::custom.quotes.convert')}}</a></li>
                        @endcan
                    </div>
                </div>
                @endif
                @endcan
            @endif

            @can('quote_more_options')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-microchip" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.more')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('quote_task_access')
                    <?php
                    $count = Modules\Quotes\Entities\QuoteTask::where('quote_id', '=',$invoice->id )->count();
                    ?>
                    <li>
                        <a href="{{route('admin.quote_tasks.index', $invoice->id)}}"  class="dropdown-item">
                        {{trans('custom.invoices.tasks')}}&nbsp;<span class="badge">{{$count}}</span>
                        </a></li>
                    @endcan
                    @can('quote_reminder_access')
                    <?php
                    $count = Modules\Quotes\Entities\QuotesReminder::where('quote_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.quote_reminders.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.reminders')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                    @can('quotes_note_access')
                    <?php
                    $count = Modules\Quotes\Entities\QuotesNote::where('quote_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.quotes_notes.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.notes')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                </div>
            </div>
            @endcan
                    
            @can('quote_preview')
            <a href="{{ route( 'admin.quotes.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" target="_blank"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan

            @can('quote_duplicate')
            <a href="{{ route( 'admin.quotes.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" onclick="return confirm(window.are_you_sure_duplicate)"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan


            @can('quote_upload')
            <a href="{{ route( 'admin.quotes.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>
            @endcan

            @can('quote_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('quote_pdf_view')
                    <li><a class="dropdown-item" href="{{route('admin.quotes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('quote_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.quotes.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan

                </div>
            </div>
            @endcan

            @can('quote_print')
            <a href="{{route('admin.quotes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            </div>
        </div>
</div>