<?php
$controller = getController('controller');
$method = getController('method');
?>

<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            @can('proposal_edit')
            <a href="{{ route('admin.proposals.edit', $invoice->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('proposals::custom.proposals.edit')}}</a>
            @endcan

            @can('proposal_email_access')
             @if ( ! in_array( $controller, array( 'ProposalTasksController', 'ProposalsRemindersController', 'ProposalsNotesController' ) ) )
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                @can('proposal_email_created')
                <?php
                $is_sent = $invoice->history()->where('comments', 'proposal-created')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="proposal-created-ema" data-invoice_id="{{$invoice->id}}">{{trans('custom.proposals.proposal-created')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan
              </ul>
            </div>
            @endif
            @endcan

            
            @can('proposal_sms_access')
             @if ( ! in_array( $controller, array( 'ProposalTasksController', 'ProposalsRemindersController', 'ProposalsNotesController' ) ) )
            <!-- SMS -->
            <div class="btn-group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.sms')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('proposal_sms_created')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'proposal-created')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="proposal-created-sms" data-invoice_id="{{$invoice->id}}">{{trans('proposals::custom.proposals.send-proposal')}}</a></li>
                    @endcan

                    @can('proposal_sms_accepted')
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="proposal-accepted-sms" data-invoice_id="{{$invoice->id}}">{{trans('proposals::custom.proposals.proposal-accepted')}}</a></li>
                    @endcan
                    @can('proposal_sms_cancelled')
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="proposal-cancelled-sms" data-invoice_id="{{$invoice->id}}">{{trans('proposals::custom.proposals.proposal-cancelled')}}</a></li>
                    @endcan
                </div>

            </div>
            @endif
            @endcan            
            
            @can('proposal_changestatus_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('proposal_changestatus_delivered')
                    <li><a class="dropdown-item markas" href="{{route('admin.proposals.changestatus', [ 'id' => $invoice->id, 'status' => 'delivered'])}}">{{trans('proposals::custom.proposals.delivered')}}</a></li>
                    @endcan
                    
                    @can('proposal_changestatus_onhold')
                    <li><a class="dropdown-item markas" href="{{route('admin.proposals.changestatus', [ 'id' => $invoice->id, 'status' => 'on-hold'])}}">{{trans('proposals::custom.proposals.on-hold')}}</a></li>
                    @endcan
                    
                    @can('proposal_changestatus_accepted')
                    <li><a class="dropdown-item markas" href="{{route('admin.proposals.changestatus', [ 'id' => $invoice->id, 'status' => 'accepted'])}}">{{trans('proposals::custom.proposals.accepted')}}</a></li>
                    @endcan
                    
                    @can('proposal_changestatus_rejected')
                    <li><a class="dropdown-item markas" href="{{route('admin.proposals.changestatus', [ 'id' => $invoice->id, 'status' => 'rejected'])}}">{{trans('proposals::custom.proposals.rejected')}}</a></li>
                    @endcan
                    
                    @can('proposal_changestatus_lost')
                    <li><a class="dropdown-item markas" href="{{route('admin.proposals.changestatus', [ 'id' => $invoice->id, 'status' => 'lost'])}}">{{trans('proposals::custom.proposals.lost')}}</a></li>
                    @endcan
                    
                    @can('proposal_changestatus_dead')
                    <li><a class="dropdown-item markas" href="{{route('admin.proposals.changestatus', [ 'id' => $invoice->id, 'status' => 'dead'])}}">{{trans('proposals::custom.proposals.dead')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan
    @if( empty( $invoice->quote_id ) || empty( $invoice->invoice_id ) )
      @can('proposal_convertinvoice')
         @if ( ! in_array( $controller, array( 'ProposalTasksController', 'ProposalsRemindersController', 'ProposalsNotesController' ) ) )
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                <i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;{{trans('proposals::custom.proposals.convert-to')}}&nbsp;<span class="caret"></span>
                </button> 
                <div class="dropdown-menu">
                @if( empty( $invoice->invoice_id ) )
                    @can('proposal_convertinvoice')
                        <?php
                        $invoice_url = '';
                        if ( ! empty( $invoice->invoice_id ) ) {
                        $invoice_url = route('admin.invoices.show', [$invoice->invoice_id]);
                        }
                        ?>
                        @can('proposal_convertinvoice')
                        <li><a href="javascript:void(0);" data-url="{{route('admin.proposals.convertinvoice', ['slug' => $invoice->slug, 'type' => 'convertsavedraft'])}}" class="dropdown-item markas convertProposal" title="{{trans('proposals::custom.proposals.convert-to-invoice')}}" data-proposal_id="{{$invoice->id}}" data-slug="{{$invoice->slug}}" data-invoice_id="{{$invoice->invoice_id}}" data-invoice_url="{{$invoice_url}}">{{trans('proposals::custom.proposals.convert-to-invoice-save-draft')}}</a></li>
                        @endcan
                        
                        @can('proposal_convertinvoice')
                        <li><a href="javascript:void(0);" data-url="{{route('admin.proposals.convertinvoice', ['slug' => $invoice->slug, 'type' => 'convert'])}}" class="dropdown-item markas convertProposal" title="{{trans('proposals::custom.proposals.convert-to-invoice')}}" data-proposal_id="{{$invoice->id}}" data-slug="{{$invoice->slug}}" data-invoice_id="{{$invoice->invoice_id}}" data-invoice_url="{{$invoice_url}}">{{trans('proposals::custom.proposals.convert-invoice')}}</a></li>
                         <li role="separator" class="divider"></li>
                        @endcan
                    @endcan
                @endif
                

                @if( empty( $invoice->quote_id ) && empty( $invoice->invoice_id ) )
                    @can('proposal_convertquote')
                        <?php
                        $quote_url = '';
                        if ( ! empty( $quote->quote_id ) ) {
                        $quote_url = route('Quotes::admin.quotes.show', [$quote->quote_id]);
                        }
                        ?>
                        @can('proposal_convertquote')
                        <li><a href="javascript:void(0);" data-url="{{route('admin.proposals.convertquote', ['slug' => $invoice->slug, 'type' => 'convertsavedraft'])}}" class="dropdown-item markas convertQuote" title="{{trans('proposals::custom.proposals.convert-to-quote')}}" data-proposal_id="{{$invoice->id}}" data-slug="{{$invoice->slug}}" data-quinvoiceote_id="{{$invoice->quote_id}}" data-quote_url="{{$quote_url}}">{{trans('proposals::custom.proposals.convert-to-quote-save-draft')}}</a></li>
                        @endcan
                        
                        @can('proposal_convertquote')
                        <li><a href="javascript:void(0);" data-url="{{route('admin.proposals.convertquote', ['slug' => $invoice->slug, 'type' => 'convert'])}}" class="dropdown-item markas convertQuote" title="{{trans('proposals::custom.proposals.convert-to-quote')}}" data-proposal_id="{{$invoice->id}}" data-slug="{{$invoice->slug}}" data-quote_id="{{$invoice->quote_id}}" data-quote_url="{{$quote_url}}">{{trans('proposals::custom.proposals.convert-quote')}}</a></li>
                        @endcan
                    @endcan
                @endif
                </div>
            </div>
        @endif
        @endcan
    @endif



        
 
            @can('proposal_more_options')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-microchip" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.more')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('proposal_task_access')
                    <?php
                    $count = Modules\Proposals\Entities\ProposalTask::where('proposal_id', '=',$invoice->id )->count();
                    ?>
                    <li>
                        <a href="{{route('admin.proposal_tasks.index', $invoice->id)}}"  class="dropdown-item">
                        {{trans('custom.invoices.tasks')}}&nbsp;<span class="badge">{{$count}}</span>
                        </a></li>
                    @endcan
                    @can('proposal_reminder_access')
                    <?php
                    $count = Modules\Proposals\Entities\ProposalsReminder::where('proposal_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.proposal_reminders.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.reminders')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                    @can('proposals_note_access')
                    <?php
                    $count = Modules\Proposals\Entities\ProposalsNote::where('proposal_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.proposals_notes.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.notes')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                </div>
            </div>
            @endcan
                    
            @can('proposal_preview')
            <a href="{{ route( 'admin.proposals.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" target="_blank"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan

            @can('proposal_duplicate')
            <a href="{{ route( 'admin.proposals.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" onclick="return confirm(window.are_you_sure_duplicate)"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan


            @can('proposal_upload')
            <a href="{{ route( 'admin.proposals.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>
            @endcan

            @can('proposal_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('proposal_pdf_view')
                    <li><a class="dropdown-item" href="{{route('admin.proposals.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('proposal_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.proposals.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan

                </div>
            </div>
            @endcan

            @can('proposal_print')
            <a href="{{route('admin.proposals.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            </div>
        </div>
</div>