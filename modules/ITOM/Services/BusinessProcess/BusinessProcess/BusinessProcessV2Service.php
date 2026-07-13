<?php

namespace Modules\ITOM\Services\BusinessProcess\BusinessProcess;

use Modules\ITOM\Models\MstProsesBisnis;

class BusinessProcessV2Service
{
    /**
     * Retrieve all business process v2 items with their parent relations.
     */
    public function getProsesBisnisV2List()
    {
        $items = MstProsesBisnis::with(['company', 'kpis', 'regulations.sopCategories'])->orderBy('id')->get();
        
        // Build map for efficient parent lookup in memory
        $itemsMap = $items->keyBy('id');
        
        $levels = [];
        foreach ($items as $item) {
            $id = $item->id;
            if (isset($levels[$id])) {
                continue;
            }
            
            $path = [];
            $visitedInPath = [];
            $current = $item;
            while ($current && !isset($levels[$current->id])) {
                if (isset($visitedInPath[$current->id])) {
                    break;
                }
                $visitedInPath[$current->id] = true;
                $path[] = $current;
                $current = $current->parent_id ? ($itemsMap[$current->parent_id] ?? null) : null;
            }
            
            $baseLevel = $current ? $levels[$current->id] : 0;
            
            for ($i = count($path) - 1; $i >= 0; $i--) {
                $baseLevel++;
                $levels[$path[$i]->id] = $baseLevel;
            }
        }
        
        foreach ($items as $item) {
            $level = $levels[$item->id] ?? 1;
            $item->setAttribute('level', $level);
            $item->setAttribute('depth', $level - 1);
        }
        
        return $items;
    }

    /**
     * Store a newly created item.
     */
    public function create(array $payload): MstProsesBisnis
    {
        $item = MstProsesBisnis::create($this->normalizePayload($payload));
        if (isset($payload['kpi_ids'])) {
            $item->kpis()->sync($payload['kpi_ids']);
        }
        if (isset($payload['regulation_ids'])) {
            $item->regulations()->sync($payload['regulation_ids']);
        }
        return $item;
    }

    /**
     * Update the specified item.
     */
    public function update(MstProsesBisnis $item, array $payload): MstProsesBisnis
    {
        $item->update($this->normalizePayload($payload));
        if (isset($payload['kpi_ids'])) {
            $item->kpis()->sync($payload['kpi_ids']);
        }
        if (isset($payload['regulation_ids'])) {
            $item->regulations()->sync($payload['regulation_ids']);
        }
        return $item->refresh();
    }

    /**
     * Remove the specified item.
     */
    public function delete(MstProsesBisnis $item): bool
    {
        if ($item->children()->exists()) {
            return false;
        }

        $item->delete();
        return true;
    }

    /**
     * Normalize the payload fields.
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'company_id' => !empty($payload['company_id']) ? (int) $payload['company_id'] : null,
            'name' => isset($payload['name']) ? trim($payload['name']) : null,
            'deskripsi' => isset($payload['deskripsi']) ? trim($payload['deskripsi']) : null,
            'parent_id' => !empty($payload['parent_id']) ? (int) $payload['parent_id'] : null,
            'order' => isset($payload['order']) && $payload['order'] !== '' && $payload['order'] !== null ? (int) $payload['order'] : null,
        ];
    }
}
