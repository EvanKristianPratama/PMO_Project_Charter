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
        $items = MstApqc::with('parent')->orderBy('id')->get();
        
        // Build map for efficient parent lookup in memory
        $itemsMap = $items->keyBy('id');
        
        foreach ($items as $item) {
            $level = 1;
            $current = $item;
            while ($current->parent_id && isset($itemsMap[$current->parent_id])) {
                $level++;
                $current = $itemsMap[$current->parent_id];
            }
            $item->setAttribute('level', $level);
            $item->setAttribute('depth', $level - 1);
        }
        
        return $items;
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
            'deskripsi' => isset($payload['deskripsi']) ? trim($payload['deskripsi']) : null,
            'parent_id' => !empty($payload['parent_id']) ? (int) $payload['parent_id'] : null,
        ];
    }
}
