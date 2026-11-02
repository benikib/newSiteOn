<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte est en attente de validation par un administrateur.',
            ]);
        }

        return $next($request);
    }
}
