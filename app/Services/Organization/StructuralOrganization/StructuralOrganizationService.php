<?php

namespace App\Services\Organization\StructuralOrganization;

use App\Models\Groub;
use App\Models\TrsOrganization;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StructuralOrganizationService
{
    /**
     * Retrieve all structural organization items.
     *
     * @return array
     */
    public function getOrganizationStructureRows(): array
    {
        return Groub::query()
            ->select(['id', 'company_id', 'name'])
            ->with([
                'company:id,name',
                'organizations' => fn (HasMany $query) => $query
                    ->select(['id', 'groub_id', 'parent_id', 'code', 'name', 'alias', 'jabatan', 'pejabat', 'sk'])
                    ->with([
                        'picOrganization:id,organization_id,name',
                        'resources:id,jabatan,name',
                    ]),
            ])
            ->get()
            ->flatMap(fn (Groub $groub) => $groub->organizations
                ->map(fn (TrsOrganization $organization): array => $this->organizationStructureRow($groub, $organization)))
            ->sortBy([
                ['company_name', 'asc'],
                ['groub_name', 'asc'],
                ['code', 'asc'],
            ])
            ->values()
            ->all();
    }

    /**
     * Format a single organization structure row.
     *
     * @param Groub $groub
     * @param TrsOrganization $organization
     * @return array
     */
    private function organizationStructureRow(Groub $groub, TrsOrganization $organization): array
    {
        return [
            'organization_id' => (int) $organization->id,
            'parent_id' => $organization->parent_id ? (int) $organization->parent_id : null,
            'code' => trim((string) ($organization->code ?? '')),
            'organization_code' => trim((string) ($organization->code ?? '')),
            'organization_name' => $organization->name,
            'alias' => $organization->alias,
            'jabatan' => $organization->jabatan,
            'pejabat' => $organization->resources->pluck('name')->implode(', ') ?: null,
            'pejabat_original' => $organization->pejabat,
            'sk' => $organization->sk,
            'groub_id' => (int) $groub->id,
            'groub_name' => $groub->name,
            'company_id' => $groub->company?->id ? (int) $groub->company->id : null,
            'company_name' => $groub->company?->name,
            'pic_projects' => $organization->picOrganization->map(fn ($pic) => [
                'id' => $pic->id,
                'name' => $pic->name,
            ])->values()->all(),
        ];
    }

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
