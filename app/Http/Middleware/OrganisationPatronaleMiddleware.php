<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganisationPatronaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->role === 'organisation_patronale'
            && $request->user()->is_approved
            && $request->user()->organisationPatronale()->exists(),
            403
        );

        return $next($request);
    }
}
