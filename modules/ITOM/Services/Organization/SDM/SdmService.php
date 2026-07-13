<?php

namespace Modules\ITOM\Services\Organization\SDM;

use Modules\ITOM\Models\MstResource;

class SdmService
{
    public function create(array $payload): MstResource
    {
        $payload = $this->normalizedPayload($payload);
        return MstResource::create($payload);
    }

    public function update(int $id, array $payload): bool
    {
        $payload = $this->normalizedPayload($payload);
        $resource = MstResource::findOrFail($id);
        return $resource->update($payload);
    }

    public function delete(int $id): bool
    {
        $resource = MstResource::findOrFail($id);
        return $resource->delete();
    }

    private function normalizedPayload(array $payload): array
    {
        $keys = [
            'name',
            'jabatan',
            'internal_id',
            'sk',
            'start',
            'end',
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
