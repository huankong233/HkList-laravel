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

Route::middleware("installCheck:notInstall")->group(function () {
    Route::post("/do_install", [\App\Http\Controllers\InstallController::class, 'do_install'])->name("do_install");
});


Route::middleware(['installCheck:haveInstall', 'web'])->group(function () {
    Route::prefix(config("94list.prefix"))->name("admin.")->group(function () {
        Route::post("/login", [\App\Http\Controllers\AdminController::class, 'login'])->name("login");
        Route::post('/register', [\App\Http\Controllers\AdminController::class, 'register'])->name("register");

        Route::middleware('auth')->group(function () {
            Route::middleware("isAdmin")->group(function () {
                Route::post("/changeConfig", [\App\Http\Controllers\AdminController::class, 'changeConfig'])
                     ->name("changeConfig");
                Route::post("/getAccountInfo", [\App\Http\Controllers\AdminController::class, 'getAccountInfo'])
                     ->name("getAccountInfo");
                Route::post("/addAccount", [\App\Http\Controllers\AdminController::class, 'addAccount'])
                     ->name("addAccount");
                Route::post("/deleteAccount", [\App\Http\Controllers\AdminController::class, 'deleteAccount'])
                     ->name("deleteAccount");
                Route::post("/switchAccount", [\App\Http\Controllers\AdminController::class, 'switchAccount'])
                     ->name("switchAccount");
                Route::post("/getAccounts", [\App\Http\Controllers\AdminController::class, 'getAccounts'])
                     ->name("getAccounts");
            });

            Route::post("/changeUserInfo", [\App\Http\Controllers\AdminController::class, 'changeUserInfo'])
                 ->name("changeUserInfo");
            Route::post("/logout", [\App\Http\Controllers\AdminController::class, 'logout'])->name("logout");
        });
    });

    Route::prefix("/user")->name("user.")->group(function () {
        Route::post("/getFileList", [\App\Http\Controllers\UserController::class, 'getFileList'])->name("getFileList");
        Route::post("/getSign", [\App\Http\Controllers\UserController::class, 'getSign'])->name("getSign");
        Route::post("/downloadFiles", [\App\Http\Controllers\UserController::class, 'downloadFiles'])
             ->name("downloadFiles");
    });
});
