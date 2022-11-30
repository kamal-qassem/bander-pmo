<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            <a href="{{route('admin.recurring_invoices.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('custom.invoices.app_back_to_recurring_invoice')</a>
            
            @can('recurring_invoice_edit')
            <a href="{{ route('admin.recurring_invoices.edit', $invoice->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('custom.invoices.recurring-edit')}}</a>
            @endcan

            @can('recurring_invoice_changestatus_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('recurring_invoice_changestatus_paid')
                    <li><a class="dropdown-item" href="{{route('admin.recurring_invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'paid'])}}">{{trans('custom.invoices.paid')}}</a></li>
                    @endcan
                    
                    @can('recurring_invoice_changestatus_due')
                    <li><a class="dropdown-item" href="{{route('admin.recurring_invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'due'])}}">{{trans('custom.invoices.due')}}</a></li>
                    @endcan
                    
                    @can('recurring_invoice_changestatus_partial')
                    <li><a class="dropdown-item" href="{{route('admin.recurring_invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'partial'])}}">{{trans('custom.invoices.partial')}}</a></li>
                    @endcan
                    
                    @can('recurring_invoice_changestatus_cancelled')
                    <li><a class="dropdown-item" href="{{route('admin.recurring_invoices.changestatus', [ 'id' => $invoice->id, 'status' => 'cancelled'])}}">{{trans('custom.invoices.cancelled')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan

             @can('recurring_invoice_preview')
            <a href="{{ route( 'admin.recurring_invoices.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan

            @can('recurring_invoice_duplicate')
            <a href="{{ route( 'admin.recurring_invoices.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan

            @can('recurring_invoice_uploads')
            <a href="{{ route( 'admin.recurring_invoices.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>
            @endcan

            @can('recurring_invoice_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('recurring_invoice_pdf_view')
                    <li><a class="dropdown-item" href="{{route('admin.recurring_invoices.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('recurring_invoice_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.recurring_invoices.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan

                </div>
            </div>
            @endcan

             @can('recurring_invoice_print')
            <a href="javascript:void(0);" class="btn btn-large btn-primary buttons-print ml-sm" onclick="printItem('invoice_pdf')" title="{{trans('custom.common.print')}}"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            </div>
        </div>
</div>