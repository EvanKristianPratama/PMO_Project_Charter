<?php

namespace App\Services\OperatingModel\ItGovernance;

use App\Models\MstItSteeringComittee;
use App\Models\TrsOrganization;

class ItGovernance
{
    /**
     * Get the formatted IT Steering Committee rows.
     *
     * @return array
     */
    public function getSteeringRows(): array
    {
        return MstItSteeringComittee::with("organization")
            ->orderBy("code")
            ->get()
            ->map(function ($item) {
                return [
                    "id" => $item->id,
                    "code" => trim((string) ($item->code ?? "")),
                    "organization_id" => $item->organization_id,
                    "organization_name" => $item->organization?->jabatan ?? "-",
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Get organizational options for selection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrganizationOptions()
    {
        return TrsOrganization::orderBy("name")->get([
            "id",
            "name",
            "jabatan",
        ]);
    }

    /**
     * Store a new IT Steering Committee record.
     *
     * @param array $data
     * @return \App\Models\MstItSteeringComittee
     */
    public function storeSteering(array $data)
    {
        return MstItSteeringComittee::create($data);
    }

    /**
     * Update an existing IT Steering Committee record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSteering(int $id, array $data): bool
    {
        $item = MstItSteeringComittee::findOrFail($id);
        return $item->update($data);
    }

    /**
     * Delete an existing IT Steering Committee record.
     *
     * @param int $id
     * @return bool|null
     */
    public function destroySteering(int $id): ?bool
    {
        $item = MstItSteeringComittee::findOrFail($id);
        return $item->delete();
    }
}
