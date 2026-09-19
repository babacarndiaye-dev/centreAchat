<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Usage: 'permission:produits.voir' (single) or 'permission:produits.voir,stock.voir'
     * (passes if the user has ANY one of the listed permissions — for screens shared
     * by more than one module, e.g. the catalogue page also used by stock roles).
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user || ! collect($permissions)->contains(fn (string $permission) => $user->hasPermission($permission))) {
            abort(403, "Vous n'avez pas la permission requise (".implode(' ou ', $permissions).") pour cette action.");
        }

        return $next($request);
    }
}
