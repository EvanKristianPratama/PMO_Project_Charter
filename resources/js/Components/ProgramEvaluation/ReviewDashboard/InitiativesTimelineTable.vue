<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-[0.06em] text-slate-500 dark:bg-white/5 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Building Block</th>
                        <th class="px-4 py-3 font-semibold">Nama Initiative</th>
                        <th class="px-4 py-3 font-semibold">Baseline (Tanggal)</th>
                        <th class="px-4 py-3 font-semibold">Approve (Tanggal)</th>
                        <th class="px-4 py-3 font-semibold">Bulan Proses (Baseline ke Approve)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in items"
                        :key="`review-dashboard-row-${row.initiative_id}`"
                        class="border-t border-slate-100 dark:border-white/10"
                    >
                        <td class="px-4 py-3">{{ row.no }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-200">
                                {{ row.building_block_type || '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">{{ row.initiative_name }}</td>
                        <td class="px-4 py-3">{{ row.baseline_date || '-' }}</td>
                        <td class="px-4 py-3">{{ row.approve_date || '-' }}</td>
                        <td class="px-4 py-3">{{ row.process_month || '-' }}</td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                            Belum ada data initiatives timeline.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>