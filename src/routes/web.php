<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;


Route::get('/', [TripController::class, 'index']);
Route::post('/trips', [TripController::class, 'getAll']);
Route::post(
    '/trips/calculated',
    [TripController::class, 'getCalculatePayableTimeForAll']
);
