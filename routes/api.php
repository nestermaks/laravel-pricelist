<?php

use Nestermaks\LaravelPricelist\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nestermaks\LaravelPricelist\Http\Controllers\PricelistController;
use Nestermaks\LaravelPricelist\Http\Controllers\PricelistItemController;

Route::prefix(config('pricelist.api'))->group(function () {

    Route::apiResource(config('pricelist.pricelists'), PricelistController::class);

    Route::apiResource(config('pricelist.items'), PricelistItemController::class);

    Route::post(config('pricelist.pricelists') . '/change-order', [BaseController::class, 'changeOrder']);
    Route::post(config('pricelist.pricelists') . '/relation/{action}', [BaseController::class, 'relation']);
    Route::get(config('pricelist.pricelists') . '/get/{model_name}/{model_id}', [BaseController::class, 'getPricelistsOfModel']);
    Route::post(config('pricelist.pricelists') . '/model/{action}', [BaseController::class, 'relationWithModel']);
});