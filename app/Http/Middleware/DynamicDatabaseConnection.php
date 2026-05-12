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

        return $next($request);
    }
}
