<template>
    <UserLayout title="Peran dan Tanggung Jawab">
        <div class="space-y-6 animate-fade-in-up print:m-0 print:p-0">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Peran & Tanggung Jawab</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Print Button -->
                        <button
                            @click="printDocument"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
                            </svg>
                            Cetak PDF
                        </button>

                        <!-- Go to Master Responsible CRUD page -->
                        <Link
                            :href="route('policy.responsible.manage')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H3.75A2.25 2.25 0 001.5 4.5v15a2.25 2.25 0 002.25 2.25h12.75a2.25 2.25 0 002.25-2.25V14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15.75h6m-6-4.5h6m-6-4.5h3.75" />
                            </svg>
                            Kelola Master Responsible
                        </Link>

                        <!-- Go to Management CRUD page -->
                        <Link
                            :href="route('policy.roles.manage')"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Kelola Peran & Tanggung Jawab
                        </Link>
                    </div>
                </div>
            </section>

            <!-- A4 Document Page Preview -->
            <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                
                <!-- Formal Pertamina Document Grid Header -->
                <div class="border border-slate-900 dark:border-white text-[11px] text-slate-950 dark:text-white font-sans uppercase z-10 relative mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-900 dark:divide-white">
                        <!-- Left Column (Fungsi & Judul) -->
                        <div class="md:col-span-7 flex flex-col divide-y divide-slate-900 dark:divide-white">
                            <div class="p-3 flex items-start gap-1.5 min-h-[46px]">
                                <span class="font-bold shrink-0">FUNGSI :</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.owner || 'ENTERPRISE IT – DIREKTORAT PENUNJANG BISNIS' }}</span>
                            </div>
                            <div class="p-3 flex items-start gap-1.5 min-h-[46px]">
                                <span class="font-bold shrink-0">JUDUL :</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.judul || 'TATA KELOLA TEKNOLOGI INFORMASI' }}</span>
                            </div>
                        </div>
                        <!-- Right Column (Metadata) -->
                        <div class="md:col-span-5 flex flex-col divide-y divide-slate-900 dark:divide-white">
                            <div class="p-2.5 flex items-center gap-1.5 min-h-[23px]">
                                <span class="font-bold shrink-0">NOMOR :</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ activeRegulation ? `REG-${activeRegulation.id.toString().padStart(4, '0')}` : '-' }}</span>
                            </div>
                            <div class="p-2.5 flex items-center min-h-[23px]">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold shrink-0">REVISI KE :</span>
                                    <template v-if="[0, 1, 2, 3, 4].includes(parseInt(activeRegulation?.revisi))">
                                        <span v-for="num in [0, 1, 2, 3, 4]" :key="num" class="inline-flex items-center gap-1 mr-1">
                                            <span class="w-3.5 h-3.5 border border-slate-900 dark:border-white flex items-center justify-center text-[10px] font-black bg-transparent select-none animate-none">
                                                {{ parseInt(activeRegulation?.revisi) === num ? '✓' : '' }}
                                            </span>
                                            <span class="font-mono">{{ num }}</span>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span class="w-3.5 h-3.5 border border-slate-900 dark:border-white flex items-center justify-center text-[10px] font-black bg-transparent select-none mr-1">✓</span>
                                        <span class="font-bold text-slate-900 dark:text-white mr-2">{{ activeRegulation?.revisi || '0' }}</span>
                                    </template>
                                </div>
                            </div>
                            <div class="p-2.5 flex items-center gap-1.5 min-h-[23px]">
                                <span class="font-bold shrink-0">BERLAKU TMT :</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.berlaku ? formatDate(activeRegulation.berlaku) : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Document Title Section -->
                <div class="mt-16 text-center space-y-2 relative z-10">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                        BAB III
                    </h2>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        TUGAS, KEBIJAKAN DAN TANGGUNG JAWAB
                    </h2>
                </div>

                <!-- Literal Document Content in Serif style for premium Word document look -->
                <div class="mt-8 space-y-8 text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 font-serif">
                    
                    <!-- Short Description Paragraph -->
                    <p class="text-justify font-serif text-[15px] leading-relaxed mb-6">
                        Tugas, kewenangan, dan tanggung jawab pada Pedoman ini merujuk pada peran yang dijelaskan pada kerangka COBIT 2019 sesuai dengan pemetaan peran dan tanggung jawab, serta diselaraskan dengan hasil pemetaan kebijakan Pedoman ini dengan pemetaan proses bisnis Perusahaan (SK 07/C00000/2021-08 tentang Model Proses Bisnis Pertamina Group).
                    </p>
                    
                    <div v-for="(role, idx) in roles" :key="role.id" class="space-y-3">
                        <!-- Role Title (A. Dewan Direksi (Board), B. Executive Committee, dsb) -->
                        <h4 class="text-[16px] font-bold text-slate-950 dark:text-white">
                            {{ getLetterNumbering(idx) }}. {{ role.name }}
                        </h4>

                        <!-- Role Description -->
                        <p v-if="role.description" class="text-slate-700 dark:text-slate-300 italic text-justify pl-4 font-sans text-[14px]">
                            {{ role.description }}
                        </p>

                        <!-- Responsibilities List -->
                        <div class="pl-4 space-y-1.5 mt-2">
                            <div 
                                v-for="(resp, respIdx) in role.responsibilities" 
                                :key="resp.id"
                                class="flex gap-3 items-start py-0"
                            >
                                <span class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right font-serif select-none">
                                    {{ respIdx + 1 }}.
                                </span>
                                <p class="text-justify font-serif text-[15px]">
                                    {{ resp.content }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="roles.length === 0" class="text-center text-slate-400 py-12 font-sans">
                        Belum ada data Peran & Tanggung Jawab. Silakan klik "Kelola Peran & Tanggung Jawab" di atas untuk menambah data.
                    </div>

                </div>

            </div>

            <!-- Print Floating Control Bar for Easy Navigation (print:hidden) -->
            <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-30 print:hidden">
                <button 
                    @click="scrollToTop"
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Kembali ke Atas"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                    </svg>
                </button>
                <Link 
                    :href="route('dashboard')" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Kembali ke Beranda"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </Link>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
});

const page = usePage();

// Selected Regulation state
const selectedRegulationId = ref(null);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || null;
});

// Format Date helper
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return dateString;
        return d.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

// Letter numbering utility for roles (A, B, C, ...)
function getLetterNumbering(index) {
    const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    return letters[index] || (index + 1).toString();
}

function printDocument() {
    window.print();
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>

<style scoped>
@media print {
    body {
        background-color: white !important;
        color: black !important;
    }
}
</style>
