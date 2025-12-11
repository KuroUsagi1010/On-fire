<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Redirect to team creation only when onboarding is active and we're not already on that route
        if (
            auth()->check()
            && ! auth()->user()->isOnboarded()
            && ! $request->is('team*')
        ) {
            return redirect()->intended('/team/create');
        }

        return $next($request);
    }
}
