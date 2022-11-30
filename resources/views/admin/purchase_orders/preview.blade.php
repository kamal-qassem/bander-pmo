@extends('layouts.app-9')

@section('content')
    <div class="text-right">
    <br>
    <a href="{{route('admin.purchase_orders.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('custom.purchase_orders.app_back_to_quote')</a>
    <a href="{{route('admin.purchase_orders.invoicepdf', $invoice->slug)}}" class="btn btn-primary buttons-pdf ml-sm"><i class="fa fa-file-pdf-o"></i> @lang('custom.common.download-pdf')</a>
    <a href="{{route('admin.purchase_orders.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}" class="btn btn-primary buttons-excel ml-sm"><i class="fa fa-file-text-o"></i> @lang('custom.common.view-pdf')</a>
    <a href="javascript:void(0);" class="btn btn-primary buttons-print ml-sm" onclick="printItem('invoice_pdf')"><i class="fa fa-print"></i> @lang('custom.common.print')</a>
    </div>
    @include('admin.purchase_orders.invoice.invoice-content', compact('invoice'))
@stop

@section('javascript')
    @parent
    <script type="text/javascript">
    function printItem( elem ) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600' );
        mywindow.document.write('<html><head><title>' + document.title  + '</title>' );
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