<?php

namespace Tests\Integration\UnitModels;

use App\Models\InitiativeStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitiativeStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_constants_are_defined(): void
    {
        $this->assertSame(1, InitiativeStatus::DRAFTING);
        $this->assertSame(2, InitiativeStatus::PROPOSE);
        $this->assertSame(3, InitiativeStatus::REVIEW);
        $this->assertSame(4, InitiativeStatus::APPROVE);
        $this->assertSame(5, InitiativeStatus::BASELINE);
    }

    public function test_table_name(): void
    {
        $this->assertSame('trs_status_initiative', (new InitiativeStatus)->getTable());
    }

    public function test_id_is_not_auto_incrementing(): void
    {
        $model = new InitiativeStatus;
        $this->assertFalse($model->getIncrementing());
        $this->assertSame('int', $model->getKeyType());
    }

    public function test_fillable(): void
    {
        $fillable = (new InitiativeStatus)->getFillable();
        $this->assertContains('id', $fillable);
        $this->assertContains('name', $fillable);
    }

    public function test_id_is_cast_to_integer(): void
    {
        $casts = (new InitiativeStatus)->getCasts();
        $this->assertSame('integer', $casts['id']);
    }

    public function test_ordered_returns_collection(): void
    {
        InitiativeStatus::query()->updateOrInsert(['id' => 1], ['name' => 'drafting']);
        InitiativeStatus::query()->updateOrInsert(['id' => 2], ['name' => 'propose']);
        InitiativeStatus::query()->updateOrInsert(['id' => 3], ['name' => 'review']);

        $result = InitiativeStatus::ordered();
        $this->assertGreaterThanOrEqual(3, $result->count());
        $this->assertSame(1, $result->first()->id);
    }

    public function test_baseline_id_returns_baseline_status_id(): void
    {
        InitiativeStatus::query()->updateOrInsert(['id' => 1], ['name' => 'drafting']);
        InitiativeStatus::query()->updateOrInsert(['id' => 5], ['name' => 'baseline']);

        $this->assertSame(5, InitiativeStatus::baselineId());
    }

    public function test_baseline_id_returns_constant_when_not_found(): void
    {
        // No baseline record inserted
        $this->assertSame(InitiativeStatus::BASELINE, InitiativeStatus::baselineId());
    }
}
