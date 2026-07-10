<template>
    <div class="animate-fade-in-up space-y-6">

            <!-- A4 Document Page Preview -->
            <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-4 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                <!-- Formal Pertamina Document Grid Header -->
                <PertaminaDocumentHeader v-if="isHeaderVisible" :activeRegulation="activeRegulation" class="flex-1" />

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
        
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import PertaminaDocumentHeader from '@/Components/modules/ITOM/Regulation/PertaminaDocumentHeader.vue';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
    isHeaderVisible: {
        type: Boolean,
        default: true,
    },
});

const selectedRegulationId = ref(props.selectedRegulationId);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || null;
});
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
