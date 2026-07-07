<?php

namespace Modules\ITOM\Controllers\OperatingModel\STK;

use App\Http\Controllers\Controller;
use App\Models\MstCompany;
use App\Models\MstKpi;
use App\Models\MstRegulation;
use App\Services\BusinessProcess\BusinessProcessV2Service;
use Inertia\Inertia;
use Inertia\Response;

class StkController extends Controller
{
    /**
     * Display a listing of STK items under Operating Model.
     */
    public function index(BusinessProcessV2Service $businessProcessV2Service): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/STK/Index', [
            'prosesBisnisV2' => Inertia::defer(fn() => $businessProcessV2Service->getProsesBisnisV2List()),
            'companyOptions' => Inertia::defer(fn() => MstCompany::orderBy('name')->get(['id', 'name'])),
            'kpiList' => Inertia::defer(fn() => MstKpi::orderBy('deskripsi')->get(['id', 'deskripsi'])),
            'regulations' => Inertia::defer(fn() => MstRegulation::orderBy('judul')->get(['id', 'judul', 'nomor', 'tipe', 'parent_id', 'status'])),
        ]);
    }
}