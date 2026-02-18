<?php

namespace App\Http\Controllers\CompanyProfile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['groups.organization'])->get();

        return Inertia::render('CompanyProfile/Index', [
            'companies' => $companies,
        ]);
    }
}
