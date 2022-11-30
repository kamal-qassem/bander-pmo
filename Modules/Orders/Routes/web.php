<?php
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('orders', 'Admin\OrdersController');
    Route::post('orders_mass_destroy', ['uses' => 'Admin\OrdersController@massDestroy', 'as' => 'orders.mass_destroy']);
    Route::post('orders_restore/{id}', ['uses' => 'Admin\OrdersController@restore', 'as' => 'orders.restore']);
    Route::delete('orders_perma_del/{id}', ['uses' => 'Admin\OrdersController@perma_del', 'as' => 'orders.perma_del']);
    Route::get('orders_cancel/{slug}/{status?}', ['uses' => 'Admin\OrdersController@cancelOrder', 'as' => 'orders.cancel']);
	Route::get('orders_retry/{slug}', ['uses' => 'Admin\OrdersController@retryOrder', 'as' => 'orders.retry']);
	
   
    Route::post('orders/searchproduct', ['uses' => 'Admin\OrdersController@searchProduct', 'as' => 'orders.searchproduct']);
    Route::post('orders/addtocart', ['uses' => 'Admin\OrdersController@addToCart', 'as' => 'orders.addtocart']);
    Route::get('orders_updatecart', ['uses' => 'Admin\OrdersController@updateCart', 'as' => 'orders.updatecart']);
    Route::post('orders/remove-cart-product', ['uses' => 'Admin\OrdersController@removeFromCart', 'as' => 'orders.remove_from_cart']);

    Route::post('orders/refresh-stats', [ 'uses' => 'Admin\OrdersController@refreshStats', 'as' => 'orders.refresh-stats'] ); 

    Route::post('orders/update-cart-product', ['uses' => 'Admin\OrdersController@updateCartProduct', 'as' => 'orders.update_cart_product']);
    Route::get('orders_clear_cart', ['uses' => 'Admin\OrdersController@clearCart', 'as' => 'orders.clear_cart']);
    Route::get('shop/checkout', ['uses' => 'Admin\OrdersController@checkOut', 'as' => 'orders.checkout']);

    Route::post('shop/paynow/{slug}/{module}', ['uses' => 'Admin\OrdersController@payNow', 'as' => 'shop.paynow']);
    Route::post('shop/process-payment/{slug}/{module}', ['uses' => 'Admin\OrdersController@processPayment', 'as' => 'shop.process-payment']);
    Route::get('shop/payment-payu/{slug}/{module}', ['uses' => 'Admin\OrdersController@processPayu', 'as' => 'shop.process-payu']);

    Route::get('shop/payment-failed/{slug}/{module}', ['uses' => 'Admin\OrdersController@paymentFailed', 'as' => 'shop.payment-failed']);
    Route::get('shop/payment-cancelled/{slug}/{module}', ['uses' => 'Admin\OrdersController@paymentCancelled', 'as' => 'shop.payment-cancelled']);
    Route::get('shop/payment-success/{slug}/{module}', ['uses' => 'Admin\OrdersController@paymentSuccess', 'as' => 'shop.payment-success']);
    Route::get('orders/payment-now/{slug}', ['uses' => 'Admin\OrdersController@paymentNow', 'as' => 'orders.payment-now']);
    Route::post('orders/process-payment-now/{slug}/{module}', ['uses' => 'Admin\OrdersController@processPaymentNow', 'as' => 'orders.process-payment-now']);
	
	Route::get('orders/re-order/{slug}', ['uses' => 'Admin\OrdersController@reOrder', 'as' => 'orders.reorder']);
    Route::post('orders/save-payment', ['uses' => 'Admin\OrdersController@savePayment', 'as' => 'orders.save-payment']);

});
