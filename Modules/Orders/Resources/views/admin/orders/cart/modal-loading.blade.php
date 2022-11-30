<!-- Modal HTML -->
<div id="loadingModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content" id="loadingModalContent">                        
            <div id="loading_icon" style="display: none;"><img src="{{asset('images/loading.gif')}}"></div>
            <div id="content"></div>

            <div class="modal-footer">
		        <button id="addToCart" class="btn btn-primary"><i class="fa fa-shopping-cart"></i>&nbsp;{{trans('orders::global.orders.add-to-cart')}}</button>
		        <button type="button" data-dismiss="modal" class="btn">{{trans('custom.common.close')}}</button>
			</div>
        </div>
    </div>
</div>


