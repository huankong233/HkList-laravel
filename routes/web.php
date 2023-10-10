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

//Route::middleware("installCheck:haveInstall")->group(function () {
//    Route::get('/', [\App\Http\Controllers\UserController::class, 'view'])->name("user");
//    Route::get(config("94list.prefix"), [\App\Http\Controllers\AdminController::class, 'view'])->name("admin");
//});
//
//
//Route::middleware("installCheck:notInstall")->group(function () {
//    Route::view("/install", "pages.install");
//});

Route::get('/{any}', [\App\Http\Controllers\UserController::class, 'view'])->where('any', '.*');