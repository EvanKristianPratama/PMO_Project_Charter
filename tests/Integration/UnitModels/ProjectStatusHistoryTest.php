<?php

namespace Tests\Integration\UnitModels;

use App\Models\ProjectStatusHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectStatusHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new ProjectStatusHistory)->getFillable();

        $expected = ['project_charter_id', 'status', 'version', 'tanggal', 'notes'];
        foreach ($expected as $field) {
            $this->assertContains($field, $fillable, "Missing fillable field: {$field}");
        }
    }

    public function test_table_name(): void
    {
        $this->assertSame('trs_project_status_history', (new ProjectStatusHistory)->getTable());
    }

    public function test_casts(): void
    {
        $casts = (new ProjectStatusHistory)->getCasts();

        $this->assertSame('integer', $casts['project_charter_id']);
        $this->assertSame('integer', $casts['status']);
        $this->assertSame('integer', $casts['version']);
        $this->assertSame('date', $casts['tanggal']);
    }

    public function test_project_charter_relationship(): void
    {
        $relation = (new ProjectStatusHistory)->projectCharter();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('project_charter_id', $relation->getForeignKeyName());
    }

    public function test_status_ref_relationship(): void
    {
        $relation = (new ProjectStatusHistory)->statusRef();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('status', $relation->getForeignKeyName());
    }
}
