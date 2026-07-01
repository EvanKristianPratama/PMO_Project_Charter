<?php

namespace Modules\ITSP\Controllers\StrategicHouse\BusinessStrategy;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPlanning\StrategicHouse\BusinessStrategy\BulkUpdateBusinessStrategyRequest;
use App\Http\Requests\ProgramPlanning\StrategicHouse\BusinessStrategy\StoreBusinessStrategyRequest;
use App\Http\Requests\ProgramPlanning\StrategicHouse\BusinessStrategy\UpdateBusinessStrategyRequest;
use App\Models\TrsBusinessStrategy;
use App\Services\StrategicHouse\BusinessStrategy\BusinessStrategyService;
use Illuminate\Http\RedirectResponse;

class BusinessStrategyController extends Controller
{
    public function __construct(
        private readonly BusinessStrategyService $businessStrategyService
    ) {}

    public function store(StoreBusinessStrategyRequest $request): RedirectResponse
    {
        $this->businessStrategyService->createStrategy($request->validated());

        return back()->with('success', 'Business strategy berhasil ditambahkan.');
    }

    public function update(UpdateBusinessStrategyRequest $request, TrsBusinessStrategy $businessStrategy): RedirectResponse
    {
        $this->businessStrategyService->updateStrategy($businessStrategy, $request->validated());

        return back()->with('success', 'Business strategy berhasil diperbarui.');
    }

    public function bulkUpdate(BulkUpdateBusinessStrategyRequest $request): RedirectResponse
    {
        $this->businessStrategyService->bulkUpdateStrategies($request->validated('rows'));

        return back()->with('success', 'Perubahan business strategy berhasil disimpan.');
    }

    public function destroy(TrsBusinessStrategy $businessStrategy): RedirectResponse
    {
        $this->businessStrategyService->deleteStrategy($businessStrategy);

        return back()->with('success', 'Business strategy berhasil dihapus.');
    }
}
