<?php

namespace Modules\ITOM\Controllers\Regulation;

use App\Http\Controllers\Controller;
use App\Models\MstRegulation;
use App\Services\Regulation\RegulationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PolicyStandartProcedureController extends Controller
{
    /**
     * @var RegulationService
     */
    protected $regulationService;

    /**
     * PolicyStandartProcedureController constructor.
     *
     * @param RegulationService $regulationService
     */
    public function __construct(RegulationService $regulationService)
    {
        $this->regulationService = $regulationService;
    }

    /**
     * Display a listing of regulations for CRUD management.
     */
    public function index(): Response
    {
        $data = $this->regulationService->getIndexData();

        return Inertia::render('modules/ITOM/Regulation/PolicyStandartProcedure/Index', $data);
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
            'company_id' => 'nullable|integer|exists:mst_company,id',
            'owner_id' => 'nullable|integer|exists:mst_bod,id',
            'parent_id' => 'nullable|integer|exists:mst_regulation,id',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|string',
            'revoked_ids' => 'nullable|array',
            'revoked_ids.*' => 'integer|exists:mst_regulation,id',
            'related_ids' => 'nullable|array',
            'related_ids.*' => 'integer|exists:mst_regulation,id',
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

        $this->regulationService->create($validated);

        return redirect()
            ->route('itom.policy.regulation.index')
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
            'company_id' => 'nullable|integer|exists:mst_company,id',
            'owner_id' => 'nullable|integer|exists:mst_bod,id',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|string',
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
            'revoked_ids' => 'nullable|array',
            'revoked_ids.*' => [
                'integer',
                'exists:mst_regulation,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Kebijakan tidak boleh mencabut dirinya sendiri.');
                    }
                },
            ],
            'related_ids' => 'nullable|array',
            'related_ids.*' => [
                'integer',
                'exists:mst_regulation,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Kebijakan tidak boleh terkait dengan dirinya sendiri.');
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

        $this->regulationService->update($regulation, $validated);

        return redirect()
            ->route('itom.policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil diperbarui.');
    }

    /**
     * Remove the specified regulation.
     */
    public function destroy(int $id): RedirectResponse
    {
        $regulation = MstRegulation::findOrFail($id);
        $this->regulationService->delete($regulation);

        return redirect()
            ->route('itom.policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil dihapus.');
    }

    /**
     * Get the preview data for a specific regulation.
     */
    public function previewData(int $id)
    {
        $regulation = MstRegulation::findOrFail($id);
        $data = $this->regulationService->getPreviewData($regulation);

        return response()->json($data);
    }
}
