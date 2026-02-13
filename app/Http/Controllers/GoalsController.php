<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Inertia\Inertia;
use Inertia\Response;

class GoalsController extends Controller
{
    public function index(): Response
    {
        $goals = Goal::with('themes')->get();

        return Inertia::render('Goals/Index', [
            'goals' => $goals,
        ]);
    }
}
