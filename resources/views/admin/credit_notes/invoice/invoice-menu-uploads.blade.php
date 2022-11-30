<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            <a href="{{route('admin.credit_notes.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('custom.credit_notes.app_back_to_invoice')</a>
            
            @can('invoice_edit')
            <a href="{{ route('admin.credit_notes.edit', $invoice->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('custom.credit_notes.edit')}}</a>
            @endcan 

            @can('invoice_edit')
            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_changestatus_paid')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.changestatus', [ 'id' => $invoice->id, 'status' => 'paid'])}}">{{trans('custom.credit_notes.paid')}}</a></li>
                    @endcan 
                    @can('invoice_changestatus_partial')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.changestatus', [ 'id' => $invoice->id, 'status' => 'partial'])}}">{{trans('custom.credit_notes.partial')}}</a></li>
                    @endcan 
                  
                </div>
            </div>
            @endcan 

            @can('invoice_preview')
            <a href="{{ route( 'admin.credit_notes.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan 

            @can('invoice_duplicate')
            <a href="{{ route( 'admin.credit_notes.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
            @endcan 

            @can('invoice_uploads')
            <a href="{{ route( 'admin.credit_notes.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.credit_notes.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.credit_notes.upload-documents')}}
            </a>
            @endcan 

            @can('invoice_pdf_access')
            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    @can('invoice_pdf_view')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan 
                    
                    @can('invoice_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.credit_notes.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan 
               @can('invoice_print')
           <a href="{{route('admin.credit_notes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan

            </div>
        </div>
</div>