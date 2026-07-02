<template>
    <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white px-5 py-3.5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>
 
                <div class="relative flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Kelola Dokumen</h2>
                    </div>
                    <!-- Chapter Switcher -->
                    <GuidanceChapterNavigation />
                </div>
            </section>

            <!-- A4 Document Page Preview -->
            <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                <!-- Formal Pertamina Document Grid Header -->
                <PertaminaDocumentHeader :activeRegulation="activeRegulation" class="flex-1" />

                <!-- Document Title Section -->
                <div class="mt-8 text-center space-y-2 relative z-10">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                        BAB V
                    </h2>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        PENUTUP
                    </h2>
                </div>

                <!-- Document Body -->
                <div class="mt-10 space-y-8 text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 relative z-10 font-serif text-justify">
                    
                    <!-- Ketentuan Peralihan -->
                    <div class="space-y-3">
                        <h3 class="text-[16px] font-bold text-slate-950 dark:text-white font-sans border-b border-slate-900/10 pb-2 dark:border-white/10">
                            1. Ketentuan Peralihan
                        </h3>
                        <p class="indent-8 text-justify font-serif">
                            Sejak ditetapkannya pedoman ini, seluruh ketentuan tata kelola dan operasional TI yang berlaku di lingkungan PT Pertamina (Persero), Subholding, dan Anak Perusahaan wajib menyesuaikan proses bisnisnya dengan pedoman ini dalam masa transisi paling lambat 6 (enam) bulan terhitung sejak tanggal pemberlakuan pedoman ini.
                        </p>
                        <p class="indent-8 text-justify font-serif">
                            Dokumen teknis berupa Standard Operating Procedure (SOP), Instruksi Kerja (IK), dan Ketentuan Pelaksana lainnya yang tidak bertentangan dengan prinsip-prinsip dasar pedoman ini dinyatakan tetap berlaku sampai dilakukannya revisi lanjutan atau penggantian dokumen terkait.
                        </p>
                    </div>

                    <!-- Pengawasan Kepatuhan -->
                    <div class="space-y-3">
                        <h3 class="text-[16px] font-bold text-slate-950 dark:text-white font-sans border-b border-slate-900/10 pb-2 dark:border-white/10">
                            2. Pengawasan Kepatuhan
                        </h3>
                        <p class="indent-8 text-justify font-serif">
                            Fungsi Enterprise IT Korporat bersama dengan Audit Internal bertanggung jawab untuk secara berkala mengawasi, mengevaluasi, dan mengaudit kepatuhan (compliance) implementasi butir-butir kebijakan dalam pedoman ini di seluruh tingkatan organisasi. Hasil audit kepatuhan tata kelola TI akan dilaporkan secara langsung kepada Direksi dan menjadi salah satu indikator evaluasi kinerja strategis teknologi informasi Perusahaan.
                        </p>
                    </div>

                    <!-- Ketentuan Penutup -->
                    <div class="space-y-3">
                        <h3 class="text-[16px] font-bold text-slate-950 dark:text-white font-sans border-b border-slate-900/10 pb-2 dark:border-white/10">
                            3. Ketentuan Penutup
                        </h3>
                        <p class="indent-8 text-justify font-serif">
                            Pedoman Tata Kelola Teknologi Informasi ini akan ditinjau secara periodik minimal setiap 2 (dua) tahun sekali atau sewaktu-waktu sesuai kebutuhan strategis perusahaan serta perkembangan regulasi eksternal pemerintah. Perubahan dan penyempurnaan atas pedoman ini harus disetujui dan disahkan secara formal oleh Dewan Direksi PT Pertamina (Persero).
                        </p>
                    </div>

                </div>
            </div>
        
        <!-- Floating Action Buttons -->
        <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-4 print:hidden">
            <!-- Print Button -->
            <button @click="printDocument" title="Cetak PDF"
                class="group flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white/40 shadow-2xl backdrop-blur-md transition-all hover:bg-white hover:text-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-[#1a1a1a] active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import PertaminaDocumentHeader from '@/Components/modules/ITOM/Regulation/PertaminaDocumentHeader.vue';
import GuidanceChapterNavigation from '@/Components/modules/ITOM/Regulation/GuidanceChapterNavigation.vue';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
});

const selectedRegulationId = ref(props.selectedRegulationId);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || null;
});

function printDocument() {
    window.print();
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
