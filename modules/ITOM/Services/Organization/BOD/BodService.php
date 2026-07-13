<?php

namespace Modules\ITOM\Services\Organization\BOD;

use Modules\ITOM\Models\MstBod;

class BodService
{
    /**
     * Store a newly created BOD member.
     *
     * @param array $data
     * @return MstBod
     */
    public function create(array $data): MstBod
    {
        $normalized = $this->normalizedPayload($data);
        return MstBod::create($normalized);
    }

    /**
     * Update the specified BOD member.
     *
     * @param int $id
     * @param array $data
     * @return MstBod
     */
    public function update(int $id, array $data): MstBod
    {
        $bod = MstBod::findOrFail($id);
        $normalized = $this->normalizedPayload($data);
        $bod->update($normalized);
        return $bod;
    }

    /**
     * Remove the specified BOD member.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $bod = MstBod::findOrFail($id);
        $bod->delete();
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
            'company_id',
            'parent_id',
            'name',
            'nama_jabatan',
            'alias',
            'sumber',
            'pejabat',
            'grup_function',
            'role_function',
            'tipe',
            'regulation_id',
            'order',
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
