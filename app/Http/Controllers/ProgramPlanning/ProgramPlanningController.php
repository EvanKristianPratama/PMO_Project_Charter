<?php

namespace App\Http\Controllers\ProgramPlanning;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramPlanningController extends Controller
{
    public function rstiSubHolding(): Response|RedirectResponse
    {
        return Inertia::render('ProgramPlanning/RstiSubHolding');
    }
}
