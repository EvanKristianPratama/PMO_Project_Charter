<?php

namespace App\Http\Controllers\Policy;

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
        return Inertia::render('Policy/CobitComponent/Index');
    }
}
