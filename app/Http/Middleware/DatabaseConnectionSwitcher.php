<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class DatabaseConnectionSwitcher
{
    public function handle(Request $request, Closure $next)
    {
        $connection = Session::get('db_connection', 'cloud'); // default to cloud

        if (in_array($connection, ['local', 'cloud'])) {
            Config::set('database.default', $connection);
        }

        return $next($request);
    }
}