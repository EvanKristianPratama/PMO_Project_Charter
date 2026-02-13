<?php

namespace App\Http\Controllers\StrategicPillar;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Inertia\Inertia;
use Inertia\Response;

class StrategicPillarController extends Controller
{
    public function index(): Response
    {
        $strategicPillars = Goal::with('themes')->get();

        return Inertia::render('StrategicPillar/Index', [
            'strategicPillars' => $strategicPillars,
        ]);
    }
}
