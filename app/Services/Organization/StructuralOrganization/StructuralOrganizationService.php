<?php

namespace App\Services\Organization\StrukturalOrganization;

use App\Models\TrsOrganization;

class StrukturalOrganizationService
{
    /**
     * Store a newly created structural organization.
     *
     * @param array $data
     * @return TrsOrganization
     */
    public function create(array $data): TrsOrganization
    {
        $normalized = $this->normalizedPayload($data);
        return TrsOrganization::create($normalized);
    }

    /**
     * Update the specified structural organization.
     *
     * @param int $id
     * @param array $data
     * @return TrsOrganization
     */
    public function update(int $id, array $data): TrsOrganization
    {
        $organization = TrsOrganization::findOrFail($id);
        $normalized = $this->normalizedPayload($data);
        $organization->update($normalized);
        return $organization;
    }

    /**
     * Remove the specified structural organization.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $organization = TrsOrganization::findOrFail($id);
        $organization->delete();
    }

    /**
     * Normalize the input payload.
     *
     * @param array $payload
     * @return array
     */
    private function normalizedPayload(array $payload): array
    {
        $keys = [
            'groub_id',
            'parent_id',
            'code',
            'name',
            'alias',
            'jabatan',
            'pejabat',
            'sk',
        ];

        $normalized = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $payload)) {
                $val = $payload[$key];
                $val = is_string($val) ? trim($val) : $val;
                $normalized[$key] = $val === '' ? null : $val;
            }
        }

        return $normalized;
    }
}
