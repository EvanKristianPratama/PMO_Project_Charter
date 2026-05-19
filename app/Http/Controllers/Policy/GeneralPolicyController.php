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
        // Auto-seed default items if table is empty (guard against DB errors)
        try {
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
                    'description' => 'Rencana startegis TI paling sedikit memuat peran TI terhadap pengembangan bisnis termasuk transformasi digital, organisasi TI, rencana pembiayaan TI, dan peta jalan TI.',
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
                    'description' => 'Perubahan rencana strategis TI dapat dilakukan 1(satu) kali dalam 1(satu) tahun dan disampaikan kepada RUPS/Kementrian BUMN.',
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
                    'description' => 'Dalam hal terjadi perubahan aspek, Perusahaan wajib melakukan pemutakhiran terhadap arsitektur TI.',
                ],
                [
                    'number' => 15,
                    'description' => 'Direksi membentuk Komite Pengarah TI beranggotakan paling sedikit Direktur yang membidangi TI dan Direktur yang membidangi Manajemen Risiko yang bertugas: a. memastikan keselarasan Rencana Strategis TI dengan RJP b. memastikan implementasi Rencana Strategis TI yang dituangkan dalam RKAP c. mengevaluasi, mengarahkan, dan memantau penyelenggaraan TI.',
                ],
                [
                    'number' => 16,
                    'description' => 'Perusahaan menerapkan pengembangan layanan TI yang andal dan aman dengan mengutamakan asas manfaat.',
                ],
                [
                    'number' => 17,
                    'description' => 'Pengembangan layanan TI dilakukan sesuai praktik terbaik dan mengacu pada Rencana Strategis TI.',
                ],
                [
                    'number' => 18,
                    'description' => 'Perusahaan wajib melakukan pendaftaran Penyelenggara Sistem Elektronik kepada kementerian atau lembaga terkait sesuai dengan ketentuan peraturan perundang-undangan.',
                ],
                [
                    'number' => 19,
                    'description' => 'Sistem elektronik Perusahaan diutamakan untuk ditempatkan pada pusat data dan pusat pemulihan bencana yang berada di Indonesia kecuali diatur lain oleh ketentuan peraturan perundang-undangan.',
                ],
                [
                    'number' => 20,
                    'description' => 'Perusahaan wajib memiliki rencana keberlangsungan layanan TI dan memastikan rencana tersebut dapat dilaksanakan, sehingga keberlangsungan operasional tetap berjalan saat terjadi bencana dan/atau gangguan pada sarana TI yang digunakan.',
                ],
                [
                    'number' => 21,
                    'description' => 'Perusahaan wajib melakukan uji coba dan evaluasi atas rencana keberlangsungan layanan TI terhadap sumber daya TI yang kritikal sesuai hasil analisis dampak bisnis dengan melibatkan pengguna TI paling sedikit 1 (satu) kali dalam 1 (satu) tahun.',
                ],
                [
                    'number' => 22,
                    'description' => 'Perusahaan wajib menjaga keamanan siber sesuai dengan prinsip utama keamanan informasi, yang meliputi kerahasiaan (confidentiality), keutuhan (integrity), dan ketersediaan (availability) serta ketentuan peraturan perundang-undangan yang mengatur mengenai keamanan siber.',
                ],
                [
                    'number' => 23,
                    'description' => 'Perusahaan wajib mengidentifikasi ancaman and kerentanan pada aset TI yang dimiliki dan menyusun rencana atau prosedur penanggulangan dan pemulihan insiden siber dengan mengacu pada praktik terbaik.',
                ],
                [
                    'number' => 24,
                    'description' => 'Perusahaan wajib mengelola data secara efektif untuk mendukung pencapaian tujuan bisnis sesuai dengan ketentuan peraturan perundangundangan dan praktik terbaik.',
                ],
                [
                    'number' => 25,
                    'description' => 'Pengelolaan data setidaknya memperhatikan aspek kepemilikan dan kepengurusan data, kualitas data, sistem pengelolaan data, dan sumber daya pendukung pengelolaan data.',
                ],
                [
                    'number' => 26,
                    'description' => 'Perusahaan wajib menyampaikan laporan penyelenggaraan TI yang menjadi satu kesatuan dalam laporan tahunan Perusahaan yang meliputi: a. tindak lanjut hasil audit dan/atau penilaian atas penyelenggaraan TI b. hasil evaluasi atas pelaksanaan Rencana Strategis TI c. hasil evaluasi atas efektivitas penyelenggaraan TI',
                ],
                [
                    'number' => 27,
                    'description' => 'Audit dan/atau penilaian penyelenggaraan TI dilakukan secara mandiri atau oleh pihak independen secara berkala 1(satu) kali dalam 1(satu) tahun. Periodisasi Audit dan/atau penilaian penyelenggaran TI dijelaskan dalam Lampiran 3.',
                ],
            ];

            foreach ($defaultPolicies as $policy) {
                MstGeneralPolicy::create($policy);
            }
        }

        $policies = MstGeneralPolicy::orderBy('number', 'asc')->get();
        } catch (\Exception $e) {
            // If the table doesn't exist on the active DB connection, render with empty data
            \Illuminate\Support\Facades\Log::warning('[GeneralPolicyController] DB error on active connection: ' . $e->getMessage());
            $policies = collect([]);
        }

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
