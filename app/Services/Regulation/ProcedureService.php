<?php

namespace App\Services\Regulation;

use App\Models\MstActor;
use App\Models\MstRegulation;
use App\Models\MstSop;
use App\Models\TrsMapActorSop;
use App\Models\TrsOrganization;
use App\Models\TrsSopCategory;
use App\Models\TrsTkoContent;
use App\Models\TrsTkoSections;
use App\Models\TrsDefinitionRegulation;
use App\Models\TrsRelatedRegulation;
use App\Services\Regulation\DefinitionService;
use Illuminate\Support\Facades\DB;

class ProcedureService
{
    /**
     * Get all procedure data.
     *
     * @param int|null $selectedRegulationId
     * @return array
     */
    public function getProcedureData(?int $selectedRegulationId): array
    {
        $regulations = MstRegulation::orderBy('id', 'desc')->get();
        $organizations = TrsOrganization::orderBy('name')->get();

        $selectedRegulation = null;
        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->firstWhere('tipe', 'Procedure');
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->first();
        }

        $actorsQuery = MstActor::with(['organization', 'functions', 'organizations']);
        if ($selectedRegulation) {
            $actorsQuery->where('regulation_id', $selectedRegulation->id);
        }
        $actors = $actorsQuery->get();

        $categories = [];
        if ($selectedRegulation) {
            $categories = TrsSopCategory::where('regulation_id', $selectedRegulation->id)
                ->orderBy('id')
                ->get();
        }

        $sopQuery = MstSop::with(['category', 'regulation']);
        $flowChartSopsQuery = MstSop::with(['category', 'mapActorSops.actor.organization']);

        if ($selectedRegulation) {
            $sopQuery->whereHas('category', function ($q) use ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            });
            $flowChartSopsQuery->whereHas('category', function ($q) use ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            });
        } else {
            $sopQuery->whereNull('category_id');
            $flowChartSopsQuery->whereNull('category_id');
        }

        $sop = $sopQuery->orderBy('category_id')
            ->orderBy('id')
            ->get();

        $flowChartSops = $flowChartSopsQuery->orderBy('category_id')
            ->orderBy('id')
            ->get();

        $tkoSections = TrsTkoSections::with(['contents' => function ($q) use ($selectedRegulation) {
            if ($selectedRegulation) {
                $q->where('regulation_id', $selectedRegulation->id);
            }
        }])
        ->orderBy('order')
        ->get();

        $definitions = [];
        $relatedRegulations = [];
        if ($selectedRegulation) {
            $definitions = app(DefinitionService::class)->getByRegulation($selectedRegulation->id);
            $relatedRegulations = $selectedRegulation->relatedRegulations()->get();
        }

        $allDefinitions = app(DefinitionService::class)->getExistingDefinitionsWithMapping();

        return [
            'actors' => $actors,
            'sop' => $sop,
            'flowChartSops' => $flowChartSops,
            'regulations' => $regulations,
            'organizations' => $organizations,
            'selectedRegulationId' => $selectedRegulation?->id,
            'categories' => $categories,
            'tkoSections' => $tkoSections,
            'definitions' => $definitions,
            'allDefinitions' => $allDefinitions,
            'relatedRegulations' => $relatedRegulations,
        ];
    }

    /**
     * Store a newly created actor.
     *
     * @param array $data
     * @return MstActor
     */
    public function createActor(array $data): MstActor
    {
        return DB::transaction(function () use ($data) {
            $actor = MstActor::create($data);

            if (!empty($data['function_ids'])) {
                $actor->functions()->sync($data['function_ids']);
            }

            if (!empty($data['organization_ids'])) {
                $actor->organizations()->sync($data['organization_ids']);
            }

            return $actor;
        });
    }

    /**
     * Update the specified actor.
     *
     * @param MstActor $actor
     * @param array $data
     * @return MstActor
     */
    public function updateActor(MstActor $actor, array $data): MstActor
    {
        return DB::transaction(function () use ($actor, $data) {
            $actor->update($data);

            $actor->functions()->sync($data['function_ids'] ?? []);
            $actor->organizations()->sync($data['organization_ids'] ?? []);

            return $actor;
        });
    }

    /**
     * Remove the specified actor.
     *
     * @param MstActor $actor
     * @return void
     */
    public function deleteActor(MstActor $actor): void
    {
        $actor->delete();
    }

    /**
     * Store a newly created SOP Category.
     *
     * @param array $data
     * @return TrsSopCategory
     */
    public function createCategory(array $data): TrsSopCategory
    {
        return TrsSopCategory::create($data);
    }

    /**
     * Update the specified SOP Category.
     *
     * @param TrsSopCategory $category
     * @param array $data
     * @return TrsSopCategory
     */
    public function updateCategory(TrsSopCategory $category, array $data): TrsSopCategory
    {
        $category->update($data);
        return $category;
    }

    /**
     * Remove the specified SOP Category and its associated SOPs.
     *
     * @param TrsSopCategory $category
     * @return void
     */
    public function deleteCategory(TrsSopCategory $category): void
    {
        DB::transaction(function () use ($category) {
            foreach ($category->procedure as $sop) {
                TrsMapActorSop::where('sop_id', $sop->id)->delete();
                $sop->delete();
            }

            $category->delete();
        });
    }

    /**
     * Store a newly created SOP.
     *
     * @param array $data
     * @return MstSop
     */
    public function createSop(array $data): MstSop
    {
        return MstSop::create($data);
    }

    /**
     * Update the specified SOP.
     *
     * @param MstSop $sop
     * @param array $data
     * @return MstSop
     */
    public function updateSop(MstSop $sop, array $data): MstSop
    {
        $sop->update($data);
        return $sop;
    }

    /**
     * Remove the specified SOP.
     *
     * @param MstSop $sop
     * @return void
     */
    public function deleteSop(MstSop $sop): void
    {
        DB::transaction(function () use ($sop) {
            TrsMapActorSop::where('sop_id', $sop->id)->delete();
            $sop->delete();
        });
    }

    /**
     * Create flowchart mapping.
     *
     * @param array $data
     * @return TrsMapActorSop|null
     */
    public function createDiagram(array $data): ?TrsMapActorSop
    {
        if ($this->diagramMappingExists($data)) {
            return null;
        }

        $sop = MstSop::findOrFail($data['sop_id']);
        $data['tipe'] = $sop->category?->tipe ?? 'A';

        return TrsMapActorSop::create($data);
    }

    /**
     * Update flowchart mapping.
     *
     * @param TrsMapActorSop $mapping
     * @param array $data
     * @return TrsMapActorSop|null
     */
    public function updateDiagram(TrsMapActorSop $mapping, array $data): ?TrsMapActorSop
    {
        if ($this->diagramMappingExists($data, $mapping->id)) {
            return null;
        }

        $sop = MstSop::findOrFail($data['sop_id']);
        $data['tipe'] = $sop->category?->tipe ?? 'A';

        $mapping->update($data);
        return $mapping;
    }

    /**
     * Remove flowchart mapping.
     *
     * @param TrsMapActorSop $mapping
     * @return void
     */
    public function deleteDiagram(TrsMapActorSop $mapping): void
    {
        $mapping->delete();
    }

    /**
     * Check if diagram mapping already exists.
     *
     * @param array $mapping
     * @param int|null $ignoreId
     * @return bool
     */
    private function diagramMappingExists(array $mapping, ?int $ignoreId = null): bool
    {
        return TrsMapActorSop::query()
            ->where('sop_id', $mapping['sop_id'])
            ->where('actor_id', $mapping['actor_id'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }

    /**
     * Store or update TKO Content.
     *
     * @param array $data
     * @return TrsTkoContent
     */
    public function storeOrUpdateTkoContent(array $data): TrsTkoContent
    {
        return TrsTkoContent::updateOrCreate(
            [
                'regulation_id' => $data['regulation_id'],
                'section_id' => $data['section_id'],
            ],
            [
                'content' => $data['content'] ?? null,
            ]
        );
    }

    /**
     * Store a newly created TKO Section.
     *
     * @param array $data
     * @return TrsTkoSections
     */
    public function createSection(array $data): TrsTkoSections
    {
        return TrsTkoSections::create($data);
    }

    /**
     * Update TKO Section.
     *
     * @param TrsTkoSections $section
     * @param array $data
     * @return TrsTkoSections
     */
    public function updateSection(TrsTkoSections $section, array $data): TrsTkoSections
    {
        $section->update($data);
        return $section;
    }

    /**
     * Delete TKO Section and associated content.
     *
     * @param TrsTkoSections $section
     * @return void
     */
    public function deleteSection(TrsTkoSections $section): void
    {
        DB::transaction(function () use ($section) {
            TrsTkoContent::where('section_id', $section->id)->delete();
            $section->delete();
        });
    }

    /**
     * Save structured document.
     *
     * @param array $data
     * @return bool
     */
    public function saveStructuredDocument(array $data): bool
    {
        $regulationId = $data['regulation_id'];
        $sectionsData = $data['sections'];

        $existingSections = TrsTkoSections::all();

        $normalize = function ($name) {
            $name = preg_replace('/^([\d\.]+|[ivxIVX]+|[a-zA-Z])[\.\)\-\s]+/u', '', $name);
            return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        };

        $processedSectionIds = [];

        DB::transaction(function () use ($regulationId, $sectionsData, $existingSections, $normalize, &$processedSectionIds) {
            foreach ($sectionsData as $secData) {
                $name = trim($secData['name']);
                $order = $secData['order'];
                $content = $secData['content'] ?? '';

                $normalizedIncoming = $normalize($name);
                $matchedSection = null;

                foreach ($existingSections as $existing) {
                    if ($normalize($existing->name) === $normalizedIncoming) {
                        $matchedSection = $existing;
                        break;
                    }
                }

                if ($matchedSection) {
                    $updates = [];
                    if (strtolower(trim($matchedSection->name)) !== strtolower($name)) {
                        $updates['name'] = $name;
                    }
                    if ($matchedSection->order !== $order) {
                        $updates['order'] = $order;
                    }
                    if (!empty($updates)) {
                        $matchedSection->update($updates);
                    }
                    $section = $matchedSection;
                } else {
                    $section = TrsTkoSections::create([
                        'name' => $name,
                        'order' => $order,
                    ]);
                }

                $processedSectionIds[] = $section->id;

                $contentExists = DB::table('trs_tko_content')
                    ->where('regulation_id', $regulationId)
                    ->where('section_id', $section->id)
                    ->exists();

                if ($contentExists) {
                    DB::table('trs_tko_content')
                        ->where('regulation_id', $regulationId)
                        ->where('section_id', $section->id)
                        ->update([
                            'content' => $content,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('trs_tko_content')->insert([
                        'regulation_id' => $regulationId,
                        'section_id' => $section->id,
                        'content' => $content,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            TrsTkoContent::where('regulation_id', $regulationId)
                ->whereNotIn('section_id', $processedSectionIds)
                ->delete();
        });

        return true;
    }

    /**
     * Map an existing glossary (definition) to a regulation.
     *
     * @param array $data
     * @return TrsDefinitionRegulation
     * @throws \Exception
     */
    public function mapGlossary(array $data): TrsDefinitionRegulation
    {
        return DB::transaction(function () use ($data) {
            $definitionId = $data['definition_id'];
            $regulationId = $data['regulation_id'];

            $exists = TrsDefinitionRegulation::where('definition_id', $definitionId)
                ->where('regulation_id', $regulationId)
                ->exists();

            if ($exists) {
                throw new \Exception('Mapping glossary dan regulasi ini sudah ada.');
            }

            return TrsDefinitionRegulation::create([
                'definition_id' => $definitionId,
                'regulation_id' => $regulationId,
            ]);
        });
    }

    /**
     * Unmap a glossary from a regulation.
     *
     * @param array $data
     * @return void
     */
    public function unmapGlossary(array $data): void
    {
        DB::transaction(function () use ($data) {
            TrsDefinitionRegulation::where('definition_id', $data['definition_id'])
                ->where('regulation_id', $data['regulation_id'])
                ->delete();
        });
    }

    /**
     * Map an existing regulation as a reference to the active regulation.
     *
     * @param array $data
     * @return TrsRelatedRegulation
     * @throws \Exception
     */
    public function mapRegulation(array $data): TrsRelatedRegulation
    {
        return DB::transaction(function () use ($data) {
            $regulationId = $data['regulation_id'];
            $relatedId = $data['related_id'];

            if ($regulationId === $relatedId) {
                throw new \Exception('Tidak bisa memetakan regulasi ke dirinya sendiri.');
            }

            $exists = TrsRelatedRegulation::where('regulation', $regulationId)
                ->where('related', $relatedId)
                ->exists();

            if ($exists) {
                throw new \Exception('Mapping regulasi referensi ini sudah ada.');
            }

            return TrsRelatedRegulation::create([
                'regulation' => $regulationId,
                'related' => $relatedId,
            ]);
        });
    }

    /**
     * Unmap a regulation reference from the active regulation.
     *
     * @param array $data
     * @return void
     */
    public function unmapRegulation(array $data): void
    {
        DB::transaction(function () use ($data) {
            TrsRelatedRegulation::where('regulation', $data['regulation_id'])
                ->where('related', $data['related_id'])
                ->delete();
        });
    }
}
