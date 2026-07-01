<?php

namespace Modules\ITSP\Controllers\StrategicHouse\StrategicPillars;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\StrategicPillars\StoreGoalRequest;
use App\Http\Requests\ProgramPlanning\StrategicPillars\UpdateGoalRequest;
use App\Models\Goal;
use App\Services\StrategicHouse\StrategicPillars\StrategicPillarStructureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function __construct(
        private readonly StrategicPillarStructureService $strategicPillarStructureService
    ) {}

    public function store(StoreGoalRequest $request): JsonResponse|RedirectResponse
    {
        $goal = $this->strategicPillarStructureService->createGoal($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Blok pilar berhasil ditambahkan.',
                'data' => $goal,
            ]);
        }

        return back()->with('success', 'Blok pilar berhasil ditambahkan.');
    }

    public function update(UpdateGoalRequest $request, Goal $goal): JsonResponse|RedirectResponse
    {
        $goal = $this->strategicPillarStructureService->updateGoal($goal, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Blok pilar berhasil diperbarui.',
                'data' => $goal,
            ]);
        }

        return back()->with('success', 'Blok pilar berhasil diperbarui.');
    }

    public function destroy(Request $request, Goal $goal): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'pilar' => ['required', 'string', 'in:1,2'],
        ]);

        $this->strategicPillarStructureService->deleteGoal($goal, (string) $validated['pilar']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Blok pilar berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Blok pilar berhasil dihapus.');
    }
}
