<?php

namespace Modules\ITOM\Controllers\Regulation\SK;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstCompany;
use Modules\ITOM\Models\MstRegulation;
use Modules\ITOM\Models\MstBod;
use App\Models\TrsOrganization;
use Inertia\Inertia;
use Inertia\Response;

class SKController extends Controller
{
    /**
     * Display Surat Keputusan regulations.
     */
    public function index(): Response
    {
        $regulations = MstRegulation::with(['parent', 'revokedRegulations', 'relatedRegulations', 'mstBod.company', 'mstCompany'])
            ->withCount(['generalPolicies'])
            ->where('tipe', 'Surat Keputusan')
            ->orderBy('id', 'asc')
            ->get();
        $organizations = TrsOrganization::all();
        $companies = MstCompany::orderBy('name')->get();
        $bods = MstBod::orderBy('order')->orderBy('name')->get();

        return Inertia::render('modules/ITOM/Regulation/SK/Index', [
            'regulations' => $regulations,
            'organizations' => $organizations,
            'companies' => $companies,
            'bods' => $bods,
        ]);
    }
}
