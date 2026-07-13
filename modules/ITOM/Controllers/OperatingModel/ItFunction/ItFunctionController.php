<?php

namespace Modules\ITOM\Controllers\OperatingModel\ItFunction;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstBod;
use Modules\ITOM\Models\MstCompany;
use Modules\ITOM\Models\MstRegulation;
use Modules\ITOM\Services\BusinessProcess\Function\FunctionService;
use Inertia\Inertia;
use Inertia\Response;

class ItFunctionController extends Controller
{
    /**
     * Display a listing of Function items under Operating Model.
     */
    public function index(FunctionService $functionService): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/ItFunction/Index', [
            'functions' => Inertia::defer(fn() => $functionService->getFunctions()),
            'companyOptions' => Inertia::defer(fn() => MstCompany::orderBy('name')->get(['id', 'name'])),
            'bodOptions' => Inertia::defer(fn() => MstBod::orderBy('order')->orderBy('name')->get(['id', 'name', 'alias', 'parent_id', 'order', 'pejabat', 'tipe'])),
            'regulations' => Inertia::defer(fn() => MstRegulation::orderBy('judul')->get(['id', 'judul', 'nomor', 'tipe', 'parent_id', 'status'])),
        ]);
    }
}