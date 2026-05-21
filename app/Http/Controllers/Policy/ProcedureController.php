<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstActor;
use App\Models\MstSop;
use Inertia\Inertia;
use Inertia\Response;

class ProcedureController extends Controller
{
    /**
     * Display a listing of procedures.
     */
    public function index(): Response
    {
        $actors = MstActor::get();
        $sop = MstSop::get();

        return Inertia::render('Procedure/Index', [
            'actors' => $actors,
            'sop' => $sop,
        ]);
    }
}
