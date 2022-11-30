<input type="hidden" name="invoice_id" value="{{$invoice->id}}" id="invoice_id">
<div class="invoice-wrapper" id="application_ajaxrender">
    <div class="content-body">
        <section class="card"> 

            <div id="invoice-template" class="card-block">

                @include('quotes::admin.quotes.invoice.invoice-menu', compact('invoice'))

                @include('quotes::admin.quotes.invoice.invoiced-notice', compact('invoice'))

                @include('quotes::admin.quotes.invoice.invoice-content', compact('invoice'))
                
            </div>
          
        </section>
    </div>
</div>
@include('quotes::admin.quotes.modal-loading', compact('invoice'))
@section('javascript')
    @parent
    @include('quotes::admin.quotes.scripts', compact('invoice'))
@stop
