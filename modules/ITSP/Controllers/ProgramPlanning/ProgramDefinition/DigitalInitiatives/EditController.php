<?php

namespace Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Illuminate\Http\RedirectResponse;

class EditController extends Controller
{
    public function __invoke(MstInitiative $digitalInitiative): RedirectResponse
    {
        return redirect()
            ->route('master-data.mst-initiatives.edit', $digitalInitiative)
            ->with('info', 'Halaman edit Digital Initiative lama sudah dipindahkan ke Master Initiative.');
    }
}
