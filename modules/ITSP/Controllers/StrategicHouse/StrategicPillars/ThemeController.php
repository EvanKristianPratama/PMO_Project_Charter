<?php

namespace Modules\ITSP\Controllers\StrategicHouse\StrategicPillars;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\StrategicPillars\StoreThemeRequest;
use App\Http\Requests\ProgramPlanning\StrategicPillars\UpdateThemeRequest;
use App\Models\Theme;
use App\Services\StrategicHouse\StrategicPillars\StrategicPillarStructureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function __construct(
        private readonly StrategicPillarStructureService $strategicPillarStructureService
    ) {}

    public function store(StoreThemeRequest $request): JsonResponse|RedirectResponse
    {
        $theme = $this->strategicPillarStructureService->createTheme($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Theme berhasil ditambahkan.',
                'data' => $theme,
            ]);
        }

        return back()->with('success', 'Theme berhasil ditambahkan.');
    }

    public function update(UpdateThemeRequest $request, Theme $theme): JsonResponse|RedirectResponse
    {
        $theme = $this->strategicPillarStructureService->updateTheme($theme, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Theme berhasil diperbarui.',
                'data' => $theme,
            ]);
        }

        return back()->with('success', 'Theme berhasil diperbarui.');
    }

    public function destroy(Request $request, Theme $theme): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'pilar' => ['required', 'string', 'in:1,2'],
        ]);

        $this->strategicPillarStructureService->deleteTheme($theme, (string) $validated['pilar']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Theme berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Theme berhasil dihapus.');
    }
}
