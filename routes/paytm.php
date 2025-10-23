<?php

//Paytm

// Mohammad Hassan - Commented out PaytmController as controller doesn't exist
// use App\Http\Controllers\Payment\PaytmController;
// Mohammad Hassan - Commented out ToyyibpayController as controller doesn't exist
// use App\Http\Controllers\Payment\ToyyibpayController;
// Mohammad Hassan - Commented out MyfatoorahController as controller doesn't exist
// use App\Http\Controllers\Payment\MyfatoorahController;
// Mohammad Hassan - Commented out KhaltiController as controller doesn't exist
// use App\Http\Controllers\Payment\KhaltiController;
// Mohammad Hassan - Commented out PhonepeController as controller doesn't exist
// use App\Http\Controllers\Payment\PhonepeController;

// Mohammad Hassan - Commented out PaytmController route group as controller doesn't exist
/*
Route::controller(PaytmController::class)->group(function () {
    Route::get('/paytm/index', 'pay');
    Route::post('/paytm/callback', 'callback')->name('paytm.callback');
});
*/

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    // Mohammad Hassan - Commented out PaytmController route group as controller doesn't exist
    /*
    Route::controller(PaytmController::class)->group(function () {
        Route::get('/paytm_configuration', 'credentials_index')->name('paytm.index');
        Route::post('/paytm_configuration_update', 'update_credentials')->name('paytm.update_credentials');
    });
    */
});

//Toyyibpay
// Mohammad Hassan - Commented out ToyyibpayController route group as controller doesn't exist
/*
Route::controller(ToyyibpayController::class)->group(function () {
    Route::get('toyyibpay-status', 'paymentstatus')->name( 'toyyibpay-status');
    Route::post('/toyyibpay-callback', 'callback')->name( 'toyyibpay-callback');
});
*/

//Myfatoorah START
// Mohammad Hassan - Commented out MyfatoorahController route as controller doesn't exist
// Route::get('/myfatoorah/callback', [MyfatoorahController::class,'callback'])->name('myfatoorah.callback');

//Khalti START
// Mohammad Hassan - Commented out KhaltiController route as controller doesn't exist
// Route::any('/khalti/payment/done', [KhaltiController::class,'paymentDone'])->name('khalti.success');

// phonepe
// Mohammad Hassan - Commented out PhonepeController route group as controller doesn't exist
/*
Route::controller(PhonepeController::class)->group(function () {
    Route::any('/phonepe/pay', 'pay')->name('phonepe.pay');
    Route::any('/phonepe/redirecturl', 'phonepe_redirecturl')->name('phonepe.redirecturl');
    Route::any('/phonepe/callbackUrl', 'phonepe_callbackUrl')->name('phonepe.callbackUrl');
});
*/
