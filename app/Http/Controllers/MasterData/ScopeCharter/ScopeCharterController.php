<?php

namespace App\Http\Controllers\MasterData\ScopeCharter;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use App\Models\TrsScInitiative;
use App\Models\UseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScopeCharterController extends Controller
{
    private function initiativeRules(): array
    {
        return [
            'category_fase' => 'nullable|string|max:255',
            'ref_id' => 'nullable|integer',
            'useCase_id' => 'nullable|integer|exists:trs_use_case,id',
            'useCase_description' => 'nullable|string',
            'value' => 'nullable|string|max:255',
            'urgency' => 'nullable|string|max:255',
            'source_id' => 'nullable|integer|exists:mst_data_source,id',
        ];
    }

    private function dropdownOptions(): array
    {
        return [
            'useCaseOptions' => UseCase::orderBy('name')
                ->get(['id', 'name'])
                ->values(),

            'sourceOptions' => DataSource::orderBy('name')
                ->get(['id', 'name'])
                ->values(),
        ];
    }

    public function index(): Response
    {
        $initiatives = TrsScInitiative::with([
            'useCase:id,name',
            'source:id,name',
        ])->latest()->get();

        return Inertia::render('MasterData/ScopeCharter/Index', [
            'initiatives' => $initiatives,
            ...$this->dropdownOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->initiativeRules());

        TrsScInitiative::create($validated);

        return redirect()
            ->route('master-data.scope-charter.index')
            ->with('success', 'Scope Charter berhasil ditambahkan.');
    }

    public function update(Request $request, TrsScInitiative $scopeCharter): RedirectResponse
    {
        $validated = $request->validate($this->initiativeRules());
        $scopeCharter->update($validated);

        return redirect()
            ->route('master-data.scope-charter.index')
            ->with('success', 'Scope Charter berhasil diperbarui.');
    }

    public function destroy(TrsScInitiative $scopeCharter): RedirectResponse
    {
        $scopeCharter->delete();

        return redirect()
            ->route('master-data.scope-charter.index')
            ->with('success', 'Scope Charter berhasil dihapus.');
    }
}
