<template>
    <UserLayout title="Dokumen Kebijakan Umum">
        <div class="animate-fade-in-up space-y-6">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Formal Pertamina Document Grid Header -->
                <div class="border border-slate-900 dark:border-white text-[11px] text-slate-950 dark:text-white font-sans uppercase z-10 relative">
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
                    <div class="flex flex-wrap items-center gap-3">

                        <!-- Print Button -->
                        <button
                            @click="printDocument"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
                            </svg>
                            Cetak PDF
                        </button>

                        <!-- Go to Management CRUD page -->
                        <Link
                            :href="route('policy.general.manage')"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Kelola Kebijakan
                        </Link>
                    </div>
                </div>
            </section>

            <!-- A4 Document Page Preview -->
            <div class=" mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                <!-- 2. Document Title Section -->
                <div class="text-center space-y-2 relative z-10">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                        BAB II
                    </h2>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        KEBIJAKAN
                    </h2>
                </div>

                <!-- 3. General Policy Subtitle -->
                <div class="mt-10 relative z-10">
                    <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                        A. Kebijakan Umum
                    </h3>
                </div>

                <!-- 4. Document Body List of Policies -->
                <div class="mt-8 space-y-1.5 text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 relative z-10 font-serif">
                    <div 
                        v-for="policy in policies" 
                        :key="policy.id" 
                        class="flex gap-3 items-start pl-2 py-0"
                    >
                        <span class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right font-serif select-none">
                            {{ policy.number }}.
                        </span>
                        <p class="text-justify whitespace-pre-line font-serif">
                            {{ formatDescription(policy.description) }}
                        </p>
                    </div>

                    <!-- Empty State -->
                    <div 
                        v-if="policies.length === 0" 
                        class="text-center py-20 text-slate-400 dark:text-slate-500 border border-dashed border-slate-300 rounded-xl"
                    >
                        Policy Not Available
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    policies: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
});

// Selected Regulation state
const selectedRegulationId = ref(props.selectedRegulationId);

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

// Format Description helper to auto-break and indent sub-points like a., b., c.
function formatDescription(text) {
    if (!text) return '';
    return text.replace(/\s+([a-z])([\.\)])\s+/g, '\n   $1$2 ');
}

// Print Handler
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
