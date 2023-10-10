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
    Route::post("/doInstall", [\App\Http\Controllers\InstallController::class, 'doInstall']);
});

Route::middleware(['installCheck:haveInstall'])->group(function () {
    Route::middleware("web")->prefix(config("94list.prefix"))->group(function () {
        Route::post("/login", [\App\Http\Controllers\AdminController::class, 'login']);
//        Route::post('/register', [\App\Http\Controllers\AdminController::class, 'register']);

        Route::middleware('auth')->group(function () {
            Route::middleware("isAdmin")->group(function () {
                Route::post('/getConfig', [\App\Http\Controllers\AdminController::class, 'getConfig']);
                Route::post("/changeConfig", [\App\Http\Controllers\AdminController::class, 'changeConfig']);
                Route::post("/getAccountInfo", [\App\Http\Controllers\AdminController::class, 'getAccountInfo']);
                Route::post("/addAccount", [\App\Http\Controllers\AdminController::class, 'addAccount']);
                Route::post("/deleteAccount", [\App\Http\Controllers\AdminController::class, 'deleteAccount']);
                Route::post("/switchAccount", [\App\Http\Controllers\AdminController::class, 'switchAccount']);
                Route::post("/getAccounts", [\App\Http\Controllers\AdminController::class, 'getAccounts']);
            });

            Route::post("/changeUserInfo", [\App\Http\Controllers\AdminController::class, 'changeUserInfo']);
            Route::post("/logout", [\App\Http\Controllers\AdminController::class, 'logout']);
        });
    });

    Route::prefix("/user")->group(function () {
        Route::post('/getConfig', [\App\Http\Controllers\UserController::class, 'getConfig']);
        Route::post("/getFileList", [\App\Http\Controllers\UserController::class, 'getFileList']);
        Route::post("/getSign", [\App\Http\Controllers\UserController::class, 'getSign']);
        Route::post("/downloadFiles", [\App\Http\Controllers\UserController::class, 'downloadFiles']);
    });
});
