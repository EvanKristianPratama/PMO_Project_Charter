<template>
    <section v-if="data.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-125">
        <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Project Owner</th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Aproval</th>
                        <th colspan="2" class="border-b border-slate-900 px-4 py-2 text-center dark:border-white/20">no aprove</th>
                    </tr>
                    <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#171717]">
                    <tr v-for="row in data" :key="row.owner" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-slate-900 px-4 py-4 font-black uppercase text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.owner }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.approved.length }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in row.approved"
                                    :key="init.no"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.status"
                                >
                                    {{ init.no }}
                                </span>
                                <span v-if="row.approved.length === 0" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.notApproved.length }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in row.notApproved"
                                    :key="init.no"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.status"
                                >
                                    {{ init.no }}
                                </span>
                                <span v-if="row.notApproved.length === 0" class="text-slate-400">-</span>
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
    data: { type: Array, required: true },
    getCircleColor: { type: Function, required: true },
});
</script>
