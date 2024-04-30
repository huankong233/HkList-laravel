<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['IsInstall'])->group(function () {
    Route::prefix('/v1')->group(function () {
        Route::prefix('/user')->group(function () {
            Route::middleware('CheckCaptcha')->group(function () {
                Route::get('/login', [UserController::class, 'login']);
                Route::post('/register', [UserController::class, 'register']);
            });
            Route::delete('/', [UserController::class, 'logout']);
        });

        Route::prefix('/admin')->middleware('RoleFilter:admin')->group(function () {
            Route::pattern('user_id', '[0-9]+');
            Route::prefix('/user')->group(function () {
                Route::get('/{user_id?}', [UserController::class, 'getUser']);
                Route::post('/', [UserController::class, 'addUser']);
                Route::patch('/{user_id}', [UserController::class, 'updateUser']);
                Route::delete('/{user_id}', [UserController::class, 'removeUser']);
            });

            Route::pattern('group_id', '[0-9]+');
            Route::prefix('/group')->group(function () {
                Route::get('/{group_id?}', [GroupController::class, 'getGroup']);
                Route::post('/', [GroupController::class, 'addGroup']);
                Route::patch('/{group_id}', [GroupController::class, 'updateGroup']);
                Route::delete('/{group_id}', [GroupController::class, 'removeGroup']);
            });

            Route::pattern('record_id', '[0-9]+');
            Route::prefix('/record')->group(function () {
                Route::get('/{record_id?}', [RecordController::class, 'getRecord']);
                Route::delete('/{record_id}', [RecordController::class, 'removeRecord']);
            });

            Route::pattern('account_id', '[0-9]+');
            Route::prefix('/account')->group(function () {
                Route::get('/{account_id?}', [AccountController::class, 'getAccount']);
                Route::get('/info', [AccountController::class, 'getAccountInfo']);
                Route::post('/', [AccountController::class, 'addAccount']);
                Route::patch('/{account_id}', [AccountController::class, 'updateAccount']);
                Route::delete('/{account_id}', [AccountController::class, 'removeAccount']);
            });
        });
    });


//            Route::post('/getConfig', [\App\Http\Controllers\AdminController::class, 'getConfig']);
//            Route::post('/changeConfig', [\App\Http\Controllers\AdminController::class, 'changeConfig']);
//            Route::post('/getMailConfig', [\App\Http\Controllers\AdminController::class, 'getMailConfig']);
//            Route::post('/changeMailConfig', [\App\Http\Controllers\AdminController::class, 'changeMailConfig']);
//            Route::post('/sendTestMsg', [\App\Http\Controllers\AdminController::class, 'sendTestMsg']);
//            Route::post('/getAccountInfo', [\App\Http\Controllers\AdminController::class, 'getAccountInfo']);
//            Route::post('/addAccount', [\App\Http\Controllers\AdminController::class, 'addAccount']);
//            Route::post('/updateAccount', [\App\Http\Controllers\AdminController::class, 'updateAccount']);
//            Route::post('/deleteAccount', [\App\Http\Controllers\AdminController::class, 'deleteAccount']);
//            Route::post('/switchAccount', [\App\Http\Controllers\AdminController::class, 'switchAccount']);
//            Route::post('/getAccounts', [\App\Http\Controllers\AdminController::class, 'getAccounts']);
//            Route::post('/changeUserInfo', [\App\Http\Controllers\AdminController::class, 'changeUserInfo']);


//    Route::prefix('/user')->group(function () {
//        Route::post('/getConfig', [\App\Http\Controllers\UserController::class, 'getConfig']);
//        Route::post('/getFileList', [\App\Http\Controllers\UserController::class, 'getFileList']);
//        Route::post('/getSign', [\App\Http\Controllers\UserController::class, 'getSign']);
//        Route::post('/downloadFiles', [\App\Http\Controllers\UserController::class, 'downloadFiles']);
//    });
});
