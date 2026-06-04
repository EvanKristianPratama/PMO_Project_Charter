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
        $projects = TrsProject::query()
            ->where('tipe_inisiative', 2) // IT Initiatives
            ->with([
                'projectCharter.statusRef', // latestOfMany
                'statusRef:id,name',
                'owner:id,name',
                'mapPicProject',
                'mapCrossFunctions',
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
                        'risks_identified', 'risk_mitigation', 'notes'
                    ];
                } else {
                    $fields = [
                        'category', 'duration', 'tgl_dokumen', 'owner',
                        'background', 'objectives', 'impact_value', 'key_personnel',
                        'key_items', 'budget', 'risks_identified', 'risk_mitigation'
                    ];
                }

                // All possible fields to provide in details for frontend
                $allFields = [
                    'category', 'duration', 'tgl_dokumen', 'sponsor', 'owner', 'leader',
                    'background', 'objectives', 'key_milestone', 'target_kpi',
                    'impact_value', 'key_personnel', 'key_items', 'budget',
                    'risks_identified', 'risk_mitigation', 'notes'
                ];
                
                $completedFields = 0;
                $details = [];
                
                foreach ($allFields as $field) {
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
                    'code' => $project->code,
                    'name' => $project->name,
                    'status_name' => $statusMap[$status] ?? $charter->statusRef?->name ?? $project->statusRef?->name ?? '-',
                    'charter_version' => $charter->version_label ?? '-',
                    'completeness_score' => $completenessScore,
                    'details' => $details,
                    'updated_at' => $charter->updated_at->toISOString(),
                ];
            })
            ->filter()
            ->values();

        return Inertia::render('ProgramEvaluation/ReviewDocument/Index', [
            'projects' => $projects,
        ]);
    }
}
