<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);

Route::group(['middleware' => \App\Http\Middleware\CheckAuthenticated::class], function ($router) {
    Route::get('customer/{customer}/chat', [\App\Http\Controllers\CustomerController::class, 'messages'])->name('api.messages');
    Route::get('customer/{customer}/mark', [\App\Http\Controllers\CustomerController::class, 'mark'])->name('api.mark');
    Route::post('customer/{customer}/chat', [\App\Http\Controllers\CustomerController::class, 'respond'])->name('api.respond');
    Route::post('/upload-audio/{customer}', [\App\Http\Controllers\TelegramController::class, 'uploadAudio'])->name('api.record-voice');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users')->name('api.users');
    Route::get('user/{customer}/toggle', [\App\Http\Controllers\UserController::class, 'toggle'])->name('api.toggle');
    Route::get('user/{customer}/delete', [\App\Http\Controllers\UserController::class, 'delete'])->name('api.delete');
    Route::get('chat', [\App\Http\Controllers\CustomerController::class, 'chat'])->name('api.chat');
    Route::get('event/{customer}', [\App\Http\Controllers\CustomerController::class, 'event'])->name('api.event');
});


Route::post('/telegram-bot', [\App\Http\Controllers\TelegramController::class, 'handle']);
Route::post('/{customer}/upload', [\App\Http\Controllers\TelegramController::class, 'upload']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
