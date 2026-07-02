<?php

namespace App\Services\BusinessProcess;

use App\Models\MstBusinessCapability;

class BusinessCapabilityService
{
    public function getBusinessCapabilities(): array
    {
        return MstBusinessCapability::query()
            ->select([
                'id',
                'group_business',
                'group_function',
                'subGroup_function',
                'subSubGroup_function',
            ])
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
        $keys = [
            'group_business',
            'group_function',
            'subGroup_function',
            'subSubGroup_function',
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
