<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            <a href="{{route('admin.purchase_orders.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('custom.invoices.app_back_to_purchase_orders')</a>
            
            <a href="{{ route('admin.purchase_orders.edit', $invoice->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('custom.invoices.purchase-order-edit')}}</a> 

            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'paid'])}}">{{trans('custom.invoices.paid')}}</a></li>
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'due'])}}">{{trans('custom.invoices.due')}}</a></li>
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'partial'])}}">{{trans('custom.invoices.partial')}}</a></li>
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.changestatus', [ 'id' => $invoice->id, 'status' => 'cancelled'])}}">{{trans('custom.invoices.cancelled')}}</a></li>
                </div>
            </div>

            <a href="{{ route( 'admin.purchase_orders.preview', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-street-view"></i>{{trans('custom.common.preview')}}</a>

            <a href="{{ route( 'admin.purchase_orders.duplicate', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-info"><i class="fa fa-clone"></i> {{trans('custom.common.duplicate')}}</a>

            <a href="{{ route( 'admin.purchase_orders.upload', [ 'slug' => $invoice->slug ] ) }}" class="btn btn-success" title="{{trans('custom.invoices.upload-documents')}}">                                
                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.upload-documents')}}
            </a>

            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}">{{trans('custom.common.view-pdf')}}</a></li>
                    
                    <li><a class="dropdown-item" href="{{route('admin.purchase_orders.invoicepdf', $invoice->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>

                </div>
            </div>
            <a href="javascript:void(0);" class="btn btn-large btn-primary buttons-print ml-sm" onclick="printItem('invoice_pdf')" title="{{trans('custom.common.print')}}"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            </div>
        </div>
</div>