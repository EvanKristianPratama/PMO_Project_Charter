<?php

namespace Modules\ITSP\Controllers\StrategicHouse\StrategicPillars;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\StrategicPillars\StoreInitiativeTaggingRequest;
use App\Models\InitiativeTagging;
use App\Services\StrategicHouse\StrategicPillars\InitiativeTaggingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InitiativeTaggingController extends Controller
{
    public function __construct(
        private readonly InitiativeTaggingService $initiativeTaggingService
    ) {}

    public function store(StoreInitiativeTaggingRequest $request): JsonResponse|RedirectResponse
    {
        $initiativeTagging = $this->initiativeTaggingService->createInitiativeTagging($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Initiative tagging berhasil ditambahkan.',
                'data' => $initiativeTagging,
            ]);
        }

        return back()->with('success', 'Initiative tagging berhasil ditambahkan.');
    }

    public function destroy(Request $request, InitiativeTagging $tagging): JsonResponse|RedirectResponse
    {
        $this->initiativeTaggingService->deleteInitiativeTagging($tagging);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Initiative tagging berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Initiative tagging berhasil dihapus.');
    }
}
