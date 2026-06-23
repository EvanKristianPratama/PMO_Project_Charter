<template>
    <UserLayout title="Dokumen Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">

            <!-- Capsule Page Navigation Menu -->
            <div class="flex flex-wrap items-center gap-1.5 rounded-xl bg-slate-100 p-1 dark:bg-white/5 w-fit print:hidden">
                <button
                    @click="activeTab = 'proses-bisnis'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'proses-bisnis'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    Business Process
                </button>
                <button
                    @click="activeTab = 'function'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'function'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    Function
                </button>
                <button
                    @click="activeTab = 'apqc'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'apqc'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    APQC
                </button>
            </div>

            <!-- Table Component -->
            <ProsesBisnisTable
                v-if="activeTab === 'proses-bisnis'"
                :proses-bisnis="prosesBisnis"
                :organizations="organizations"
            />
            <FunctionTable
                v-else-if="activeTab === 'function'"
                :functions="functions"
                :groub-options="groubOptions"
                :regulations="regulations"
            />
            <div
                v-else-if="activeTab === 'apqc'"
                class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-2">APQC Process Classification Framework</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        Halaman ini akan menampilkan klasifikasi proses bisnis standar APQC. Modul ini sedang dalam tahap perencanaan.
                    </p>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProsesBisnisTable from '@/Components/Architecture/ProsesBisnis/ProsesBisnisTable.vue';
import FunctionTable from '@/Components/Architecture/Function/FunctionTable.vue';

defineProps({
    prosesBisnis: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        required: true,
    },
    functions: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref('proses-bisnis');
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
