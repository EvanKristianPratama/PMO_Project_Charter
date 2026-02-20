<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use App\Models\InitiativeStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class ProgramImplementationController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions = $this->statusOptions();
        $typeColumn = $this->resolveInitiativeTypeColumn();
        $digitalStatusCountsRaw = collect();
        $itStatusCountsRaw = $this->statusCountsRawByType($typeColumn, '2');

        return Inertia::render('ProgramImplementation/Index', [
            'projectCharterOverview' => [
                'status_options' => $statusOptions,
                'digital_status_counts' => $this->mapCountsByStatus($statusOptions, $digitalStatusCountsRaw),
                'it_status_counts' => $this->mapCountsByStatus($statusOptions, $itStatusCountsRaw),
            ],
        ]);
    }

    private function statusOptions(): array
    {
        return InitiativeStatus::ordered()
            ->map(fn (InitiativeStatus $status) => [
                'id' => (int) $status->id,
                'name' => $status->name,
                'label' => ucfirst($status->name),
            ])
            ->values()
            ->all();
    }

    private function resolveInitiativeTypeColumn(): ?string
    {
        foreach (['tipe_inisiative', 'tipe_initiative', 'tipe_inisiatif'] as $column) {
            if (Schema::hasColumn('mst_digitalInitiative', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function statusCountsRawByType(?string $typeColumn, string $typeValue): Collection
    {
        if ($typeColumn === null) {
            return collect();
        }

        $numericTypeValue = (int) $typeValue;

        return DigitalInitiative::query()
            ->whereNotNull($typeColumn)
            ->whereRaw("NULLIF(TRIM(`{$typeColumn}`), '') IS NOT NULL")
            ->where(function ($query) use ($typeColumn, $typeValue, $numericTypeValue): void {
                $query
                    ->where($typeColumn, (string) $typeValue)
                    ->orWhere($typeColumn, $numericTypeValue)
                    ->orWhereRaw("CAST(TRIM(`{$typeColumn}`) AS UNSIGNED) = ?", [$numericTypeValue]);
            })
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function mapCountsByStatus(array $statusOptions, Collection $rawCounts): array
    {
        return collect($statusOptions)
            ->mapWithKeys(fn (array $status) => [
                (string) $status['id'] => (int) ($rawCounts[$status['id']] ?? $rawCounts[(string) $status['id']] ?? 0),
            ])
            ->all();
    }
}
