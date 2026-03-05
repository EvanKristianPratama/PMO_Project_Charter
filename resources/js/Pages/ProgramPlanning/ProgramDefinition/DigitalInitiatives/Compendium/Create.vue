<template>
    <UserLayout title="Program Definition Digital Initiatives — New Compendium">
        <div class="animate-fade-in-up space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Compendium</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Mode tabel (WYSIWYG) untuk `trs_sc_initiative` + `trs_sc_details`.</p>
                    </div>
                    <Link
                        href="/program-planning/program-definition/digital-initiatives/compendium"
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
                            <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Compendium Data Entry</h2>
                            <p class="mt-1 text-[10px] text-slate-500">Skala: 1 = Low, 2 = Medium, 3 = High, 4 = TBC (To be Discussed)</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-1 rounded-lg bg-[#0f63b5] px-4 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#0c4e8f] disabled:opacity-50"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                            <colgroup>
                                <col class="w-[4%]">
                                <col class="w-[8%]">
                                <col class="w-[7%]">
                                <col class="w-[14%]">
                                <col class="w-[16%]">
                                <col class="w-[19%]">
                                <col class="w-[7%]">
                                <col class="w-[7%]">
                                <col class="w-[8%]">
                                <col class="w-[8%]">
                                <col class="w-[14%]">
                                <col class="w-[8%]">
                            </colgroup>
                            <thead class="bg-slate-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Group</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">No</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Project Owner</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Use Case</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Desc</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Value</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Urgency</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">RJPP</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">CoE</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">DataSource</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Waktu DataSource Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                                <tr>
                                    <td class="px-2 py-2.5 text-slate-600">1</td>
                                    <td class="px-2 py-2.5 text-slate-700 dark:text-slate-200">{{ selectedInitiative?.group ?? '-' }}</td>
                                    <td class="px-2 py-2.5">
                                        <select v-model.number="form.initiative_id" class="form-input-sm">
                                            <option value="">-</option>
                                            <option v-for="initiative in initiativeOptions" :key="initiative.id" :value="initiative.id">
                                                {{ initiative.code ?? '-' }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2.5 text-slate-700 dark:text-slate-200">{{ selectedInitiative?.project_owner ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-slate-700 dark:text-slate-200">{{ selectedInitiative?.name ?? '-' }}</td>
                                    <td class="px-2 py-2.5">
                                        <input v-model="form.useCase_description" type="text" class="form-input-sm" placeholder="Description" />
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <select v-model.number="form.value" class="form-input-sm">
                                            <option v-for="score in scoreOptions" :key="`value-${score.value}`" :value="score.value">{{ score.labelShort }}</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <select v-model.number="form.urgency" class="form-input-sm">
                                            <option v-for="score in scoreOptions" :key="`urgency-${score.value}`" :value="score.value">{{ score.labelShort }}</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <input v-model="form.rjpp" type="text" class="form-input-sm" placeholder="RJPP" />
                                    </td>
                                    <td class="px-2 py-2.5 text-slate-700 dark:text-slate-200">{{ selectedInitiative?.coe ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-slate-700 dark:text-slate-200">{{ selectedInitiative?.data_source ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-slate-700 dark:text-slate-200">{{ selectedInitiative?.data_source_created ?? '-' }}</td>
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
import { computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
});

const scoreOptions = [
    { value: 1, labelShort: 'Low', labelLong: 'Low' },
    { value: 2, labelShort: 'Medium', labelLong: 'Medium' },
    { value: 3, labelShort: 'High', labelLong: 'High' },
    { value: 4, labelShort: 'TBC', labelLong: 'TBC (To be Discussed)' },
];

const form = useForm({
    initiative_id: '',
    alias: '',
    useCase_description: '',
    value: 4,
    urgency: 4,
    rjpp: '',
    detail_useCase_description: '',
    current_situation: '',
    key_functionalities: '',
    value_detail: '',
    urgency_detail: '',
    ease_implementation: 4,
    ease_detail: '',
    resource_requirement: 4,
    resource_detail: '',
    interpendencies: '',
    sign_by: '',
});

const selectedInitiative = computed(() => {
    const selectedId = Number(form.initiative_id);
    if (!selectedId) {
        return null;
    }

    return props.initiativeOptions.find((item) => Number(item.id) === selectedId) ?? null;
});

const scoreToLabel = (value) => {
    const found = scoreOptions.find((item) => item.value === Number(value));
    return found ? found.labelLong : '-';
};

watch(() => form.initiative_id, () => {
    if (!selectedInitiative.value) {
        return;
    }

    if (!String(form.alias ?? '').trim()) {
        form.alias = String(selectedInitiative.value.code ?? '');
    }

    if (!String(form.useCase_description ?? '').trim()) {
        form.useCase_description = String(
            selectedInitiative.value.description
                ?? selectedInitiative.value.name
                ?? ''
        );
    }
});

const submit = () => {
    const desc = String(form.useCase_description ?? '').trim()
        || String(selectedInitiative.value?.description ?? '').trim()
        || String(selectedInitiative.value?.name ?? '').trim()
        || '-';

    form.alias = String(form.alias ?? '').trim()
        || String(selectedInitiative.value?.code ?? '').trim()
        || '-';

    form.useCase_description = desc;
    form.detail_useCase_description = desc;
    form.current_situation = String(form.current_situation ?? '').trim() || '-';
    form.key_functionalities = String(form.key_functionalities ?? '').trim() || '-';
    form.value_detail = String(form.rjpp ?? '').trim() || '-';
    form.urgency_detail = scoreToLabel(form.urgency);
    form.ease_implementation = Number(form.ease_implementation || 4);
    form.ease_detail = String(form.ease_detail ?? '').trim() || '-';
    form.resource_requirement = Number(form.resource_requirement || 4);
    form.resource_detail = String(form.resource_detail ?? '').trim() || '-';
    form.interpendencies = String(form.interpendencies ?? '').trim() || '-';
    form.sign_by = String(form.sign_by ?? '').trim() || 'SYSTEM';

    form.post('/program-planning/program-definition/digital-initiatives/compendium');
};
</script>

<style scoped>
@reference "tailwindcss";

.form-input-sm {
    @apply w-full rounded border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5];
}
</style>
