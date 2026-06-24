<?php

namespace App\Services\Architecture;

use App\Models\MstKpi;

class KpiService
{
    /**
     * Retrieve all KPI items.
     */
    public function getKpis()
    {
        return MstKpi::with('company')->orderBy('id', 'asc')->get();
    }

    /**
     * Store a newly created KPI item.
     */
    public function createKpi(array $payload): MstKpi
    {
        return MstKpi::create([
            'deskripsi' => $payload['deskripsi'] ?? null,
            'company_id' => $payload['company_id'] ?? null,
        ]);
    }

    /**
     * Update the specified KPI item.
     */
    public function updateKpi(MstKpi $kpi, array $payload): MstKpi
    {
        $kpi->update([
            'deskripsi' => $payload['deskripsi'] ?? null,
            'company_id' => $payload['company_id'] ?? null,
        ]);
        return $kpi->refresh();
    }

    /**
     * Remove the specified KPI item.
     */
    public function deleteKpi(MstKpi $kpi): void
    {
        $kpi->delete();
    }
}
