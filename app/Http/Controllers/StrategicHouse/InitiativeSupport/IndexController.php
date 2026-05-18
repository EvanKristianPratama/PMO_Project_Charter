<?php

namespace App\Http\Controllers\StrategicHouse\InitiativeSupport;

use App\Http\Controllers\Controller;
use App\Services\StrategicHouse\InitiativeSupport\InitiativeSupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly InitiativeSupportService $initiativeSupportService
    ) {}
    public function __invoke(): Response|RedirectResponse
    {
        return Inertia::render(
            'StrategicHouse/InitiativeSupport/Index',
            $this->initiativeSupportService->getPageProps()
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'digital_ids' => ['required', 'array', 'min:1'],
            'digital_ids.*' => [
                'required',
                'integer',
                Rule::exists('mst_initiative', 'id')->where(fn ($query) => $query->where('tipe_initiative', 1)),
            ],
            'it_ids' => ['required', 'array', 'min:1'],
            'it_ids.*' => [
                'required',
                'integer',
                Rule::exists('mst_initiative', 'id')->where(fn ($query) => $query->where('tipe_initiative', 2)),
            ],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $createdCount = $this->initiativeSupportService->storeMappings($validated);

        return back()->with('success', $createdCount > 1
            ? "{$createdCount} mapping initiative support berhasil ditambahkan."
            : 'Mapping initiative support berhasil ditambahkan.');
    }

    public function destroyMappings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mapping_ids' => ['required', 'array', 'min:1'],
            'mapping_ids.*' => ['required', 'integer', 'exists:trs_initiative_support,id'],
        ]);

        $deletedCount = $this->initiativeSupportService->deleteMappings($validated['mapping_ids']);

        return back()->with('success', $deletedCount > 1
            ? "{$deletedCount} mapping initiative support berhasil dihapus."
            : 'Mapping initiative support berhasil dihapus.');
    }
}
