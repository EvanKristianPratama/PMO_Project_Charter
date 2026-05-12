<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DynamicDatabaseConnection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Read preferred connection from session
        if ($request->hasSession() && $request->session()->has('active_db_connection')) {
            $connection = $request->session()->get('active_db_connection');

            // Ensure connection definition exists in config before switching
            if (Config::has("database.connections.{$connection}")) {
                Config::set('database.default', $connection);
                
                // Force a clean reconnection with new settings if not yet instantiated
                DB::purge(); 
            }
        }

        $response = $next($request);

        // Post-Request Enforcement Guard:
        // If a logged-in user is detected NOT to be an Administrator, 
        // immediately enforce 'sqlite' fallback to lock non-admins into local mode exclusively.
        if (auth()->check() && !auth()->user()->isAdminUser()) {
            if (Config::get('database.default') !== 'sqlite') {
                auth()->logout();
                
                if ($request->hasSession()) {
                    $request->session()->put('active_db_connection', 'sqlite');
                    $request->session()->flash('error', 'Akses Master dibatasi hanya untuk Administrator. Mode Lokal diaktifkan.');
                }
                
                return redirect()->route('login');
            }
        }

        return $response;
    }
}
