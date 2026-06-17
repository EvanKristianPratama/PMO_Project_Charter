<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstRegulation;
use App\Models\TrsOrganization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegulationController extends Controller
{

    /**
     * Display a listing of regulations for CRUD management.
     */
    public function index(): Response
    {
        $regulations = MstRegulation::with(['organization', 'parent'])
            ->withCount(['generalPolicies'])
            ->orderBy('id', 'asc')
            ->get();
        $organizations = TrsOrganization::all();

        return Inertia::render('Policy/Regulation/Index', [
            'regulations' => $regulations,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Display management page for regulations.
     */
    public function manage(): Response
    {
        $regulations = MstRegulation::with(['organization', 'parent'])
            ->withCount(['generalPolicies'])
            ->orderBy('id', 'asc')
            ->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        return Inertia::render('Policy/Regulation/Manage', [
            'regulations' => $regulations,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Store a newly created regulation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'nomor' => 'nullable|string|max:255',
            'tipe' => 'required|string|max:255',
            'stk' => 'nullable|string|max:255',
            'owner' => 'required|string|max:255',
            'revisi' => 'required|string|max:255',
            'terbit' => 'nullable|date',
            'berlaku' => 'nullable|date',
            'pic_id' => 'nullable|integer|exists:trs_organization,id',
            'parent_id' => 'nullable|integer|exists:mst_regulation,id',
        ], [
            'judul.required' => 'Judul Kebijakan wajib diisi.',
            'tipe.required' => 'Tipe Kebijakan wajib diisi.',
            'owner.required' => 'Owner Kebijakan wajib diisi.',
            'revisi.required' => 'Revisi Kebijakan wajib diisi.',
            'terbit.required' => 'Tanggal Terbit wajib diisi.',
            'terbit.date' => 'Tanggal Terbit harus berupa format tanggal.',
            'berlaku.required' => 'Tanggal Berlaku wajib diisi.',
            'berlaku.date' => 'Tanggal Berlaku harus berupa format tanggal.',
        ]);

        MstRegulation::create($validated);

        return redirect()
            ->route('policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil ditambahkan.');
    }

    /**
     * Update the specified regulation.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $regulation = MstRegulation::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'nomor' => 'nullable|string|max:255',
            'tipe' => 'required|string|max:255',
            'stk' => 'nullable|string|max:255',
            'owner' => 'required|string|max:255',
            'revisi' => 'required|string|max:255',
            'terbit' => 'nullable|date',
            'berlaku' => 'nullable|date',
            'pic_id' => 'nullable|integer|exists:trs_organization,id',
            'parent_id' => [
                'nullable',
                'integer',
                'exists:mst_regulation,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Kebijakan tidak boleh merujuk ke dirinya sendiri sebagai parent.');
                    }
                },
            ],
        ], [
            'judul.required' => 'Judul Kebijakan wajib diisi.',
            'tipe.required' => 'Tipe Kebijakan wajib diisi.',
            'owner.required' => 'Owner Kebijakan wajib diisi.',
            'revisi.required' => 'Revisi Kebijakan wajib diisi.',
            'terbit.required' => 'Tanggal Terbit wajib diisi.',
            'terbit.date' => 'Tanggal Terbit harus berupa format tanggal.',
            'berlaku.required' => 'Tanggal Berlaku wajib diisi.',
            'berlaku.date' => 'Tanggal Berlaku harus berupa format tanggal.',
        ]);

        $regulation->update($validated);

        return redirect()
            ->route('policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil diperbarui.');
    }

    /**
     * Remove the specified regulation.
     */
    public function destroy(int $id): RedirectResponse
    {
        $regulation = MstRegulation::findOrFail($id);
        $regulation->delete();

        return redirect()
            ->route('policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil dihapus.');
    }

    /**
     * Get the preview data for a specific regulation.
     */
    public function previewData(int $id)
    {
        $regulation = MstRegulation::with(['organization', 'parent'])->findOrFail($id);

        if (strtolower($regulation->tipe ?? '') === 'procedure') {
            $actors = \App\Models\MstActor::with('organization')
                ->where('regulation_id', $regulation->id)
                ->get();

            $categories = \App\Models\TrsSopCategory::where('regulation_id', $regulation->id)
                ->orderBy('id')
                ->get();

            $sop = \App\Models\MstSop::with(['category', 'regulation.organization'])
                ->whereHas('category', function ($q) use ($regulation) {
                    $q->where('regulation_id', $regulation->id);
                })
                ->orderBy('category_id')
                ->orderBy('id')
                ->get();

            $flowChartSops = \App\Models\MstSop::with(['category', 'mapActorSops.actor.organization'])
                ->whereHas('category', function ($q) use ($regulation) {
                    $q->where('regulation_id', $regulation->id);
                })
                ->orderBy('category_id')
                ->orderBy('id')
                ->get();

            $tkoSections = \App\Models\TrsTkoSections::with(['contents' => function ($q) use ($regulation) {
                $q->where('regulation_id', $regulation->id);
            }])
            ->orderBy('order')
            ->get();

            return response()->json([
                'tipe' => 'Procedure',
                'regulation' => $regulation,
                'actors' => $actors,
                'categories' => $categories,
                'sop' => $sop,
                'flowChartSops' => $flowChartSops,
                'tkoSections' => $tkoSections,
            ]);
        } else {
            $policies = $regulation->generalPolicies()->orderBy('number')->get();

            $objectives = \App\Models\MstObjective::with(['practices' => function($query) {
                $query->orderBy('practice_id', 'asc');
            }])
            ->where('regulation_id', $regulation->id)
            ->orderByRaw("
                CASE 
                    WHEN objective_id LIKE 'EDM%' THEN 1
                    WHEN objective_id LIKE 'APO%' THEN 2
                    WHEN objective_id LIKE 'BAI%' THEN 3
                    WHEN objective_id LIKE 'DSS%' THEN 4
                    WHEN objective_id LIKE 'MEA%' THEN 5
                    ELSE 6
                END ASC
            ")
            ->orderBy('objective_id', 'asc')
            ->get();

            if ($objectives->isEmpty()) {
                $objectives = \App\Models\MstObjective::with(['practices' => function($query) {
                    $query->orderBy('practice_id', 'asc');
                }])
                ->orderByRaw("
                    CASE 
                        WHEN objective_id LIKE 'EDM%' THEN 1
                        WHEN objective_id LIKE 'APO%' THEN 2
                        WHEN objective_id LIKE 'BAI%' THEN 3
                        WHEN objective_id LIKE 'DSS%' THEN 4
                        WHEN objective_id LIKE 'MEA%' THEN 5
                        ELSE 6
                    END ASC
                ")
                ->orderBy('objective_id', 'asc')
                ->get();
            }

            $roles = \App\Models\MstRole::with(['responsibilities' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->orderBy('id', 'asc')->get();

            return response()->json([
                'tipe' => 'Policy',
                'regulation' => $regulation,
                'policies' => $policies,
                'objectives' => $objectives,
                'roles' => $roles,
            ]);
        }
    }
}
