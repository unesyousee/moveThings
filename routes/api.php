<?php
Route::get('isOk', function () {
    \Illuminate\Support\Facades\DB::table('carriers')->select('*');
    $status = "200";
    $message = "کد تخفیف با موفقیت ایجاد شد.";
    $final = array(
        "status" => $status,
        "message" => $message,
        "isOK" => "yes"
    );
    return response()->json($final);
});
Route::get("discount_counter", "DiscountController@counter");
Route::any('transaction/settle', 'TransactionController@settle');
Route::post('boot','UserController@boot');
Route::get('redirect_to_bank', 'TransactionController@direct');
Route::get('send_mobile/{mobile}', 'LoginController@sendActivationCodeSMS');
Route::get('send_activeCode/{mobile}/{activeCode}', 'LoginController@authenticate');
Route::post('driver_login', 'LoginController@login');
Route::get('get_vasayeleHazinedar', 'HeavyThingsController@all');
Route::post('get_vasayeleHazinedar_new', 'HeavyThingsController@allNew');
Route::get('get_car', 'CarrierController@all');
Route::get('updatecheck/{app_id}', 'AppController@checkVersion');
Route::post('update_transaction', 'TransactionController@update');
Route::post('checklocation', 'OrderController@checkLocation');
Route::get('get_user_profile/{id}', 'UserController@getUserProfile');
Route::post('get_price', 'OrderController@getPrice');
Route::group(['middleware' => ['jwt.auth','cors']], function() {
    Route::group(["prefix" => "v1"], function () {
        Route::get('get_active_order', 'OrderController@getActiveOrder');
        Route::get('all_active_order', 'OrderController@allActiveOrder');
    });

    Route::get("/alive","AliveController@store");
    Route::get('driver_invoice/{id}', 'TransactionController@driverInvoice');
//    Route::post('accept_order', 'OrderController@acceptOrder');
    Route::post('add_transaction', 'TransactionController@store');
    Route::post('send_to_bank_wallet', 'TransactionController@redirect');
//    Route::post('checklocation', 'OrderController@checkLocation');
    Route::post('paymentcheck', 'TransactionController@paymentCheck');
    Route::post('send_profile', 'UserController@updateProfile');
    Route::get('get_share_code', 'UserController@getShareCode');
    Route::post('cancel_order', 'OrderController@cancelOrder');
    Route::post('send_order', 'OrderController@sendOrder');
    Route::post('accept_order', 'OrderController@acceptOrder')->name('accept_order');
    Route::get('get_active_order', 'OrderController@getActiveOrder');
    Route::get('all_active_order', 'OrderController@allActiveOrder');
    Route::get('all_my_orders', 'OrderController@allMyOrders');
    Route::get('all_user_orders', 'OrderController@allUserOrders');
    Route::get('get_profile', 'UserController@getProfile');
    Route::get('get_order_status/{order_id}', 'OrderController@getOrderStatus');
    Route::post('upload_profile', 'UserController@uploadProfilePic');
    Route::get('get_order_by_order_id/{order_id}', 'OrderController@getOrder');
    Route::get('get_extra_time_by_order_id/{order_id}', 'OrderController@getExtraTime');
    Route::post('update_driver', 'CarrierUserController@updateDriver');
    Route::post('add_rank', 'CommentController@store');
    Route::post('update_order_driver_status', 'OrderController@updateStatus');
    Route::post('driver_transportation_history', 'CarrierUserController@getOrderHistory');
    Route::get('get_driver_ranks', 'CarrierUserController@getDriverComments');
    Route::post('get_driver_total_rank', 'CarrierUserController@getDriverTotalRating');
    Route::post('add_worker', 'CarrierUserController@store');
    Route::post('delete_worker', 'CarrierUserController@delete');
    Route::post('get_worker_list', 'CarrierUserController@getWorkerList');
    Route::post('upload_signature', 'OrderController@uploadSignature');
    Route::get  ('get_wallet', 'TransactionController@getTotal');
    Route::get('get_all_payment_by_id', 'TransactionController@getDriverTransactions');
    Route::post('send_message', 'MessageController@store');
    Route::get('get_order', 'OrderController@getUserOrders');
    Route::post('changepassword','CarrierUserController@changePassword');
    Route::get('get_all_user_orders','OrderController@getAllUserOrders');
    Route::post('check_discount_code','DiscountController@checkDiscount');
    Route::post('delete_discount_code','DiscountController@deleteDiscount');
    Route::get('get_notification','notificationController@getNotification');
    Route::get('get_user_notification','notificationController@getUserNotification');
    Route::post('update_order','OrderController@updateOrder');
    Route::post('set_reg_id', 'UserController@setRegId');
    Route::post('check_order_comment','CommentController@checkOrderComment');
    Route::post('select_workers','OrderController@selectWorkers');
    Route::post('set_app_error','AppErrorController@insertNewError');
    Route::get('get_user_notifs','UserController@getUserNotifs');
    Route::get("detach_order/{id}", "OrderController@detach");
});
Route::get('bill/{id}',"OrderController@bill");
Route::group(['prefix' => 'blog'],
    function () {
        Route::get('sliders','SliderController@allSliders');
        Route::get('posts','PostController@allPosts');
        Route::get('post/{id}','PostController@SinglePost');
});
Route::post('notif_to_user', 'UserController@notify');
Route::post('notif_to_topic', 'UserController@notifyTopic');

Route::group(['prefix' => 'web'],
    function () {
        Route::get('settings','WebController@settings');
});
