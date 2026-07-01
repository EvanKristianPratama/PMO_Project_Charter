<template>
    <section v-if="stats.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-100">
        <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Category</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Statistik</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Deskripsi</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">Bulan</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">Jumlah</th>
                        <th class="border-b border-slate-900 px-4 py-3 dark:border-white/20">Inisiatif</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#171717]">
                    <tr v-for="(stat, index) in stats" :key="stat.label" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <!-- Rowspan for Total Approval Category and Total Count -->
                        <td v-if="index === 0" :rowspan="stats.length" class="border-r border-slate-900 p-4 text-center font-black uppercase dark:border-white/20">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-slate-900 dark:text-white">Total Approval</span>
                            </div>
                        </td>
                        <td v-if="index === 0" :rowspan="stats.length" class="border-r border-slate-900 p-4 text-center text-3xl font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ totalApproved }}
                        </td>

                        <!-- Stat Rows -->
                        <td class="border-r border-slate-900 px-4 py-2 font-bold uppercase text-slate-700 dark:border-white/20 dark:text-slate-300">
                            {{ stat.label }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-2 italic text-slate-500 dark:border-white/20 dark:text-slate-400">
                            {{ stat.desc }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-2 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ stat.bulan }} <span class="ml-0.5 text-[9px] font-normal text-slate-400">bulan</span>
                        </td>
                        <td class="border-r border-slate-900 px-4 py-2 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ stat.jumlah }}
                        </td>
                        <td class="px-4 py-2 font-medium text-slate-600 dark:text-slate-400">
                            <div v-if="stat.customText" class="italic text-slate-500 dark:text-slate-400">
                                {{ stat.customText }}
                            </div>
                            <div v-else class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in stat.initiatives"
                                    :key="init.no"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.projectCharterName || init.status"
                                >
                                    {{ init.no }}
                                </span>
                                <span v-if="stat.initiatives.length === 0" class="text-slate-400">-</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Not Approved Row -->
                    <tr class="border-t border-slate-900 bg-slate-50/50 dark:border-white/20 dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-slate-900 p-4 text-center font-black uppercase dark:border-white/20">
                            Total Not Approval
                        </td>
                        <td class="border-r border-slate-900 p-4 text-center text-3xl font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ totalNotApproved }}
                        </td>
                        <td colspan="5" class="px-4 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Inisiatif:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="init in notApprovedInitiatives"
                                        :key="init.no"
                                        class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                        :class="getCircleColor(init.status)"
                                        :title="init.projectCharterName || init.status"
                                    >
                                        {{ init.no }}
                                    </span>
                                    <span v-if="notApprovedInitiatives.length === 0" class="text-slate-400">-</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
defineProps({
    stats: { type: Array, required: true },
    totalApproved: { type: Number, required: true },
    totalNotApproved: { type: Number, required: true },
    notApprovedInitiatives: { type: Array, required: true },
    getCircleColor: { type: Function, required: true },
});
</script>
