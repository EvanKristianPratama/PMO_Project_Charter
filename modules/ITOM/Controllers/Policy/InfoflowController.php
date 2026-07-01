<?php

namespace Modules\ITOM\Controllers\Policy;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class InfoflowController extends Controller
{
    /**
     * Display the GAMO Information Flow view.
     */
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/RaciAnalysis/Infoflow/Index');
    }
}
