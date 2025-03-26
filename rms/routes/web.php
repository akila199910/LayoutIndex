<?php

use Illuminate\Support\Facades\Auth;
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

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
});

Auth::routes();

//Forgot Password
Route::get('/forget_password', [App\Http\Controllers\ForgotPasswordController::class, 'index'])->name('buiness.forget_password.index');
Route::post('/forget_password', [App\Http\Controllers\ForgotPasswordController::class, 'emailcheck'])->name('buiness.forget_password.email.check');
Route::get('/forget_password/verify/{id}', [App\Http\Controllers\ForgotPasswordController::class, 'forget_password_verify'])->name('buiness.forget_password.verify');
Route::get('/new_password/{id}', [App\Http\Controllers\ForgotPasswordController::class, 'new_password'])->name('buiness.new_password.view');
Route::post('/new_password', [App\Http\Controllers\ForgotPasswordController::class, 'password_create'])->name('buiness.password_create');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
