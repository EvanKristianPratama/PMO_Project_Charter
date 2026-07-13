<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\ITSP\Models\MstInitiative;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __invoke(Request $request, MstInitiative $digitalInitiative): RedirectResponse
    {
        return redirect()
            ->route('master-data.mst-initiatives.edit', $digitalInitiative)
            ->with('info', 'Gunakan halaman Master Initiative untuk memperbarui data digital initiative.');
    }
}
