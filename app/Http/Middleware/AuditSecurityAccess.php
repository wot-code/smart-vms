<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuditSecurityAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Let the request finish first
        $response = $next($request);

        // 2. Define the conditions for a security log
        // Check for 401 (Unauthorized) or 403 (Forbidden)
        $isUnauthorized = in_array($response->getStatusCode(), [401, 403]);

        // Check for Guests (Logged out) trying to access any URL containing "admin"
        // This catches the 302 Redirect to the login page
        $isGuestProbingAdmin = (
            $response->getStatusCode() === 302 && 
            !Auth::check() && 
            str_contains($request->path(), 'admin')
        );

        // 3. If either condition is met, record it
        if ($isUnauthorized || $isGuestProbingAdmin) {
            \App\Models\SecurityLog::create([
                'user_id'    => Auth::id(), // Will be null for guests
                'action'     => Auth::check() ? 'UNAUTHORIZED_ACCESS_ATTEMPT' : 'GUEST_PROBING_ADMIN',
                'url'        => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}