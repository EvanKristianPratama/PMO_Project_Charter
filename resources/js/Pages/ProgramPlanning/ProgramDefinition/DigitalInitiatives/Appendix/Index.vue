<template>
    <UserLayout title="Program Definition Digital Initiatives â€” Appendix List">
        <div class="animate-fade-in space-y-4">
            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717] w-fit">
                <Link
                    href="/program-planning/program-definition/digital-initiatives"
                    class="group flex h-8 items-center gap-2 rounded-lg px-3 text-xs font-bold text-slate-500 transition-all hover:bg-slate-50 hover:text-[#0f63b5] dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-400"
                >
                    <svg class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>

                <div class="h-4 w-px bg-slate-200 dark:bg-white/10" />

                <Link
                    href="/program-planning/program-definition/digital-initiatives"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Digital Initiatives
                </Link>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/compendium"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Compendium
                </Link>
                <div
                    class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400"
                >
                    Appendix
                </div>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Appendix List</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                        >
                            Total: {{ filteredAppendixItems.length }}
                        </span>
                        <button
                            type="button"
                            @click="openCreateModal"
                            class="inline-flex items-center rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f]"
                        >
                            New Appendix
                        </button>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 border-b border-slate-100 bg-slate-50/30 px-4 py-2.5 dark:border-white/5 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Owner:</label>
                        <select
                            v-model="filters.owner"
                            class="min-w-[140px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Project Owners</option>
                            <option v-for="owner in uniqueOwners" :key="owner" :value="owner">{{ owner }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">CoE:</label>
                        <select
                            v-model="filters.coe"
                            class="min-w-[120px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All CoE</option>
                            <option v-for="coe in uniqueCoes" :key="coe" :value="coe">{{ coe }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Value:</label>
                        <select
                            v-model="filters.value"
                            class="min-w-[90px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Value</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                            <option value="TBC">TBC</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Urgency:</label>
                        <select
                            v-model="filters.urgency"
                            class="min-w-[90px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Urgency</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                            <option value="TBC">TBC</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Compendium:</label>
                        <select
                            v-model="filters.compendium"
                            class="min-w-[140px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Compendium</option>
                            <option value="none">(No Compendium)</option>
                            <option v-for="compendium in uniqueCompendiums" :key="compendium" :value="compendium">{{ compendium }}</option>
                        </select>
                    </div>

                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="resetFilters"
                        class="ml-auto text-[10px] font-bold uppercase tracking-tighter text-rose-500 hover:text-rose-600 dark:text-rose-400"
                    >
                        Reset Filters
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[40px]">
                            <col class="w-[120px]">
                            <col class="w-[150px]">
                            <col class="w-[200px]">
                            <col class="w-[70px]">
                            <col class="w-[70px]">
                            <col class="w-[80px]">
                            <col class="w-[80px]">
                            <col class="w-[100px]">
                            <col class="w-[80px]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">RJPP</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">CoE</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Compendium</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(item, index) in filteredAppendixItems" :key="`appendix-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-3 text-center text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.project_owner ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200 font-medium">{{ item.use_case ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    {{ item.desc ?? '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.value)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.value ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.urgency)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.urgency ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ (item.rjpp ?? '-').replace(/#/g, '') }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.coe ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    {{ item.compendium }}
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium">
                                    <Link
                                        :href="`/program-planning/program-definition/digital-initiatives/appendix/${item.id}/edit`"
                                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                    >
                                        Show
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="filteredAppendixItems.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-xs text-slate-500 dark:text-slate-400 italic">
                                    Data tidak ditemukan berdasarkan filter yang dipilih.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div
            v-if="isCreateModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-4"
            @click.self="closeCreateModal"
        >
            <div class="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#171717]">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-white/10">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">
                        New Appendix
                    </h2>
                    <button
                        type="button"
                        class="rounded-md px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-slate-200"
                        @click="closeCreateModal"
                    >
                        Close
                    </button>
                </div>

                <form class="flex-1 space-y-6 overflow-y-auto px-6 py-5" @submit.prevent="submitCreate">
                    <div class="space-y-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-white/10 dark:bg-white/5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-500">Compendium</label>
                                <select
                                    v-model="appendixForm.compendium_id"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Use Case (Compendium)...</option>
                                    <option
                                        v-for="option in compendiumOptions"
                                        :key="`appendix-compendium-${option.id}`"
                                        :value="String(option.id)"
                                    >
                                        {{ formatCompendiumLabel(option) }}
                                    </option>
                                </select>
                                <p v-if="appendixForm.errors.compendium_id" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.compendium_id }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-500">Initiative</label>
                                <div class="space-y-2">
                                    <select
                                        @change="(e) => { addInitiative(Number(e.target.value)); e.target.value = ''; }"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    >
                                        <option value="">+ Pilih Initiative...</option>
                                        <option
                                            v-for="opt in initiativeOptions"
                                            :key="`appendix-initiative-${opt.id}`"
                                            :value="opt.id"
                                            :disabled="appendixForm.initiative_ids.includes(Number(opt.id))"
                                        >
                                            {{ initiativeDisplayLabel(opt) }}
                                        </option>
                                    </select>
                                    <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                        <template v-if="appendixForm.initiative_ids.length">
                                            <span
                                                v-for="id in appendixForm.initiative_ids"
                                                :key="`appendix-initiative-tag-${id}`"
                                                class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                            >
                                                {{ initiativeLabel(id) }}
                                                <button type="button" class="text-blue-700/70 hover:text-rose-500 dark:text-blue-300/80" @click="removeInitiative(id)">x</button>
                                            </span>
                                        </template>
                                        <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada initiative dipilih.</span>
                                    </div>
                                </div>
                                <p v-if="appendixForm.errors.initiative_ids" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.initiative_ids }}</p>
                            </div>
                        </div>
                    </div>

                    <AppendixCharterDocument
                        :initiative="blankAppendix"
                        :form="appendixForm"
                        :editable="true"
                        :coe-options="coeOptions"
                        :theme-options="themeOptions"
                    />

                    <div v-if="Object.keys(appendixForm.errors).length" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-[11px] text-rose-600">
                        <p class="font-semibold">Terdapat error pada input:</p>
                        <p v-for="(msg, field) in appendixForm.errors" :key="field">{{ field }}: {{ msg }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4 dark:border-white/10">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/10"
                            @click="closeCreateModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="appendixForm.processing"
                            class="rounded-lg bg-[#0f63b5] px-6 py-2 text-xs font-bold text-white shadow-md transition-all active:scale-95 hover:bg-[#0c4e8f] disabled:opacity-50"
                        >
                            {{ appendixForm.processing ? 'Menyimpan...' : 'Simpan Appendix' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';

const props = defineProps({
    appendixItems: {
        type: Array,
        default: () => [],
    },
    totalAppendixItems: {
        type: Number,
        default: 0,
    },
    uniqueCompendiums: {
        type: Array,
        default: () => [],
    },
    compendiumOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    themeOptions: {
        type: Array,
        default: () => [],
    },
});

const APPENDIX_SOURCE_ID = 2;

const isCreateModalOpen = ref(false);

const openCreateModal = () => {
    isCreateModalOpen.value = true;
    resetCreateForm();
};

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
};

const filters = ref({
    owner: '',
    value: '',
    urgency: '',
    coe: '',
    compendium: '',
});

const blankAppendix = {
    usecase: '-',
    description: '-',
    owner: '-',
    coe: '-',
    value_label: '-',
    urgency_label: '-',
    organization: '-',
    update_doc: '-',
    situation: '-',
    key_functionalities: '-',
    value_rationale: '-',
    value_matrics: '-',
    urgency_rationale: '-',
    urgency_expected: '-',
    ease_label: '-',
    ease_rationale: '-',
    ease_detail: '-',
    resource_label: '-',
    resource_rationale: '-',
    resource_detail: '-',
    predecessor: '-',
    successor: '-',
    otherBU: '-',
    sign_by: [],
    rjppThemes: [],
};

const appendixForm = useForm({
    compendium_id: '',
    owner: '',
    coe: '',
    usecase: '',
    description: '',
    source_id: APPENDIX_SOURCE_ID,
    value: null,
    urgency: null,
    status: 1,
    initiative_ids: [],
    rjpp_tagging_ids: [],
    organization: '',
    update_doc: '',
    situation: '',
    key_functionalities: '',
    value_rationale: '',
    value_matrics: '',
    urgency_rationale: '',
    urgency_expected: '',
    ease: null,
    ease_rationale: '',
    ease_detail: '',
    resource: null,
    resource_rationale: '',
    resource_detail: '',
    predecessor: '',
    successor: '',
    otherBU: '',
    sign_by: [''],
    sign_others_raw: '',
});

const resetCreateForm = () => {
    appendixForm.reset();
    appendixForm.clearErrors();
    appendixForm.compendium_id = '';
    appendixForm.owner = '';
    appendixForm.coe = '';
    appendixForm.usecase = '';
    appendixForm.description = '';
    appendixForm.source_id = APPENDIX_SOURCE_ID;
    appendixForm.value = null;
    appendixForm.urgency = null;
    appendixForm.status = 1;
    appendixForm.initiative_ids = [];
    appendixForm.rjpp_tagging_ids = [];
    appendixForm.organization = '';
    appendixForm.update_doc = '';
    appendixForm.situation = '';
    appendixForm.key_functionalities = '';
    appendixForm.value_rationale = '';
    appendixForm.value_matrics = '';
    appendixForm.urgency_rationale = '';
    appendixForm.urgency_expected = '';
    appendixForm.ease = null;
    appendixForm.ease_rationale = '';
    appendixForm.ease_detail = '';
    appendixForm.resource = null;
    appendixForm.resource_rationale = '';
    appendixForm.resource_detail = '';
    appendixForm.predecessor = '';
    appendixForm.successor = '';
    appendixForm.otherBU = '';
    appendixForm.sign_by = [''];
    appendixForm.sign_others_raw = '';
};

const toNumber = (value, fallback = null) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
};

const normalizeIdList = (values) => {
    if (!Array.isArray(values)) return [];
    return values.map((value) => toNumber(value, 0)).filter((value) => value > 0);
};

const stripInitiativePrefix = (name, code) => {
    const rawName = String(name ?? '').trim();
    const rawCode = String(code ?? '').trim().replace(/#/g, '');
    if (!rawName || !rawCode) return rawName;
    const escaped = rawCode.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const pattern = new RegExp(`^\\s*(\\[\\s*)?${escaped}(\\s*\\])?\\s*[-.:)]?\\s*`, 'i');
    const cleaned = rawName.replace(pattern, '').trim();
    return cleaned !== '' ? cleaned : rawName;
};

const initiativeDisplayLabel = (initiative) => {
    if (!initiative) return '-';
    const code = String(initiative?.code ?? '').trim().replace(/#/g, '');
    const name = stripInitiativePrefix(initiative?.name ?? '', code);
    if (code && name) return `[${code}] - ${name}`;
    if (code) return `[${code}]`;
    return name || '-';
};

const initiativeLabel = (id) => {
    const selected = props.initiativeOptions.find((item) => Number(item.id) === Number(id));
    if (!selected) return String(id);
    return initiativeDisplayLabel(selected) || String(id);
};

const addInitiative = (id) => {
    const numericId = toNumber(id, 0);
    if (numericId && !appendixForm.initiative_ids.includes(numericId)) {
        appendixForm.initiative_ids.push(numericId);
    }
};

const removeInitiative = (id) => {
    appendixForm.initiative_ids = appendixForm.initiative_ids.filter((item) => item !== id);
};

const formatCompendiumLabel = (option) => {
    const text = String(option?.label ?? '').trim();
    return text !== '' ? text : `Compendium #${option?.id ?? '-'}`;
};

const buildSignByPayload = (data) => {
    const primary = String(data.sign_by?.[0] ?? '').trim();
    const others = String(data.sign_others_raw ?? '')
        .split(',')
        .map((item) => item.trim())
        .filter((item) => item !== '');

    return [primary, ...others].filter((item) => item !== '');
};

const submitCreate = () => {
    appendixForm.transform((data) => ({
        ...data,
        compendium_id: data.compendium_id ? toNumber(data.compendium_id, null) : null,
        initiative_ids: normalizeIdList(data.initiative_ids),
        rjpp_tagging_ids: normalizeIdList(data.rjpp_tagging_ids),
        owner: String(data.owner ?? '').trim(),
        coe: String(data.coe ?? '').trim(),
        usecase: String(data.usecase ?? '').trim(),
        description: String(data.description ?? '').trim(),
        organization: String(data.organization ?? '').trim(),
        update_doc: data.update_doc ? String(data.update_doc).trim() : null,
        situation: String(data.situation ?? '').trim(),
        key_functionalities: String(data.key_functionalities ?? '').trim(),
        value_rationale: String(data.value_rationale ?? '').trim(),
        value_matrics: String(data.value_matrics ?? '').trim(),
        urgency_rationale: String(data.urgency_rationale ?? '').trim(),
        urgency_expected: String(data.urgency_expected ?? '').trim(),
        ease_rationale: String(data.ease_rationale ?? '').trim(),
        ease_detail: String(data.ease_detail ?? '').trim(),
        resource_rationale: String(data.resource_rationale ?? '').trim(),
        resource_detail: String(data.resource_detail ?? '').trim(),
        predecessor: String(data.predecessor ?? '').trim(),
        successor: String(data.successor ?? '').trim(),
        otherBU: String(data.otherBU ?? '').trim(),
        value: data.value === null || data.value === '' ? null : toNumber(data.value, null),
        urgency: data.urgency === null || data.urgency === '' ? null : toNumber(data.urgency, null),
        ease: data.ease === null || data.ease === '' ? null : toNumber(data.ease, null),
        resource: data.resource === null || data.resource === '' ? null : toNumber(data.resource, null),
        sign_by: buildSignByPayload(data),
        source_id: APPENDIX_SOURCE_ID,
    })).post('/program-planning/program-definition/digital-initiatives/appendix', {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateModal();
            resetCreateForm();
        },
    });
};

const uniqueOwners = computed(() => {
    const owners = props.appendixItems
        .map((item) => String(item.project_owner ?? '').trim())
        .filter((owner) => owner !== '' && owner !== '-');
    return [...new Set(owners)].sort();
});

const uniqueCoes = computed(() => {
    const coes = props.appendixItems
        .map((item) => String(item.coe ?? '').trim())
        .filter((coe) => coe !== '' && coe !== '-');
    return [...new Set(coes)].sort();
});

const filteredAppendixItems = computed(() => {
    return props.appendixItems.filter((item) => {
        const ownerVal = String(item.project_owner ?? '').trim() || '-';
        const coeVal = String(item.coe ?? '').trim() || '-';

        const matchOwner = !filters.value.owner || ownerVal === filters.value.owner;
        const matchValue = !filters.value.value || item.value === filters.value.value;
        const matchUrgency = !filters.value.urgency || item.urgency === filters.value.urgency;
        const matchCoe = !filters.value.coe || coeVal === filters.value.coe;

        let matchCompendium = true;
        if (filters.value.compendium === 'none') {
            matchCompendium = item.compendium === '-';
        } else if (filters.value.compendium) {
            matchCompendium = (item.compendium ?? '').includes(filters.value.compendium);
        }

        return matchOwner && matchValue && matchUrgency && matchCoe && matchCompendium;
    });
});

const hasActiveFilters = computed(() => {
    return !!(filters.value.owner || filters.value.value || filters.value.urgency || filters.value.coe || filters.value.compendium);
});

const resetFilters = () => {
    filters.value.owner = '';
    filters.value.value = '';
    filters.value.urgency = '';
    filters.value.coe = '';
    filters.value.compendium = '';
};

const scoreClass = (label) => {
    const l = String(label ?? '').toLowerCase();
    if (l === 'high') return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    if (l === 'medium') return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    if (l === 'low') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    return 'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300';
};
</script>
