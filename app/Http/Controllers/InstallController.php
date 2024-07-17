<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\InvCode;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstallController extends Controller
{
    public function install(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "db_connection" => ["required", Rule::in(["sqlite", "mysql"])],
            "app_name"      => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $dbConfig = config('database');

        if ($dbConfig["default"] !== "no") {
            try {
                DB::purge();
                DB::select("select 1 = 1");
                return ResponseController::dbFileExists();
            } catch (QueryException $e) {
            }
        }

        if ($request["db_connection"] === "mysql") {
            $validator = Validator::make($request->all(), [
                "db_host"     => "required|string",
                "db_port"     => "required|string",
                "db_database" => "required|string",
                "db_username" => "required|string",
                "db_password" => "string",
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $dbConfig['default'] = 'mysql';

            $dbConfig['connections']['mysql'] = array_merge(
                $dbConfig['connections']['mysql'],
                [
                    'host'     => $request['db_host'],
                    'port'     => $request['db_port'],
                    'database' => $request['db_database'],
                    'username' => $request['db_username'],
                    'password' => $request['db_password'],
                ]
            );

            $postData = [
                'APP_NAME'      => '"' . $request['app_name'] . '"',
                'DB_CONNECTION' => $request['db_connection'],
                'DB_HOST'       => $request['db_host'],
                'DB_PORT'       => $request['db_port'],
                'DB_DATABASE'   => $request['db_database'],
                'DB_USERNAME'   => $request['db_username'],
                'DB_PASSWORD'   => $request['db_password']
            ];
        } else {
            $dbFile = database_path("database.sqlite");
            if (File::exists($dbFile)) File::delete($dbFile);
            File::put($dbFile, "");

            $dbConfig['default']               = 'sqlite';
            $dbConfig['connections']['sqlite'] = array_merge($dbConfig['connections']['sqlite'], ['database' => $dbFile]);

            $postData = [
                'APP_NAME'      => '"' . $request['app_name'] . '"',
                'DB_CONNECTION' => $request['db_connection'],
                'DB_HOST'       => "",
                'DB_PORT'       => "",
                'DB_DATABASE'   => $dbFile,
                'DB_USERNAME'   => "",
                'DB_PASSWORD'   => ""
            ];
        }

        config(['database' => $dbConfig]);

        try {
            if (Schema::hasTable('accounts')) Schema::drop('accounts');
            Schema::create("accounts", function (Blueprint $table) {
                $table->id();
                $table->string("baidu_name");
                $table->longText("cookie")->nullable();
                $table->enum("vip_type", ["普通用户", "普通会员", "超级会员", "假超级会员"]);
                $table->boolean("switch");
                $table->string("reason")->nullable();
                $table->string("prov")->nullable();
                $table->timestamp("svip_end_at")->nullable();
                $table->timestamp("last_use_at")->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            if (Schema::hasTable('file_lists')) Schema::drop('file_lists');
            Schema::create("file_lists", function (Blueprint $table) {
                $table->id();
                $table->string("surl");
                $table->string("pwd");
                $table->unsignedBigInteger("fs_id");
                $table->unsignedBigInteger("size");
                $table->string("filename");
                $table->string("md5");
                $table->timestamps();
            });

            if (Schema::hasTable('groups')) Schema::drop('groups');
            Schema::create("groups", function (Blueprint $table) {
                $table->id();
                $table->string("name");
                $table->unsignedBigInteger("count");
                $table->unsignedBigInteger("size");
                $table->timestamps();
                $table->softDeletes();
            });

            if (Schema::hasTable('inv_codes')) Schema::drop('inv_codes');
            Schema::create("inv_codes", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("group_id");
                $table->string("name");
                $table->unsignedBigInteger("can_count");
                $table->timestamps();
                $table->softDeletes();

                $table->foreign("group_id")->references("id")->on("groups");
            });

            if (Schema::hasTable('ips')) Schema::drop('ips');
            Schema::create("ips", function (Blueprint $table) {
                $table->id();
                $table->string("ip");
                $table->unsignedBigInteger("mode");
                $table->timestamps();
            });

            if (Schema::hasTable('tokens')) Schema::drop('tokens');
            Schema::create("tokens", function (Blueprint $table) {
                $table->id();
                $table->string("name");
                $table->unsignedBigInteger("count");
                $table->unsignedBigInteger("size");
                $table->unsignedBigInteger("day");
                $table->string("ip")->nullable();
                $table->timestamp("expired_at")->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            if (Schema::hasTable('users')) Schema::drop('users');
            Schema::create("users", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("inv_code_id");
                $table->string("username");
                $table->string("password");
                $table->string("role");
                $table->timestamps();
                $table->softDeletes();

                $table->foreign("inv_code_id")->references("id")->on("inv_codes");
            });

            if (Schema::hasTable('records')) Schema::drop('records');
            Schema::create("records", function (Blueprint $table) {
                $table->id();
                $table->string("ip");
                $table->unsignedBigInteger("fs_id");
                $table->longText("url");
                $table->string("ua");
                $table->unsignedBigInteger("user_id")->nullable();
                $table->unsignedBigInteger("token_id")->nullable();
                $table->unsignedBigInteger("account_id");
                $table->timestamps();
                $table->softDeletes();

                $table->foreign("fs_id")->references("id")->on("file_lists");
                $table->foreign("user_id")->references("id")->on("users");
                $table->foreign("token_id")->references("id")->on("tokens");
                $table->foreign("account_id")->references("id")->on("accounts");
            });

            $key = "base64:" . base64_encode(Encrypter::generateKey(config("app.cipher")));
            config(["app.key" => $key]);

            Group::query()->create([
                "name"  => "游客分组",
                "count" => 5,
                "size"  => 20
            ]);

            Group::query()->create([
                "name"  => "默认分组",
                "count" => 10,
                "size"  => 20
            ]);

            InvCode::query()->create([
                "group_id"  => 1,
                "name"      => "游客分组邀请码",
                "can_count" => 0
            ]);

            InvCode::query()->create([
                "group_id"  => 2,
                "name"      => "默认分组邀请码",
                "can_count" => 0
            ]);

            User::query()->create([
                "inv_code_id" => 1,
                "username"    => "游客",
                "password"    => Hash::make(Str::random(32)),
                "role"        => "user"
            ]);

            User::query()->create([
                "inv_code_id" => 2,
                "username"    => "admin",
                "password"    => Hash::make("admin"),
                "role"        => "admin"
            ]);
        } catch (QueryException $exception) {
            return ResponseController::dbConnectFailed($exception->getMessage());
        }

        // 写入key以及其他配置
        updateEnv([...$postData, "APP_KEY" => $key]);

        return ResponseController::success();
    }
}
