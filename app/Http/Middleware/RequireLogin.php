<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('logged_in')) {
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}