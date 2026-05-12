<template>
    <div class="rounded-lg bg-white shadow-sm dark:bg-[#171717] overflow-hidden">
        <div class="px-3 py-1.5 border-b border-slate-200 dark:border-slate-700/50">
            <h2 class="text-base font-semibold text-slate-700 dark:text-slate-200">Priority Strategic Initiatives of
                Pertamina 2026</h2>
        </div>
        <div class="overflow-x-auto">
            <table
                class="w-full text-[10px] text-left border-collapse border border-slate-200 dark:border-slate-700/50">
                <thead class="text-[9px] text-slate-700 uppercase bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th scope="col"
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 w-24 text-center">Priority 
                        </th>
                        <th scope="col"
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 w-16 text-center">No</th>
                        <th scope="col"
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 min-w-[200px]">Strategic Initiative</th>
                        <th scope="col"
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 min-w-[300px]">Deskripsi / Milestone</th>
                        <th scope="col"
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 min-w-[200px]">PIC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in initiatives" :key="item.id || idx"
                        class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                        <td
                            class="px-2 py-1 text-center border border-slate-200 dark:border-slate-700/50 align-midle text-slate-700 dark:text-slate-200">
                            {{ item.priority }}
                        </td>
                        <td class="px-2 py-1 text-center border border-slate-200 dark:border-slate-700/50 align-midle">
                            {{ item.no }}
                        </td>
                        <td
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 align-midle text-slate-700 dark:text-slate-200">
                            {{ item.initiative }}
                        </td>
                        <td
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 align-top text-slate-600 dark:text-slate-400">
                            {{ item.deskripsi }}
                        </td>
                        <td
                            class="px-2 py-1 border border-slate-200 dark:border-slate-700/50 align-top text-slate-600 dark:text-slate-400 whitespace-pre-wrap">
                            <ul v-if="item.map_priority_strategic_initiative && item.map_priority_strategic_initiative.length > 0" class="list-disc list-inside">
                                <li v-for="mapPic in item.map_priority_strategic_initiative" :key="mapPic.id">
                                    {{ mapPic.pic_prioriy_strategic?.name }}
                                </li>
                            </ul>
                            <span v-else>-</span>
                        </td>
                    </tr>
                    <tr v-if="!initiatives || initiatives.length === 0">
                        <td colspan="5"
                            class="px-4 py-8 text-center text-slate-500 border border-slate-200 dark:border-slate-700/50">
                            Belum ada data Priority Strategic Initiatives.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    initiatives: {
        type: Array,
        default: () => [],
    },
});
</script>
