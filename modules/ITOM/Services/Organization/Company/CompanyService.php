<?php

namespace Modules\ITOM\Services\Organization\Company;

use Modules\ITOM\Models\MstCompany;
use App\Models\Groub;
use App\Models\TrsOrganization;

class CompanyService
{
    /**
     * Store a newly created company.
     *
     * @param array $data
     * @return MstCompany
     */
    public function create(array $data): MstCompany
    {
        $normalized = $this->normalizedPayload($data);
        return MstCompany::create($normalized);
    }

    /**
     * Update the specified company.
     *
     * @param int $id
     * @param array $data
     * @return MstCompany
     */
    public function update(int $id, array $data): MstCompany
    {
        $company = MstCompany::findOrFail($id);
        $normalized = $this->normalizedPayload($data);
        $company->update($normalized);
        return $company;
    }

    /**
     * Remove the specified company and its associated data.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $company = MstCompany::findOrFail($id);

        // Delete all groups and organizations associated with this company
        $groups = Groub::where('company_id', $id)->get();
        foreach ($groups as $group) {
            TrsOrganization::where('groub_id', $group->id)->delete();
            $group->delete();
        }

        $company->delete();
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
            'parent_id',
            'name',
            'organization',
            'singkatan',
            'grup',
            'level',
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
