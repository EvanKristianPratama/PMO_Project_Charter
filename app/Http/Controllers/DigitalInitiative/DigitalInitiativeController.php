<?php

namespace App\Http\Controllers\DigitalInitiative;

use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DigitalInitiativeController extends Controller
{
    public function index(): Response
    {
        $statusOptions = InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values();

        $baselineStatus = $statusOptions->firstWhere('name', 'baseline');
        $baselineStatusId = (int) ($baselineStatus['id'] ?? InitiativeStatus::baselineId());

        $initiatives = DigitalInitiative::query()
            ->with(['statusRef:id,name'])
            ->latest()
            ->get();

        $totalDigitalInitiatives = MstInitiative::query()
            ->where('tipe_initiative', 1)
            ->count();

        $masterDigitalInitiatives = MstInitiative::query()
            ->select([
                'id',
                'coe_id',
                'tipe_initiative',
                'business_unit',
                'code',
                'name',
                'description',
                'status',
            ])
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'latestStatus',
            ])
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->get()
            ->values();

        // Build statusCounts from mst_initiative + latestStatus (name-based keys)
        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $validStatuses = ['drafting', 'propose', 'review', 'approved', 'postpone'];
        $statusCounts = [];
        foreach ($masterDigitalInitiatives as $initiative) {
            $raw = strtolower(trim($initiative->latestStatus?->status ?? $initiative->status ?? 'drafting'));
            $canonical = $aliasMap[$raw] ?? $raw;
            if (! in_array($canonical, $validStatuses)) {
                $canonical = 'drafting';
            }
            $statusCounts[$canonical] = ($statusCounts[$canonical] ?? 0) + 1;
        }
        $totalDigitalApproved = (int) ($statusCounts['approved'] ?? 0);

        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Index', [
            'initiatives' => $initiatives,
            'mstDigitalInitiatives' => $masterDigitalInitiatives,
            'statusOptions' => $statusOptions,
            'completedStatusId' => $baselineStatusId,
            'totalDigitalInitiatives' => $totalDigitalInitiatives,
            'totalDigitalApproved' => $totalDigitalApproved,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Create', [
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $status) => [
                    'id' => (int) $status->id,
                    'name' => $status->name,
                    'label' => ucfirst($status->name),
                ])
                ->values(),
            'defaultStatusId' => InitiativeStatus::DRAFTING,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'tipe_inisiative' => 'nullable|string|max:255',
            'no' => 'required|string|max:255',
            'projectOwner' => 'nullable|string|max:255',
            'useCase' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'value' => 'nullable|string',
            'urgency' => 'nullable|string|max:255',
            'rjjp' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
        ]);

        $digitalInitiative = DigitalInitiative::create($validated);

        if ($digitalInitiative->status) {
            $statusModel = InitiativeStatus::find($digitalInitiative->status);
            $statusName = $statusModel ? $statusModel->name : (string) $digitalInitiative->status;

            \App\Models\UcStatusImplementation::create([
                'digital_initiative_id' => $digitalInitiative->id,
                'status' => $statusName,
                'date' => now()->toDateString(),
                'time_start' => now()->toTimeString(),
            ]);
        }

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative created successfully.');
    }

    public function show(DigitalInitiative $digitalInitiative): Response
    {
        $digitalInitiative->load('statusRef:id,name');

        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Show', [
            'initiative' => $digitalInitiative,
        ]);
    }

    public function edit(DigitalInitiative $digitalInitiative): Response
    {
        $digitalInitiative->load('latestScStatusImplementation');

        return Inertia::render('ProgramImplementation/ProjectCharter/DigitalInitiatives/Edit', [
            'initiative' => $digitalInitiative,
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $status) => [
                    'id' => (int) $status->id,
                    'name' => $status->name,
                    'label' => ucfirst($status->name),
                ])
                ->values(),
            'defaultStatusId' => InitiativeStatus::DRAFTING,
        ]);
    }

    public function update(Request $request, DigitalInitiative $digitalInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'tipe_inisiative' => 'nullable|string|max:255',
            'no' => 'required|string|max:255',
            'projectOwner' => 'nullable|string|max:255',
            'useCase' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'value' => 'nullable|string',
            'urgency' => 'nullable|string|max:255',
            'rjjp' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'sc_status' => 'nullable|string|max:255',
            'sc_review_status' => ['nullable', Rule::in(['At Risk', 'On Track', 'Not Started', 'Not Signed'])],
        ]);

        $oldStatus = $digitalInitiative->status;
        $digitalInitiative->update($validated);

        if ((string) $digitalInitiative->status !== (string) $oldStatus) {
            $statusModel = InitiativeStatus::find($digitalInitiative->status);
            $statusName = $statusModel ? $statusModel->name : (string) $digitalInitiative->status;

            \App\Models\UcStatusImplementation::create([
                'digital_initiative_id' => $digitalInitiative->id,
                'status' => $statusName,
                'date' => now()->toDateString(),
                'time_start' => now()->toTimeString(),
            ]);
        }

        if ($request->filled('sc_status') || $request->filled('sc_review_status')) {
            \App\Models\ScStatusImplementation::updateOrCreate(
                [
                    'digital_initiative_id' => $digitalInitiative->id,
                    'date' => now()->toDateString(), // Using current date for the status log
                ],
                [
                    'status' => $validated['sc_status'],
                    'review_status' => $validated['sc_review_status'],
                    'time_start' => now()->toTimeString(),
                ]
            );
        }

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative updated successfully.');
    }

    public function destroy(DigitalInitiative $digitalInitiative): RedirectResponse
    {
        $digitalInitiative->delete();

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative deleted successfully.');
    }
}
