<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Frontend.Index');
})->name('home');

Route::get('/product', function () {
    return view('Frontend.product');
})->name('product');
