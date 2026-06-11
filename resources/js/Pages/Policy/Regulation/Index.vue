<template>
    <UserLayout title="Regulation">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">List of Policy, Standard, and Procedure</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Regulation</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('policy.regulation.manage')"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Kelola Regulasi
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Regulations Table Components -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                                <th scope="col" class="px-6 py-4">Judul</th>
                                <th scope="col" class="px-6 py-4">Nomor</th>
                                <th scope="col" class="px-6 py-4 w-36">Tipe</th>
                                <th scope="col" class="px-6 py-4">Fungsi</th>
                                <th scope="col" class="px-6 py-4">Organisasi</th>
                                <th scope="col" class="px-6 py-4 text-center w-24">Revisi</th>
                                <th scope="col" class="px-6 py-4 w-32">Berlaku</th>
                                <th scope="col" class="px-6 py-4 text-center w-32">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                            <tr v-if="regulations.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data Regulasi. Silakan hubungi admin atau klik "Kelola Regulasi" untuk menambahkan.
                                </td>
                            </tr>
                            <tr 
                                v-for="(reg, index) in regulations" 
                                :key="reg.id" 
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                            >
                                <!-- No -->
                                <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                    {{ index + 1 }}
                                </td>
                                <!-- Judul -->
                                <td class="px-6 py-4 text-slate-900 dark:text-white font-bold leading-relaxed max-w-sm">
                                    {{ reg.judul }}
                                </td>
                                <!-- Nomor -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ reg.nomor || '-' }}
                                </td>
                                 <!-- Tipe -->
                                 <td class="px-6 py-4">
                                     <span 
                                         :class="[
                                             'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                                             reg.tipe === 'Policy' 
                                                 ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400' 
                                                 : reg.tipe === 'Procedure'
                                                     ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                                                     : reg.tipe === 'Standart'
                                                         ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                                         : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                                         ]"
                                     >
                                         {{ reg.tipe }} - {{ reg.stk || '-' }}
                                     </span>
                                 </td>
                                <!-- Owner -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ reg.owner }}
                                </td>
                                <!-- Organisasi -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ reg.organization?.name || '-' }}
                                </td>
                                <!-- Revisi -->
                                <td class="px-6 py-4 text-center font-mono font-semibold text-slate-800 dark:text-slate-200">
                                    {{ reg.revisi }}
                                </td>
                                <!-- Berlaku -->
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">
                                    {{ formatDate(reg.berlaku) }}
                                </td>
                                <!-- Detail -->
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        @click="handleDetailClick(reg)"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#821f44] px-4 py-2 text-xs font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 active:scale-95"
                                        title="Lihat Detail Kebijakan"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    regulations: {
        type: Array,
        required: true,
    },
});

function handleDetailClick(reg) {
    const targetRoute = String(reg.tipe || '').toLowerCase() === 'procedure'
        ? 'policy.procedure.index'
        : 'policy.general.index';

    router.visit(route(targetRoute, { regulation_id: reg.id }));
}

// ---------------------------------------------------
// DATE FORMATTER HELPER
// ---------------------------------------------------
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
