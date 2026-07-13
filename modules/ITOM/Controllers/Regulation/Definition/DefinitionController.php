<?php

namespace Modules\ITOM\Controllers\Regulation\Definition;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstDefinition;
use Modules\ITOM\Services\Regulation\DefinitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DefinitionController extends Controller
{
    /**
     * @var DefinitionService
     */
    protected $definitionService;

    /**
     * DefinitionController constructor.
     *
     * @param DefinitionService $definitionService
     */
    public function __construct(DefinitionService $definitionService)
    {
        $this->definitionService = $definitionService;
    }

    /**
     * Display a listing of definitions.
     */
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/Regulation/Definiton/Index', [
            'definitions' => Inertia::defer(fn() => $this->definitionService->getDefinitions()),
            'regulations' => Inertia::defer(fn() => $this->definitionService->getRegulations()),
        ]);
    }

    /**
     * Store a newly created definition.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'definition' => 'required|string',
            'regulation_ids' => 'nullable|array',
            'regulation_ids.*' => 'integer|exists:mst_regulation,id',
        ], [
            'name.required' => 'Istilah/Nama definisi wajib diisi.',
            'definition.required' => 'Deskripsi definisi wajib diisi.',
        ]);

        $this->definitionService->createDefinition($validated);

        return back()->with('success', 'Definisi berhasil ditambahkan.');
    }

    /**
     * Update the specified definition.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $definition = MstDefinition::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'definition' => 'required|string',
            'regulation_ids' => 'nullable|array',
            'regulation_ids.*' => 'integer|exists:mst_regulation,id',
        ], [
            'name.required' => 'Istilah/Nama definisi wajib diisi.',
            'definition.required' => 'Deskripsi definisi wajib diisi.',
        ]);

        $this->definitionService->updateDefinition($definition, $validated);

        return back()->with('success', 'Definisi berhasil diperbarui.');
    }

    /**
     * Remove the specified definition.
     */
    public function destroy(int $id): RedirectResponse
    {
        $definition = MstDefinition::findOrFail($id);
        $this->definitionService->deleteDefinition($definition);

        return back()->with('success', 'Definisi berhasil dihapus.');
    }
}
