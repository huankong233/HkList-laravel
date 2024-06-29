<?php

use App\Http\Controllers\v1\AccountController;
use App\Http\Controllers\v1\config\MailConfigController;
use App\Http\Controllers\v1\config\MainConfigController;
use App\Http\Controllers\v1\GroupController;
use App\Http\Controllers\v1\InvCodeController;
use App\Http\Controllers\v1\IpController;
use App\Http\Controllers\v1\ParseController;
use App\Http\Controllers\v1\RecordController;
use App\Http\Controllers\v1\TokenController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware("NeedInstall")->group(function () {
    Route::prefix("/v1")->group(function () {
        Route::prefix("/user")->middleware(["ThrottleRequest"])->group(function () {
            Route::post("/login", [UserController::class, "login"]);
            Route::post("/register", [UserController::class, "register"]);
            Route::delete("/", [UserController::class, "logout"]);
        });

        Route::prefix("/parse")->middleware("IpFilter")->group(function () {
            Route::get("/config", [ParseController::class, "getConfig"]);
            Route::get("/limit", [ParseController::class, "checkLimit"]);
            Route::prefix("/")->middleware(["ThrottleRequest", "NeedPassword"])->group(function () {
                Route::post("/get_file_list", [ParseController::class, "getFileList"]);
                Route::post("/get_vcode", [ParseController::class, "getVcode"]);
                Route::post("/get_download_links", [ParseController::class, "getDownloadLinks"]);
            });
        });

        Route::prefix("/admin")->middleware("RoleFilter:admin")->group(function () {
            Route::pattern("user_id", "[0-9]+");
            Route::prefix("/user")->group(function () {
                Route::get("/{user_id?}", [UserController::class, "getUser"]);
                Route::post("/", [UserController::class, "addUser"]);
                Route::patch("/{user_id}", [UserController::class, "updateUser"]);
                Route::delete("/", [UserController::class, "removeUsers"]);
            });

            Route::pattern("group_id", "[-+]?[0-9]+");
            Route::prefix("/group")->group(function () {
                Route::get("/{group_id?}", [GroupController::class, "getGroup"]);
                Route::post("/", [GroupController::class, "addGroup"]);
                Route::patch("/{group_id}", [GroupController::class, "updateGroup"]);
                Route::delete("/", [GroupController::class, "removeGroups"]);
            });

            Route::pattern("record_id", "[0-9]+");
            Route::prefix("/record")->group(function () {
                Route::get("/{record_id?}", [RecordController::class, "getRecord"]);
                Route::get('/count', [RecordController::class, 'getRecordCount']);
                Route::delete("/", [RecordController::class, "removeRecords"]);
            });

            Route::pattern("account_id", "[0-9]+");
            Route::prefix("/account")->group(function () {
                Route::get("/{account_id?}", [AccountController::class, "getAccount"]);
                Route::post("/", [AccountController::class, "addAccount"]);
                Route::patch("/", [AccountController::class, "updateAccounts"]);
                Route::patch("/switch", [AccountController::class, "switchAccounts"]);
                Route::delete("/", [AccountController::class, "removeAccounts"]);
            });

            Route::pattern("inv_code_id", "[0-9]+");
            Route::prefix("/inv_code")->group(function () {
                Route::get("/{inv_code_id?}", [InvCodeController::class, "getInvCode"]);
                Route::post("/", [InvCodeController::class, "addInvCode"]);
                Route::post("/generate", [InvCodeController::class, "generateInvCode"]);
                Route::patch("/{inv_code_id}", [InvCodeController::class, "updateInvCode"]);
                Route::delete("/", [InvCodeController::class, "removeInvCodes"]);
            });

            Route::pattern("ip_id", "[0-9]+");
            Route::prefix("/ip")->group(function () {
                Route::get("/{ip_id?}", [IpController::class, "getIp"]);
                Route::post("/", [IpController::class, "addIp"]);
                Route::patch("/{ip_id}", [IpController::class, "updateIp"]);
                Route::delete("/", [IpController::class, "removeIps"]);
            });

            Route::pattern("token_id", "[0-9]+");
            Route::prefix("/token")->group(function () {
                Route::get("/{token_id?}", [TokenController::class, "getToken"]);
                Route::post("/", [TokenController::class, "addToken"]);
                Route::post("/generate", [TokenController::class, "generateToken"]);
                Route::patch("/{token_id}", [TokenController::class, "updateToken"]);
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
});
