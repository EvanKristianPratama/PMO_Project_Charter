<?php

namespace Modules\ITSP\Controllers\StrategicHouse\MapTechnology;

use App\Http\Controllers\Controller;
use Modules\ITSP\Models\TrsMapTechnology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapTechnologyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'initiative_id' => 'required|exists:mst_initiative,id',
            'coe_ids' => 'required|array',
            'coe_ids.*' => 'required|exists:mst_coe,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->coe_ids as $coeId) {
                TrsMapTechnology::firstOrCreate([
                    'initiative_id' => $request->initiative_id,
                    'coe_id' => $coeId,
                ]);
            }
        });

        return back()->with('success', 'Mapping teknologi berhasil disimpan.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'initiative_id' => 'required|exists:mst_initiative,id',
            'coe_id' => 'required|exists:mst_coe,id',
        ]);

        TrsMapTechnology::where('initiative_id', $request->initiative_id)
            ->where('coe_id', $request->coe_id)
            ->delete();

        return back()->with('success', 'Mapping teknologi berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'removals' => 'required|array',
            'removals.*.initiative_id' => 'required|exists:mst_initiative,id',
            'removals.*.coe_id' => 'required|exists:mst_coe,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->removals as $removal) {
                TrsMapTechnology::where('initiative_id', $removal['initiative_id'])
                    ->where('coe_id', $removal['coe_id'])
                    ->delete();
            }
        });

        return back()->with('success', 'Mapping teknologi berhasil dihapus.');
    }
}
