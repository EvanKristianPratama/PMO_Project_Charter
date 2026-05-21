<template>
    <UserLayout title="Procedure">
        <div class="animate-fade-in-up space-y-8">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Standard Operating Procedure</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Prosedur Tata Kelola IT</h1>
                    </div>
                </div>
            </section>

            <!-- Actors Section -->
            <section class="space-y-4">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-5 w-1 rounded-full bg-[#821f44]"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                    </h2>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div 
                        v-for="actor in actors" 
                        :key="actor.id"
                        class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-white/10 dark:bg-[#171717]"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-xs font-bold text-slate-400 dark:bg-white/5">
                            {{ actor.id }}
                        </div>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ actor.name }}</span>
                    </div>
                    <div v-if="actors.length === 0" class="col-span-full py-8 text-center text-sm text-slate-400">
                        Belum ada data unit organisasi terkait.
                    </div>
                </div>
            </section>

            <!-- SOP Section A -->
            <section class="space-y-4">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-5 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        A. Penyusunan RSTI
                    </h2>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Judul SOP</th>
                                    <th scope="col" class="px-6 py-4">Revisi</th>
                                    <th scope="col" class="px-6 py-4">Terbit</th>
                                    <th scope="col" class="px-6 py-4">Berlaku</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                                <tr v-if="sopA.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data SOP untuk kategori ini.
                                    </td>
                                </tr>
                                <tr 
                                    v-for="item in sopA" 
                                    :key="item.id" 
                                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                                >
                                    <td class="px-6 py-4 text-slate-900 dark:text-white font-bold leading-relaxed">
                                        {{ item.judul }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-semibold text-slate-800 dark:text-slate-200">
                                        {{ item.revisi }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">
                                        {{ formatDate(item.terbit) }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">
                                        {{ formatDate(item.berlaku) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
});

const sopA = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter(s => s.tipe === 'A');
});

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
