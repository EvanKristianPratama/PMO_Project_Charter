<?php

namespace Modules\ITOM\Controllers\BusinessProcess\RegulationMapping;

use App\Http\Controllers\Controller;
use App\Services\BusinessProcess\FunctionService;
use App\Models\MstRegulation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegulationMappingController extends Controller
{
    /**
     * Display a listing of Regulation Mappings.
     */
    public function index(Request $request, FunctionService $functionService): Response
    {
        $functions = $functionService->getFunctions();

        $regulations = MstRegulation::orderBy('judul')->get()->map(fn ($r) => [
            'id' => $r->id,
            'judul' => $r->judul,
            'nomor' => $r->nomor,
            'tipe' => $r->tipe,
            'parent_id' => $r->parent_id,
            'status' => $r->status,
        ])->values()->all();

        return Inertia::render('modules/ITOM/BusinessProcess/Index', [
            'functions' => $functions,
            'regulations' => $regulations,
        ]);
    }
}
