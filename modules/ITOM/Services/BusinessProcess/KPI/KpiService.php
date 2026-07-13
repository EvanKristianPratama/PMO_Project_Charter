<?php

namespace Modules\ITOM\Services\BusinessProcess\KPI;

use Modules\ITOM\Models\MstKpi;

class KpiService
{
    /**
     * Retrieve all KPI items.
     */
    public function getKpis()
    {
        return MstKpi::with('company:id,name')->orderBy('id', 'asc')->get();
    }

    /**
     * Store a newly created KPI item.
     */
    public function createKpi(array $payload): MstKpi
    {
        return MstKpi::create($this->normalizePayload($payload));
    }

    /**
     * Update the specified KPI item.
     */
    public function updateKpi(MstKpi $kpi, array $payload): MstKpi
    {
        $kpi->update($this->normalizePayload($payload));
        return $kpi->refresh();
    }

    /**
     * Remove the specified KPI item.
     */
    public function deleteKpi(MstKpi $kpi): void
    {
        $kpi->delete();
    }

    /**
     * Normalize the payload fields.
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'deskripsi' => isset($payload['deskripsi']) ? trim($payload['deskripsi']) : null,
            'company_id' => !empty($payload['company_id']) ? (int) $payload['company_id'] : null,
        ];
    }
}