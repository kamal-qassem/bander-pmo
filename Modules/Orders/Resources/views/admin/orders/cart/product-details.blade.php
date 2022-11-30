
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3>{{$product->name}}</h3>
</div>
<div class="modal-body">



<div class="row">
@if($product->thumbnail)
<div class="col-lg-4">
    <div class="cui-ecommerce--catalog--item">
        <div class="cui-ecommerce--catalog--item--img">
            <div class="cui-ecommerce--catalog--item--like cui-ecommerce--catalog--item--like__selected">
                <i class="icmn-heart3 cui-ecommerce--catalog--item--like--liked"></i>
                <i class="icmn-heart4 cui-ecommerce--catalog--item--like--unliked"></i>
            </div>
            <a href="javascript: void(0);">
             <img src="{{ asset(env('UPLOAD_PATH').'/' . $product->thumbnail) }}" class="img-responsive">
            </a>
        </div>
    </div>
</div>
@endif
<div class="col-lg-8">

    <h3>{{$product->name}}</h3>
    <h4>{{digiCurrency($product->sale_price)}}</h4>
    @if( $product->description)
    <hr>
    <div class="cui-ecommerce--product--descr">{!! clean($product->description) !!}</div>
    <hr>
    @endif

    <div class="form-group">
        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="@lang('orders::global.orders.quantity')" min="1" max="{{$product->stock_quantity}}" value="{{$product->selected_quantity}}">
        <input type="hidden" id="product_id" name="product_id" value="{{$product->id}}">
        <input type="hidden" id="stock_quantity" name="stock_quantity" value="{{$product->stock_quantity}}">
    </div>
</div>
</div>
</div>
