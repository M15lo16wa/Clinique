<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PatientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'patient') {
            return response()->json(['message' => 'Accès refusé. Réservé aux patients.'], 403);
        }

        if (! $request->user()->is_active) {
            return response()->json(['message' => 'Compte désactivé. Contactez l\'administrateur.'], 403);
        }

        return $next($request);
    }
}
