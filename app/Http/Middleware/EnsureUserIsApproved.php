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
            
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login', [
                'status' => $user->status,
                'name'   => $user->name,
                'email'  => $user->email,
            ]);
        }

        return $next($request);
    }
}
