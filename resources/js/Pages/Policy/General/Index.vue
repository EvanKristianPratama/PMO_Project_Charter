<template>
    <UserLayout title="Dokumen Kebijakan Umum">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Dokumen</h1>
                    </div>
                    <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <!-- Document Type Switch Menu (Horizontal) -->
                        <nav class="flex shrink-0 flex-row gap-1 rounded-xl bg-slate-100 p-1 dark:bg-white/5">
                            <Link :href="route('policy.general.index')"
                                class="flex items-center justify-center rounded-lg px-6 py-2 text-center text-xs font-bold transition-all"
                                :class="route().current('policy.general.index') ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1a1a1a] dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                                Kebijakan Umum
                            </Link>
                            <Link :href="route('policy.specific.index')"
                                class="flex items-center justify-center rounded-lg px-6 py-2 text-center text-xs font-bold transition-all"
                                :class="route().current('policy.specific.index') ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1a1a1a] dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                                Kebijakan Khusus
                            </Link>
                        </nav>
                    </div>
                </div>

            </section>


            <!-- A4 Document Page Preview -->
            <div
                class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                <!-- Formal Pertamina Document Grid Header -->
                <PertaminaDocumentHeader :activeRegulation="activeRegulation" class="flex-1" />

                <!-- 2. Document Title Section -->
                <div class="mt-8 text-center space-y-2 relative z-10">
                    <h2
                        class="text-lg sm:text-xl font-extrabold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                        BAB II
                    </h2>
                    <h2
                        class="text-xl sm:text-2xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        KEBIJAKAN
                    </h2>
                </div>

                <!-- 3. General Policy Subtitle -->
                <div class="mt-10 relative z-10">
                    <h3
                        class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                        A. Kebijakan Umum
                    </h3>
                </div>

                <!-- 4. Document Body List of Policies -->
                <div
                    class="mt-8 space-y-1.5 text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 relative z-10 font-serif">
                    <div v-for="policy in policies" :key="policy.id" class="flex gap-3 items-start pl-2 py-0">
                        <span
                            class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right font-serif select-none">
                            {{ policy.number }}.
                        </span>
                        <p class="text-justify whitespace-pre-line font-serif">
                            {{ formatDescription(policy.description) }}
                        </p>
                    </div>

                    <!-- Empty State -->
                    <div v-if="policies.length === 0"
                        class="text-center py-20 text-slate-400 dark:text-slate-500 border border-dashed border-slate-300 rounded-xl">
                        Policy Not Available
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Action Buttons (Fixed Bottom Right) -->
        <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-4 print:hidden">
            <!-- Print Button -->
            <button @click="printDocument" title="Cetak PDF"
                class="group flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white/90 shadow-2xl backdrop-blur-md transition-all hover:bg-white hover:text-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a]/90 dark:text-slate-300 dark:hover:bg-[#1a1a1a] active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
                </svg>
            </button>

            <!-- Go to Management CRUD page -->
            <Link :href="route('policy.general.manage')" title="Kelola Kebijakan"
                class="group flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-2xl shadow-[#821f44]/30 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </Link>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';

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
