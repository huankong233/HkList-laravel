<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('IsInstall')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::post('/login', [\App\Http\Controllers\UserController::class, 'login']);
        Route::post('/register', [\App\Http\Controllers\UserController::class, 'register']);
    });

    Route::prefix('/admin')->group(function () {

    });

//        Route::middleware('auth')->group(function () {
//            Route::middleware('isAdmin')->group(function () {
//                Route::post('/getConfig', [\App\Http\Controllers\AdminController::class, 'getConfig']);
//                Route::post('/changeConfig', [\App\Http\Controllers\AdminController::class, 'changeConfig']);
//                Route::post('/getMailConfig', [\App\Http\Controllers\AdminController::class, 'getMailConfig']);
//                Route::post('/changeMailConfig', [\App\Http\Controllers\AdminController::class, 'changeMailConfig']);
//                Route::post('/sendTestMsg', [\App\Http\Controllers\AdminController::class, 'sendTestMsg']);
//                Route::post('/getAccountInfo', [\App\Http\Controllers\AdminController::class, 'getAccountInfo']);
//                Route::post('/addAccount', [\App\Http\Controllers\AdminController::class, 'addAccount']);
//                Route::post('/updateAccount', [\App\Http\Controllers\AdminController::class, 'updateAccount']);
//                Route::post('/deleteAccount', [\App\Http\Controllers\AdminController::class, 'deleteAccount']);
//                Route::post('/switchAccount', [\App\Http\Controllers\AdminController::class, 'switchAccount']);
//                Route::post('/getAccounts', [\App\Http\Controllers\AdminController::class, 'getAccounts']);
//            });
//
//            Route::post('/changeUserInfo', [\App\Http\Controllers\AdminController::class, 'changeUserInfo']);
//            Route::post('/logout', [\App\Http\Controllers\AdminController::class, 'logout']);
//        });

//    Route::prefix('/user')->group(function () {
//        Route::post('/getConfig', [\App\Http\Controllers\UserController::class, 'getConfig']);
//        Route::post('/getFileList', [\App\Http\Controllers\UserController::class, 'getFileList']);
//        Route::post('/getSign', [\App\Http\Controllers\UserController::class, 'getSign']);
//        Route::post('/downloadFiles', [\App\Http\Controllers\UserController::class, 'downloadFiles']);
//    });
});
