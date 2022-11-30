@extends('layouts.app-9')

@section('content')
    <div class="text-right">
    <br>
    <a href="{{route('admin.credit_notes.show', $invoice->id)}}" class="btn btn-primary ml-sm no-shadow no-border"><i class="fa fa-long-arrow-left"></i> @lang('custom.credit_notes.app_back_to_credit_note')</a>
    
    @can('invoice_pdf_download')
    <a href="{{route('admin.credit_notes.invoicepdf', $invoice->slug)}}" class="btn btn-primary buttons-pdf ml-sm"><i class="fa fa-file-pdf-o"></i> @lang('custom.common.download-pdf')</a>
    @endcan
    
    @can('invoice_pdf_view')
    <a href="{{route('admin.credit_notes.invoicepdf', ['slug' => $invoice->slug, 'operation' => 'view'] )}}"  class="btn btn-primary buttons-excel ml-sm" target="_blank"><i class="fa fa-file-text-o"></i> @lang('custom.common.view-pdf')</a>
    @endcan
    
    @can('invoice_print')
    <a href="javascript:void(0);" class="btn btn-primary buttons-print ml-sm" onclick="printItem('invoice_pdf')"><i class="fa fa-print"></i> @lang('custom.common.print')</a>
    @endcan
    </div>
    @include('admin.credit_notes.invoice.invoice-content', compact('invoice'))
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