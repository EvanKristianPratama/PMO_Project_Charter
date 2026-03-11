<?php

namespace App\Http\Controllers\StrategicPillar;

use App\Http\Controllers\Controller;
use App\Models\InitiativeTagging;
use Illuminate\Http\Request;

class InitiativeTaggingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'initiative_id' => 'required|integer|exists:mst_initiative,id',
            'themes_id'     => 'nullable|integer|exists:trs_themes,id',
            'goal'          => 'nullable|string|max:255',
        ]);

        // Auto-populate goal code from theme's goal if themes_id is provided and goal is empty
        if (!empty($validated['themes_id']) && empty($validated['goal'])) {
            $theme = \App\Models\Theme::with('goal')->find($validated['themes_id']);
            $validated['goal'] = $theme?->goal?->code ?? null;
        }

        // Cek duplicate (termasuk TBC: goal=null, themes_id=null)
        $exists = InitiativeTagging::where('initiative_id', $validated['initiative_id'])
            ->where('themes_id', $validated['themes_id'] ?? null)
            ->where('goal', $validated['goal'] ?? null)
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Mapping ini sudah ada.'], 422);
            }
            return back()->with('error', 'Mapping ini sudah ada.');
        }

        $tagging = InitiativeTagging::create($validated);
        
        // Eager load relationships for the frontend to use immediately
        $tagging->load(['initiative:id,name,code', 'theme:id,name,idGoal']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Initiative tagging berhasil ditambahkan.',
                'data' => $tagging
            ]);
        }

        return back()->with('success', 'Initiative tagging berhasil ditambahkan.');
    }

    public function destroy(Request $request, InitiativeTagging $tagging)
    {
        $tagging->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Initiative tagging berhasil dihapus.'
            ]);
        }

        return back()->with('success', 'Initiative tagging berhasil dihapus.');
    }
}
