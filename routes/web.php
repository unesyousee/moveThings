<?php

use Illuminate\Http\Request;

Route::post('app_link', 'AppController@AppLink');
Auth::routes();
Route::post('carrierUser_register', 'CarrierUserController@SiteRegister');
Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'log.actions']],
    function () {
        Route::get('workers', 'CarrierUserController@workers')->name('workers');
        Route::get('drivers', 'CarrierUserController@drivers')->name('drivers');
        Route::get('carrier/{id}', 'CarrierUserController@show')->name('carrierUserShow');
        Route::delete('discount', 'DiscountController@destroy')->name('discountMultiDestroy');
        Route::get('searchuser', 'UserController@search')->name('user.search');
        Route::get('searchdriver', 'CarrierUserController@driverSearch')->name('driver.search');
        Route::get('order_search', 'OrderController@search')->name('order.search');
        Route::get('', 'dasboardController@index')->name('dashboard');
        Route::post('seen_all', 'OrderController@seenAll')->name('seen_all');
        Route::post('add_worker', 'CarrierUserController@addWorker')->name('addWorker');
        Route::get('export', 'OrderController@export')->name('orderExport');
        Route::get('notification', 'OrderController@notification')->name('notification');
        Route::get('all_user_exel', 'UserController@AllUserExel')->name('all_user_exel');
        Route::resource('notifications', 'notificationController');
        Route::get('admins', 'UserController@admins');
        Route::get('showadmins/{id}', 'UserController@showAdmin')->name('showAdmin');
        Route::any('nobaar_accounting','TransactionController@nobaarAccounting')->name('nobaarAccounting');
        Route::group(['middleware' => ['log.actions']],
            function () {
                Route::patch('UpdateDate/{id}',"orderController@dateUpdate")->name("dateUpdate");
                Route::post('third_discount', 'DiscountController@thirdDiscount')->name('thirdDiscount');
                Route::post('store_customer','UserController@storeCustomer')->name('storeCustomer');
                Route::post('upload_file','FileController@upload')->name('uploadFile');
                Route::get('/order/tracked/{id}', 'OrderController@tracked');
                Route::post('admin/{id}', 'UserController@adminDestroy')->name('adminDestroy');
                Route::delete('carrieruser/{id}', 'CarrierUserController@destroy');
                Route::resource('transaction', 'TransactionController');
                Route::resource('thirdparty', 'thirdpartyController');
                Route::resource('service', 'ServiceController');
                Route::resource('prices', 'priceController');
                Route::resource('heavythings', 'HeavyThingsController');
                Route::resource('users', 'UserController');
                Route::resource('orders', 'OrderController', ['except' => ['distroy']]);
                Route::resource('discount', 'DiscountController', ['except' => ['distroy']]);
                Route::post('update_price', 'OrderController@updatePrice')->name('updatePrice');
                Route::post('checkout', 'TransactionController@driverCheckout')->name('driverCheckout');
                Route::post('changedriver', 'OrderController@changeDriver')->name('change_driver');
                Route::post('addtowallet', 'TransactionController@addToWallet')->name('addToWallet');
                Route::post('add-admin', 'UserController@addAdmin')->name('addAdmin');
                Route::post('manual_info/{id}', 'OrderController@manualInfo')->name('manualInfo');
                Route::post('manual_user_info/{id}', 'UserController@manualInfo')->name('manual_user_info');
                Route::put('update_worker/{id}', 'CarrierUserController@updateWorker')->name('updateWorker');
                Route::delete('remove_worker', 'CarrierUserController@removeWorker')->name('removeWorker');
                Route::resource('area', 'AreaController');
                Route::post('coords', 'AreaController@store')->name('coords');
                Route::post('add_order_comment', 'CommentController@addOrderComment')->name('addOrderComment');
                Route::post('drivers_notifications', 'notificationController@drivers')->name('driversNotifications');
                Route::post('users_notifications', 'notificationController@users')->name('usersNotifications');
                Route::post('user_notifications', 'notificationController@user')->name('userNotifications');
                Route::post('source_assign', 'orderController@sourceAssign')->name('sourceAssign');
                Route::get('errors', 'AppErrorController@index')->name('errors');
                Route::put('transaction_type', 'OrderController@transactionType')->name('transactionType');
                Route::post('order_match/{id}', "OrderController@match")->name('order.match');
                Route::post('make_provider/{id}', "CarrierUserController@makeProvider")->name('makeProvider');
                Route::post("trafic_price/{id}", "OrderController@traficPrice")->name("traficPrice");
                Route::get("gift/{id}","OrderController@gift")->name("gift");
            });
    });
Route::group([
    'prefix' => 'blog',
    'middleware' => 'auth'],
    function () {
        Route::resource('/slider', 'SliderController');
        Route::resource('/post', 'PostController');
    });
Route::group(['middleware' => 'local.ip'],
    function () {
        Route::get('discount_to_first_users', 'AlertController@discountToFirstUsers');
        Route::get('success_bk', 'AlertController@successBackUp');
        Route::get('fail_bk', 'AlertController@failBackUp');
        Route::get('driver_alert', 'AlertController@driverAlert');
    });
Route::get('test', 'TestController@test');
new \App\OrderEvent();