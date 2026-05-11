<?php

namespace App\Services\Shared;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheManager
{
    /**
     * Clears cache keys related to Initiatives, Relations, and Building Blocks.
     */
    public static function clearInitiativeCaches(): void
    {
        $keys = [
            'initiative_relation_index_props',
            'initiative_relations',
            'sh_initiative_support_props_v1',
            'sh_support_initiative_opts_1_v1',
            'sh_support_initiative_opts_2_v1',
            'it_building_block_coe_options',
            'it_building_block_it_initiative_options',
            'it_building_block_status_periods',
            'it_building_block_digital_initiative_options',
            'it_building_block_grouped_mappings',
            'sh_relation_initiatives_v1',
            'sh_relation_initiatives_v3',
            'sh_initiative_relations_v1',
            'sh_model_relation_options_v1',
            'sh_map_technology_props_v1',
            'sh_it_building_block_matrix_v1',
            'strategic_pillar_all_initiatives',
            'business_strategy_initiatives_v2', // Included from Business Strategy
        ];

        self::forgetKeys($keys);
    }

    /**
     * Clears cache keys related to Roadmaps, Project Charters, and Dashboard Stats.
     */
    public static function clearRoadmapCaches(): void
    {
        $keys = [
            'pp_it_roadmap_v1',
            'sh_it_roadmap_v1',
            'sh_roadmap_summary_v1',
            'pi_dashboard_props_v1',
            'pi_overview_props_v1',
            'pi_roadmap_sources_v1',
        ];

        self::forgetKeys($keys);
    }

    /**
     * Clears cache keys related to Strategic Pillars, Goals, Themes, and CoEs.
     */
    public static function clearPillarCaches(): void
    {
        $keys = [
            'strategic_pillar_all_organizations',
            'business_strategy_metadata_v2',
            'sh_coes_fixed_v2',
            'sh_focus_band_goals_v1',
            'sh_roof_fixed',
            'sh_dual_goals_fixed_v3',
            'sh_periods',
        ];

        self::forgetKeys($keys);
    }

    /**
     * Utility to clear an array of keys.
     */
    private static function forgetKeys(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        // Log the cache invalidation event for debugging purposes
        Log::info('CacheManager invalidated keys: ' . implode(', ', $keys));
    }
}
