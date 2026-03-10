<?php

namespace Tests\Unit\Models;

use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use App\Models\PcStatusImplementation;
use App\Models\ProjectCharter;
use App\Models\ProjectStatusHistory;
use App\Models\TrsProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrsProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new TrsProject())->getFillable();

        $this->assertContains('code', $fillable);
        $this->assertContains('name', $fillable);
        $this->assertContains('owner_id', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('metadata', $fillable);
        $this->assertContains('tipe_inisiative', $fillable);
    }

    public function test_metadata_is_cast_to_array(): void
    {
        $project = TrsProject::factory()->create([
            'metadata' => ['mst_initiative_id' => 5, 'auto_synced_from_mst_initiative' => true],
        ]);

        $this->assertIsArray($project->metadata);
        $this->assertSame(5, $project->metadata['mst_initiative_id']);
        $this->assertTrue($project->metadata['auto_synced_from_mst_initiative']);
    }

    public function test_status_is_cast_to_integer(): void
    {
        $project = TrsProject::factory()->create(['status' => '2']);

        $this->assertSame(2, $project->status);
    }

    public function test_uses_soft_deletes(): void
    {
        $project = TrsProject::factory()->create();
        $project->delete();

        $this->assertSoftDeleted($project);
        $this->assertNotNull(TrsProject::withTrashed()->find($project->id));
    }

    public function test_owner_relationship(): void
    {
        $project = new TrsProject();
        $relation = $project->owner();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('owner_id', $relation->getForeignKeyName());
    }

    public function test_charter_relationship_returns_latest(): void
    {
        $project = new TrsProject();
        $relation = $project->charter();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $relation);
    }

    public function test_charters_relationship(): void
    {
        $project = new TrsProject();
        $relation = $project->charters();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_status_ref_relationship(): void
    {
        $project = new TrsProject();
        $relation = $project->statusRef();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame('status', $relation->getForeignKeyName());
    }

    public function test_pc_status_implementations_relationship(): void
    {
        $project = new TrsProject();
        $relation = $project->pcStatusImplementations();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_project_status_histories_relationship(): void
    {
        $project = new TrsProject();
        $relation = $project->projectStatusHistories();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class, $relation);
    }

    public function test_mapped_initiatives_relationship(): void
    {
        $project = new TrsProject();
        $relation = $project->mappedInitiatives();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
    }

    public function test_table_name_is_trs_projects(): void
    {
        $this->assertSame('trs_projects', (new TrsProject())->getTable());
    }
}
