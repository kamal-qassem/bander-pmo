@extends('layouts.app-9')

@section('content')
  
<div class="text-right">
    <a href="{{route('admin.contracts.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('contracts::custom.contracts.app_back_to_contract')</a>

    @can('contract_pdf_download')
    <a href="{{route('admin.contracts.invoicepdf', $invoice->slug)}}" class="btn btn-info buttons-pdf ml-sm"><i class="fa fa-file-pdf-o"></i> @lang('custom.common.download-pdf')</a>
    @endcan

    @can('contract_pdf_view')
    <a href="{{route('admin.contracts.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" class="btn btn-warning buttons-excel ml-sm"><i class="fa fa-file-text-o"></i> @lang('custom.common.view-pdf')</a>
    @endcan

    @can('contract_changestatus_accepted')
    <a href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'accepted'])}}" class="btn btn-success buttons-excel ml-sm">{{trans('contracts::custom.contracts.accepted')}}</a>
    @endcan

    @can('contract_changestatus_rejected')
    <a href="{{route('admin.contracts.changestatus', [ 'id' => $invoice->id, 'status' => 'rejected'])}}" class="btn btn-danger buttons-excel ml-sm">{{trans('contracts::custom.contracts.rejected')}}</a>
    @endcan

    @can('contract_print')
    <a href="javascript:void(0);" class="btn btn-primary buttons-print ml-sm" onclick="printItem('invoice_pdf')"><i class="fa fa-print"></i> @lang('custom.common.print')</a>
    @endcan
</div>
       
    @include('contracts::admin.contracts.invoice.invoice-content', compact('invoice'))
@stop

@section('javascript')
    @parent
    <script type="text/javascript">
    function printItem( elem ) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600' );
        mywindow.document.write('<html><head>' );
        mywindow.document.write('<title>' + document.title  + '</title>' );
        mywindow.document.write('</head><body >' );
       
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>' );

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
   
    </script>
@stop