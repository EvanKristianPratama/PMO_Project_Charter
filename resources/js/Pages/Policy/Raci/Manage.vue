<template>
    <UserLayout title="Kelola Matriks RACI">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Matriks RACI COBIT</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('policy.raci.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke Dokumen
                        </Link>
                        <button
                            @click="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95 disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan Perubahan Matriks
                        </button>
                    </div>
                </div>
            </section>

            <!-- Floating Alerts / Feedback at Bottom-Left -->
            <div class="fixed bottom-6 left-6 z-[9999] max-w-sm space-y-3 pointer-events-none">
                <transition name="fade">
                    <div v-if="localSuccess" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-emerald-200 bg-white p-4 text-sm text-[#065f46] shadow-2xl backdrop-blur-sm dark:border-emerald-500/30 dark:bg-[#1a1a1a] dark:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.807-9.617a.9.9 0 111.386 1.134l-4.5 5.5a.9.9 0 01-1.302.08l-2.5-2.5a.9.9 0 011.272-1.272l1.782 1.782 3.862-4.724z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localSuccess }}</span>
                    </div>
                </transition>

                <transition name="fade">
                    <div v-if="localError" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-rose-200 bg-white p-4 text-sm text-[#991b1b] shadow-2xl backdrop-blur-sm dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localError }}</span>
                    </div>
                </transition>
            </div>

            <!-- Mapping Grid Editor -->
            <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-slate-200 dark:border-white/10 p-6 shadow-sm overflow-visible">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Matriks Input Pemetaan RACI</h2>
                    
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Silakan pilih RACI level di setiap cell, lalu klik Simpan di kanan atas atau bawah.
                    </div>
                </div>

                <!-- RACI Matrix Table container -->
                <div class="overflow-x-auto border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full">
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
                                    v-for="role in roles" 
                                    :key="role.id" 
                                    class="p-0.5 text-center w-[24px] min-w-[24px] max-w-[24px] align-bottom pb-2 select-none"
                                    :title="role.name"
                                >
                                    <div class="inline-flex items-center justify-center h-36 w-[18px] mx-auto">
                                        <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <!-- Body grouped by objective -->
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                            <template v-for="obj in objectives" :key="obj.objective_id">
                                <!-- Objective Header Row -->
                                <tr class="bg-slate-100 dark:bg-[#202020] border-y border-slate-300 dark:border-white/10">
                                    <td 
                                        :colspan="roles.length + 1" 
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
                                        <div class="text-[9px] font-bold text-slate-900 dark:text-white">{{ pr.practice_id }}</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 font-sans leading-tight mt-0.5 line-clamp-1" :title="pr.practice_name">
                                            {{ pr.practice_name || pr.practice_description || '-' }}
                                        </div>
                                    </td>

                                    <!-- RACI Cell Select Option Inputs -->
                                    <td 
                                        v-for="role in roles" 
                                        :key="role.id" 
                                        class="p-0 text-center w-[24px] min-w-[24px] max-w-[24px] align-middle h-[20px]"
                                    >
                                        <select 
                                            v-model="matrixState[pr.practice_id][role.id]"
                                            class="text-[9px] font-black rounded border text-center focus:ring-1 focus:ring-[#821f44]/40 h-5.5 w-[20px] transition-all duration-300 appearance-none bg-no-repeat bg-center select-none py-0 px-0"
                                            :class="getCellColorClass(matrixState[pr.practice_id]?.[role.id])"
                                        >
                                            <option value="" class="bg-white text-slate-400 dark:bg-[#222] dark:text-slate-600 font-semibold">-</option>
                                            <option value="R" class="bg-emerald-50 text-emerald-700 dark:bg-[#102a1c] dark:text-emerald-400 font-black">R</option>
                                            <option value="A" class="bg-rose-50 text-rose-700 dark:bg-[#341118] dark:text-rose-400 font-black">A</option>
                                            <option value="C" class="bg-amber-50 text-amber-700 dark:bg-[#2e1d0c] dark:text-amber-400 font-black">C</option>
                                            <option value="I" class="bg-indigo-50 text-indigo-700 dark:bg-[#13172e] dark:text-indigo-400 font-black">I</option>
                                        </select>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="objectives.length === 0">
                                <td 
                                    :colspan="roles.length + 1" 
                                    class="p-8 text-center text-xs font-sans text-slate-400 dark:text-slate-500"
                                >
                                    Belum ada data Governance Objective / Practices.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sticky bottom save action bar -->
                <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 dark:border-white/5 pt-6">
                    <Link
                        :href="route('policy.raci.index')"
                        class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        Batal
                    </Link>
                    <button
                        @click="submit"
                        class="rounded-xl bg-[#821f44] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition hover:bg-[#9c2552] disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                        Simpan Pemetaan
                    </button>
                </div>

            </div>

            <!-- Floating Navigation Controls on the Right Side -->
            <div class="fixed right-6 bottom-24 z-[9999] flex flex-col gap-3 pointer-events-none">
                <button 
                    @click="submit" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-lg border border-[#821f44]/20 transition-all hover:bg-[#9c2552] active:scale-90 hover:shadow-xl"
                    title="Simpan Pemetaan RACI"
                    :disabled="form.processing"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>

                <button 
                    @click="scrollToTop" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Kembali ke Atas"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                    </svg>
                </button>

                <button 
                    @click="scrollToBottom" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Ke Paling Bawah"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                    </svg>
                </button>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage, useForm, Link } from '@inertiajs/vue3';
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

const page = usePage();

// Alert notifications
const localSuccess = ref(page.props.flash?.success || null);
const localError = ref(page.props.flash?.error || null);

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            localSuccess.value = flash.success;
            setTimeout(() => { localSuccess.value = null; }, 5000);
        }
        if (flash?.error) {
            localError.value = flash.error;
            setTimeout(() => { localError.value = null; }, 5000);
        }
    },
    { deep: true, immediate: true }
);

// ---------------------------------------------------
// RACI MATRIX STATE & SUBMIT HELPERS
// ---------------------------------------------------
const matrixState = ref({});

// Initialize 2D grid matrix state from current DB mapping
props.objectives.forEach(obj => {
    obj.practices.forEach(pr => {
        matrixState.value[pr.practice_id] = {};
        props.roles.forEach(role => {
            const map = props.mappings.find(m => m.practice_id === pr.practice_id && m.role_id === role.id);
            matrixState.value[pr.practice_id][role.id] = map ? map.r_a : '';
        });
    });
});

const form = useForm({
    mappings: []
});

function submit() {
    const list = [];
    Object.keys(matrixState.value).forEach(practiceId => {
        Object.keys(matrixState.value[practiceId]).forEach(roleId => {
            list.push({
                practice_id: practiceId,
                role_id: parseInt(roleId),
                r_a: matrixState.value[practiceId][roleId] || ''
            });
        });
    });

    form.mappings = list;
    form.post(route('policy.raci.update'), {
        preserveScroll: true,
        onSuccess: () => {
            localSuccess.value = "Matriks RACI berhasil disimpan!";
        },
        onError: () => {
            localError.value = "Terjadi kesalahan saat menyimpan matriks.";
        }
    });
}

// Return beautifully stylized color badges for RACI select boxes
function getCellColorClass(val) {
    if (!val) {
        return "bg-slate-50/50 text-slate-400 border-slate-200 dark:bg-transparent dark:border-white/10 dark:text-slate-600";
    }
    const cleanVal = val.toUpperCase();
    if (cleanVal === 'A') {
        return "bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800 font-extrabold focus:bg-rose-50";
    }
    if (cleanVal === 'R') {
        return "bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800 font-extrabold focus:bg-emerald-50";
    }
    if (cleanVal === 'C') {
        return "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800 font-extrabold focus:bg-amber-50";
    }
    if (cleanVal === 'I') {
        return "bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800 font-extrabold focus:bg-indigo-50";
    }
    return "bg-slate-50 text-slate-700 border-slate-200 dark:bg-transparent dark:border-white/10 dark:text-slate-400";
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function scrollToBottom() {
    window.scrollTo({
        top: document.documentElement.scrollHeight,
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
</style>
