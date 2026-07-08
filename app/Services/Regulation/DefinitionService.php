<?php

namespace App\Services\Regulation;

use App\Models\MstDefinition;
use App\Models\MstRegulation;
use Illuminate\Support\Facades\DB;

class DefinitionService
{
    /**
     * Get all definitions with their related regulations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDefinitions()
    {
        return MstDefinition::with('regulations')->orderBy('name')->get();
    }

    /**
     * Get all regulations for mapping options.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRegulations()
    {
        return MstRegulation::select(['id', 'judul', 'nomor', 'tipe'])->orderBy('judul')->get();
    }

    /**
     * Get definitions mapped to a specific regulation.
     *
     * @param int $regulationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByRegulation(int $regulationId)
    {
        return MstDefinition::with('regulations')
            ->whereHas('regulations', function ($query) use ($regulationId) {
                $query->where('mst_regulation.id', $regulationId);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new definition and sync its regulations.
     *
     * @param array $data
     * @return MstDefinition
     */
    public function createDefinition(array $data)
    {
        return DB::transaction(function () use ($data) {
            $definition = MstDefinition::create([
                'name' => $data['name'],
                'definition' => $data['definition'],
            ]);

            if (!empty($data['regulation_ids'])) {
                $definition->regulations()->sync($data['regulation_ids']);
            }

            return $definition;
        });
    }

    /**
     * Update an existing definition and sync its regulations.
     *
     * @param MstDefinition $definition
     * @param array $data
     * @return MstDefinition
     */
    public function updateDefinition(MstDefinition $definition, array $data)
    {
        return DB::transaction(function () use ($definition, $data) {
            $definition->update([
                'name' => $data['name'],
                'definition' => $data['definition'],
            ]);

            if (isset($data['regulation_ids'])) {
                $definition->regulations()->sync($data['regulation_ids']);
            } else {
                $definition->regulations()->detach();
            }

            return $definition;
        });
    }

    /**
     * Delete a definition.
     *
     * @param MstDefinition $definition
     * @return void
     */
    public function deleteDefinition(MstDefinition $definition)
    {
        DB::transaction(function () use ($definition) {
            $definition->regulations()->detach();
            $definition->delete();
        });
    }

    /**
     * Get all definitions for mapping options.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExistingDefinitionsWithMapping()
    {
        return MstDefinition::with('regulations')->orderBy('name')->get();
    }
}
