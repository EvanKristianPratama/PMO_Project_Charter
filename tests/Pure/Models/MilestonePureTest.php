<?php

namespace Tests\Pure\Models;

use App\Models\Milestone;
use PHPUnit\Framework\TestCase;

class MilestonePureTest extends TestCase
{
    public function test_roadmap_type_codes_match_definition_keys(): void
    {
        $this->assertSame(
            array_keys(Milestone::ROADMAP_TYPE_DEFINITIONS),
            Milestone::roadmapTypeCodes()
        );
    }

    public function test_roadmap_type_options_have_expected_shape(): void
    {
        $options = Milestone::roadmapTypeOptions();

        $this->assertNotEmpty($options);
        $this->assertArrayHasKey('value', $options[0]);
        $this->assertArrayHasKey('label', $options[0]);
        $this->assertArrayHasKey('timeline_style', $options[0]);
    }

    public function test_normalize_roadmap_type_defaults_for_empty_values(): void
    {
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(null));
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(''));
    }

    public function test_normalize_roadmap_type_defaults_for_invalid_value(): void
    {
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(99));
    }

    public function test_normalize_roadmap_type_accepts_valid_numeric_string(): void
    {
        $this->assertSame(Milestone::ROADMAP_TYPE_DASHED_PRIMARY, Milestone::normalizeRoadmapType('2'));
    }
}
