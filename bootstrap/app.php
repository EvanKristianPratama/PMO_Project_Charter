<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'approved' => \App\Http\Middleware\EnsureUserIsApproved::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e) {
            // Check if it's a connection refused or timeout error (SQLSTATE[HY000] [2002])
            // or other common database connection errors
            $connectionErrorCodes = [2002, 1045, 1049, 2003, 2005, 2006];
            
            if ($e->getCode() === 2002 || 
                str_contains($e->getMessage(), 'Connection refused') ||
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]') ||
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [1045]') ||
                str_contains($e->getMessage(), 'Access denied for user')) {
                return response()->view('errors.db-offline', [], 503);
            }
        });

        $exceptions->render(function (\PDOException $e) {
            return response()->view('errors.db-offline', [], 503);
        });
    })->create();
