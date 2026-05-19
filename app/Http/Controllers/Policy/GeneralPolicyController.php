<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstGeneralPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GeneralPolicyController extends Controller
{
    /**
     * Display a listing of general policies.
     */
    public function index(): Response
    {
        // Auto-seed default 14 items if empty
        if (MstGeneralPolicy::count() === 0) {
            $defaultPolicies = [
                [
                    'number' => 1,
                    'description' => 'Perusahaan wajib menerapkan tata kelola Teknologi Informasi (TI) berdasarkan kebijakan tata kelola TI yang berlaku.',
                ],
                [
                    'number' => 2,
                    'description' => 'Prinsip tata kelola TI setidaknya mencakup prinsip manajemen, data & informasi, teknologi, dan keamanan TI.',
                ],
                [
                    'number' => 3,
                    'description' => 'Kebijakan TI memperhatikan aspek keselarasan strategi, nilai tambah penerapan TI, manajemen risiko, manajemen sumber daya, dan pengukuran kinerja.',
                ],
                [
                    'number' => 4,
                    'description' => 'Kebijakan tata kelola TI dilakukan evaluasi secara berkala.',
                ],
                [
                    'number' => 5,
                    'description' => 'Perusahaan perlu menyusun rencana strategis TI sesuai periode Rencana Jangka Panjang (RJP) dan diimplementasikan dalam rencana tahunan yang menjadi bagian dari RKAP.',
                ],
                [
                    'number' => 6,
                    'description' => 'Rencana strategis TI paling sedikit memuat peran TI terhadap pengembangan bisnis termasuk transformasi digital, organisasi TI, rencana pembiayaan TI, dan peta jalan TI.',
                ],
                [
                    'number' => 7,
                    'description' => 'Rencana strategis TI ditetapkan oleh Direksi dan disampaikan kepada RUPS sesuai dengan periode waktu penyampaian RJP.',
                ],
                [
                    'number' => 8,
                    'description' => 'Dewan Komisaris melakukan evaluasi, mengarahkan, dan memantau rencana strategis TI.',
                ],
                [
                    'number' => 9,
                    'description' => 'Rencana strategis TI dapat diubah jika terjadi kondisi yang signifikan mempengaruhi sasaran dan strategi TI antara lain perubahan RJP, perkembangan TI, atau perubahan perundang-undangan mengenai penyelenggaraan TI.',
                ],
                [
                    'number' => 10,
                    'description' => 'Perubahan rencana strategis TI dapat dilakukan 1(satu) kali dalam 1(satu) tahun dan disampaikan kepada RUPS/Kementerian BUMN.',
                ],
                [
                    'number' => 11,
                    'description' => 'Dalam rangka menyelenggarakan TI, Direksi menetapkan arsitektur TI.',
                ],
                [
                    'number' => 12,
                    'description' => 'Arsitektur TI dapat menjadi bagian atau dokumen yang terpisahkan dari Rencana Strategis TI.',
                ],
                [
                    'number' => 13,
                    'description' => 'Penyusunan Arsitektur TI paling sedikit mempertimbangkan aspek proses bisnis, data dan informasi, serta teknologi.',
                ],
                [
                    'number' => 14,
                    'description' => 'Dalam hal terjadi perubahan aspek proses bisnis, data dan informasi, serta teknologi, Perusahaan wajib melakukan penyesuaian arsitektur TI.',
                ],
            ];

            foreach ($defaultPolicies as $policy) {
                MstGeneralPolicy::create($policy);
            }
        }

        $policies = MstGeneralPolicy::orderBy('number', 'asc')->get();

        return Inertia::render('Policy/General/Index', [
            'policies' => $policies,
        ]);
    }

    /**
     * Store a newly created general policy.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => 'required|integer',
            'description' => 'required|string',
        ], [
            'number.required' => 'Nomor Kebijakan wajib diisi.',
            'number.integer' => 'Nomor Kebijakan harus berupa angka.',
            'description.required' => 'Deskripsi Kebijakan wajib diisi.',
        ]);

        MstGeneralPolicy::create($validated);

        return redirect()
            ->route('policy.general.index')
            ->with('success', 'Kebijakan Umum berhasil ditambahkan.');
    }

    /**
     * Update the specified general policy.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $policy = MstGeneralPolicy::findOrFail($id);

        $validated = $request->validate([
            'number' => 'required|integer',
            'description' => 'required|string',
        ], [
            'number.required' => 'Nomor Kebijakan wajib diisi.',
            'number.integer' => 'Nomor Kebijakan harus berupa angka.',
            'description.required' => 'Deskripsi Kebijakan wajib diisi.',
        ]);

        $policy->update($validated);

        return redirect()
            ->route('policy.general.index')
            ->with('success', 'Kebijakan Umum berhasil diperbarui.');
    }

    /**
     * Remove the specified general policy.
     */
    public function destroy(int $id): RedirectResponse
    {
        $policy = MstGeneralPolicy::findOrFail($id);
        $policy->delete();

        return redirect()
            ->route('policy.general.index')
            ->with('success', 'Kebijakan Umum berhasil dihapus.');
    }
}
