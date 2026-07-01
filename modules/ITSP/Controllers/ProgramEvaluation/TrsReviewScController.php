<?php

namespace Modules\ITSP\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\TrsReviewSc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TrsReviewScController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'initiative_id' => 'required|exists:mst_initiative,id',
            'month' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'year' => 'required|integer',
            'notes' => 'required|string',
        ]);

        TrsReviewSc::create($validated);

        return Redirect::back()->with('success', 'Note added successfully.');
    }

    public function update(Request $request, TrsReviewSc $trsReviewSc)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $trsReviewSc->update($validated);

        return Redirect::back()->with('success', 'Note updated successfully.');
    }

    public function destroy(TrsReviewSc $trsReviewSc)
    {
        $trsReviewSc->delete();

        return Redirect::back()->with('success', 'Note deleted successfully.');
    }
}
