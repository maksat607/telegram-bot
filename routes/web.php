<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('customer/{customer}/chat', [\App\Http\Controllers\CustomerController::class, 'messages'])->name('messages');
    Route::post('customer/{customer}/chat', [\App\Http\Controllers\CustomerController::class, 'respond']);
    Route::post('/upload-audio', [\App\Http\Controllers\TelegramController::class, 'uploadAudio'])->name('record-voice');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('user/{customer}/toggle', [\App\Http\Controllers\UserController::class, 'toggle'])->name('toggle');
    Route::get('user/{customer}/delete', [\App\Http\Controllers\UserController::class, 'delete'])->name('delete');
    Route::get('chat', [\App\Http\Controllers\CustomerController::class, 'chat'])->name('chat');
    Route::get('event/{customer}', [\App\Http\Controllers\CustomerController::class, 'event']);
    Route::view('page1','page1');
    Route::view('page2','page2');

    Route::get('/dashboard', function () {
        return redirect('customer/1/chat');
    })->middleware(['verified'])->name('dashboard');



//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
