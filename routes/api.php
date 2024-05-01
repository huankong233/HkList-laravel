<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\config\CaptchaConfigController;
use App\Http\Controllers\config\ConfigController;
use App\Http\Controllers\config\MailConfigController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ParseController;
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

            Route::prefix('/config')->group(function () {
                Route::get('/', [ConfigController::class, 'getConfig']);
                Route::patch('/', [ConfigController::class, 'updateConfig']);
            });

            Route::prefix('/mail')->group(function () {
                Route::get('/', [MailConfigController::class, 'getMailConfig']);
                Route::post('/', [MailConfigController::class, 'sendTestMail']);
                Route::patch('/', [MailConfigController::class, 'updateMailConfig']);
            });

            Route::prefix('/captcha')->group(function () {
                Route::get('/', [CaptchaConfigController::class, 'getCaptchaConfig']);
                Route::post('/', [CaptchaConfigController::class, 'sendCaptchaVerify']);
                Route::patch('/', [CaptchaConfigController::class, 'updateCaptchaConfig']);
            });
        });

        Route::prefix('/parse')->middleware('NeedPassword')->group(function () {
            Route::get('/config', [ParseController::class, 'getConfig']);
            Route::get('/fileList', [ParseController::class, 'getFileList']);
            Route::get('/sign', [ParseController::class, 'getSign']);
            Route::post('/downloadFiles', [ParseController::class, 'downloadFiles']);
        });
    });
});
