<?php

namespace Tests\Unit\Models;

use App\Models\Milestone;
use App\Models\ProjectCharter;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCharterTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new ProjectCharter())->getFillable();

        $expected = [
            'project_id', 'version_label', 'status', 'owner', 'category',
            'duration', 'background', 'objectives', 'scope', 'impact_value',
            'key_personnel', 'key_items', 'budget', 'risks_identified',
            'risk_mitigation', 'tgl_dokumen', 'metadata',
        ];
        foreach ($expected as $field) {
            $this->assertContains($field, $fillable, "Missing fillable field: {$field}");
        }
    }

    public function test_table_name(): void
    {
        $this->assertSame('trs_project_charters', (new ProjectCharter())->getTable());
    }

    public function test_metadata_is_cast_to_array(): void
    {
        $casts = (new ProjectCharter())->getCasts();
        $this->assertSame('array', $casts['metadata']);
    }

    public function test_status_is_cast_to_integer(): void
    {
        $casts = (new ProjectCharter())->getCasts();
        $this->assertSame('integer', $casts['status']);
    }

    public function test_project_relationship(): void
    {
        $relation = (new ProjectCharter())->project();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_milestones_relationship(): void
    {
        $relation = (new ProjectCharter())->milestones();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_project_status_histories_relationship(): void
    {
        $relation = (new ProjectCharter())->projectStatusHistories();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }
}
