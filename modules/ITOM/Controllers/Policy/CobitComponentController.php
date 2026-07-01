<?php

namespace Modules\ITOM\Controllers\Policy;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CobitComponentController extends Controller
{
    /**
     * Display the COBIT Component API Documentation page.
     */
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/Policy/CobitComponent/Index');
    }
}
