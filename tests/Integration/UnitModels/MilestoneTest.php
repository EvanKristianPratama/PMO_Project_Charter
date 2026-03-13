<?php

namespace Tests\Integration\UnitModels;

use App\Models\Milestone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MilestoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new Milestone)->getFillable();

        $expected = ['pc_id', 'version', 'title', 'output', 'start_date', 'end_date', 'type', 'milestone_type', 'order'];
        foreach ($expected as $field) {
            $this->assertContains($field, $fillable, "Missing fillable field: {$field}");
        }
    }

    public function test_table_name(): void
    {
        $this->assertSame('trs_milestones', (new Milestone)->getTable());
    }

    public function test_casts(): void
    {
        $casts = (new Milestone)->getCasts();

        $this->assertSame('date', $casts['start_date']);
        $this->assertSame('date', $casts['end_date']);
        $this->assertSame('integer', $casts['milestone_type']);
    }

    public function test_charter_relationship(): void
    {
        $relation = (new Milestone)->charter();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('pc_id', $relation->getForeignKeyName());
    }

    public function test_roadmap_type_constants(): void
    {
        $this->assertSame(1, Milestone::ROADMAP_TYPE_BLOCK_PRIMARY);
        $this->assertSame(2, Milestone::ROADMAP_TYPE_DASHED_PRIMARY);
        $this->assertSame(3, Milestone::ROADMAP_TYPE_BLOCK_SECONDARY);
        $this->assertSame(4, Milestone::ROADMAP_TYPE_DASHED_SECONDARY);
        $this->assertSame(5, Milestone::ROADMAP_TYPE_BLOCK_TERTIARY);
    }

    public function test_roadmap_type_codes_returns_all_keys(): void
    {
        $codes = Milestone::roadmapTypeCodes();
        $this->assertSame([1, 2, 3, 4, 5], $codes);
    }

    public function test_roadmap_type_options_returns_correct_structure(): void
    {
        $options = Milestone::roadmapTypeOptions();

        $this->assertCount(5, $options);
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('timeline_style', $option);
        }
    }

    public function test_normalize_roadmap_type_valid_values(): void
    {
        $this->assertSame(1, Milestone::normalizeRoadmapType(1));
        $this->assertSame(3, Milestone::normalizeRoadmapType(3));
        $this->assertSame(5, Milestone::normalizeRoadmapType(5));
    }

    public function test_normalize_roadmap_type_invalid_falls_back(): void
    {
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(null));
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(''));
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(99));
        $this->assertSame(Milestone::ROADMAP_TYPE_BLOCK_PRIMARY, Milestone::normalizeRoadmapType(-1));
    }

    public function test_roadmap_type_definitions_structure(): void
    {
        foreach (Milestone::ROADMAP_TYPE_DEFINITIONS as $code => $definition) {
            $this->assertIsInt($code);
            $this->assertArrayHasKey('label', $definition);
            $this->assertArrayHasKey('timeline_style', $definition);
            $this->assertContains($definition['timeline_style'], ['block', 'dashed']);
        }
    }
}
