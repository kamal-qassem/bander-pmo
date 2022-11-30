<?php
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('cart_orders', 'Admin\CartOrdersController');
    Route::post('cart_orders_mass_destroy', ['uses' => 'Admin\CartOrdersController@massDestroy', 'as' => 'cart_orders.mass_destroy']);
    Route::post('cart_orders_restore/{id}', ['uses' => 'Admin\CartOrdersController@restore', 'as' => 'cart_orders.restore']);
    Route::delete('cart_orders_perma_del/{id}', ['uses' => 'Admin\CartOrdersController@perma_del', 'as' => 'cart_orders.perma_del']);
    Route::get('cart_orders_cancel/{slug}', ['uses' => 'Admin\CartOrdersController@cancelOrder', 'as' => 'cart_orders.cancel']);
    Route::post('cart_orders/searchproduct', ['uses' => 'Admin\CartOrdersController@searchProduct', 'as' => 'cart_orders.searchproduct']);
    Route::post('cart_orders/addtocart', ['uses' => 'Admin\CartOrdersController@addToCart', 'as' => 'cart_orders.addtocart']);
});
