<input type="hidden" name="invoice_id" value="{{$invoice->id}}" id="invoice_id">
<div class="invoice-wrapper" id="application_ajaxrender">
    <div class="content-body">
        <section class="card"> 

            <div id="invoice-template" class="card-block">

                @include('contracts::admin.contracts.invoice.invoice-menu', compact('invoice'))

                @include('contracts::admin.contracts.invoice.invoiced-notice', compact('invoice'))

                @include('contracts::admin.contracts.invoice.invoice-content', compact('invoice'))
                
            </div>
        
            
        </section>
    </div>
</div>
@include('contracts::admin.contracts.modal-loading', compact('invoice'))
@section('javascript')
    @parent
    @include('contracts::admin.contracts.scripts', compact('invoice'))
@stop
