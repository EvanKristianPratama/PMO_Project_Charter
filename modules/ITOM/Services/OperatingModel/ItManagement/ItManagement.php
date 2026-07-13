<?php

namespace Modules\ITOM\Services\OperatingModel\ItManagement;

use Modules\ITOM\Models\MstBod;

class ItManagement
{
    /**
     * Get the formatted and ordered IT Management organization structure rows.
     *
     * @return array
     */
    public function getOrganizationStructureRows(): array
    {
        // Target IDs to always include:
        // 1: Direktur Utama
        // 5: Direktur Penunjang Bisnis
        // 67: SVP Enterprise IT
        // 100: SVP Shared Services
        // 103: Manager Shared Service Information and Communication Technology
        $ids = [1, 5, 67, 100, 103];

        $getDescendants = function ($parentIds) use (&$getDescendants) {
            if (empty($parentIds)) {
                return [];
            }

            $children = MstBod::whereIn("parent_id", $parentIds)
                ->pluck("id")
                ->toArray();
            if (empty($children)) {
                return [];
            }

            return array_merge($children, $getDescendants($children));
        };

        $descendantIds = $getDescendants([67]);
        $allIds = array_merge($ids, $descendantIds);

        $findWakilFungsiDescendants = function ($parentIds) use (&$findWakilFungsiDescendants, $getDescendants) {
            if (empty($parentIds)) {
                return [];
            }
            $specialChildren = MstBod::whereIn("parent_id", $parentIds)
                ->whereIn("role_function", ["wakil", "fungsi"])
                ->pluck("id")
                ->toArray();
            
            if (empty($specialChildren)) {
                return [];
            }
            
            $theirDescendants = $getDescendants($specialChildren);
            $allNewIds = array_merge($specialChildren, $theirDescendants);
            
            return array_merge($allNewIds, $findWakilFungsiDescendants($allNewIds));
        };

        $specialIds = $findWakilFungsiDescendants($allIds);
        $allIds = array_unique(array_merge($allIds, $specialIds));

        $preferredOrders = [
            5 => [
                67 => 10,
                100 => 20,
            ],
            67 => [
                68 => 10,
                71 => 20,
                72 => 30,
                73 => 40,
            ],
            100 => [
                103 => 10,
            ],
        ];

        $resolveOrder = function (MstBod $bod) use ($preferredOrders) {
            $parentId = $bod->parent_id ? (int) $bod->parent_id : null;
            $bodId = (int) $bod->id;

            if (
                $parentId !== null &&
                isset($preferredOrders[$parentId][$bodId])
            ) {
                return $preferredOrders[$parentId][$bodId];
            }

            return $bod->order;
        };

        $bods = MstBod::with("company:id,name")->whereIn("id", $allIds)->get();

        return $bods
            ->map(function ($bod) use ($resolveOrder) {
                return [
                    "organization_id" => (int) $bod->id,
                    "parent_id" => $bod->parent_id
                        ? (int) $bod->parent_id
                        : null,
                    "organization_name" => $bod->name,
                    "alias" => $bod->alias,
                    "pejabat" => $bod->pejabat,
                    "groub_id" => 0,
                    "groub_name" => "Holding",
                    "company_id" => $bod->company_id
                        ? (int) $bod->company_id
                        : null,
                    "company_name" => $bod->company?->name ?? "Tanpa Holding",
                    "order" => $resolveOrder($bod),
                    "role_function" => $bod->role_function,
                ];
            })
            ->sortBy([
                ["company_name", "asc"],
                ["groub_name", "asc"],
                ["order", "asc"],
                ["organization_name", "asc"],
            ])
            ->values()
            ->all();
    }
}
