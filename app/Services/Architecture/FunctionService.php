<?php

namespace App\Services\Architecture;

use App\Models\MstFunction;

class FunctionService
{
    /**
     * Retrieve all function items with relations.
     */
    public function getFunctions()
    {
        return MstFunction::with(['company', 'regulations'])->orderBy('code')->get();
    }

    /**
     * Store a newly created function item.
     */
    public function createFunction(array $payload): MstFunction
    {
        $function = MstFunction::create([
            'company_id' => $payload['company_id'],
            'parent_id'  => !empty($payload['parent_id']) ? $payload['parent_id'] : null,
            'code'       => $payload['code'],
            'name'       => $payload['name'],
            'alias'      => $payload['alias'] ?? null,
        ]);

        $function->regulations()->sync($payload['regulation_ids'] ?? []);

        return $function;
    }

    /**
     * Update the specified function item.
     */
    public function updateFunction(MstFunction $function, array $payload): MstFunction
    {
        $function->update([
            'company_id' => $payload['company_id'],
            'parent_id'  => !empty($payload['parent_id']) ? $payload['parent_id'] : null,
            'code'       => $payload['code'],
            'name'       => $payload['name'],
            'alias'      => $payload['alias'] ?? null,
        ]);

        $function->regulations()->sync($payload['regulation_ids'] ?? []);

        return $function->refresh();
    }

    /**
     * Remove the specified function item.
     */
    public function deleteFunction(MstFunction $function): void
    {
        // Unsync regulations first to avoid orphaned mapping rows
        $function->regulations()->detach();
        $function->delete();
    }
}
