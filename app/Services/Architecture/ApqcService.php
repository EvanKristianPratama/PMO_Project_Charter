<?php

namespace App\Services\Architecture;

use App\Models\MstApqc;

class ApqcService
{
    /**
     * Retrieve all APQC items with their parent relations.
     */
    public function getApqcList()
    {
        return MstApqc::with('parent')->orderBy('id')->get();
    }

    /**
     * Store a newly created APQC item.
     */
    public function createApqc(array $payload): MstApqc
    {
        return MstApqc::create($this->normalizePayload($payload));
    }

    /**
     * Update the specified APQC item.
     */
    public function updateApqc(MstApqc $apqc, array $payload): MstApqc
    {
        $apqc->update($this->normalizePayload($payload));
        return $apqc->refresh();
    }

    /**
     * Remove the specified APQC item.
     * Returns true on success, false if the item has children and cannot be deleted.
     */
    public function deleteApqc(MstApqc $apqc): bool
    {
        if ($apqc->children()->exists()) {
            return false;
        }

        $apqc->delete();
        return true;
    }

    /**
     * Normalize the payload fields.
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'name' => isset($payload['name']) ? trim($payload['name']) : null,
            'parent_id' => !empty($payload['parent_id']) ? (int) $payload['parent_id'] : null,
        ];
    }
}
