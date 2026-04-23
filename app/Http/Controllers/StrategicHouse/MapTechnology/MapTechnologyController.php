<?php

namespace App\Http\Controllers\StrategicHouse\MapTechnology;

use App\Http\Controllers\Controller;
use App\Models\TrsMapTechnology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapTechnologyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'initiative_id' => 'required|exists:mst_initiative,id',
            'coed_ids' => 'required|array',
            'coed_ids.*' => 'required|exists:mst_coe,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->coed_ids as $coedId) {
                TrsMapTechnology::firstOrCreate([
                    'initiative_id' => $request->initiative_id,
                    'coed_id' => $coedId,
                ]);
            }
        });

        return back()->with('success', 'Mapping teknologi berhasil disimpan.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'initiative_id' => 'required|exists:mst_initiative,id',
            'coed_id' => 'required|exists:mst_coe,id',
        ]);

        TrsMapTechnology::where('initiative_id', $request->initiative_id)
            ->where('coed_id', $request->coed_id)
            ->delete();

        return back()->with('success', 'Mapping teknologi berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'removals' => 'required|array',
            'removals.*.initiative_id' => 'required|exists:mst_initiative,id',
            'removals.*.coed_id' => 'required|exists:mst_coe,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->removals as $removal) {
                TrsMapTechnology::where('initiative_id', $removal['initiative_id'])
                    ->where('coed_id', $removal['coed_id'])
                    ->delete();
            }
        });

        return back()->with('success', 'Mapping teknologi berhasil dihapus.');
    }
}
