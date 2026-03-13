<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ActivityLog::query()->latest();

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by user (hanya user dengan role admin yang bisa filter semua user;
        // user biasa hanya melihat aktivitas sendiri jika filter user dipilih)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by tanggal mulai
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter by tanggal akhir
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search deskripsi / nama / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        // Untuk non-admin, hanya tampilkan event CRUD (bukan login/logout semua user)
        // Opsional: bisa dibatasi hanya event created/updated/deleted
        $logs = $query->paginate(20)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('MasterData/ActivityLog', [
            'logs' => $logs,
            'users' => $users,
            'filters' => $request->only(['event', 'user_id', 'date_from', 'date_to', 'search']),
            'events' => ['login', 'logout', 'created', 'updated', 'deleted'],
            'currentUser' => Auth::user()?->only(['id', 'name', 'email']),
        ]);
    }
}
