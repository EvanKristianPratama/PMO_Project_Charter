<?php

namespace Tests\Unit\Models;

use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\MstInitiativeRelation;
use App\Models\PcInitiative;
use App\Models\StatusMstInitiative;
use App\Models\TrsOrganization;
use App\Models\TrsProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MstInitiativeTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new MstInitiative())->getFillable();

        $expected = ['coe_id', 'tipe_initiative', 'business_unit', 'project_id', 'code', 'name', 'description', 'status', 'source'];
        foreach ($expected as $field) {
            $this->assertContains($field, $fillable, "Missing fillable field: {$field}");
        }
    }

    public function test_table_name_is_mst_initiative(): void
    {
        $this->assertSame('mst_initiative', (new MstInitiative())->getTable());
    }

    public function test_coe_relationship(): void
    {
        $relation = (new MstInitiative())->coe();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('coe_id', $relation->getForeignKeyName());
    }

    public function test_organization_relationship(): void
    {
        $relation = (new MstInitiative())->organization();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('business_unit', $relation->getForeignKeyName());
    }

    public function test_initiative_relations_row(): void
    {
        $relation = (new MstInitiative())->initiativeRelationsRow();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_initiative_relations_column(): void
    {
        $relation = (new MstInitiative())->initiativeRelationsColumn();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_status_history_relationship(): void
    {
        $relation = (new MstInitiative())->statusHistory();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_latest_status_relationship(): void
    {
        $relation = (new MstInitiative())->latestStatus();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $relation);
    }

    public function test_mapped_projects_relationship(): void
    {
        $relation = (new MstInitiative())->mappedProjects();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
    }

    public function test_latest_planning_status_value_returns_latest_status(): void
    {
        $initiative = new MstInitiative();
        $initiative->status = 'drafting';

        // Without latestStatus relation loaded, falls back to $this->status
        $this->assertSame('drafting', $initiative->latestPlanningStatusValue());
    }

    public function test_latest_planning_status_value_returns_null_for_empty(): void
    {
        $initiative = new MstInitiative();
        $initiative->status = '';

        $this->assertNull($initiative->latestPlanningStatusValue());
    }

    public function test_is_approved_for_implementation_recognizes_approved_aliases(): void
    {
        $aliases = ['approved', 'approve', 'aproved'];
        foreach ($aliases as $alias) {
            $initiative = new MstInitiative();
            $initiative->status = $alias;
            $this->assertTrue(
                $initiative->isApprovedForImplementation(),
                "Failed to recognize '{$alias}' as approved"
            );
        }
    }

    public function test_is_approved_for_implementation_rejects_non_approved(): void
    {
        $statuses = ['drafting', 'propose', 'review', '', 'random'];
        foreach ($statuses as $status) {
            $initiative = new MstInitiative();
            $initiative->status = $status;
            $this->assertFalse(
                $initiative->isApprovedForImplementation(),
                "Incorrectly recognized '{$status}' as approved"
            );
        }
    }

    public function test_is_approved_case_insensitive(): void
    {
        $initiative = new MstInitiative();
        $initiative->status = 'APPROVED';
        $this->assertTrue($initiative->isApprovedForImplementation());

        $initiative->status = 'Approve';
        $this->assertTrue($initiative->isApprovedForImplementation());
    }
}
