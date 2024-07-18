<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class AutoUpdate
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1.3.5 迁移 添加卡密ip
        if (!Schema::hasColumn("tokens", "ip")) {
            Schema::table("tokens", function (Blueprint $table) {
                $table->string("ip")->nullable()->after("day");
            });
        }

        // 1.3.14 迁移 增加账号类型 access_token refresh_token
        if (!Schema::hasColumn("accounts", "account_type")) {
            Schema::table("accounts", function (Blueprint $table) {
                $table->enum("account_type", ["cookie", "access_token"])->default("cookie")->after("baidu_name");
                $table->longText("cookie")->nullable()->change();
                $table->longText("access_token")->nullable()->after("cookie");
                $table->longText("refresh_token")->nullable()->after("access_token");
            });
        }

        // 1.3.14 迁移 增加token到期时间
        if (!Schema::hasColumn("accounts", "expired_at")) {
            Schema::table("accounts", function (Blueprint $table) {
                $table->timestamp("expired_at")->nullable()->after("refresh_token");
            });
        }

        return $next($request);
    }
}
