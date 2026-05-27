<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Acesso restrito ao insight de ranking (usuário id 1 em produção).
 */
class EnsureUserIdOneInsight
{
    public const ALLOWED_USER_ID = 1;

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || (int) Auth::id() !== self::ALLOWED_USER_ID) {
            abort(403, 'Esta área é exclusiva para o usuário autorizado.');
        }

        return $next($request);
    }
}
