<?php

namespace Modules\ITOM\Controllers\BusinessProcess\BusinessCapability;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\BusinessCapability\UpsertBusinessCapabilityRequest;
use App\Models\MstBusinessCapability;
use App\Services\BusinessProcess\BusinessCapabilityService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BusinessCapabilityController extends Controller
{
    public function index(BusinessCapabilityService $businessCapabilityService): Response
    {
        return Inertia::render('modules/ITOM/BusinessProcess/BusinessCapability/Index', [
            'businessCapabilities' => Inertia::defer(fn() => $businessCapabilityService->getBusinessCapabilities()),
        ]);
    }

    public function store(
        UpsertBusinessCapabilityRequest $request,
        BusinessCapabilityService $businessCapabilityService
    ): RedirectResponse
    {
        $businessCapabilityService->createBusinessCapability($request->validated());

        return redirect()
            ->route('itom.business-process.business-capability.index')
            ->with('success', 'Business Capability berhasil ditambahkan.');
    }

    public function update(
        UpsertBusinessCapabilityRequest $request,
        MstBusinessCapability $businessCapability,
        BusinessCapabilityService $businessCapabilityService
    ): RedirectResponse
    {
        $businessCapabilityService->updateBusinessCapability($businessCapability, $request->validated());

        return redirect()
            ->route('itom.business-process.business-capability.index')
            ->with('success', 'Business Capability berhasil diperbarui.');
    }

    public function destroy(
        MstBusinessCapability $businessCapability,
        BusinessCapabilityService $businessCapabilityService
    ): RedirectResponse
    {
        $businessCapabilityService->deleteBusinessCapability($businessCapability);

        return redirect()
            ->route('itom.business-process.business-capability.index')
            ->with('success', 'Business Capability berhasil dihapus.');
    }
}
