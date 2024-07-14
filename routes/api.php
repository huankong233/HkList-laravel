<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\config\MailConfigController;
use App\Http\Controllers\config\MainConfigController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\InvCodeController;
use App\Http\Controllers\IpController;
use App\Http\Controllers\ParseController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post("/install", [InstallController::class, "install"]);

Route::middleware(["NeedInstall", "AutoUpdate"])->group(function () {
    Route::prefix("/parse")->middleware("IpFilter")->group(function () {
        Route::get("/config", [ParseController::class, "getConfig"]);
        Route::get("/limit", [ParseController::class, "checkLimit"]);
        Route::prefix("/")->middleware(["ThrottleRequest", "NeedPassword"])->group(function () {
            Route::post("/get_file_list", [ParseController::class, "getFileList"]);
            Route::post("/get_vcode", [ParseController::class, "getVcode"]);
            Route::post("/get_download_links", [ParseController::class, "getDownloadLinks"]);
        });
    });

    Route::prefix("/user")->middleware(["ThrottleRequest"])->group(function () {
        Route::post("/login", [UserController::class, "login"]);
        Route::post("/register", [UserController::class, "register"]);
        Route::delete("/", [UserController::class, "logout"]);
    });

    Route::prefix("/admin")->middleware("RoleFilter:admin")->group(function () {
        Route::pattern("id", "[0-9]+");

        Route::prefix("/user")->group(function () {
            Route::get("/", [UserController::class, "getUsers"]);
            Route::post("/", [UserController::class, "addUser"]);
            Route::patch("/{id}", [UserController::class, "updateUser"]);
            Route::delete("/", [UserController::class, "removeUsers"]);
        });

        Route::prefix("/group")->group(function () {
            Route::get("/", [GroupController::class, "getGroups"]);
            Route::post("/", [GroupController::class, "addGroup"]);
            Route::patch("/{id}", [GroupController::class, "updateGroup"]);
            Route::delete("/", [GroupController::class, "removeGroups"]);
        });

        Route::prefix("/record")->group(function () {
            Route::get("/", [RecordController::class, "getRecords"]);
            Route::get('/count', [RecordController::class, 'getRecordsCount']);
            Route::delete("/", [RecordController::class, "removeRecords"]);
        });

        Route::prefix("/account")->group(function () {
            Route::get("/", [AccountController::class, "getAccounts"]);
            Route::post("/", [AccountController::class, "addAccount"]);
            Route::patch("/{id}", [AccountController::class, "updateAccount"]);
            Route::patch("/info", [AccountController::class, "updateAccountsInfo"]);
            Route::patch("/switch", [AccountController::class, "switchAccounts"]);
            Route::delete("/", [AccountController::class, "removeAccounts"]);
        });

        Route::prefix("/inv_code")->group(function () {
            Route::get("/", [InvCodeController::class, "getInvCodes"]);
            Route::post("/", [InvCodeController::class, "addInvCode"]);
            Route::post("/generate", [InvCodeController::class, "generateInvCode"]);
            Route::patch("/{id}", [InvCodeController::class, "updateInvCode"]);
            Route::delete("/", [InvCodeController::class, "removeInvCodes"]);
        });

        Route::prefix("/ip")->group(function () {
            Route::get("/", [IpController::class, "getIps"]);
            Route::post("/", [IpController::class, "addIp"]);
            Route::patch("/{id}", [IpController::class, "updateIp"]);
            Route::delete("/", [IpController::class, "removeIps"]);
        });

        Route::prefix("/token")->group(function () {
            Route::get("/", [TokenController::class, "getTokens"]);
            Route::post("/", [TokenController::class, "addToken"]);
            Route::post("/generate", [TokenController::class, "generateToken"]);
            Route::patch("/{id}", [TokenController::class, "updateToken"]);
            Route::delete("/", [TokenController::class, "removeTokens"]);
        });

        Route::prefix("/config")->group(function () {
            Route::prefix("/main")->group(function () {
                Route::get("/", [MainConfigController::class, "getConfig"]);
                Route::patch("/", [MainConfigController::class, "updateConfig"]);
                Route::post("/testAuth", [MainConfigController::class, "testAuth"]);
            });

            Route::prefix("/mail")->group(function () {
                Route::get("/", [MailConfigController::class, "getMailConfig"]);
                Route::post("/", [MailConfigController::class, "sendTestMail"]);
                Route::patch("/", [MailConfigController::class, "updateMailConfig"]);
            });
        });
    });
});