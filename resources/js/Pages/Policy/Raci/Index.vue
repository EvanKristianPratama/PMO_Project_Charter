<template>
    <UserLayout title="RACI Analisis">
        <div class="space-y-6 animate-fade-in-up print:m-0 print:p-0">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">RACI Analisis</h1>
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
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H3.75A2.25 2.25 0 001.5 4.5v15a2.25 2.25 0 002.25 2.25h12.75a2.25 2.25 0 002.25-2.25V14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15.75h6m-6-4.5h6m-6-4.5h3.75" />
                            </svg>
                            Kelola Master Responsible
                        </Link>

                        <!-- Go to Management Mapping page -->
                        <Link
                            :href="route('policy.raci.manage')"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Kelola Pemetaan RACI
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 print:hidden">
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Filter GAMO</label>
                    <select v-model="selectedGamo" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 shadow-sm transition focus:border-[#821f44] focus:ring-2 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white">
                        <option value="">Semua GAMO</option>
                        <option v-for="obj in objectives" :key="obj.objective_id" :value="obj.objective_id">
                            {{ obj.objective_id }} - {{ obj.objective }}
                        </option>
                    </select>
                </div>
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Filter Role</label>
                    <select v-model="selectedRole" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 shadow-sm transition focus:border-[#821f44] focus:ring-2 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white">
                        <option value="">Semua Role</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- A4 Document Page Preview (Modified to fit wider table) -->
            <div class="w-full bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-4 sm:p-6 md:p-8 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0 overflow-visible">
                
                <!-- Document Title Section -->
                <div class="text-center space-y-1.5 relative z-10 py-4 mb-2">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#821f44] dark:text-[#a83262] uppercase">
                        RACI Analisis
                    </h2>
                </div>

                <!-- RACI Legend Key Guide -->
                <div class="mt-8 flex flex-wrap gap-4 items-center justify-center bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-4 rounded-xl print:hidden">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Keterangan RACI:</div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-rose-50 text-[11px] font-black text-rose-600 border border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/30 shadow-sm">A</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Accountable</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-[11px] font-black text-emerald-600 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/30 shadow-sm">R</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Responsible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-[11px] font-black text-amber-600 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/30 shadow-sm">C</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Consulted</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-indigo-50 text-[11px] font-black text-indigo-600 border border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-400 dark:border-indigo-900/30 shadow-sm">I</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Informed</span>
                    </div>
                </div>

                <!-- RACI Matrix Table container -->
                <div class="mt-8 overflow-x-auto border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full">
                    <table class="min-w-full border-collapse text-center">
                        <!-- Headings -->
                        <thead>
                            <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246]">
                                <!-- Left Sticky Header space -->
                                <th class="sticky left-0 z-20 bg-[#d2e4df] dark:bg-[#1b3a32] p-2 text-left w-[180px] min-w-[180px] max-w-[180px] border-r border-[#b2cfc7] dark:border-[#255246] select-none">
                                    <div class="text-[9.5px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">Governance Processes</div>
                                </th>
                                <!-- Role Headers rotated vertically -->
                                <th 
                                    v-for="role in filteredRoles" 
                                    :key="role.id" 
                                    class="p-0.5 text-center w-[18px] min-w-[18px] max-w-[18px] align-bottom pb-2 select-none"
                                    :title="role.name"
                                >
                                    <div class="inline-flex items-center justify-center h-36 w-[14px] mx-auto">
                                        <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <!-- Body grouped by objective -->
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                            <template v-for="obj in filteredObjectives" :key="obj.objective_id">
                                <!-- Objective Header Row -->
                                <tr class="bg-slate-100 dark:bg-[#202020] border-y border-slate-300 dark:border-white/10">
                                    <td 
                                        :colspan="filteredRoles.length + 1" 
                                        class="sticky left-0 p-1.5 text-left font-bold text-[9px] text-slate-900 dark:text-white tracking-wide uppercase select-none"
                                    >
                                        {{ obj.objective_id }}: {{ obj.objective }}
                                    </td>
                                </tr>

                                <!-- Practices Rows -->
                                <tr 
                                    v-for="pr in obj.practices" 
                                    :key="pr.practice_id" 
                                    class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5"
                                >
                                    <!-- Practice details (sticky) -->
                                    <td class="sticky left-0 z-10 bg-white dark:bg-[#1a1a1a] border-r border-slate-200 dark:border-white/10 p-1.5 text-left shadow-[2px_0_5px_rgba(0,0,0,0.02)] select-none">
                                        <div class="flex items-start gap-1">
                                            <span class="text-[9px] font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ pr.practice_id }}</span>
                                            <span class="text-[9px] font-bold text-slate-900 dark:text-white" :title="pr.practice_name">
                                                {{ pr.practice_name || pr.practice_description || '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- RACI Cell Values -->
                                    <td 
                                        v-for="role in filteredRoles" 
                                        :key="role.id" 
                                        class="p-0 text-center w-[18px] min-w-[18px] max-w-[18px] align-middle h-[20px]"
                                    >
                                        <template v-if="getRaciValue(pr.practice_id, role.id)">
                                               <span 
                                                   v-if="getRaciValue(pr.practice_id, role.id).toUpperCase() === 'I'"
                                                   class="inline-block w-[2.5px] h-[10px] bg-[#1e293b] dark:bg-slate-200 rounded-[0.5px] align-middle"
                                                   :title="`${pr.practice_id} - ${role.name}: Informed`"
                                               ></span>
                                            <span 
                                                v-else
                                                :class="getRaciTextClass(getRaciValue(pr.practice_id, role.id))"
                                                :title="`${pr.practice_id} - ${role.name}: ${getRaciLabel(getRaciValue(pr.practice_id, role.id))}`"
                                            >
                                                {{ getRaciValue(pr.practice_id, role.id) }}
                                            </span>
                                        </template>
                                    </td>
                                </tr>

                                <!-- Empty practices warning if any -->
                                <tr v-if="!obj.practices || obj.practices.length === 0">
                                    <td 
                                        :colspan="filteredRoles.length + 1" 
                                        class="p-2 text-center text-[9px] italic text-slate-400 dark:text-slate-500"
                                    >
                                        Belum ada practices untuk Objective ini.
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="filteredObjectives.length === 0">
                                <td 
                                    :colspan="filteredRoles.length + 1" 
                                    class="p-6 text-center text-xs font-sans text-slate-400 dark:text-slate-500"
                                >
                                    Belum ada data Governance Objective / Practices.
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
    objectives: {
        type: Array,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    mappings: {
        type: Array,
        required: true,
    },
});

const selectedGamo = ref('');
const selectedRole = ref('');

const filteredRoles = computed(() => {
    if (!selectedRole.value) return props.roles;
    return props.roles.filter(r => String(r.id) === String(selectedRole.value));
});

const filteredObjectives = computed(() => {
    if (!selectedGamo.value) return props.objectives;
    return props.objectives.filter(o => String(o.objective_id) === String(selectedGamo.value));
});

const raciLookup = computed(() => {
    const entries = props.mappings.map((mapping) => [
        `${String(mapping.practice_id)}::${String(mapping.role_id)}`,
        mapping.r_a || '',
    ]);

    return new Map(entries);
});

// Get RACI value for a specific cell
function getRaciValue(practiceId, roleId) {
    return raciLookup.value.get(`${String(practiceId)}::${String(roleId)}`) || '';
}

// Get descriptive tooltip label
function getRaciLabel(val) {
    const labels = {
        'A': 'Accountable',
        'R': 'Responsible',
        'C': 'Consulted',
        'I': 'Informed',
    };
    return labels[val.toUpperCase()] || '';
}

// Return beautifully stylized color class for RACI letters
function getRaciTextClass(val) {
    const cleanVal = val.toUpperCase();
    if (cleanVal === 'A') {
        return "text-[#d9383a] dark:text-[#f87171] font-black text-[10.5px] select-none";
    }
    if (cleanVal === 'R') {
        return "text-[#2d9a68] dark:text-[#34d399] font-black text-[10.5px] select-none";
    }
    if (cleanVal === 'C') {
        return "text-[#dd7d36] dark:text-[#fbbf24] font-black text-[10.5px] select-none";
    }
    return "text-slate-800 dark:text-slate-200 font-bold text-[10px] select-none";
}

function getCurrentDate() {
    return new Date().toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
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
/* Custom Webkit scrollbar for premium touch */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
    background-color: transparent;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: rgba(130, 31, 68, 0.2);
    border-radius: 99px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(130, 31, 68, 0.4);
}

@media print {
    body {
        background-color: white !important;
        color: black !important;
    }
    /* Hide scrollbars during print */
    .overflow-x-auto {
        overflow-x: visible !important;
    }
    /* Keep sticky columns standard on print */
    .sticky {
        position: static !important;
        box-shadow: none !important;
    }
}
</style>
