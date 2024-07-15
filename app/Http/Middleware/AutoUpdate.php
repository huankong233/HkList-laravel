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
            Schema::disableForeignKeyConstraints();

            Schema::rename("tokens", "tokens_old");

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

            $tokens = DB::table("tokens_old")->get();
            foreach ($tokens as $token) {
                DB::table("tokens")->insert([
                    "id"         => $token->id,
                    "name"       => $token->name,
                    "count"      => $token->count,
                    "size"       => $token->size,
                    "day"        => $token->day,
                    "ip"         => null,
                    "expired_at" => $token->expired_at,
                    "created_at" => $token->created_at,
                    "updated_at" => $token->updated_at,
                    "deleted_at" => $token->deleted_at,
                ]);
            }

            Schema::drop("tokens_old");
            if (config("database.default") === "mysql") DB::raw("ALTER TABLE `records` DROP FOREIGN KEY `records_token_id_foreign`; ALTER TABLE `records` ADD CONSTRAINT `records_token_id_foreign` FOREIGN KEY (`token_id`) REFERENCES `tokens`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;");
            Schema::enableForeignKeyConstraints();
        }

        return $next($request);
    }
}
