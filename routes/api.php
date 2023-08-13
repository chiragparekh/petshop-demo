<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->as('v1.')
    ->group(
        base_path('routes/v1/api.php')
    );