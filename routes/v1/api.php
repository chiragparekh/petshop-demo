<?php

use App\Http\Controllers\V1\AdminLoginController;
use Illuminate\Support\Facades\Route;


Route::post('/admin/login', AdminLoginController::class)->name('admin.login');

Route::group([
    'middleware' => ['auth:admin']
], function() {
    Route::get('/orders/dashboard', \App\Http\Controllers\V1\OrderDashboardController::class)->name('orders.dashboard');
});
