<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = 'mst_general_policy';

        // Clear existing data
        DB::table($table)->truncate();

        $defaultPolicies = [
            [
                'number' => 1,
                'description' => 'Perusahaan wajib menerapkan tata kelola Teknologi Informasi (TI) berdasarkan kebijakan tata kelola TI yang berlaku.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 2,
                'description' => 'Prinsip tata kelola TI setidaknya mencakup prinsip manajemen, data & informasi, teknologi, dan keamanan TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 3,
                'description' => 'Kebijakan TI memperhatikan aspek keselarasan strategi, nilai tambah penerapan TI, manajemen risiko, manajemen sumber daya, dan pengukuran kinerja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 4,
                'description' => 'Kebijakan tata kelola TI dilakukan evaluasi secara berkala.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 5,
                'description' => 'Perusahaan perlu menyusun rencana strategis TI sesuai periode Rencana Jangka Panjang (RJP) dan diimplementasikan dalam rencana tahunan yang menjadi bagian dari RKAP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 6,
                'description' => 'Rencana startegis TI paling sedikit memuat peran TI terhadap pengembangan bisnis termasuk transformasi digital, organisasi TI, rencana pembiayaan TI, dan peta jalan TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 7,
                'description' => 'Rencana strategis TI ditetapkan oleh Direksi dan disampaikan kepada RUPS sesuai dengan periode waktu penyampaian RJP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 8,
                'description' => 'Dewan Komisaris melakukan evaluasi, mengarahkan, dan memantau rencana strategis TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 9,
                'description' => 'Rencana strategis TI dapat diubah jika terjadi kondisi yang signifikan mempengaruhi sasaran dan strategi TI antara lain perubahan RJP, perkembangan TI, atau perubahan perundang-undangan mengenai penyelenggaraan TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 10,
                'description' => 'Perubahan rencana strategis TI dapat dilakukan 1(satu) kali dalam 1(satu) tahun dan disampaikan kepada RUPS/Kementrian BUMN.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 11,
                'description' => 'Dalam rangka menyelenggarakan TI, Direksi menetapkan arsitektur TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 12,
                'description' => 'Arsitektur TI dapat menjadi bagian atau dokumen yang terpisahkan dari Rencana Strategis TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 13,
                'description' => 'Penyusunan Arsitektur TI paling sedikit mempertimbangkan aspek proses bisnis, data dan informasi, serta teknologi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 14,
                'description' => 'Dalam hal terjadi perubahan aspek, Perusahaan wajib melakukan pemutakhiran terhadap arsitektur TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 15,
                'description' => 'Direksi membentuk Komite Pengarah TI beranggotakan paling sedikit Direktur yang membidangi TI dan Direktur yang membidangi Manajemen Risiko yang bertugas: a. memastikan keselarasan Rencana Strategis TI dengan RJP b. memastikan implementasi Rencana Strategis TI yang dituangkan dalam RKAP c. mengevaluasi, mengarahkan, dan memantau penyelenggaraan TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 16,
                'description' => 'Perusahaan menerapkan pengembangan layanan TI yang andal dan aman dengan mengutamakan asas manfaat.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 17,
                'description' => 'Pengembangan layanan TI dilakukan sesuai praktik terbaik dan mengacu pada Rencana Strategis TI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 18,
                'description' => 'Perusahaan wajib melakukan pendaftaran Penyelenggara Sistem Elektronik kepada kementerian atau lembaga terkait sesuai dengan ketentuan peraturan perundang-undangan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 19,
                'description' => 'Sistem elektronik Perusahaan diutamakan untuk ditempatkan pada pusat data dan pusat pemulihan bencana yang berada di Indonesia kecuali diatur lain oleh ketentuan peraturan perundang-undangan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 20,
                'description' => 'Perusahaan wajib memiliki rencana keberlangsungan layanan TI and memastikan rencana tersebut dapat dilaksanakan, sehingga keberlangsungan operasional tetap berjalan saat terjadi bencana dan/atau gangguan pada sarana TI yang digunakan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 21,
                'description' => 'Perusahaan wajib melakukan uji coba dan evaluasi atas rencana keberlangsungan layanan TI terhadap sumber daya TI yang kritikal sesuai hasil analisis dampak bisnis dengan melibatkan pengguna TI paling sedikit 1 (satu) kali dalam 1 (satu) tahun.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 22,
                'description' => 'Perusahaan wajib menjaga keamanan siber sesuai dengan prinsip utama keamanan informasi, yang meliputi kerahasiaan (confidentiality), keutuhan (integrity), dan ketersediaan (availability) serta ketentuan peraturan perundang-undangan yang mengatur mengenai keamanan siber.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 23,
                'description' => 'Perusahaan wajib mengidentifikasi ancaman dan kerentanan pada aset TI yang dimiliki dan menyusun rencana atau prosedur penanggulangan dan pemulihan insiden siber dengan mengacu pada praktik terbaik.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 24,
                'description' => 'Perusahaan wajib mengelola data secara efektif untuk mendukung pencapaian tujuan bisnis sesuai dengan ketentuan peraturan perundangundangan dan praktik terbaik.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 25,
                'description' => 'Pengelolaan data setidaknya memperhatikan aspek kepemilikan dan kepengurusan data, kualitas data, sistem pengelolaan data, dan sumber daya pendukung pengelolaan data.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 26,
                'description' => 'Perusahaan wajib menyampaikan laporan penyelenggaraan TI yang menjadi satu kesatuan dalam laporan tahunan Perusahaan yang meliputi: a. tindak lanjut hasil audit dan/atau penilaian atas penyelenggaraan TI b. hasil evaluasi atas pelaksanaan Rencana Strategis TI c. hasil evaluasi atas efektivitas penyelenggaraan TI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'number' => 27,
                'description' => 'Audit dan/atau penilaian penyelenggaraan TI dilakukan secara mandiri atau oleh pihak independen secara berkala 1(satu) kali dalam 1(satu) tahun. Periodisasi Audit dan/atau penilaian penyelenggaran TI dijelaskan dalam Lampiran 3.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table($table)->insert($defaultPolicies);
    }
}
