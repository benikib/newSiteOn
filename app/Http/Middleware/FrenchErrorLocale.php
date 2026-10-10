<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FrenchErrorLocale
{
    public function handle(Request $request, Closure $next)
    {
        $previousLocale = app()->getLocale();
        app()->setLocale('fr');

        try {
            return $next($request);
        } finally {
            app()->setLocale($previousLocale);
        }
    }
}