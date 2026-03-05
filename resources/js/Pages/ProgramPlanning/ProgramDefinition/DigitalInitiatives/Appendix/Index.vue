<template>
    <UserLayout title="Program Definition Digital Initiatives — Appendix List">
        <div class="animate-fade-in space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            href="/program-planning/program-definition/digital-initiatives"
                            class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
                        >
                            <span aria-hidden="true">←</span>
                            Kembali ke Digital Initiatives
                        </Link>
                        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Appendix List</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Data bersumber dari tabel <code>trs_sc_initiative</code>.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                        >
                            Total: {{ totalAppendixItems }}
                        </span>
                        <Link
                            href="/program-planning/program-definition/digital-initiatives/appendix/create"
                            class="inline-flex items-center rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f]"
                        >
                            New Appendix
                        </Link>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                    <colgroup>
                        <col class="w-[6%]">
                        <col class="w-[12%]">
                        <col class="w-[20%]">
                        <col class="w-[18%]">
                        <col class="w-[26%]">
                        <col class="w-[6%]">
                        <col class="w-[6%]">
                        <col class="w-[6%]">
                    </colgroup>
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Initiative</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Alias</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case Description</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                        <tr v-for="(item, index) in appendixItems" :key="`appendix-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="px-3 py-3 text-slate-600 dark:text-slate-400">{{ index + 1 }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.initiative_code ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.initiative_name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.alias ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.use_case_description ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.value ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.urgency ?? '-' }}</td>
                            <td class="px-3 py-3 text-[10px] font-medium">
                                <Link
                                    :href="`/program-planning/program-definition/digital-initiatives/appendix/${item.id}/edit`"
                                    class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                >
                                    Edit
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="appendixItems.length === 0">
                            <td colspan="8" class="px-6 py-10 text-center text-xs text-slate-500 dark:text-slate-400">
                                Belum ada data di <code>trs_sc_initiative</code>.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    appendixItems: {
        type: Array,
        default: () => [],
    },
    totalAppendixItems: {
        type: Number,
        default: 0,
    },
});
</script>
