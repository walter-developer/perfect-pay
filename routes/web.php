<?php

use App\Http\Controllers\{
    PaymentController,
    PersonController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('/')->group(function () {
    //
    //
    Route::prefix('/client')->group(function () {
        //
        Route::post('/', [PersonController::class, 'createClient'])->name('view.payment');
        //
    });
    //
    //
    Route::prefix('/payment')->group(function () {

        Route::get('/', [PaymentController::class, 'viewPayment'])->name('view.payment');

        Route::get('/success/', [PaymentController::class, 'viewPaymentSucess'])->name('view.payment.success');

        Route::post('/tikect', [PaymentController::class, 'paymentTikect'])->name('payment.ticket');

        Route::post('/pix', [PaymentController::class, 'paymentPix'])->name('payment.pix');

        Route::post('/card', [PaymentController::class, 'paymentTikect'])->name('payment.card');
    });
    //
    //
});
