<?php

namespace Modules\ITOM\Controllers\OperatingModel\Framework;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class FrameworkController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/Framework/Index');
    }
}
