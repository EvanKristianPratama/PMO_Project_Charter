<?php

namespace App\Services\OperatingModel\ItGovernance;

use App\Models\MstItSteeringComittee;
use App\Models\TrsOrganization;
use Illuminate\Database\Eloquent\Collection;

class ItGovernance
{
    /**
     * Get the formatted IT Steering Committee rows.
     */
    public function getSteeringRows(): array
    {
        return MstItSteeringComittee::with('organization:id,jabatan')
            ->orderBy('code')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => trim((string) ($item->code ?? '')),
                    'organization_id' => $item->organization_id,
                    'organization_name' => $item->organization?->jabatan ?? '-',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Get organizational options for selection.
     *
     * @return Collection
     */
    public function getOrganizationOptions()
    {
        return TrsOrganization::orderBy('name')->get([
            'id',
            'name',
            'jabatan',
        ]);
    }

    /**
     * Store a new IT Steering Committee record.
     *
     * @return MstItSteeringComittee
     */
    public function storeSteering(array $data)
    {
        return MstItSteeringComittee::create($data);
    }

    /**
     * Update an existing IT Steering Committee record.
     */
    public function updateSteering(int $id, array $data): bool
    {
        $item = MstItSteeringComittee::findOrFail($id);

        return $item->update($data);
    }

    /**
     * Delete an existing IT Steering Committee record.
     */
    public function destroySteering(int $id): ?bool
    {
        $item = MstItSteeringComittee::findOrFail($id);

        return $item->delete();
    }
}
