<?php

namespace App\Http\Controllers\MasterData\MstInitiative;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use App\Models\Goal;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\ScInitiative;
use App\Models\StatusMstInitiative;
use App\Models\Theme;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MstInitiativeController extends Controller
{
    /* ── Validation ────────────────────────────────── */

    private function rules(): array
    {
        return [
            'code' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tipe_initiative' => 'required|integer|in:1,2',
            'coe_id' => 'nullable|integer|exists:mst_coe,id',
            'business_unit' => 'nullable|integer|exists:trs_organization,id',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|integer|exists:mst_data_source,id',
        ];
    }

    private function statusRules(): array
    {
        return [
            'status' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ];
    }

    /* ── Dropdown options (DRY) ────────────────────── */

    private function dropdownOptions(): array
    {
        return [
            'coeOptions' => MstCoe::orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
                ->values(),

            'organizationOptions' => TrsOrganization::with('groub:id,name')
                ->orderBy('name')
                ->get(['id', 'name', 'groub_id'])
                ->map(fn ($o) => [
                    'id' => $o->id,
                    'name' => $o->name,
                    'groub' => $o->groub?->name,
                ])
                ->values(),

            'tipeOptions' => [
                ['id' => 1, 'label' => 'Digital Initiative'],
                ['id' => 2, 'label' => 'IT Initiative'],
            ],
            'sourceOptions' => DataSource::query()
                ->select(['id', 'name'])
                ->orderBy('id')
                ->get()
                ->values(),
        ];
    }

    /* ── Initiative CRUD ──────────────────────────── */

    public function index(): Response
    {
        $initiatives = MstInitiative::with([
            'coe:id,name',
            'organization:id,name,groub_id',
            'organization.groub:id,name',
            'latestStatus',
            'sourceData:id,name',
        ])->orderBy('code')->get();

        return Inertia::render('MasterData/MstInitiative/Index', [
            'mstInitiatives' => $initiatives,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('MasterData/MstInitiative/Create', $this->dropdownOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $payload = $validated;
        $payload['status'] = $payload['status'] ?? 'drafting';

        $initiative = MstInitiative::create($payload);
        $this->syncScopeCharterFromSource($initiative);

        // Auto-create initial status history entry
        $initiative->statusHistory()->create([
            'status' => $validated['status'],
            'tanggal' => now(),
            'notes' => null,
        ]);

        // Save additional statuses if provided (from Create page)
        foreach ($request->input('statuses', []) as $s) {
            if (! empty($s['status'])) {
                $initiative->statusHistory()->create([
                    'status' => $s['status'],
                    'tanggal' => $s['tanggal'] ?? null,
                    'notes' => $s['notes'] ?? null,
                ]);
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Master Initiative berhasil ditambahkan.');
    }

    public function edit(MstInitiative $mstInitiative): Response
    {
        $mstInitiative->load([
            'coe:id,name',
            'organization:id,name,groub_id',
            'organization.groub:id,name',
            'taggings' => fn ($q) => $q->with(['theme', 'initiative']),
            'statusHistory' => fn ($q) => $q->orderByDesc('id'),
        ]);

        $allGoals = Goal::select('id', 'code', 'title')
            ->orderBy('code', 'asc')
            ->get();

        $allThemes = Theme::select('id', 'name', 'theme_number', 'idGoal')
            ->orderBy('theme_number', 'asc')
            ->get();

        return Inertia::render('MasterData/MstInitiative/Edit', [
            'initiative' => $mstInitiative,
            'allGoals' => $allGoals,
            'allThemes' => $allThemes,
            ...$this->dropdownOptions(),
        ]);
    }

    public function update(Request $request, MstInitiative $mstInitiative): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $mstInitiative->update($validated);
        $this->syncScopeCharterFromSource($mstInitiative->fresh());

        return redirect()
            ->route('master-data.mst-initiatives.edit', $mstInitiative)
            ->with('success', 'Master Initiative berhasil diperbarui.');
    }

    public function destroy(MstInitiative $mstInitiative): RedirectResponse
    {
        $mstInitiative->delete();

        return redirect()
            ->route('master-data.mst-initiatives.index')
            ->with('success', 'Master Initiative berhasil dihapus.');
    }

    /* ── Status History CRUD ──────────────────────── */

    public function storeStatus(Request $request, MstInitiative $mstInitiative): RedirectResponse
    {
        $mstInitiative->statusHistory()->create($request->validate($this->statusRules()));

        return redirect()
            ->route('master-data.mst-initiatives.edit', $mstInitiative)
            ->with('success', 'Status berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, StatusMstInitiative $status): RedirectResponse
    {
        $status->update($request->validate($this->statusRules()));

        return redirect()
            ->route('master-data.mst-initiatives.edit', $status->initiative_id)
            ->with('success', 'Status berhasil diperbarui.');
    }

    public function destroyStatus(StatusMstInitiative $status): RedirectResponse
    {
        $initiativeId = $status->initiative_id;
        $status->delete();

        return redirect()
            ->route('master-data.mst-initiatives.edit', $initiativeId)
            ->with('success', 'Status berhasil dihapus.');
    }

    private function syncScopeCharterFromSource(MstInitiative $initiative): void
    {

        $sourceType = $this->resolveSourceType($initiative->source);

        if ($sourceType === null) {
            return;
        }

        $scopeInitiative = ScInitiative::query()
            ->where('initiative_id', $initiative->id)
            ->latest('id')
            ->first();

        if (! $scopeInitiative) {
            $scopeInitiative = ScInitiative::create([
                'initiative_id' => $initiative->id,
                'alias' => $this->initiativeAlias($initiative),
                'useCase_description' => $this->initiativeDescription($initiative),
                'value' => 4,
                'urgency' => 4,
            ]);
        } else {
            $scopeInitiative->update([
                'alias' => $this->initiativeAlias($initiative),
                'useCase_description' => $this->initiativeDescription($initiative),
            ]);
        }

        if ($sourceType === 'compendium') {
            $this->upsertScopeDetail($scopeInitiative, $initiative);

            return;
        }

        // Appendix: hanya butuh scope initiative (tanpa detail).
        DB::table('trs_sc_details')->where('digital_id', $scopeInitiative->id)->delete();
    }

    private function resolveSourceType(?int $sourceId): ?string
    {
        if (! $sourceId) {
            return null;
        }

        $sourceName = strtolower((string) DataSource::query()->whereKey($sourceId)->value('name'));

        if (str_contains($sourceName, 'appendix')) {
            return 'appendix';
        }

        if (str_contains($sourceName, 'compendium') || str_contains($sourceName, 'compedium')) {
            return 'compendium';
        }

        return null;
    }

    private function upsertScopeDetail(ScInitiative $scopeInitiative, MstInitiative $initiative): void
    {
        $now = now();
        $existing = DB::table('trs_sc_details')
            ->where('digital_id', $scopeInitiative->id)
            ->first();

        $payload = [
            'digital_id' => $scopeInitiative->id,
            'useCase_description' => $this->initiativeDescription($initiative),
            'current_situation' => '-',
            'key_functionalities' => '-',
            'value_detail' => '-',
            'urgency_detail' => '-',
            'ease_implementation' => 4,
            'ease_detail' => '-',
            'resource_requirement' => 4,
            'resource_detail' => '-',
            'interpendencies' => '-',
            'sign_by' => 'SYSTEM',
            'updated_at' => $now,
        ];

        if ($existing) {
            DB::table('trs_sc_details')
                ->where('id', $existing->id)
                ->update($payload);

            return;
        }

        DB::table('trs_sc_details')->insert([
            ...$payload,
            'created_at' => $now,
        ]);
    }

    private function initiativeAlias(MstInitiative $initiative): string
    {
        $code = trim((string) ($initiative->code ?? ''));

        if ($code !== '') {
            return $code;
        }

        return str($initiative->name)->limit(255)->toString();
    }

    private function initiativeDescription(MstInitiative $initiative): string
    {
        $description = trim((string) ($initiative->description ?? ''));

        if ($description !== '') {
            return $description;
        }

        return trim((string) ($initiative->name ?? '-')) ?: '-';
    }
}
