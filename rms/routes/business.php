<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Business Routes
|--------------------------------------------------------------------------
|
| This Route related to business
|
*/

Route::middleware(['auth', 'isUserExist'])->group(function () {

    //Dashboard
    Route::post('/dashboard', [App\Http\Controllers\DashboardController::class, 'getOrders'])->name('dashboard.order');
    Route::get('/dashboard/graph', [App\Http\Controllers\DashboardController::class, 'graph'])->name('dashboard.graph');
    Route::post('/dashboard/get_purchase', [App\Http\Controllers\DashboardController::class, 'get_purchase'])->name('dashboard.get_purchase');
    Route::get('/dashboard/get_purchase_list', [App\Http\Controllers\DashboardController::class, 'get_purchase_list'])->name('dashboard.get_purchase_list');


    Route::get('/concessions', [App\Http\Controllers\ConcessionController::class, 'index'])->name('concessions');
    Route::get('/concessions/create', [App\Http\Controllers\ConcessionController::class, 'create_form'])->name('concessions.create.form');
    Route::post('/concessions/create', [App\Http\Controllers\ConcessionController::class, 'create'])->name('concessions.create');
    Route::get('/concessions/update/{id}', [App\Http\Controllers\ConcessionController::class, 'update_form'])->name('concessions.update.form');
    Route::post('/concessions/update', [App\Http\Controllers\ConcessionController::class, 'update'])->name('concessions.update');
    Route::post('/concessions/delete', [App\Http\Controllers\ConcessionController::class, 'delete'])->name('concessions.delete');
    Route::get('/concessions/view/{ref_no}', [App\Http\Controllers\ConcessionController::class, 'view_details'])->name('concessions.view_details');

    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/create', [App\Http\Controllers\OrderController::class, 'create_form'])->name('orders.create.form');
    Route::post('/orders/create', [App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders/update/{id}', [App\Http\Controllers\OrderController::class, 'update_form'])->name('orders.update.form');
    Route::post('/orders/update', [App\Http\Controllers\OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/delete', [App\Http\Controllers\OrderController::class, 'delete'])->name('orders.delete');
    Route::get('/orders/view/{ref_no}', [App\Http\Controllers\OrderController::class, 'view_details'])->name('orders.view_details');

    Route::get('/orders/concessions', [App\Http\Controllers\OrderController::class, 'get_concessions'])->name('orders.concession');
    Route::post('/orders/concessions', [App\Http\Controllers\OrderController::class, 'update_status'])->name('orders.update_status');


    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create_form'])->name('users.create.form');
    Route::post('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::get('/users/update/{id}', [App\Http\Controllers\UserController::class, 'update_form'])->name('users.update.form');
    Route::post('/users/update', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::post('/users/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
    Route::get('/users/view/{ref_no}', [App\Http\Controllers\UserController::class, 'view_details'])->name('users.view_details');


});
