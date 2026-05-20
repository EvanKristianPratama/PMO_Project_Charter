<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstRole;
use App\Models\TrsResponsibility;
use App\Models\MstRegulation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of roles & responsibilities in formal document view mode.
     */
    public function index(): Response
    {
        try {
            // Auto-seed default roles & responsibilities if the table is empty
            if (MstRole::count() === 0) {
                $board = MstRole::create([
                    'name' => 'Dewan Direksi (Board)',
                    'description' => 'Dewan Direksi adalah sekelompok eksekutif paling senior dan/atau direktur noneksekutif yang bertanggung jawab atas tata kelola dan kendali keseluruhan atas sumber daya Perusahaan.'
                ]);

                $boardResponsibilities = [
                    'Menetapkan IT Governance.',
                    'Menetapkan IT Strategy Definition.',
                    'Mengevaluasi dan mendapatkan pelaporan hasil pencapaian layanan TIK.',
                    'Memastikan monitor perkembangan teknologi.',
                    'Menetapkan Business Impact and Risk Analysis.',
                    'Menetapkan Risk Mitigation.',
                    'Menetapkan Risk Monitoring.',
                    'Menetapkan Pengelolaan sumber daya informasi.',
                    'Melakukan koordinasi strategi secara internal dengan pemangku kepentingan untuk memastikan keselarasan.',
                    'Menetapkan rencana perubahan dan pengelolaan stakeholders.'
                ];

                foreach ($boardResponsibilities as $content) {
                    TrsResponsibility::create([
                        'role_id' => $board->id,
                        'content' => $content
                    ]);
                }

                $execComm = MstRole::create([
                    'name' => 'Executive Committee',
                    'description' => 'Executive Committee adalah sekelompok eksekutif senior yang ditunjuk oleh dewan untuk memastikan bahwa dewan terlibat dan terus mendapat informasi tentang keputusan-keputusan penting.'
                ]);

                $execResponsibilities = [
                    'Mengelola IT Governance.',
                    'Mengelola IT Strategy Definition.',
                    'Mengevaluasi dan laporkan hasil pencapaian layanan TIK.',
                    'Memonitor perkembangan teknologi.',
                    'Menyusun Business Impact and Risk Analysis.',
                    'Melaksanakan Risk Mitigation.',
                    'Melaksanakan Risk Monitoring.',
                    'Mengelola sumber daya informasi.',
                    'Mengelola Business Process Implementation.',
                    'Melakukan koordinasi strategi secara internal dengan pemangku kepentingan untuk memastikan keselarasan.',
                    'Membangun rencana perubahan dan pengelolaan stakeholders.'
                ];

                foreach ($execResponsibilities as $content) {
                    TrsResponsibility::create([
                        'role_id' => $execComm->id,
                        'content' => $content
                    ]);
                }
            }

            $roles = MstRole::with(['responsibilities' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->orderBy('id', 'asc')->get();

            $regulations = MstRegulation::orderBy('id', 'desc')->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('[RoleController] DB error loading roles: ' . $e->getMessage());
            $roles = collect([]);
            $regulations = collect([]);
        }

        return Inertia::render('Policy/Role/Index', [
            'roles' => $roles,
            'regulations' => $regulations,
        ]);
    }

    /**
     * Display the roles & responsibilities management CRUD view.
     */
    public function manage(): Response
    {
        $roles = MstRole::with(['responsibilities' => function ($query) {
            $query->orderBy('id', 'asc');
        }])->orderBy('id', 'asc')->get();

        return Inertia::render('Policy/Role/Manage', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
        ]);

        MstRole::create($validated);

        return redirect()
            ->route('policy.roles.manage')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Update the specified role.
     */
    public function updateRole(Request $request, int $id): RedirectResponse
    {
        $role = MstRole::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
        ]);

        $role->update($validated);

        return redirect()
            ->route('policy.roles.manage')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified role.
     */
    public function destroyRole(int $id): RedirectResponse
    {
        $role = MstRole::findOrFail($id);
        $role->delete();

        return redirect()
            ->route('policy.roles.manage')
            ->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Store a newly created responsibility.
     */
    public function storeResponsibility(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:mst_roles,id',
            'content' => 'required|string',
        ], [
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
            'content.required' => 'Isi Tanggung Jawab / Responsibility wajib diisi.',
        ]);

        TrsResponsibility::create($validated);

        return redirect()
            ->route('policy.roles.manage')
            ->with('success', 'Responsibility berhasil ditambahkan.');
    }

    /**
     * Update the specified responsibility.
     */
    public function updateResponsibility(Request $request, int $id): RedirectResponse
    {
        $responsibility = TrsResponsibility::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'Isi Tanggung Jawab / Responsibility wajib diisi.',
        ]);

        $responsibility->update($validated);

        return redirect()
            ->route('policy.roles.manage')
            ->with('success', 'Responsibility berhasil diperbarui.');
    }

    /**
     * Remove the specified responsibility.
     */
    public function destroyResponsibility(int $id): RedirectResponse
    {
        $responsibility = TrsResponsibility::findOrFail($id);
        $responsibility->delete();

        return redirect()
            ->route('policy.roles.manage')
            ->with('success', 'Responsibility berhasil dihapus.');
    }
}
