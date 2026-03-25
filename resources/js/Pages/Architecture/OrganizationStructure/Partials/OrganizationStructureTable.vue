<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Organization List</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">No</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Company</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Groub</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Organization</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(organizationStructureRow, index) in organizationStructureRows"
                        :key="organizationStructureRow.organization_id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-100">
                            {{ displayValue(organizationStructureRow.company_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.groub_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.organization_name) }}
                        </td>
                    </tr>
                    <tr v-if="organizationStructureRows.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data organization tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
});

const displayValue = (value) => value ?? '-';
</script>
