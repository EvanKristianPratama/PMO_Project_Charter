<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('itsp.program-planning.program-definition.digital-initiatives');
    }
}
