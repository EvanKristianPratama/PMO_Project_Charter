<template>
    <UserLayout title="Dokumen Kebijakan Umum">
        <div class="animate-fade-in-up space-y-6">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pratinjau Dokumen Kebijakan</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kebijakan Umum</h1>
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
            <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                


                <!-- Clean Word-Style Header Info -->
                <div class="border-b-2 border-slate-900 pb-6 dark:border-white text-slate-900 dark:text-white relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                        <div class="space-y-1">
                            <span class="text-xs font-bold uppercase tracking-[0.15em] text-[#821f44] dark:text-[#a83262]">
                                {{ activeRegulation?.owner || 'ENTERPRISE IT - DIREKTORAT PENUNJANG BISNIS' }}
                            </span>
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-950 dark:text-white uppercase leading-snug mt-1">
                                {{ activeRegulation?.judul || 'TATA KELOLA TEKNOLOGI INFORMASI' }}
                            </h1>
                        </div>
                        <div class="grid grid-cols-2 md:flex md:flex-col gap-x-6 gap-y-1 text-xs font-bold text-slate-500 dark:text-slate-400">
                            <div>
                                <span class="uppercase tracking-wider mr-1.5 text-slate-400 dark:text-slate-500">Nomor:</span>
                                <span class="font-mono text-slate-900 dark:text-white">{{ activeRegulation ? `REG-${activeRegulation.id.toString().padStart(4, '0')}` : '-' }}</span>
                            </div>
                            <div>
                                <span class="uppercase tracking-wider mr-1.5 text-slate-400 dark:text-slate-500">Revisi:</span>
                                <span class="text-slate-900 dark:text-white">{{ activeRegulation?.revisi || '0' }}</span>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <span class="uppercase tracking-wider mr-1.5 text-slate-400 dark:text-slate-500">Berlaku:</span>
                                <span class="text-slate-900 dark:text-white">{{ activeRegulation?.berlaku ? formatDate(activeRegulation.berlaku) : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Document Title Section -->
                <div class="mt-16 text-center space-y-2 relative z-10">
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
                <div class="mt-8 space-y-6 text-sm sm:text-base leading-relaxed text-slate-800 dark:text-slate-200 relative z-10 font-sans">
                    <div 
                        v-for="policy in policies" 
                        :key="policy.id" 
                        class="flex gap-4 items-start pl-2 transition-all hover:bg-slate-50/50 dark:hover:bg-white/5 py-2 rounded-lg"
                    >
                        <span class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right font-mono select-none">
                            {{ policy.number }}.
                        </span>
                        <p class="font-medium text-slate-850 dark:text-slate-200">
                            {{ policy.description }}
                        </p>
                    </div>

                    <!-- Empty State -->
                    <div 
                        v-if="policies.length === 0" 
                        class="text-center py-20 text-slate-400 dark:text-slate-500 border border-dashed border-slate-300 rounded-xl"
                    >
                        Belum ada butir Kebijakan Umum. Silakan klik "Kelola Kebijakan" di bagian atas untuk menambahkan data.
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
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
});

const page = usePage();

// Selected Regulation state
const selectedRegulationId = ref(null);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        // Fall back to first regulation if available, otherwise null
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
