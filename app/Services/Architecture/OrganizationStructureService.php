<?php

namespace App\Services\Architecture;

use App\Models\Groub;
use App\Models\TrsOrganization;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationStructureService
{
    public function getOrganizationStructureRows(): array
    {
        return Groub::query()
            ->select(['id', 'company_id', 'name'])
            ->with([
                'company:id,name',
                'organizations' => fn (HasMany $query) => $query
                    ->select(['id', 'groub_id', 'name'])
                    ->orderBy('name'),
            ])
            ->orderBy('name')
            ->get()
            ->flatMap(fn (Groub $groub) => $groub->organizations
                ->map(fn (TrsOrganization $organization): array => $this->organizationStructureRow($groub, $organization)))
            ->values()
            ->all();
    }

    private function organizationStructureRow(Groub $groub, TrsOrganization $organization): array
    {
        return [
            'organization_id' => (int) $organization->id,
            'organization_name' => $organization->name,
            'groub_id' => (int) $groub->id,
            'groub_name' => $groub->name,
            'company_id' => $groub->company?->id ? (int) $groub->company->id : null,
            'company_name' => $groub->company?->name,
        ];
    }
}
