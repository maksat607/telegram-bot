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

use App\Http\Controllers\LogController;

Route::get('/logs', [LogController::class, 'index']);
Route::get('/logs/{filename}', [LogController::class, 'show']);
Route::delete('/logs/{filename}', [LogController::class, 'destroy']);





// Custom login routes
Route::get('/login', [\App\Http\Controllers\ApiLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\ApiLoginController::class, 'login'])->name('login.post');

// Custom logout route
Route::middleware(\App\Http\Middleware\CheckAuthenticated::class)->post('/logout', [\App\Http\Controllers\ApiLoginController::class, 'logout'])->name('logout.post');

// You can also add routes for registration if needed


Route::middleware(\App\Http\Middleware\CheckAuthenticated::class)->get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(\App\Http\Middleware\CheckAuthenticated::class)->group(function () {
    Route::get('customer/{customer}/chat', [\App\Http\Controllers\CustomerController::class, 'messages'])->name('messages');
    Route::get('customer/{customer}/mark', [\App\Http\Controllers\CustomerController::class, 'mark']);
    Route::post('customer/{customer}/chat', [\App\Http\Controllers\CustomerController::class, 'respond']);
    Route::post('/upload-audio/{customer}', [\App\Http\Controllers\TelegramController::class, 'uploadAudio'])->name('record-voice');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('user/{customer}/toggle', [\App\Http\Controllers\UserController::class, 'toggle'])->name('toggle');
    Route::get('user/{customer}/delete', [\App\Http\Controllers\UserController::class, 'delete'])->name('delete');
    Route::get('chat', [\App\Http\Controllers\CustomerController::class, 'chat'])->name('chat');
    Route::get('event/{customer}', [\App\Http\Controllers\CustomerController::class, 'event']);
    Route::view('page1','page1');
    Route::view('page2','page2');

//    Route::get('/dashboard', function () {
//        return redirect('customer/1/chat');
//    })->name('dashboard');
//


//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
