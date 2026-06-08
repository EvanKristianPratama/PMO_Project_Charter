<?php

namespace App\Http\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\TrsProject;
use Inertia\Inertia;
use Inertia\Response;

class ReviewDocumentController extends Controller
{
    public function index(): Response
    {
        $itInitiatives = $this->getInitiatives(2); // IT Initiatives
        $digitalInitiatives = $this->getDigitalInitiatives(); // Digital Initiatives

        return Inertia::render('ProgramEvaluation/ReviewDocument/Index', [
            'it_projects' => $itInitiatives,
            'digital_projects' => $digitalInitiatives,
            // Keep 'projects' for backward compatibility if needed, defaulting to IT
            'projects' => $itInitiatives,
        ]);
    }

    private function getInitiatives(int $type)
    {
        return TrsProject::query()
            ->where('tipe_inisiative', $type)
            ->with([
                'projectCharter.statusRef', // latestOfMany
                'projectCharter.milestones',
                'statusRef:id,name',
                'owner:id,name',
                'mapPicProject',
                'mapCrossFunctions',
                'mappedInitiatives.coe',
                'latestPcStatusImplementation',
            ])
            ->get()
            ->map(function ($project) {
                $charter = $project->projectCharter;
                if (!$charter) {
                    return null;
                }

                $status = (int) $charter->status;
                
                // Determine which structure to evaluate based on status
                $isApprovedStructure = in_array($status, [2, 3, 4]);
                
                if ($isApprovedStructure) {
                    $fields = [
                        'duration', 'tgl_dokumen', 'sponsor', 'owner', 'leader',
                        'background', 'objectives', 'key_milestone', 'target_kpi',
                        'impact_value', 'key_personnel', 'key_items', 'budget',
                        'risks_identified', 'risk_mitigation', 'notes', 'has_roadmap'
                    ];
                } else {
                    $fields = [
                        'category', 'duration', 'tgl_dokumen', 'owner',
                        'background', 'objectives', 'impact_value', 'key_personnel',
                        'key_items', 'budget', 'risks_identified', 'risk_mitigation', 'has_roadmap'
                    ];
                }

                // All possible fields to provide in details for frontend
                $allFields = [
                    'category', 'duration', 'tgl_dokumen', 'sponsor', 'owner', 'leader',
                    'background', 'objectives', 'key_milestone', 'target_kpi',
                    'impact_value', 'key_personnel', 'key_items', 'budget',
                    'risks_identified', 'risk_mitigation', 'notes', 'has_roadmap'
                ];
                
                $completedFields = 0;
                $details = [];
                
                foreach ($allFields as $field) {
                    if ($field === 'has_roadmap') {
                        $isFilled = $charter->milestones->count() > 0;
                    } else {
                        $value = $charter->{$field};
                        
                        // Special handling for target_kpi which might be in metadata
                        if ($field === 'target_kpi' && (empty($value) || $value === '-')) {
                            $metadata = $charter->metadata ?? [];
                            $value = $metadata['target_kpi'] ?? $metadata['targetKpi'] ?? $metadata['kpi_target'] ?? $metadata['kpi'] ?? null;
                        }

                        $isFilled = false;
                        if (is_array($value)) {
                            $isFilled = !empty($value);
                        } else {
                            $stringValue = trim((string) ($value ?? ''));
                            $isFilled = $stringValue !== '' && $stringValue !== '-';
                        }
                    }

                    if ($isFilled && in_array($field, $fields)) {
                        $completedFields++;
                    }
                    $details[$field] = $isFilled;
                }
                
                $completenessScore = count($fields) > 0 
                    ? round(($completedFields / count($fields)) * 100) 
                    : 0;

                $statusMap = [
                    1 => 'Draft',
                    2 => 'Propose',
                    3 => 'Review',
                    4 => 'Approved',
                    5 => 'Baseline',
                ];

                return [
                    'id' => $project->id . '-' . $charter->id,
                    'project_id' => $project->id,
                    'status' => $project->status,
                    'code' => $project->code,
                    'name' => $project->name,
                    'status_name' => $statusMap[$status] ?? $charter->statusRef?->name ?? $project->statusRef?->name ?? '-',
                    'charter_version' => $charter->version_label ?? '-',
                    'completeness_score' => $completenessScore,
                    'coe_name' => $project->mappedInitiatives->first()?->coe?->name ?? 'Uncategorized',
                    'implementation_status' => $project->latestPcStatusImplementation?->status ?? 'Belum Ada Status',
                    'implementation_period' => $project->latestPcStatusImplementation 
                        ? trim($project->latestPcStatusImplementation->month . ' ' . $project->latestPcStatusImplementation->year)
                        : null,
                    'details' => $details,
                    'updated_at' => $charter->updated_at->toISOString(),
                ];
            })
            ->filter()
            ->values();
    }

    private function getDigitalInitiatives()
    {
        $initiatives = \App\Models\MstInitiative::query()
            ->where('tipe_initiative', 1)
            ->with([
                'coe',
                'organization',
                'masterMilestones',
                'sourceData',
                'taggings.theme.goal',
            ])
            ->get();

        return $initiatives->map(function ($initiative) {
            // Find corresponding TrsProject
            $project = \App\Models\TrsProject::query()
                ->where(function ($query) use ($initiative) {
                    $query->whereHas('mappedInitiatives', function ($q) use ($initiative) {
                        $q->where('initiative_id', $initiative->id);
                    })
                    ->orWhere('code', sprintf('AUTO-MI-%d', $initiative->id))
                    ->orWhere(function ($q) use ($initiative) {
                        $q->where('name', $initiative->name)
                          ->where('tipe_inisiative', (string) $initiative->tipe_initiative);
                    });
                })
                ->with(['projectCharter.statusRef'])
                ->first();

            // Load Compendium (source_id = 1)
            $compendium = \App\Models\TrsScInitiative::query()
                ->where('source_id', 1)
                ->whereHas('mstInitiatives', function ($q) use ($initiative) {
                    $q->where('initiative_id', $initiative->id);
                })
                ->with(['scDetails' => fn ($q) => $q->latest('id'), 'rjpps'])
                ->first();

            // Load Appendix (source_id = 2)
            $appendix = null;
            if ($compendium) {
                $appendix = $compendium->appendixes()
                    ->with(['scDetails' => fn ($q) => $q->latest('id'), 'rjpps'])
                    ->first();
            }

            if (!$appendix) {
                $appendix = \App\Models\TrsScInitiative::query()
                    ->where('source_id', 2)
                    ->whereHas('mstInitiatives', function ($q) use ($initiative) {
                        $q->where('initiative_id', $initiative->id);
                    })
                    ->with(['scDetails' => fn ($q) => $q->latest('id'), 'rjpps'])
                    ->first();
            }

            // --- 1. Master Completeness ---
            $sourceDateFilled = false;
            if ($initiative->sourceData) {
                $m = trim((string) ($initiative->sourceData->month ?? ''));
                $y = trim((string) ($initiative->sourceData->year ?? ''));
                if (($m !== '' && $m !== '-') || ($y !== '' && $y !== '-')) {
                    $sourceDateFilled = true;
                }
            }
            if (!$sourceDateFilled) {
                $created = trim((string) ($initiative->data_source_created ?? $initiative->source_data_created ?? ''));
                if ($created !== '' && $created !== '-') {
                    $sourceDateFilled = true;
                }
            }

            $goalsFilled = false;
            foreach ($initiative->taggings as $tag) {
                $hasTheme = !empty($tag->themes_id);
                $hasGoal = !empty($tag->goal) && trim((string) $tag->goal) !== '';
                if (!$hasTheme && !$hasGoal) {
                    continue;
                }

                $theme = $tag->theme;
                $goal = $theme ? $theme->goal : null;

                $vals = [
                    $goal ? $goal->pilar : null,
                    $goal ? $goal->id : null,
                    $theme ? $theme->idGoal : null,
                    $tag->pilar,
                    $tag->goal,
                ];

                foreach ($vals as $val) {
                    if ($val !== null) {
                        $digits = preg_replace('/[^\d-]/', '', (string) $val);
                        if ($digits !== '' && intval($digits) === 1) {
                            $goalsFilled = true;
                            break 2;
                        }
                    }
                }
            }

            $masterFields = [
                'usecase' => $initiative->name,
                'description' => $initiative->description,
                'owner' => $initiative->business_unit || ($initiative->organization?->name),
                'pic' => $project?->owner_id || $project?->owner_name || ($project?->mapPicProject?->project_owner) || ($project?->mapPicProject?->project_leader),
                'coe' => $initiative->coe_id || ($initiative->coe?->name),
                'source' => $initiative->source || ($initiative->sourceData?->name),
                'source_date' => $sourceDateFilled,
                'goals' => $goalsFilled,
            ];

            $masterLabels = [
                'usecase' => 'Use Case Title',
                'description' => 'Description',
                'owner' => 'Project Owner',
                'pic' => 'PIC',
                'coe' => 'CoE',
                'source' => 'Data Source',
                'source_date' => 'Data Source Date',
                'goals' => 'Goal & Strategic Pillar',
            ];

            $masterFilled = 0;
            $masterIncomplete = [];
            $masterDetails = [];
            foreach ($masterFields as $f => $val) {
                $isFilled = $this->isValFilled($val);
                if ($isFilled) {
                    $masterFilled++;
                } else {
                    $masterIncomplete[] = '[' . $masterLabels[$f] . ']';
                }
                $masterDetails[$f] = $isFilled;
            }
            $masterScore = round(($masterFilled / count($masterFields)) * 100);

            // --- 2. Roadmap Completeness ---
            $milestonesCount = $initiative->masterMilestones->count();
            $hasRoadmap = $milestonesCount > 0;
            $roadmapScore = $hasRoadmap ? '100%' : 'X';
            $roadmapIncomplete = $hasRoadmap ? '-' : 'Not Available';

            // --- 3. Compendium Completeness ---
            $compendiumScore = 'X';
            $compendiumIncomplete = 'Not Available';
            $compendiumDetails = [];
            
            if ($compendium) {
                $compDetail = $compendium->scDetails->first();
                $compFields = [
                    'usecase' => $compendium->usecase,
                    'description' => $compendium->description,
                    'value' => $compendium->value,
                    'urgency' => $compendium->urgency,
                    'owner' => $compendium->owner,
                    'coe' => $compendium->coe,
                    'source_id' => $compendium->source_id,
                    'rjpp_tagging' => $compendium->rjpps->count() > 0,
                ];

                $compLabels = [
                    'usecase' => 'Use Case Title',
                    'description' => 'Description',
                    'value' => 'Value',
                    'urgency' => 'Urgency',
                    'owner' => 'Project Owner',
                    'coe' => 'CoE',
                    'source_id' => 'Data Source',
                    'rjpp_tagging' => 'RJPP Tagging',
                ];

                $compFilled = 0;
                $compIncompleteList = [];
                foreach ($compFields as $f => $val) {
                    $isFilled = $this->isValFilled($val);
                    if ($isFilled) {
                        $compFilled++;
                    } else {
                        $compIncompleteList[] = '[' . $compLabels[$f] . ']';
                    }
                    $compendiumDetails[$f] = $isFilled;
                }
                $compScoreVal = round(($compFilled / count($compFields)) * 100);
                $compendiumScore = $compScoreVal . '%';
                $compendiumIncomplete = empty($compIncompleteList) ? '-' : implode(' ', $compIncompleteList);
            } else {
                $compLabels = [
                    'usecase' => 'Use Case Title',
                    'description' => 'Description',
                    'value' => 'Value',
                    'urgency' => 'Urgency',
                    'owner' => 'Project Owner',
                    'coe' => 'CoE',
                    'source_id' => 'Data Source',
                    'rjpp_tagging' => 'RJPP Tagging',
                ];
                foreach ($compLabels as $f => $lbl) {
                    $compendiumDetails[$f] = false;
                }
            }

            // --- 4. Appendix Completeness ---
            $appendixScore = 'X';
            $appendixIncomplete = 'Not Available';
            $appendixDetails = [];

            if ($appendix) {
                $appDetail = $appendix->scDetails->first();
                
                $signByRaw = $appDetail?->sign_by;
                if (is_string($signByRaw)) {
                    try {
                        $signByVal = json_decode($signByRaw, true) ?? [];
                    } catch (\Exception $e) {
                        $signByVal = $signByRaw ? [$signByRaw] : [];
                    }
                } else {
                    $signByVal = $signByRaw ?? [];
                }

                $appFields = [
                    'usecase' => $appendix->usecase,
                    'owner' => $appendix->owner,
                    'coe' => $appendix->coe,
                    'organization' => $appDetail?->organization,
                    'update_doc' => $appDetail?->update_doc,
                    'description' => $appendix->description,
                    'situation' => $appDetail?->situation,
                    'key_functionalities' => $appDetail?->key_functionalities,
                    'value' => $appendix->value,
                    'value_rationale' => $appDetail?->value_rationale,
                    'value_matrics' => $appDetail?->value_matrics,
                    'urgency' => $appendix->urgency,
                    'urgency_rationale' => $appDetail?->urgency_rationale,
                    'urgency_expected' => $appDetail?->urgency_expected ?: ($appDetail?->expected_q && $appDetail?->year_q ? $appDetail?->expected_q . ' ' . $appDetail?->year_q : null),
                    'ease' => $appDetail?->ease,
                    'ease_rationale' => $appDetail?->ease_rationale,
                    'ease_detail' => $appDetail?->ease_detail,
                    'resource' => $appDetail?->resource,
                    'resource_rationale' => $appDetail?->resource_rationale,
                    'resource_detail' => $appDetail?->resource_detail,
                    'predecessor' => $appDetail?->predecessor,
                    'successor' => $appDetail?->successor,
                    'otherBU' => $appDetail?->otherBU,
                    'sign_by' => $signByVal,
                    'rjpp_tagging' => $appendix->rjpps->count() > 0,
                ];

                $appLabels = [
                    'usecase' => 'Use Case Title',
                    'owner' => 'Project Owner',
                    'coe' => 'CoE',
                    'organization' => 'PIC',
                    'update_doc' => 'Updated',
                    'description' => 'Use Case Description',
                    'situation' => 'Current Situation',
                    'key_functionalities' => 'Key Functionalities',
                    'value' => 'Value',
                    'value_rationale' => 'Value Rationale',
                    'value_matrics' => 'Value Metrics Impacted',
                    'urgency' => 'Urgency',
                    'urgency_rationale' => 'Urgency Rationale',
                    'urgency_expected' => 'Expected Go-Live',
                    'ease' => 'Ease of Implementation',
                    'ease_rationale' => 'Ease Rationale',
                    'ease_detail' => 'Ease Detail',
                    'resource' => 'Resource Requirement',
                    'resource_rationale' => 'Resource Rationale',
                    'resource_detail' => 'Resource Requirement Detail',
                    'predecessor' => 'Predecessor',
                    'successor' => 'Successor',
                    'otherBU' => 'Other BUs Implement',
                    'sign_by' => 'Sign By',
                    'rjpp_tagging' => 'RJPP Tagging',
                ];

                $appFilled = 0;
                $appIncompleteList = [];
                $scoringFields = [
                    'organization',
                    'update_doc',
                    'resource_detail',
                    'situation',
                    'key_functionalities',
                    'value_rationale',
                    'urgency_rationale',
                    'ease_rationale',
                    'resource_rationale',
                    'predecessor',
                    'successor',
                    'otherBU',
                    'sign_by',
                    'rjpp_tagging',
                    'urgency_expected'
                ];

                foreach ($appFields as $f => $val) {
                    $isFilled = $this->isValFilled($val);
                    if (in_array($f, $scoringFields)) {
                        if ($isFilled) {
                            $appFilled++;
                        } else {
                            $appIncompleteList[] = '[' . $appLabels[$f] . ']';
                        }
                    }
                    $appendixDetails[$f] = $isFilled;
                }
                $appScoreVal = round(($appFilled / count($scoringFields)) * 100);
                $appendixScore = $appScoreVal . '%';
                $appendixIncomplete = empty($appIncompleteList) ? '-' : implode(' ', $appIncompleteList);
            } else {
                $appLabels = [
                    'usecase' => 'Use Case Title',
                    'owner' => 'Project Owner',
                    'coe' => 'CoE',
                    'organization' => 'PIC',
                    'update_doc' => 'Updated',
                    'description' => 'Use Case Description',
                    'situation' => 'Current Situation',
                    'key_functionalities' => 'Key Functionalities',
                    'value' => 'Value',
                    'value_rationale' => 'Value Rationale',
                    'value_matrics' => 'Value Metrics Impacted',
                    'urgency' => 'Urgency',
                    'urgency_rationale' => 'Urgency Rationale',
                    'urgency_expected' => 'Expected Go-Live',
                    'ease' => 'Ease of Implementation',
                    'ease_rationale' => 'Ease Rationale',
                    'ease_detail' => 'Ease Detail',
                    'resource' => 'Resource Requirement',
                    'resource_rationale' => 'Resource Rationale',
                    'resource_detail' => 'Resource Requirement Detail',
                    'predecessor' => 'Predecessor',
                    'successor' => 'Successor',
                    'otherBU' => 'Other BUs Implement',
                    'sign_by' => 'Sign By',
                    'rjpp_tagging' => 'RJPP Tagging',
                ];
                foreach ($appLabels as $f => $lbl) {
                    $appendixDetails[$f] = false;
                }
            }

            return [
                'id' => $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
                'coe_name' => $initiative->coe?->name ?? 'Uncategorized',
                'status_name' => $initiative->status ?? '-',
                'master_score' => $masterScore . '%',
                'master_incomplete' => empty($masterIncomplete) ? '-' : implode(' ', $masterIncomplete),
                'roadmap_score' => $roadmapScore,
                'roadmap_incomplete' => $roadmapIncomplete,
                'compendium_score' => $compendiumScore,
                'compendium_incomplete' => $compendiumIncomplete,
                'appendix_score' => $appendixScore,
                'appendix_incomplete' => $appendixIncomplete,
                'details' => [
                    'master' => $masterDetails,
                    'compendium' => $compendiumDetails,
                    'appendix' => $appendixDetails,
                ],
            ];
        });
    }

    private function isValFilled($val): bool
    {
        if (is_null($val)) {
            return false;
        }
        if (is_array($val)) {
            return !empty($val);
        }
        $stringVal = trim((string) $val);
        return $stringVal !== '' && $stringVal !== '-' && $stringVal !== '[]';
    }
}
