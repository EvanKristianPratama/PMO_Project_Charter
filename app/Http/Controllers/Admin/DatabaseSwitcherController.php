<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;


class DatabaseSwitcherController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate([
            'connection' => 'required|string|in:sqlite,cloud,mysql'
        ]);

        $target = $request->input('connection');

        // Logout user terlebih dahulu sebelum invalidate session.
        Auth::guard('web')->logout();

        // Invalidate session lama dan generate CSRF token baru.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah session baru dibuat, simpan preferensi koneksi DB.
        // Ini dilakukan SETELAH invalidate agar tersimpan di session yang baru.
        // Middleware DynamicDatabaseConnection akan membaca nilai ini pada
        // request berikutnya (setelah login), BUKAN di halaman login itu sendiri.
        $request->session()->put('active_db_connection', $target);

        return redirect()->route('login')->with('status', "Database switched to {$target}. Please re-login.");
    }
}
