<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApprovedB2b
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isApprovedB2B()) {
            abort(403, 'Votre compte professionnel doit être validé pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
