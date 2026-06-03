<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstObjective;
use App\Models\MstPractice;
use App\Models\TrsItspInfoflowInput;
use App\Models\TrsItspInfoflowOutput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ItspInfoflowController extends Controller
{
    /**
     * Display the ITSP Information Flow view.
     */
    public function index(Request $request): Response
    {
        // Standard page view render: Fetch grouped objectives for the select dropdown
        $allObjectives = MstObjective::orderBy('objective_id', 'asc')->get();

        $groupedObjectives = [];
        foreach ($allObjectives as $obj) {
            $domain = $obj->domain ?: 'Lain-lain';
            if (!isset($groupedObjectives[$domain])) {
                $groupedObjectives[$domain] = [
                    'domain' => $domain,
                    'items' => [],
                ];
            }
            $groupedObjectives[$domain]['items'][] = [
                'id' => $obj->objective_id,
                'name' => $obj->objective,
            ];
        }

        return Inertia::render('Policy/ItspInfoflow/Index', [
            'objectiveGroups' => array_values($groupedObjectives),
        ]);
    }

    /**
     * Fetch flow data JSON for a specific objective.
     */
    public function getData(Request $request): JsonResponse
    {
        $objectiveId = $request->query('objective_id');
        if (!$objectiveId) {
            return response()->json([
                'success' => false,
                'message' => 'Objective ID is required.',
            ], 400);
        }

        try {
            // Fetch local objective details with practices, roles, responsibles & responsibles' mapped roles
            $objective = MstObjective::with([
                'practices' => function ($query) {
                    $query->orderBy('practice_id', 'asc');
                },
                'practices.roles',
                'practices.itspInputs',
                'practices.itspOutputs',
                'responsibles' => function ($query) {
                    $query->orderBy('id', 'asc');
                },
                'responsibles.mappedRoles',
            ])
            ->where('objective_id', $objectiveId)
            ->first();

            if (!$objective) {
                return response()->json([
                    'success' => false,
                    'message' => "Objective {$objectiveId} not found in local database.",
                ], 404);
            }

            // Check if this objective has any locally saved inputs/outputs
            $hasLocalData = false;
            foreach ($objective->practices as $practice) {
                if ($practice->itspInputs->isNotEmpty() || $practice->itspOutputs->isNotEmpty()) {
                    $hasLocalData = true;
                    break;
                }
            }

            $inputsOutputsMap = [];
            
            if (!$hasLocalData) {
                // FALLBACK: Translate local objective ID (e.g. "1.01" -> "EDM01") and fetch COBIT API data for inputs and outputs
                $cobitObjectiveId = $this->translateObjectiveId($objectiveId);
                $cobitApiUrl = "https://cobit2019.divusi.co.id/api/cobit/gamo-infoflow?objective_id=" . urlencode($cobitObjectiveId);
                
                try {
                    $apiResponse = Http::timeout(5)->get($cobitApiUrl);
                    if ($apiResponse->successful()) {
                        $apiData = $apiResponse->json();
                        if (!empty($apiData['success']) && !empty($apiData['objectives'][0]['practices'])) {
                            foreach ($apiData['objectives'][0]['practices'] as $cobitPractice) {
                                $cPracId = $cobitPractice['practice_id'] ?? '';
                                $inputsOutputsMap[$cPracId] = [
                                    'inputs' => $cobitPractice['inputs'] ?? [],
                                    'outputs' => $cobitPractice['outputs'] ?? [],
                                ];
                            }
                        }
                    }
                } catch (\Exception $apiEx) {
                    Log::warning("ITSP Infoflow API Fetch failed: " . $apiEx->getMessage());
                }
            }

            // Merge local or COBIT inputs and outputs into local practices structure
            $practices = [];
            foreach ($objective->practices as $practice) {
                $practiceData = $practice->toArray();
                
                if ($hasLocalData) {
                    // Populate from local tables
                    $practiceData['inputs'] = $practice->itspInputs->map(function ($input) {
                        return [
                            'input_id' => $input->id,
                            'from' => $input->from,
                            'description' => $input->description,
                        ];
                    })->toArray();

                    $practiceData['outputs'] = $practice->itspOutputs->map(function ($output) {
                        return [
                            'output_id' => $output->id,
                            'to' => $output->to,
                            'description' => $output->description,
                        ];
                    })->toArray();
                } else {
                    // Populate from COBIT API map
                    $cobitPracticeId = $this->translatePracticeId($practice->practice_id);
                    $rawInputs = $inputsOutputsMap[$cobitPracticeId]['inputs'] ?? [];
                    $rawOutputs = $inputsOutputsMap[$cobitPracticeId]['outputs'] ?? [];

                    $practiceData['inputs'] = $this->localizeFlowReferences($rawInputs);
                    $practiceData['outputs'] = $this->localizeFlowReferences($rawOutputs);
                }

                // Map role assignments with structured RACI payload
                $roleAssignments = [];
                foreach ($practice->roles as $role) {
                    $roleAssignments[$role->id] = [
                        'role_id' => $role->id,
                        'role_name' => $role->name,
                        'raci' => $role->pivot->r_a,
                    ];
                }
                $practiceData['role_assignments'] = $roleAssignments;
                $practices[] = $practiceData;
            }

            return response()->json([
                'success' => true,
                'is_local' => $hasLocalData,
                'objective_id' => $objective->objective_id,
                'objective' => $objective->objective,
                'objective_description' => $objective->objective_description,
                'objective_purpose' => $objective->objective_purpose,
                'responsibles' => $objective->responsibles->map(function ($resp) {
                    return [
                        'id' => $resp->id,
                        'responsible' => $resp->responsible,
                        'mapped_roles' => $resp->mappedRoles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name,
                            ];
                        }),
                    ];
                }),
                'practices' => $practices,
            ]);

        } catch (\Exception $e) {
            Log::error("Error in ItspInfoflowController getData: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save the bulk inputs and outputs for practices locally (CRUD).
     */
    public function saveData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'objective_id' => 'required|string',
            'practices' => 'required|array',
            'practices.*.practice_id' => 'required|string',
            'practices.*.inputs' => 'present|array',
            'practices.*.inputs.*.from' => 'required|string',
            'practices.*.inputs.*.description' => 'required|string',
            'practices.*.outputs' => 'present|array',
            'practices.*.outputs.*.to' => 'required|string',
            'practices.*.outputs.*.description' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['practices'] as $practiceData) {
                    $practiceId = $practiceData['practice_id'];

                    // Clear previous local entries for this practice
                    TrsItspInfoflowInput::where('practice_id', $practiceId)->delete();
                    TrsItspInfoflowOutput::where('practice_id', $practiceId)->delete();

                    // Insert inputs
                    foreach ($practiceData['inputs'] as $input) {
                        TrsItspInfoflowInput::create([
                            'practice_id' => $practiceId,
                            'from' => $input['from'],
                            'description' => $input['description'],
                        ]);
                    }

                    // Insert outputs
                    foreach ($practiceData['outputs'] as $output) {
                        TrsItspInfoflowOutput::create([
                            'practice_id' => $practiceId,
                            'to' => $output['to'],
                            'description' => $output['description'],
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Aliran informasi ITSP berhasil disimpan secara lokal.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error in ItspInfoflowController saveData: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Translate local objective ID (e.g. "1.01") to COBIT ID (e.g. "EDM01")
     */
    private function translateObjectiveId(string $id): string
    {
        $parts = explode('.', $id);
        if (count($parts) !== 2) {
            return $id;
        }

        $domainNum = intval($parts[0]);
        $objNum = intval($parts[1]);

        $domains = [
            1 => 'EDM',
            2 => 'APO',
            3 => 'BAI',
            4 => 'DSS',
            5 => 'MEA',
        ];

        $prefix = $domains[$domainNum] ?? 'COBIT';
        $objSeq = str_pad($objNum, 2, '0', STR_PAD_LEFT);

        return "{$prefix}{$objSeq}";
    }

    /**
     * Translate local practice ID (e.g. "1.1.01") to COBIT Practice ID (e.g. "EDM01.01")
     */
    private function translatePracticeId(string $id): string
    {
        $parts = explode('.', $id);
        if (count($parts) !== 3) {
            return $id;
        }

        $domainNum = intval($parts[0]);
        $objNum = intval($parts[1]);
        $pracNum = intval($parts[2]);

        $domains = [
            1 => 'EDM',
            2 => 'APO',
            3 => 'BAI',
            4 => 'DSS',
            5 => 'MEA',
        ];

        $prefix = $domains[$domainNum] ?? 'COBIT';
        $objSeq = str_pad($objNum, 2, '0', STR_PAD_LEFT);
        $pracSeq = str_pad($pracNum, 2, '0', STR_PAD_LEFT);

        return "{$prefix}{$objSeq}.{$pracSeq}";
    }

    /**
     * Translate COBIT references in inputs/outputs back to local format if possible
     */
    private function localizeFlowReferences(array $items): array
    {
        $localized = [];
        $domainsMap = [
            'EDM' => 1,
            'APO' => 2,
            'BAI' => 3,
            'DSS' => 4,
            'MEA' => 5,
        ];

        foreach ($items as $item) {
            $fromTo = $item['from'] ?? $item['to'] ?? '';
            
            // Try to translate from/to references like "APO02.05" to "2.2.05"
            $translatedRef = $fromTo;
            preg_match('/^([A-Z]{3})(\d{2})\.(\d{2})$/', trim(strtoupper($fromTo)), $matches);
            if (!empty($matches)) {
                $dPrefix = $matches[1];
                $oNum = intval($matches[2]);
                $pNum = intval($matches[3]);

                if (isset($domainsMap[$dPrefix])) {
                    $translatedRef = "{$domainsMap[$dPrefix]}.{$oNum}.{$pNum}";
                }
            } else {
                // Check if it's objective level e.g. "APO02" to "2.02"
                preg_match('/^([A-Z]{3})(\d{2})$/', trim(strtoupper($fromTo)), $matchesObj);
                if (!empty($matchesObj)) {
                    $dPrefix = $matchesObj[1];
                    $oNum = intval($matchesObj[2]);

                    if (isset($domainsMap[$dPrefix])) {
                        $translatedRef = "{$domainsMap[$dPrefix]}.{$oNum}";
                    }
                }
            }

            if (isset($item['from'])) {
                $item['from'] = $translatedRef;
            }
            if (isset($item['to'])) {
                $item['to'] = $translatedRef;
            }

            $localized[] = $item;
        }

        return $localized;
    }

    /**
     * Render the ITSP Information Flow CRUD management view.
     */
    public function manage(): Response
    {
        $inputs = TrsItspInfoflowInput::with(['practice.objective'])->get();
        $outputs = TrsItspInfoflowOutput::with(['practice.objective'])->get();

        $practices = MstPractice::orderBy('practice_id', 'asc')->get();
        $objectives = MstObjective::orderBy('objective_id', 'asc')->get();

        return Inertia::render('Policy/ItspInfoflow/Manage', [
            'inputs' => $inputs,
            'outputs' => $outputs,
            'practices' => $practices,
            'objectives' => $objectives,
        ]);
    }

    /**
     * Sync default inputs and outputs from the COBIT API in bulk.
     */
    public function syncFromCobit(Request $request): JsonResponse
    {
        set_time_limit(240);

        try {
            $objectives = MstObjective::all();
            $insertedInputs = 0;
            $insertedOutputs = 0;

            DB::transaction(function () use ($objectives, &$insertedInputs, &$insertedOutputs) {
                // Clear existing records to avoid duplicates when re-syncing
                TrsItspInfoflowInput::query()->delete();
                TrsItspInfoflowOutput::query()->delete();

                foreach ($objectives as $obj) {
                    $objectiveId = $obj->objective_id;
                    $cobitObjectiveId = $this->translateObjectiveId($objectiveId);
                    $cobitApiUrl = "https://cobit2019.divusi.co.id/api/cobit/gamo-infoflow?objective_id=" . urlencode($cobitObjectiveId);

                    try {
                        $apiResponse = Http::timeout(8)->get($cobitApiUrl);
                        if ($apiResponse->successful()) {
                            $apiData = $apiResponse->json();
                            if (!empty($apiData['success']) && !empty($apiData['objectives'][0]['practices'])) {
                                foreach ($apiData['objectives'][0]['practices'] as $cobitPractice) {
                                    $cPracId = $cobitPractice['practice_id'] ?? '';
                                    $localPracticeId = $this->translateCobitPracticeIdToLocal($cPracId);

                                    // Verify practice exists in local DB
                                    if (!MstPractice::where('practice_id', $localPracticeId)->exists()) {
                                        continue;
                                    }

                                    $rawInputs = $cobitPractice['inputs'] ?? [];
                                    $rawOutputs = $cobitPractice['outputs'] ?? [];

                                    $localizedInputs = $this->localizeFlowReferences($rawInputs);
                                    $localizedOutputs = $this->localizeFlowReferences($rawOutputs);

                                    foreach ($localizedInputs as $input) {
                                        TrsItspInfoflowInput::create([
                                            'practice_id' => $localPracticeId,
                                            'from' => $input['from'],
                                            'description' => $input['description'],
                                        ]);
                                        $insertedInputs++;
                                    }

                                    foreach ($localizedOutputs as $output) {
                                        TrsItspInfoflowOutput::create([
                                            'practice_id' => $localPracticeId,
                                            'to' => $output['to'],
                                            'description' => $output['description'],
                                        ]);
                                        $insertedOutputs++;
                                    }
                                }
                            }
                        }
                    } catch (\Exception $apiEx) {
                        Log::warning("Bulk Sync for objective {$objectiveId} failed: " . $apiEx->getMessage());
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil melakukan sinkronisasi data dari COBIT API. Dimasukkan {$insertedInputs} input dan {$insertedOutputs} output secara lokal.",
            ]);

        } catch (\Exception $e) {
            Log::error("Error in syncFromCobit: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store new Input.
     */
    public function storeInput(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'practice_id' => 'required|string|exists:mst_practice,practice_id',
            'from' => 'required|string',
            'description' => 'required|string',
        ]);

        TrsItspInfoflowInput::create($validated);

        return redirect()->back()->with('success', 'Input aliran informasi berhasil ditambahkan.');
    }

    /**
     * Update Input.
     */
    public function updateInput(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'practice_id' => 'required|string|exists:mst_practice,practice_id',
            'from' => 'required|string',
            'description' => 'required|string',
        ]);

        $input = TrsItspInfoflowInput::findOrFail($id);
        $input->update($validated);

        return redirect()->back()->with('success', 'Input aliran informasi berhasil diperbarui.');
    }

    /**
     * Destroy Input.
     */
    public function destroyInput($id): RedirectResponse
    {
        $input = TrsItspInfoflowInput::findOrFail($id);
        $input->delete();

        return redirect()->back()->with('success', 'Input aliran informasi berhasil dihapus.');
    }

    /**
     * Store new Output.
     */
    public function storeOutput(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'practice_id' => 'required|string|exists:mst_practice,practice_id',
            'to' => 'required|string',
            'description' => 'required|string',
        ]);

        TrsItspInfoflowOutput::create($validated);

        return redirect()->back()->with('success', 'Output aliran informasi berhasil ditambahkan.');
    }

    /**
     * Update Output.
     */
    public function updateOutput(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'practice_id' => 'required|string|exists:mst_practice,practice_id',
            'to' => 'required|string',
            'description' => 'required|string',
        ]);

        $output = TrsItspInfoflowOutput::findOrFail($id);
        $output->update($validated);

        return redirect()->back()->with('success', 'Output aliran informasi berhasil diperbarui.');
    }

    /**
     * Destroy Output.
     */
    public function destroyOutput($id): RedirectResponse
    {
        $output = TrsItspInfoflowOutput::findOrFail($id);
        $output->delete();

        return redirect()->back()->with('success', 'Output aliran informasi berhasil dihapus.');
    }

    /**
     * Translate COBIT practice ID back to local format (e.g. "EDM01.01" to "1.1.01")
     */
    private function translateCobitPracticeIdToLocal(string $cobitId): string
    {
        preg_match('/^([A-Z]{3})(\d{2})\.(\d{2})$/', trim(strtoupper($cobitId)), $matches);
        if (empty($matches)) {
            return $cobitId;
        }

        $domainsMap = [
            'EDM' => 1,
            'APO' => 2,
            'BAI' => 3,
            'DSS' => 4,
            'MEA' => 5,
        ];

        $dPrefix = $matches[1];
        $oNum = intval($matches[2]);
        $pNum = intval($matches[3]);

        if (isset($domainsMap[$dPrefix])) {
            $domainVal = $domainsMap[$dPrefix];
            $objVal = $oNum; 
            $pracVal = str_pad($pNum, 2, '0', STR_PAD_LEFT); 
            return "{$domainVal}.{$objVal}.{$pracVal}";
        }

        return $cobitId;
    }
}
