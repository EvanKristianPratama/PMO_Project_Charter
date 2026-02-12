<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isApproved()) {
            $user = $request->user();
            Auth::logout();
            
            $statusData = base64_encode(json_encode([
                'status' => $user->status,
                'name' => $user->name,
                'email' => $user->email,
            ]));

            return redirect()->route('login', ['check_status' => $statusData]);
        }

        return $next($request);
    }
}
