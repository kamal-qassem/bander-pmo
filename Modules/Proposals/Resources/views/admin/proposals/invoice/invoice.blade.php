<input type="hidden" name="invoice_id" value="{{$invoice->id}}" id="invoice_id">
<div class="invoice-wrapper" id="application_ajaxrender">
    <div class="content-body">
        <section class="card"> 

            <div id="invoice-template" class="card-block">

                @include('proposals::admin.proposals.invoice.invoice-menu', compact('invoice'))

                @include('proposals::admin.proposals.invoice.invoiced-notice', compact('invoice'))

                @include('proposals::admin.proposals.invoice.invoice-content', compact('invoice'))
                
            </div>
          
        </section>
    </div>
</div>
@include('proposals::admin.proposals.modal-loading', compact('invoice'))
@section('javascript')
    @parent
    @include('proposals::admin.proposals.scripts', compact('invoice'))
@stop
