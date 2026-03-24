<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirige al usuario a su dashboard correspondiente después del login.
 */
class RedirectByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            return redirect($user->panelUrl());
        }

        return $next($request);
    }
}
