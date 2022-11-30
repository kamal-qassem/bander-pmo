<?php
$controller = getController('controller');
$method = getController('method');
?>

<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            @can('contract_edit')
            <a href="{{ route('admin.contracts.edit', $invoice->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('contracts::custom.contracts.edit')}}</a>
            @endcan

            @can('contract_email_access')
             @if ( ! in_array( $controller, array( 'ContractTasksController', 'ContractsRemindersController', 'ContractsNotesController' ) ) )
            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                @can('contract_email_created')
                <?php
                $is_sent = $invoice->history()->where('comments', 'contract-created')->where('operation_type', 'email')->first();
                ?>
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="contract-created-ema" data-invoice_id="{{$invoice->id}}">{{trans('contracts::custom.contracts.contract-created')}}@if( $is_sent ) (@lang('custom.messages.sent')) @endif</a></li>
                @endcan
              </ul>
            </div>
            @endif
            @endcan

            
            @can('contract_sms_access')
             @if ( ! in_array( $controller, array( 'ContractTasksController', 'ContractsRemindersController', 'ContractsNotesController' ) ) )
            <!-- SMS -->
            <div class="btn-group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.sms')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('contract_sms_created')
                    <?php
                    $is_sent = $invoice->history()->where('comments', 'contract-created')->where('operation_type', 'sms')->first();
                    ?>
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="contract-created-sms" data-invoice_id="{{$invoice->id}}">{{trans('contracts::custom.contracts.send-contract')}}</a></li>
                    @endcan

                    @can('contract_sms_accepted')
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="contract-accepted-sms" data-invoice_id="{{$invoice->id}}">{{trans('contracts::custom.contracts.contract-accepted')}}</a></li>
                    @endcan
                    @can('contract_sms_cancelled')
                    <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="contract-cancelled-sms" data-invoice_id="{{$invoice->id}}">{{trans('contracts::custom.contracts.contract-cancelled')}}</a></li>
                    @endcan
                </div>

            </div>
            @endif
            @endcan            
            
            @can('contract_changestatus_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('contract_changestatus_delivered')
                    <li><a class="dropdown-item markas" href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'delivered'])}}">{{trans('contracts::custom.contracts.delivered')}}</a></li>
                    @endcan
                    
                    @can('contract_changestatus_onhold')
                    <li><a class="dropdown-item markas" href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'on-hold'])}}">{{trans('contracts::custom.contracts.on-hold')}}</a></li>
                    @endcan
                    
                    @can('contract_changestatus_accepted')
                    <li><a class="dropdown-item markas" href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'accepted'])}}">{{trans('contracts::custom.contracts.accepted')}}</a></li>
                    @endcan
                    
                    @can('contract_changestatus_rejected')
                    <li><a class="dropdown-item markas" href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'rejected'])}}">{{trans('contracts::custom.contracts.rejected')}}</a></li>
                    @endcan
                    
                    @can('contract_changestatus_lost')
                    <li><a class="dropdown-item markas" href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'lost'])}}">{{trans('contracts::custom.contracts.lost')}}</a></li>
                    @endcan
                    
                    @can('contract_changestatus_dead')
                    <li><a class="dropdown-item markas" href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'dead'])}}">{{trans('contracts::custom.contracts.dead')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan

            @can('contract_more_options')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-microchip" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.more')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('contract_task_access')
                    <?php
                    $count = Modules\Contracts\Entities\ContractTask::where('contract_id', '=',$invoice->id )->count();
                    ?>
                    <li>
                        <a href="{{route('admin.contract_tasks.index', $invoice->id)}}"  class="dropdown-item">
                        {{trans('custom.invoices.tasks')}}&nbsp;<span class="badge">{{$count}}</span>
                        </a></li>
                    @endcan
                    @can('contract_reminder_access')
                    <?php
                    $count = Modules\Contracts\Entities\ContractsReminder::where('contract_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.contract_reminders.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.reminders')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                    @can('contracts_note_access')
                    <?php
                    $count = Modules\Contracts\Entities\ContractsNote::where('contract_id', '=',$invoice->id )->count();
                    ?>
                    <li><a href="{{route('admin.contracts_notes.index', $invoice->id)}}"  class="dropdown-item">{{trans('custom.invoices.notes')}}&nbsp;<span class="badge">{{$count}}</span></a></li>
                    @endcan
                </div>
            </div>
            @endcan
               
            @can('contract_preview')
            <a href="{{ route( 'admin.contracts.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" target="_blank"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan

            @can('contract_duplicate')
            <a href="{{ route( 'admin.contracts.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info" onclick="return confirm(window.are_you_sure_duplicate)"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan


            @can('contract_upload')
            <a href="{{ route( 'admin.contracts.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>
            @endcan

            @can('contract_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('contract_pdf_view')
                    <li><a class="dropdown-item" href="{{route('admin.contracts.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('contract_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.contracts.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan

                </div>
            </div>
            @endcan

            @can('contract_print')
            <a href="{{route('admin.contracts.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            </div>
        </div>
</div>