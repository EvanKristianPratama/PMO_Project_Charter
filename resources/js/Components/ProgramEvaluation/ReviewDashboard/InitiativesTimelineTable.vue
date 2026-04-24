<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-[0.06em] text-slate-500 dark:bg-white/5 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Building Block</th>
                        <th class="px-4 py-3 font-semibold">Nama Initiative</th>
                        <th class="px-4 py-3 font-semibold">Status Review Terbaru</th>
                        <th class="px-4 py-3 font-semibold">Periode Review</th>
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
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold" :class="statusBadgeClass(row.latest_review_status)">
                                {{ row.latest_review_status || 'Belum Ada Status' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ row.latest_review_period || '-' }}</td>
                        <td class="px-4 py-3">{{ row.baseline_date || '-' }}</td>
                        <td class="px-4 py-3">{{ row.approve_date || '-' }}</td>
                        <td class="px-4 py-3">{{ row.process_month || '-' }}</td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
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

const statusBadgeClass = (status) => ({
    'On Track': 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-300',
    'At Risk': 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
    'Delayed': 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
    'Done': 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    'On Progress': 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
    'On Review': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-300',
    'Not Started': 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
    'Not Signed': 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    'Belum Ada Status': 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400',
}[status] ?? 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300');
</script>
