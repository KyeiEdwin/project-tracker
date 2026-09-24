<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerifiedOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web');

        if ($user && ($user->role?->name === 'admin' || $user->hasVerifiedEmail())) {
            return $next($request);
        }

        return redirect()->route('verification.notice');
    }
}
