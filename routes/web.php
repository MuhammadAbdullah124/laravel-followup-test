<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\TestController;

Route::get('/', [TestController::class, 'index'])->name('products.index');
Route::post('/save-or-update', [TestController::class, 'saveOrUpdate'])->name('products.saveOrUpdate');
