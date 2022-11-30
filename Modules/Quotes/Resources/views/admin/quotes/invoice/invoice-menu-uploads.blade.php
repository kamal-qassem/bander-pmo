<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            <a href="{{route('admin.quotes.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('custom.invoices.app_back_to_quote')</a>
            
            @can('quote_edit')
            <a href="{{ route('admin.quotes.edit', $invoice->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('custom.invoices.quote-edit')}}</a>
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
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'lost'])}}">{{trans('quotes::custom.quotes.rejected')}}</a></li>
                    @endcan
                    
                    @can('quote_changestatus_dead')
                    <li><a class="dropdown-item markas" href="{{route('admin.quotes.changestatus', [ 'id' => $invoice->id, 'status' => 'dead'])}}">{{trans('quotes::custom.quotes.dead')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan

            @can('quote_preview')
            <a href="{{ route( 'admin.quotes.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>
            @endcan

            @can('quote_duplicate')
            <a href="{{ route( 'admin.quotes.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>
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
                    <li><a class="dropdown-item" href="{{route('admin.quotes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}">{{trans('custom.common.view-pdf')}}</a></li>
                    @endcan
                    
                    @can('quote_pdf_download')
                    <li><a class="dropdown-item" href="{{route('admin.quotes.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>
                    @endcan
                </div>
            </div>
            @endcan
            
            @can('quote_print')
            <a href="javascript:void(0);" class="btn btn-large btn-primary buttons-print ml-sm" onclick="printItem('invoice_pdf')" title="{{trans('custom.common.print')}}"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            @endcan
            </div>
        </div>
</div>