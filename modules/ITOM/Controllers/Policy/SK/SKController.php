<?php

namespace Modules\ITOM\Controllers\Policy\SK;

use App\Http\Controllers\Controller;
use App\Models\MstCompany;
use App\Models\MstRegulation;
use App\Models\MstBod;
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
        $regulations = MstRegulation::with(['organization.groub.company', 'parent', 'revokedRegulations', 'relatedRegulations', 'company.company'])
            ->withCount(['generalPolicies'])
            ->where('tipe', 'Surat Keputusan')
            ->orderBy('id', 'asc')
            ->get();
        $organizations = TrsOrganization::all();
        $companies = MstCompany::orderBy('name')->get();
        $bods = MstBod::orderBy('order')->orderBy('name')->get();

        return Inertia::render('modules/ITOM/Policy/SK/Index', [
            'regulations' => $regulations,
            'organizations' => $organizations,
            'companies' => $companies,
            'bods' => $bods,
        ]);
    }
}
