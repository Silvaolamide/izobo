<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('izobo_admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Please sign in to continue.');
        }

        return $next($request);
    }
}
