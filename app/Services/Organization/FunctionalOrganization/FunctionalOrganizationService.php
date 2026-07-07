<?php

namespace App\Services\Organization\FunctionalOrganization;

use App\Models\MstFunctionalOrganization;
use App\Models\TrsFunctionalStructure;
use App\Models\TrsFunctionalOrganization;
use App\Models\TrsFunctionalFunction;

class FunctionalOrganizationService
{
    /**
     * Store a newly created functional organization.
     *
     * @param array $data
     * @return MstFunctionalOrganization
     */
    public function create(array $data): MstFunctionalOrganization
    {
        $normalized = $this->normalizedPayload($data, ['company_id', 'name', 'regulation_id']);
        return MstFunctionalOrganization::create($normalized);
    }

    /**
     * Update the specified functional organization.
     *
     * @param int $id
     * @param array $data
     * @return MstFunctionalOrganization
     */
    public function update(int $id, array $data): MstFunctionalOrganization
    {
        $functional = MstFunctionalOrganization::findOrFail($id);
        $normalized = $this->normalizedPayload($data, ['company_id', 'name', 'regulation_id']);
        $functional->update($normalized);
        return $functional;
    }

    /**
     * Remove the specified functional organization.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $functional = MstFunctionalOrganization::findOrFail($id);

        $structures = TrsFunctionalStructure::where('functional_id', $functional->id)->get();
        foreach ($structures as $structure) {
            TrsFunctionalOrganization::where('structure_id', $structure->id)->delete();
            $structure->delete();
        }

        $functional->delete();
    }

    /**
     * Store functional member.
     *
     * @param array $data
     * @return void
     */
    public function addMember(array $data): void
    {
        $memberType = $data['member_type'] ?? 'bod';

        if ($memberType === 'function') {
            $normalized = $this->normalizedPayload($data, ['functional_id', 'organization_id']);
            TrsFunctionalFunction::firstOrCreate([
                'functional_id' => $normalized['functional_id'],
                'function_id' => $normalized['organization_id'],
            ]);
        } else {
            $normalized = $this->normalizedPayload($data, ['structure_id', 'organization_id']);
            TrsFunctionalOrganization::firstOrCreate([
                'structure_id' => $normalized['structure_id'],
                'organization_id' => $normalized['organization_id'],
            ]);
        }
    }

    /**
     * Delete functional member.
     *
     * @param array $data
     * @return void
     */
    public function removeMember(array $data): void
    {
        $memberType = $data['member_type'] ?? 'bod';

        if ($memberType === 'function') {
            $normalized = $this->normalizedPayload($data, ['functional_id', 'organization_id']);
            TrsFunctionalFunction::where('functional_id', $normalized['functional_id'])
                ->where('function_id', $normalized['organization_id'])
                ->delete();
        } else {
            $normalized = $this->normalizedPayload($data, ['structure_id', 'organization_id']);
            TrsFunctionalOrganization::where('structure_id', $normalized['structure_id'])
                ->where('organization_id', $normalized['organization_id'])
                ->delete();
        }
    }

    /**
     * Store functional structure.
     *
     * @param array $data
     * @return TrsFunctionalStructure
     */
    public function addStructure(array $data): TrsFunctionalStructure
    {
        $normalized = $this->normalizedPayload($data, ['functional_org_id', 'name', 'parent_id']);
        return TrsFunctionalStructure::firstOrCreate([
            'functional_id' => $normalized['functional_org_id'],
            'name' => $normalized['name'],
            'parent_id' => $normalized['parent_id'] ?? null,
        ]);
    }

    /**
     * Destroy functional structure.
     *
     * @param int $structureId
     * @return void
     */
    public function deleteStructure(int $structureId): void
    {
        $structure = TrsFunctionalStructure::findOrFail($structureId);
        TrsFunctionalOrganization::where('structure_id', $structure->id)->delete();
        $structure->delete();
    }

    /**
     * Normalize the input payload based on specific keys.
     *
     * @param array $payload
     * @param array $keys
     * @return array
     */
    private function normalizedPayload(array $payload, array $keys): array
    {
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
