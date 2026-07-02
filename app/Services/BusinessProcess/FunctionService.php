<?php

namespace App\Services\BusinessProcess;

use App\Models\MstFunction;

class FunctionService
{
    /**
     * Retrieve all function items with relations.
     */
    public function getFunctions()
    {
        return MstFunction::with([
            'company:id,name,singkatan',
            'regulations:id,judul,nomor,tipe,status',
            'organizations:id,name,alias'
        ])->orderBy('name')->get();
    }

    /**
     * Store a newly created function item.
     */
    public function createFunction(array $payload): MstFunction
    {
        $function = MstFunction::create($this->normalizePayload($payload));

        $function->regulations()->sync($payload['regulation_ids'] ?? []);
        $function->organizations()->sync($payload['organization_ids'] ?? []);

        return $function;
    }

    /**
     * Update the specified function item.
     */
    public function updateFunction(MstFunction $function, array $payload): MstFunction
    {
        $function->update($this->normalizePayload($payload));

        $function->regulations()->sync($payload['regulation_ids'] ?? []);
        $function->organizations()->sync($payload['organization_ids'] ?? []);

        return $function->refresh();
    }

    /**
     * Remove the specified function item.
     */
    public function deleteFunction(MstFunction $function): void
    {
        // Unsync regulations first to avoid orphaned mapping rows
        $function->regulations()->detach();
        $function->organizations()->detach();
        $function->delete();
    }

    /**
     * Normalize the payload fields.
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'company_id' => isset($payload['company_id']) ? (int) $payload['company_id'] : null,
            'parent_id'  => !empty($payload['parent_id']) ? (int) $payload['parent_id'] : null,
            'name'       => isset($payload['name']) ? trim($payload['name']) : null,
            'alias'      => isset($payload['alias']) && $payload['alias'] !== '' ? trim($payload['alias']) : null,
            'deskripsi'  => isset($payload['deskripsi']) && $payload['deskripsi'] !== '' ? trim($payload['deskripsi']) : null,
        ];
    }
}
