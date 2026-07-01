<?php

namespace Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives\StoreVersionAnalysisRequest;
use App\Models\TrsPcVersionAnalysis;
use App\Models\TrsProject;
use App\Services\ProgramImplementation\ProjectCharter\ITInitiatives\VersionAnalysisService;
use Illuminate\Http\RedirectResponse;

class VersionAnalysisController extends Controller
{
    public function __construct(
        private readonly VersionAnalysisService $versionAnalysisService
    ) {}

    public function store(StoreVersionAnalysisRequest $request, TrsProject $project): RedirectResponse
    {
        $analysis = $this->versionAnalysisService->storeVersionAnalysis($project, $request->validated());
        $versionLabel = trim((string) ($analysis->version_label ?? ''));
        $successLabel = $versionLabel !== '' ? $versionLabel : 'versi terpilih';

        return back()->with('success', sprintf('Version analysis %s berhasil disimpan.', $successLabel));
    }

    public function update(
        StoreVersionAnalysisRequest $request,
        TrsProject $project,
        TrsPcVersionAnalysis $analysis
    ): RedirectResponse {
        $updatedAnalysis = $this->versionAnalysisService->updateVersionAnalysis($project, $analysis, $request->validated());
        $versionLabel = trim((string) ($updatedAnalysis->version_label ?? ''));
        $successLabel = $versionLabel !== '' ? $versionLabel : 'versi terpilih';

        return back()->with('success', sprintf('Version analysis %s berhasil diperbarui.', $successLabel));
    }
}
