<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleFilter
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        $user = Auth::user();
        if (!$user) return ResponseController::userNotLogin();
        if (!in_array($user['role'], $role)) return ResponseController::permissionsDenied();
        return $next($request);
    }
}
