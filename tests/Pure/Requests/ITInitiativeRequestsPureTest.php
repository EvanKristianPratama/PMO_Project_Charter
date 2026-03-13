<?php

namespace Tests\Pure\Requests;

use App\Http\Requests\ITInitiative\CharterStoreRequest;
use App\Http\Requests\ITInitiative\ITInitiativeStoreRequest;
use App\Http\Requests\ITInitiative\ITInitiativeUpdateRequest;
use App\Http\Requests\ITInitiative\MilestoneStoreRequest;
use PHPUnit\Framework\TestCase;

class ITInitiativeRequestsPureTest extends TestCase
{
    public function test_store_request_authorize_false_without_user(): void
    {
        $request = new ITInitiativeStoreRequest;
        $request->setUserResolver(static fn () => null);

        $this->assertFalse($request->authorize());
    }

    public function test_store_request_authorize_true_with_user(): void
    {
        $request = new ITInitiativeStoreRequest;
        $request->setUserResolver(static fn () => new \stdClass);

        $this->assertTrue($request->authorize());
    }

    public function test_store_request_rules_contain_core_fields(): void
    {
        $request = new ITInitiativeStoreRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('code', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('project_status_changed_at', $rules);
    }

    public function test_update_request_rules_contain_code_uniqueness_and_status_fields(): void
    {
        $request = new ITInitiativeUpdateRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('code', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('project_status_changed_at', $rules);
    }

    public function test_milestone_prepare_for_validation_casts_milestone_type_to_integer(): void
    {
        $request = new MilestoneStoreRequest;
        $request->merge(['milestone_type' => '3']);

        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertSame(3, $request->input('milestone_type'));
    }

    public function test_milestone_rules_include_version_regex(): void
    {
        $request = new MilestoneStoreRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('version', $rules);
        $this->assertContains('regex:/^(v\\d+)$/i', $rules['version']);
    }

    public function test_charter_store_rules_include_key_document_fields(): void
    {
        $request = new CharterStoreRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('version_label', $rules);
        $this->assertArrayHasKey('owner_name', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('risk_mitigation', $rules);
    }
}
