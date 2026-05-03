<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'homeProducts'])->name('home');

Route::get('/about', function () {
    return view('aboutus.index');
})->name('about.index');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Flash Sale
Route::get('/flash-sale', [ProductController::class, 'flashSale'])->name('flash-sale.index');

// Products
Route::prefix('products')->name('products.')->controller(ProductController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'show')->name('show');
});


/*
|--------------------------------------------------------------------------
| Authentication (Guest Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Register
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::get('register', 'create')->name('register');
        Route::post('register', 'store');
    });

    // Login
    Route::controller(AuthenticatedSessionController::class)->group(function () {
        Route::get('login', 'create')->name('login');
        Route::post('login', 'store');
    });

    // Password Reset
    Route::controller(PasswordResetLinkController::class)->group(function () {
        Route::get('forgot-password', 'create')->name('password.request');
        Route::post('forgot-password', 'store')->name('password.email');
    });

    Route::controller(NewPasswordController::class)->group(function () {
        Route::get('reset-password/{token}', 'create')->name('password.reset');
        Route::post('reset-password', 'store')->name('password.store');
    });
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('password.update');
        Route::delete('/', 'destroy')->name('destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */
    Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/checkout', 'checkout')->name('checkout');

        Route::post('/add', 'add')->name('add');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/remove/{id}', 'remove')->name('remove');
        Route::delete('/clear', 'clear')->name('clear');

        Route::get('/count', 'getCount')->name('count');
    });


    /*
    |--------------------------------------------------------------------------
    | Addresses
    |--------------------------------------------------------------------------
    */
    Route::prefix('addresses')->name('addresses.')->controller(AddressController::class)->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('/user-addresses', 'userAddresses')->name('user');

        Route::get('/{address}', 'show');
        Route::get('/{address}/edit-data', 'editData')->name('edit-data');
        Route::get('/{address}/shipping-fee', 'getShippingFee')->name('shipping-fee');

        Route::put('/{address}', 'update')->name('update');
        Route::delete('/{address}', 'destroy')->name('destroy');
    });


    /*
|--------------------------------------------------------------------------
| Districts 
|--------------------------------------------------------------------------
*/

    Route::prefix('districts')->name('districts.')->controller(DistrictController::class)->group(function () {
        Route::get('/by-province/{province}', 'byProvince')->name('byProvince');
    });
    Route::get('/districts', [DistrictController::class, 'byProvince'])
        ->name('districts.list');


    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */
    Route::prefix('wishlist')->name('wishlist.')->controller(WishlistController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'add')->name('add');
        Route::delete('/remove/{id}', 'remove')->name('remove');
        Route::post('/toggle', 'toggle')->name('toggle');
        Route::get('/check/{productId}', 'check')->name('check');
    });


    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
        Route::post('/prepare', 'prepare')->name('prepare');
        Route::post('/place', 'placeOrder')->name('store');

        Route::get('/place/{productId}/{variantId?}', 'directOrderForm')->name('place');
        Route::get('/success/{order}', 'success')->name('success');

        Route::get('/my-orders', 'myOrders')->name('my');
        Route::get('/{order}', 'show')->name('show');
        Route::put('/{order}/cancel', 'cancel')->name('cancel');
    });


    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */
    Route::prefix('payment')->name('payment.')->controller(PaymentController::class)->group(function () {
        Route::get('/{order}', 'show')->name('show');
        Route::post('/process', 'process')->name('process');
    });

    Route::get('/payment/khalti/{order}', [PaymentController::class, 'khaltiInitiate'])->name('payment.khalti');
    Route::get('/khalti/callback/{orderId}', [PaymentController::class, 'khaltiCallback'])->name('payment.khalti.callback');
});
