<input type="hidden" name="invoice_id" value="{{$invoice->id}}" id="invoice_id">
<div class="invoice-wrapper" id="application_ajaxrender">
    <div class="content-body">
        <section class="card"> 

            <div id="invoice-template" class="card-block">

                @include('admin.purchase_orders.invoice.invoice-menu', compact('invoice'))

                @include('admin.purchase_orders.invoice.invoice-content', compact('invoice'))
                
            </div>
            
            <div class="col-xl-12">
            @include ('admin.purchase_orders.invoice.invoice-transactions', compact('invoice'))
        </div>

            @include ('admin.purchase_orders.invoice.invoice-access-log', compact('invoice'))
            
        </section>
    </div>
</div>
@include('admin.purchase_orders.modal-loading', compact('invoice'))
@section('javascript')
    @parent
    @include('admin.purchase_orders.scripts', compact('invoice'))
@stop
