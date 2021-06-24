<?php

use Nestermaks\LaravelPricelist\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nestermaks\LaravelPricelist\Http\Controllers\PricelistController;

Route::prefix(config('pricelist.api'))->group(function () {
    Route::apiResource(config('pricelist.pricelists'), PricelistController::class);
    Route::post(config('pricelist.pricelists') . '/change-order', [BaseController::class, 'changeOrder']);
    Route::post(config('pricelist.pricelists') . '/{action}', [BaseController::class, 'relation']);
});