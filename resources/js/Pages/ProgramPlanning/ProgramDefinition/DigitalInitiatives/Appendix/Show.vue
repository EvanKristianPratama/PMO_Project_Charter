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
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="mb-4">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Master Initiative Mapping</h2>
                        <p class="mt-1 text-xs text-slate-500">Pilih atau sesuaikan inisiatif yang terhubung ke Appendix.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="max-w-xl">
                            <select
                                @change="onSelectInitiative"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">+ Pilih Initiative untuk dimapping...</option>
                                <option
                                    v-for="opt in initiativeOptions"
                                    :key="`appendix-initiative-opt-${opt.id}`"
                                    :value="opt.id"
                                    :disabled="form.initiative_ids.includes(Number(opt.id))"
                                >
                                    {{ opt.code ? `[${String(opt.code).replace(/#/g, '')}] ` : '' }}{{ opt.name }}
                                </option>
                            </select>
                        </div>

                        <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="item in selectedInitiatives"
                                    :key="`appendix-tag-${item.id}`"
                                    class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                                >
                                    <span class="max-w-[280px] truncate">{{ initiativeTagLabel(item).replace(/#/g, '') }}</span>
                                    <button
                                        type="button"
                                        @click="removeInitiative(item.id)"
                                        class="text-slate-400 transition-colors hover:text-rose-500"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        <p v-if="selectedInitiatives.length === 0" class="flex items-center text-xs italic text-slate-400">
                            Belum ada inisiatif yang dipilih.
                        </p>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-100 pt-6 dark:border-white/5">
                    <div class="mb-4">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Compendium Mapping</h2>
                        <p class="mt-1 text-xs text-slate-500">Pilih compendium yang terhubung ke Appendix.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="max-w-xl">
                            <select
                                @change="onSelectCompendium"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">+ Pilih Compendium untuk dimapping...</option>
                                <option
                                    v-for="opt in compendiumOptions"
                                    :key="`appendix-compendium-opt-${opt.id}`"
                                    :value="opt.id"
                                    :disabled="form.compendium_ids.includes(Number(opt.id))"
                                >
                                    {{ formatCompendiumLabel(opt) }}
                                </option>
                            </select>
                        </div>

                        <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="item in selectedCompendiums"
                                    :key="`appendix-compendium-tag-${item.id}`"
                                    class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                                >
                                    <span class="max-w-[280px] truncate">{{ item.label }}</span>
                                    <button
                                        type="button"
                                        @click="removeCompendium(item.id)"
                                        class="text-slate-400 transition-colors hover:text-rose-500"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <p v-if="selectedCompendiums.length === 0" class="flex items-center text-xs italic text-slate-400">
                                Belum ada compendium yang dipilih.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

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
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Use Case</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Description</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Value</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">Urgency</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                                <tr>
                                    <td class="px-3 py-2.5">
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                v-for="item in selectedInitiatives"
                                                :key="`table-initiative-${item.id}`"
                                                class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                            >
                                                {{ initiativeTagLabel(item) }}
                                            </span>
                                            <span v-if="selectedInitiatives.length === 0" class="text-[10px] italic text-slate-400">
                                                Belum ada inisiatif dipilih.
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <input v-model="form.usecase" type="text" class="form-input-sm" placeholder="Use case" />
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <input v-model="form.description" type="text" class="form-input-sm" placeholder="Description" />
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
import { computed } from 'vue';
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
    compendiumOptions: {
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

const toNumber = (value, fallback = null) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
};

const normalizeIdList = (values) => {
    if (!Array.isArray(values)) return [];
    return values.map((value) => toNumber(value, 0)).filter((value) => value > 0);
};

const form = useForm({
    initiative_ids: normalizeIdList(
        Array.isArray(props.appendix.initiative_ids)
            ? props.appendix.initiative_ids
            : (props.appendix.initiative_id ? [props.appendix.initiative_id] : [])
    ),
    compendium_ids: normalizeIdList(
        Array.isArray(props.appendix.compendium_ids)
            ? props.appendix.compendium_ids
            : (props.appendix.compendium_id ? [props.appendix.compendium_id] : [])
    ),
    usecase: props.appendix.usecase ?? props.appendix.alias ?? '',
    description: props.appendix.description ?? props.appendix.useCase_description ?? '',
    value: props.appendix.value ?? 4,
    urgency: props.appendix.urgency ?? 4,
});

const selectedInitiatives = computed(() => {
    const optionsById = new Map(
        (props.initiativeOptions ?? []).map((option) => [toNumber(option.id, 0), option])
    );
    return normalizeIdList(form.initiative_ids).map((id) => {
        const option = optionsById.get(id);
        return {
            id,
            code: option?.code ?? '',
            name: option?.name ?? `Initiative ${id}`,
        };
    });
});

const initiativeTagLabel = (item) => {
    if (!item) return '-';
    if (item.code) return `[${item.code}] ${item.name}`;
    return item.name;
};

const formatCompendiumLabel = (option) => {
    const text = String(option?.label ?? '').trim();
    return text !== '' ? text : `Compendium #${option?.id ?? '-'}`;
};

const selectedCompendiums = computed(() => {
    const optionsById = new Map(
        (props.compendiumOptions ?? []).map((option) => [toNumber(option.id, 0), option])
    );
    return normalizeIdList(form.compendium_ids).map((id) => {
        const option = optionsById.get(id);
        return {
            id,
            label: option ? formatCompendiumLabel(option) : `Compendium #${id}`,
        };
    });
});

const addInitiative = (id) => {
    const numericId = toNumber(id, 0);
    if (!numericId) return;
    if (!form.initiative_ids.includes(numericId)) {
        form.initiative_ids.push(numericId);
    }
};

const removeInitiative = (id) => {
    const numericId = toNumber(id, 0);
    form.initiative_ids = normalizeIdList(form.initiative_ids).filter((item) => item !== numericId);
};

const onSelectInitiative = (event) => {
    const selectedId = toNumber(event.target.value, 0);
    addInitiative(selectedId);
    event.target.value = '';
};

const addCompendium = (id) => {
    const numericId = toNumber(id, 0);
    if (!numericId) return;
    if (!form.compendium_ids.includes(numericId)) {
        form.compendium_ids.push(numericId);
    }
};

const removeCompendium = (id) => {
    const numericId = toNumber(id, 0);
    form.compendium_ids = normalizeIdList(form.compendium_ids).filter((item) => item !== numericId);
};

const onSelectCompendium = (event) => {
    const selectedId = toNumber(event.target.value, 0);
    addCompendium(selectedId);
    event.target.value = '';
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        initiative_ids: normalizeIdList(data.initiative_ids),
        compendium_ids: normalizeIdList(data.compendium_ids),
    })).put(`/program-planning/program-definition/digital-initiatives/appendix/${props.appendix.id}`);
};
</script>

<style scoped>
@reference "tailwindcss";

.form-input-sm {
    @apply w-full rounded border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5];
}
</style>
