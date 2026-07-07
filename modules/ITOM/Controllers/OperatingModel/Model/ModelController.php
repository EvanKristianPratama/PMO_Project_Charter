<?php

namespace Modules\ITOM\Controllers\OperatingModel\Model;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ModelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/Model/Index');
    }
}
