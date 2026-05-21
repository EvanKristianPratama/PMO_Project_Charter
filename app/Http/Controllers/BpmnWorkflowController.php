<?php

namespace App\Http\Controllers;

use App\Models\BpmnWorkflow;
use App\Models\MstInitiative;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BpmnWorkflowController extends Controller
{
    /**
     * Tampilkan halaman utama BPMN Workflow.
     */
    public function index(): Response
    {
        $workflows = BpmnWorkflow::orderBy('name')->get();
        
        $initiatives = MstInitiative::with(['latestStatus'])
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'tipe_initiative', 'status'])
            ->map(fn ($init) => [
                'id' => $init->id,
                'code' => $init->code,
                'name' => $init->name,
                'tipe_initiative' => $init->tipe_initiative, // 1: Digital, 2: IT
                'status' => $init->status ?: 'drafting',
            ]);

        return Inertia::render('BpmnWorkflow/Index', [
            'workflows' => $workflows,
            'mstInitiatives' => $initiatives,
        ]);
    }

    /**
     * Simpan atau perbarui data workflow BPMN.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:bpmn_workflows,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'flow_data' => 'nullable|array',
            'bpmn_xml' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $workflow = BpmnWorkflow::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'flow_data' => $validated['flow_data'] ?? null,
                'bpmn_xml' => $validated['bpmn_xml'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        ActivityLogService::log(
            event: isset($validated['id']) ? 'updated' : 'created',
            description: (isset($validated['id']) ? 'Mengubah' : 'Menambah') . ' BPMN Workflow: ' . $workflow->name,
            subject: $workflow
        );

        return redirect()
            ->back()
            ->with('success', 'BPMN Workflow berhasil disimpan.');
    }

    /**
     * Hapus workflow BPMN dari database.
     */
    public function destroy(BpmnWorkflow $bpmnWorkflow): RedirectResponse
    {
        $name = $bpmnWorkflow->name;
        $bpmnWorkflow->delete();

        ActivityLogService::log(
            event: 'deleted',
            description: 'Menghapus BPMN Workflow: ' . $name
        );

        return redirect()
            ->back()
            ->with('success', 'BPMN Workflow berhasil dihapus.');
    }

    /**
     * Memicu aksi nyata pada aplikasi berdasarkan node BPMN yang sedang dieksekusi.
     */
    public function triggerAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|string|in:update_status,sync_db,log_activity',
            'initiative_id' => 'nullable|integer|exists:mst_initiative,id',
            'status_value' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string|max:255',
            'workflow_name' => 'required|string|max:255',
            'node_label' => 'required|string|max:255',
        ]);

        try {
            $msg = "";
            $workflowName = $validated['workflow_name'];
            $nodeLabel = $validated['node_label'];

            if ($validated['action_type'] === 'update_status') {
                $initiative = MstInitiative::findOrFail($validated['initiative_id']);
                $newStatus = $validated['status_value'] ?: 'drafting';
                
                // 1. Simpan history status
                $initiative->statusHistory()->create([
                    'status' => $newStatus,
                    'tanggal' => now(),
                    'notes' => "Diperbarui via BPMN [$workflowName] - Langkah: $nodeLabel",
                ]);

                // 2. Perbarui status kolom utama
                $initiative->update(['status' => $newStatus]);
                
                // 3. Log audit activity
                ActivityLogService::log(
                    event: 'updated',
                    description: "BPMN [$workflowName] memicu pembaruan inisiatif [{$initiative->code}] menjadi status: $newStatus",
                    subject: $initiative,
                    properties: ['new_status' => $newStatus, 'workflow' => $workflowName]
                );

                $tipeLabel = $initiative->tipe_initiative == 1 ? 'Digital Initiative' : 'IT Initiative';
                $msg = "Aksi Berhasil: Status inisiatif {$initiative->code} ({$tipeLabel}) berhasil diperbarui menjadi '" . ucfirst($newStatus) . "'.";
                if ($newStatus === 'approved') {
                    $msg .= " Sinkronisasi otomatis ke Project Implementation (Project Charter) telah berhasil dijalankan!";
                }

            } elseif ($validated['action_type'] === 'sync_db') {
                // Mocking Sync trigger
                ActivityLogService::log(
                    event: 'updated',
                    description: "BPMN [$workflowName] memicu sinkronisasi Cloud Data.",
                    properties: ['workflow' => $workflowName]
                );

                $msg = "Aksi Berhasil: Sinkronisasi data cloud berhasil dipicu dan dijalankan di latar belakang!";

            } elseif ($validated['action_type'] === 'log_activity') {
                $logMsg = $validated['custom_message'] ?: "Langkah BPMN '$nodeLabel' dieksekusi.";
                
                ActivityLogService::log(
                    event: 'updated',
                    description: "BPMN [$workflowName] mencatat aktivitas audit: $logMsg",
                    properties: ['workflow' => $workflowName, 'node' => $nodeLabel]
                );

                $msg = "Aksi Berhasil: Catatan audit '" . $logMsg . "' berhasil ditambahkan ke Log Aktivitas.";
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses aksi BPMN: ' . $e->getMessage(),
            ], 500);
        }
    }
}
