<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

class DatabaseSwitcherController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate([
            'connection' => 'required|string|in:sqlite,cloud,mysql'
        ]);

        $target = $request->input('connection');

        // Persist the selected connection preference to user session
        Session::put('active_db_connection', $target);

        // Important: log user out immediately on change, forcing re-authentication 
        // in context of new database structure to avoid user data cross-contamination.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Restore chosen target for NEXT lifecycle re-check on login redirect
        Session::put('active_db_connection', $target);

        return redirect()->route('login')->with('status', "Database switched to {$target}. Please re-login.");
    }
}
