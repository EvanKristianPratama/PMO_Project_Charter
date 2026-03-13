<?php

namespace App\Http\Controllers\MasterData\BusinessCapability;

use App\Http\Controllers\Controller;
use App\Models\MstBusinessCapability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BusinessCapabilityController extends Controller
{
    private function rules(): array
    {
        return [
            'group_business' => 'nullable|string|max:255',
            'group_function' => 'nullable|string|max:255',
            'subGroup_function' => 'nullable|string|max:255',
            'subSubGroup_function' => 'nullable|string|max:255',
        ];
    }

    public function index(): Response
    {
        $capabilities = MstBusinessCapability::query()
            ->orderBy('id')
            ->get();

        return Inertia::render('MasterData/BusinessCapability/Index', [
            'businessCapabilities' => $capabilities,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        MstBusinessCapability::create($validated);

        return redirect()
            ->route('master-data.business-capabilities.index')
            ->with('success', 'Business Capability berhasil ditambahkan.');
    }

    public function update(Request $request, MstBusinessCapability $businessCapability): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $businessCapability->update($validated);

        return redirect()
            ->route('master-data.business-capabilities.index')
            ->with('success', 'Business Capability berhasil diperbarui.');
    }

    public function destroy(MstBusinessCapability $businessCapability): RedirectResponse
    {
        $businessCapability->delete();

        return redirect()
            ->route('master-data.business-capabilities.index')
            ->with('success', 'Business Capability berhasil dihapus.');
    }
}
