<template>
    <ModulLayout title="Dokumen Kebijakan Umum">
        <div class="animate-fade-in-up space-y-6">
            <!-- Navigation Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
                <!-- Back Button -->
                <Link
                    :href="route('itom.policy.regulation.index')"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </Link>

                <!-- Fast Document Switcher -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilih Dokumen:</span>
                    <div class="relative">
                        <select
                            :value="selectedRegulationId"
                            @change="handleFastDocumentSwitch($event.target.value)"
                            class="appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-8 py-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[240px] max-w-[320px] truncate"
                        >
                            <option
                                v-for="reg in regulations"
                                :key="reg.id"
                                :value="reg.id"
                            >
                                [{{ reg.tipe }}] {{ reg.judul }}
                            </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Dokumen</h1>
                    </div>
                    <!-- Guidance Chapter Navigation -->
                    <GuidanceChapterNavigation />
                </div>

            </section>


            <!-- A4 Document Page Preview -->
            <div
                class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative text-justify print:shadow-none print:border-none print:p-0 print:m-0"
                style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #1e293b;">
                <!-- Formal Pertamina Document Grid Header -->
                <PertaminaDocumentHeader :activeRegulation="activeRegulation" class="flex-1" />

                <!-- 2. Document Title Section -->
                <div class="mt-8 text-center space-y-2 relative z-10">
                    <h2
                        class="text-lg sm:text-xl font-bold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                    </h2>
                    <h2
                        class="text-xl sm:text-2xl font-bold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        KEBIJAKAN
                    </h2>
                </div>

                <!-- 3. General Policy Subtitle -->
                <div class="mt-8 relative z-10">
                    <h3
                        class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                        A. Kebijakan Umum
                    </h3>
                </div>

                <!-- 4. Document Body List of Policies -->
                <div
                    class="mt-4 space-y-4 relative z-10">
                    <div v-for="policy in policies" :key="policy.id" class="flex gap-3 items-start pl-2 py-0">
                        <span
                            class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right select-none">
                            {{ policy.number }}.
                        </span>
                        <p class="text-justify whitespace-pre-line m-0 p-0">
                            {{ formatDescription(policy.description) }}
                        </p>
                    </div>

                    <!-- Empty State -->
                    <div v-if="policies.length === 0"
                        class="text-center py-20 text-slate-400 dark:text-slate-500 border border-dashed border-slate-300 rounded-xl">
                        Not Available
                    </div>
                </div>

                <div class="my-12"></div>

                <!-- 5. Specific Policy Subtitle -->
                <div class="mt-8 relative z-10">
                    <h3
                        class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                        B. Kebijakan Khusus
                    </h3>
                </div>

                <!-- Literal Document Content in Serif style for premium Word document look -->
                <div class="mt-6 space-y-6 relative z-10 font-serif">

                    <!-- Loop through Domains -->
                    <div v-for="(objectivesInDomain, domainName, domainIdx) in groupedObjectives" :key="domainName"
                        class="space-y-4">
                        <!-- Domain Heading (e.g. 1. Evaluasi, Arahkan, dan Pantau) -->
                        <div class="space-y-0">
                            <h3 class="font-bold text-slate-950 dark:text-white">
                                {{ domainIdx + 1 }}. {{ cleanDomainTitle(domainName) }}
                            </h3>
                            <!-- Domain Description -->
                            <p
                                class="text-slate-700 dark:text-slate-300 italic text-justify pl-6 m-0 p-0">
                                {{ getDomainDescriptionByObjectives(objectivesInDomain) }}
                            </p>
                        </div>

                        <!-- Objectives under this Domain -->
                        <div v-for="(obj, objIdx) in objectivesInDomain" :key="obj.objective_id" class="pl-6 space-y-4">
                            <!-- Objective Heading (e.g. 1) Memastikan Pengaturan dan Pemeliharaan Kerangka Tata Kelola I&T) -->
                            <h4 class="font-bold text-slate-950 dark:text-white m-0 p-0">
                                {{ objIdx + 1 }}) {{ obj.objective }}
                            </h4>

                            <!-- Description / Deskripsi -->
                            <div v-if="obj.objective_description" class="pl-4 space-y-0 mt-2">
                                <span class="font-bold text-slate-950 dark:text-white block">a. Deskripsi</span>
                                <div v-if="getListItems(obj.objective_description).length === 1"
                                    class="text-justify mt-0 m-0 p-0">
                                    {{ getListItems(obj.objective_description)[0] }}
                                </div>
                                <ol v-else class="list-none mt-0 space-y-0 m-0 p-0">
                                    <li v-for="(item, itemIdx) in getListItems(obj.objective_description)"
                                        :key="itemIdx" class="flex gap-2 text-justify">
                                        <span class="shrink-0">{{ getLetterNumbering(itemIdx) }})</span>
                                        <span>{{ item }}</span>
                                    </li>
                                </ol>
                            </div>

                            <!-- Purpose / Tujuan -->
                            <div v-if="obj.objective_purpose" class="pl-4 space-y-0 mt-4">
                                <span class="font-bold text-slate-950 dark:text-white block">b. Tujuan</span>
                                <div v-if="getListItems(obj.objective_purpose).length === 1"
                                    class="text-justify mt-0 m-0 p-0">
                                    {{ getListItems(obj.objective_purpose)[0] }}
                                </div>
                                <ol v-else class="list-none mt-0 space-y-0 m-0 p-0">
                                    <li v-for="(item, itemIdx) in getListItems(obj.objective_purpose)" :key="itemIdx"
                                        class="flex gap-2 text-justify">
                                        <span class="shrink-0">{{ getLetterNumbering(itemIdx) }})</span>
                                        <span>{{ item }}</span>
                                    </li>
                                </ol>
                            </div>

                            <!-- Butir Kebijakan / Practices -->
                            <div v-if="obj.practices && obj.practices.length > 0" class="pl-4 space-y-0 mt-4">
                                <span class="font-bold text-slate-950 dark:text-white block">c. Butir
                                    Kebijakan</span>
                                <ol class="list-none mt-0 space-y-0 m-0 p-0">
                                    <li v-for="(prac, pracIdx) in obj.practices" :key="prac.practice_id"
                                        class="flex gap-2 text-justify">
                                        <span class="shrink-0">{{ pracIdx + 1 }})</span>
                                        <span>
                                            {{ prac.practice_name }}
                                            <span v-if="prac.practice_description"> — {{ prac.practice_description
                                                }}</span>
                                        </span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="objectives.length === 0"
                        class="text-center py-20 text-slate-400 dark:text-slate-500 border border-dashed border-slate-300 rounded-xl font-sans text-sm">
                        Not Available
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Action Buttons (Fixed Bottom Right) -->
        <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-4 print:hidden">
            <!-- Scroll to Top Button -->
            <button @click="scrollToTop" title="Kembali ke Atas"
                class="group flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white/40 shadow-2xl backdrop-blur-md transition-all hover:bg-white hover:text-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-[#1a1a1a] active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                </svg>
            </button>

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
            </button>            <!-- Go to Management CRUD page -->
            <Link :href="route('itom.policy.general.manage', { regulation_id: selectedRegulationId })" title="Kelola Kebijakan"
                class="group flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44]/80 text-white shadow-2xl shadow-[#821f44]/30 backdrop-blur-md transition-all hover:bg-[#821f44] hover:shadow-[#821f44]/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </Link>

            <!-- Go to Specific Policy CRUD page -->
            <Link :href="route('itom.policy.specific.manage', { regulation_id: selectedRegulationId })" title="Kelola Kebijakan Khusus"
                class="group flex h-12 w-12 items-center justify-center rounded-full bg-amber-500/80 text-[#821f44] shadow-2xl shadow-yellow-500/30 backdrop-blur-md transition-all hover:bg-amber-50 hover:shadow-yellow-500/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </Link>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import PertaminaDocumentHeader from '@/Components/modules/ITOM/Regulation/PertaminaDocumentHeader.vue';
import GuidanceChapterNavigation from '@/Components/modules/ITOM/Regulation/GuidanceChapterNavigation.vue';

const props = defineProps({
    policies: {
        type: Array,
        required: true,
    },
    objectives: {
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

function handleFastDocumentSwitch(regId) {
    const selectedReg = props.regulations.find(r => r.id === Number(regId));
    if (!selectedReg) return;

    const targetRoute = String(selectedReg.tipe || '').toLowerCase() === 'procedure'
        ? 'policy.procedure.index'
        : 'policy.general.index';

    router.visit(route(targetRoute, { regulation_id: regId }));
}

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || null;
});

const groupedObjectives = computed(() => {
    const groups = {};
    props.objectives.forEach(obj => {
        const domain = obj.domain || 'Domain Lainnya';
        if (!groups[domain]) {
            groups[domain] = [];
        }
        groups[domain].push(obj);
    });
    return groups;
});

function cleanDomainTitle(domain) {
    if (!domain) return '';
    return domain.replace(/\s*\(.*\)\s*$/, '').trim();
}

const domainDescriptions = {
    'EDM': 'Kebijakan ini berkaitan dengan aktivitas mengevaluasi opsi strategis, mengarahkan manajemen puncak pada opsi strategis yang dipilih dan memantau pencapaian strategi.',
    'APO': 'Kebijakan ini berkaitan dengan aktivitas menyelaraskan strategi TI dengan strategi bisnis, merencanakan arah pengembangan TI, dan mengatur organisasi serta sumber daya pendukung.',
    'BAI': 'Kebijakan ini berkaitan dengan aktivitas membangun, mengakuisisi, menerapkan solusi teknologi informasi secara aman dan efektif, serta mengintegrasikan solusi tersebut ke dalam proses bisnis.',
    'DSS': 'Kebijakan ini berkaitan dengan aktivitas pemberian layanan TI, operasional harian, pengelolaan keamanan dan kelangsungan layanan, serta penyediaan dukungan bagi pengguna.',
    'MEA': 'Kebijakan ini berkaitan dengan aktivitas memantau, mengevaluasi, dan mengukur kinerja serta kepatuhan tata kelola TI perusahaan terhadap standar internal maupun eksternal.'
};

function getDomainDescriptionByObjectives(objectivesList) {
    if (!objectivesList || objectivesList.length === 0) return '';
    const firstId = objectivesList[0].objective_id;
    if (firstId.startsWith('EDM')) return domainDescriptions['EDM'];
    if (firstId.startsWith('APO')) return domainDescriptions['APO'];
    if (firstId.startsWith('BAI')) return domainDescriptions['BAI'];
    if (firstId.startsWith('DSS')) return domainDescriptions['DSS'];
    if (firstId.startsWith('MEA')) return domainDescriptions['MEA'];
    return '';
}

function getListItems(text) {
    if (!text) return [];
    return text.split('\n')
        .map(line => line.trim())
        .filter(line => line.length > 0)
        .map(line => {
            // strip leading numbering or bullet markers like a), b), 1., etc.
            return line.replace(/^([a-z0-9A-Z]+[\)\.]|[\-\*])\s*/i, '');
        });
}

function getLetterNumbering(index) {
    const letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    return letters[index] || (index + 1).toString();
}


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

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
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
