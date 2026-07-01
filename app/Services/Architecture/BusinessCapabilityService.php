<?php

namespace App\Services\Architecture;

use Modules\ITOM\Models\MstBusinessCapability;

class BusinessCapabilityService
{
    public function getBusinessCapabilities(): array
    {
        return MstBusinessCapability::query()
            ->orderBy('id')
            ->get()
            ->map(fn (MstBusinessCapability $businessCapability): array => $this->businessCapabilityRow($businessCapability))
            ->all();
    }

    public function createBusinessCapability(array $payload): MstBusinessCapability
    {
        return MstBusinessCapability::query()->create($this->normalizedPayload($payload));
    }

    public function updateBusinessCapability(MstBusinessCapability $businessCapability, array $payload): MstBusinessCapability
    {
        $businessCapability->update($this->normalizedPayload($payload));

        return $businessCapability->refresh();
    }

    public function deleteBusinessCapability(MstBusinessCapability $businessCapability): void
    {
        $businessCapability->delete();
    }

    private function businessCapabilityRow(MstBusinessCapability $businessCapability): array
    {
        return [
            'id' => (int) $businessCapability->id,
            'group_business' => $businessCapability->group_business,
            'group_function' => $businessCapability->group_function,
            'subGroup_function' => $businessCapability->subGroup_function,
            'subSubGroup_function' => $businessCapability->subSubGroup_function,
        ];
    }

    private function normalizedPayload(array $payload): array
    {
        return collect($payload)
            ->only([
                'group_business',
                'group_function',
                'subGroup_function',
                'subSubGroup_function',
            ])
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->map(fn ($value) => $value === '' ? null : $value)
            ->all();
    }
}
