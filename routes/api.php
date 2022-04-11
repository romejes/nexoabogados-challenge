<?php

use App\Http\Controllers\CurrentSubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserSubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api'])->prefix("v1")->group(function () {
    Route::controller(SubscriptionController::class)->group(function () {
        Route::get("subscriptions", "index");
        Route::get("subscriptions/{id}", "show");
        Route::delete("subscriptions/{id}", "destroy");
    });

    Route::controller(CurrentSubscriptionController::class)->group(function () {
        Route::get("users/{id}/subscriptions/current", "show");
        Route::delete("users/{id}/subscriptions/current", "destroy");
        Route::put("users/{id}/subscriptions/current", "update");
    });

    Route::controller(UserSubscriptionController::class)->group(function () {
        Route::post("users/{id}/subscriptions", "store");
    });
});
