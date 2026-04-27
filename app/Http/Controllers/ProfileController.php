<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function updateConnection(Request $request)
    {
        $request->validate([
            'connection' => 'required|in:local,cloud',
        ]);

        $connection = $request->input('connection');

        // Set the default connection in session
        Session::put('db_connection', $connection);
        Config::set('database.default', $connection);

        // Return to referrer with flash data
        return redirect()->back()->with('success', 'Database connection switched to ' . ucfirst($connection) . ' successfully.');
    }
}