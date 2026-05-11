<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            try {
                // Auto-sync jika tabel users belum ada di database SQLite lokal
                if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    \Illuminate\Support\Facades\Artisan::call('app:sync-to-sqlite');
                }

                $user = User::first();
                
                if (!$user) {
                    $user = User::create([
                        'name' => 'Offline User',
                        'email' => 'user@pmo.local',
                        'status' => 'approved',
                        'app_role' => 'user',
                    ]);
                } else {
                    $user->update([
                        'status' => 'approved',
                        'app_role' => 'user',
                    ]);
                }

                Auth::login($user, remember: true);
                \App\Services\ActivityLogService::login($user);
            } catch (\Exception $e) {
                // Gagal secara senyap agar tidak merusak alur aplikasi jika DB belum terisi
                \Log::warning('Auto-login failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
