<?php

use App\Http\Controllers\V1\AdminLoginController;
use Illuminate\Support\Facades\Route;


Route::post('/admin/login', AdminLoginController::class)->name('admin.login');
