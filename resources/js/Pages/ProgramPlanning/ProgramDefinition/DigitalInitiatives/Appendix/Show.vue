<template>
    <UserLayout title="Program Definition Digital Initiatives — Edit Appendix">
        <div class="animate-fade-in-up space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Appendix</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Mode tabel (WYSIWYG) untuk data `trs_sc_initiative`.</p>
                    </div>
                    <Link
                        href="/program-planning/program-definition/digital-initiatives/appendix"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        ← Kembali
                    </Link>
                </div>
            </section>

            <form @submit.prevent="submit">
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-2.5 dark:border-white/10">
                        <div>
                            <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Data Appendix</h2>
                            <p class="mt-1 text-[10px] text-slate-500">Skala: 1 = Low, 2 = Medium, 3 = High, 4 = TBC (To be Discussed)</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-1 rounded-lg bg-[#0f63b5] px-4 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#0c4e8f] disabled:opacity-50"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Update</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                            <colgroup>
                                <col class="w-[28%]">
                                <col class="w-[14%]">
                                <col class="w-[34%]">
                                <col class="w-[12%]">
                                <col class="w-[12%]">
                            </colgroup>
                            <thead class="bg-slate-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Master Initiative</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Alias</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Use Case Description</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Value</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Urgency</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                                <tr>
                                    <td class="px-3 py-2.5">
                                        <select v-model="form.initiative_id" class="form-input-sm">
                                            <option value="">— Pilih Initiative —</option>
                                            <option v-for="initiative in initiativeOptions" :key="initiative.id" :value="initiative.id">
                                                {{ initiative.code ?? '-' }} - {{ initiative.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <input v-model="form.alias" type="text" class="form-input-sm" placeholder="Alias" />
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <input v-model="form.useCase_description" type="text" class="form-input-sm" placeholder="Use case description" />
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <select v-model.number="form.value" class="form-input-sm">
                                            <option v-for="score in scoreOptions" :key="`value-${score.value}`" :value="score.value">{{ score.label }}</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <select v-model.number="form.urgency" class="form-input-sm">
                                            <option v-for="score in scoreOptions" :key="`urgency-${score.value}`" :value="score.value">{{ score.label }}</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="Object.keys(form.errors).length" class="border-t border-slate-200 px-4 py-2 dark:border-white/10">
                        <p v-for="(msg, field) in form.errors" :key="field" class="text-[10px] text-rose-500">{{ field }}: {{ msg }}</p>
                    </div>
                </div>
            </form>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    appendix: {
        type: Object,
        required: true,
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
});

const scoreOptions = [
    { value: 1, label: '1 - Low' },
    { value: 2, label: '2 - Medium' },
    { value: 3, label: '3 - High' },
    { value: 4, label: '4 - TBC (To be Discussed)' },
];

const form = useForm({
    initiative_id: props.appendix.initiative_id ?? '',
    alias: props.appendix.alias ?? '',
    useCase_description: props.appendix.useCase_description ?? '',
    value: props.appendix.value ?? 4,
    urgency: props.appendix.urgency ?? 4,
});

const submit = () => {
    form.put(`/program-planning/program-definition/digital-initiatives/appendix/${props.appendix.id}`);
};
</script>

<style scoped>
@reference "tailwindcss";

.form-input-sm {
    @apply w-full rounded border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5];
}
</style>
