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
     * Routes yang dikecualikan dari penggantian koneksi DB dinamis.
     * Halaman login tidak perlu koneksi ke cloud/master — cukup SQLite lokal
     * untuk menghindari hang saat redirect setelah switch database.
     */
    protected array $excludedRoutes = [
        'login',
        'auth.google',
        'auth/google/callback',
        'public.sync',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && $request->session()->has('active_db_connection')) {
            $connection = $request->session()->get('active_db_connection');

            // Pastikan koneksi terdefinisi di config sebelum switch
            if (Config::has("database.connections.{$connection}")) {
                $currentDefault = Config::get('database.default');

                // Hanya purge & switch jika koneksi benar-benar berbeda
                // untuk menghindari overhead reconnect yang tidak perlu
                if ($currentDefault !== $connection) {
                    Config::set('database.default', $connection);

                    // Purge HANYA koneksi lama yang spesifik,
                    // bukan semua koneksi (DB::purge() tanpa argumen).
                    // Ini jauh lebih efisien dan tidak mempengaruhi koneksi lain.
                    DB::purge($currentDefault);
                }
            }
        }

        $response = $next($request);

        // Post-Request Enforcement Guard:
        // Jika user yang login BUKAN Administrator, paksa kembali ke 'sqlite'
        // untuk mencegah user biasa mengakses mode master.
        if (auth()->check() && ! auth()->user()->isAdminUser()) {
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
