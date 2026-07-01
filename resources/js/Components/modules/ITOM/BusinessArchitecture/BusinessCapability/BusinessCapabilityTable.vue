<template>
    <div v-if="viewMode === 'map'">
        <BusinessCapabilityMap
            :business-capabilities="businessCapabilities"
            @switch-to-table="viewMode = 'table'"
        />
    </div>

    <section v-else class="space-y-3">
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-white/10 dark:bg-[#171717]">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Business Capability List</h2>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-600"
                    >
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18" />
                        </svg>
                        Table
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20"
                        @click="viewMode = 'map'"
                    >
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h6" />
                        </svg>
                        Map
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                Group Function (Level 1)
                            </th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                SubGroup Function (Level 2)
                            </th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                SubSubGroup Function (Level 3)
                            </th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                Group Business
                            </th>
                            <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                Action
                            </th>
                        </tr>
                        <tr class="border-t border-slate-200/80 dark:border-white/10">
                            <th class="px-3 py-1.5 align-top">
                                <select
                                    v-model="selectedGroupFunction"
                                    class="w-full min-w-[150px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-normal text-slate-700 outline-none focus:border-[#1C75BC] focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                >
                                    <option value="">All</option>
                                    <option v-for="option in groupFunctionFilterOptions" :key="option" :value="option">{{ option }}</option>
                                </select>
                            </th>
                            <th class="px-3 py-1.5 align-top">
                                <select
                                    v-model="selectedSubGroupFunction"
                                    class="w-full min-w-[170px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-normal text-slate-700 outline-none focus:border-[#1C75BC] focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                >
                                    <option value="">All</option>
                                    <option v-for="option in subGroupFunctionFilterOptions" :key="option" :value="option">{{ option }}</option>
                                </select>
                            </th>
                            <th class="px-3 py-1.5 align-top">
                                <input
                                    v-model="subSubGroupSearch"
                                    type="text"
                                    placeholder="Search SubSubGroup..."
                                    class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-normal text-slate-700 outline-none focus:border-[#1C75BC] focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                />
                            </th>
                            <th class="px-3 py-1.5 align-top">
                                <select
                                    v-model="selectedGroupBusiness"
                                    class="w-full min-w-[170px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-normal text-slate-700 outline-none focus:border-[#1C75BC] focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                >
                                    <option value="">All</option>
                                    <option v-for="option in groupBusinessFilterOptions" :key="option" :value="option">{{ option }}</option>
                                </select>
                            </th>
                            <th class="px-3 py-1.5 align-top">
                                <div class="flex flex-nowrap items-center justify-end gap-1 whitespace-nowrap">
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-[#1C75BC] bg-[#1C75BC] px-2 py-1 text-[10px] font-semibold text-white transition hover:bg-[#165f98]"
                                        @click="toggleCreateRow"
                                    >
                                        {{ showCreateRow ? 'Tutup' : 'Tambah' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10"
                                        @click="resetFilters"
                                    >
                                        Reset
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#171717]">
                        <tr v-if="showCreateRow" class="bg-slate-50/60 dark:bg-white/5">
                            <td class="px-3 py-2 align-top">
                                <CapabilitySelect
                                    v-model="createForm.group_function"
                                    :options="groupFunctionOptions"
                                    placeholder="Group Function"
                                    :error="createForm.errors.group_function"
                                />
                            </td>
                            <td class="px-3 py-2 align-top">
                                <CapabilitySelect
                                    v-model="createForm.subGroup_function"
                                    :options="subGroupFunctionOptions"
                                    placeholder="SubGroup Function"
                                    :error="createForm.errors.subGroup_function"
                                />
                            </td>
                            <td class="px-3 py-2 align-top">
                                <input
                                    v-model="createForm.subSubGroup_function"
                                    type="text"
                                    placeholder="SubSubGroup Function"
                                    class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                />
                                <p v-if="createForm.errors.subSubGroup_function" class="mt-1 text-[10px] text-rose-500">
                                    {{ createForm.errors.subSubGroup_function }}
                                </p>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <CapabilitySelect
                                    v-model="createForm.group_business"
                                    :options="groupBusinessOptions"
                                    placeholder="Group Business Function"
                                    :error="createForm.errors.group_business"
                                />
                            </td>
                            <td class="px-3 py-2 align-top">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-slate-300 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 dark:hover:bg-white/10"
                                        :disabled="createForm.processing"
                                        @click="createRow"
                                    >
                                        Add
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/10"
                                        @click="resetCreate"
                                    >
                                        Clear
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-for="item in filteredRows" :key="item.id" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="px-3 py-2.5 align-top text-slate-700 dark:text-slate-200">
                                <CapabilitySelect
                                    v-if="isEditing(item)"
                                    v-model="editForm.group_function"
                                    :options="groupFunctionOptions"
                                    placeholder="Group Function"
                                    :error="editForm.errors.group_function"
                                />
                                <template v-else>{{ displayVal(item.group_function) }}</template>
                            </td>
                            <td class="px-3 py-2.5 align-top text-slate-700 dark:text-slate-200">
                                <CapabilitySelect
                                    v-if="isEditing(item)"
                                    v-model="editForm.subGroup_function"
                                    :options="subGroupFunctionOptions"
                                    placeholder="SubGroup Function"
                                    :error="editForm.errors.subGroup_function"
                                />
                                <template v-else>{{ displayVal(item.subGroup_function) }}</template>
                            </td>
                            <td class="px-3 py-2.5 align-top text-slate-600 dark:text-slate-300">
                                <template v-if="isEditing(item)">
                                    <input
                                        v-model="editForm.subSubGroup_function"
                                        type="text"
                                        class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    />
                                    <p v-if="editForm.errors.subSubGroup_function" class="mt-1 text-[10px] text-rose-500">
                                        {{ editForm.errors.subSubGroup_function }}
                                    </p>
                                </template>
                                <template v-else>{{ displayVal(item.subSubGroup_function) }}</template>
                            </td>
                            <td class="px-3 py-2.5 align-top text-slate-600 dark:text-slate-300">
                                <CapabilitySelect
                                    v-if="isEditing(item)"
                                    v-model="editForm.group_business"
                                    :options="groupBusinessOptions"
                                    placeholder="Group Business Function"
                                    :error="editForm.errors.group_business"
                                />
                                <template v-else>{{ displayVal(item.group_business) }}</template>
                            </td>
                            <td class="px-3 py-2.5 align-top">
                                <div v-if="isEditing(item)" class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-slate-300 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 dark:hover:bg-white/10"
                                        :disabled="editForm.processing"
                                        @click="saveEdit"
                                    >
                                        Save
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/10"
                                        @click="cancelEdit"
                                    >
                                        Cancel
                                    </button>
                                </div>
                                <div v-else class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-slate-300 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 dark:hover:bg-white/10"
                                        @click="startEdit(item)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex rounded border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20"
                                        @click="confirmDelete(item)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="filteredRows.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-500">Belum ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ConfirmationModal
            :show="showDeleteModal"
            title="Hapus Business Capability"
            :message="deleteMessage"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="deleteForm.processing"
            @close="showDeleteModal = false"
            @confirm="executeDelete"
        />
    </section>
</template>

<script setup>
import { computed, defineComponent, h, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import BusinessCapabilityMap from './BusinessCapabilityMap.vue';

const CapabilitySelect = defineComponent({
    props: {
        modelValue: { type: String, default: '' },
        options: { type: Array, default: () => [] },
        placeholder: { type: String, default: 'Select' },
        error: { type: String, default: '' },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        return () => h('div', [
            h('select', {
                value: props.modelValue,
                class: 'w-full min-w-[160px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200',
                onChange: event => emit('update:modelValue', event.target.value),
            }, [
                h('option', { value: '', disabled: true }, props.placeholder),
                ...props.options.map(option => h('option', { value: option, key: option }, option)),
            ]),
            props.error ? h('p', { class: 'mt-1 text-[10px] text-rose-500' }, props.error) : null,
        ]);
    },
});

const props = defineProps({
    businessCapabilities: { type: Array, default: () => [] },
});

const route = useRouteHelper();
const viewMode = ref('map');

const groupBusinessOptions = [
    'Exploration & Production',
    'Gas & Pipeline Management',
    'Refining & Petrochemical',
    'Renewable Energy',
];

const groupFunctionOptions = [
    'Core Capability',
    'Enterprise Capability',
];

const subGroupFunctionOptions = [
    'Administrative Support & Services Management',
    'Development & Production Management',
    'Distribution & Storage',
    'Energy Analysis and Consulting',
    'Energy Efficiency Management',
    'Energy Regulations and Policies',
    'Enterprise Effectiveness Management & Business Intelligent*',
    'Environmental and Regulatory Compliance',
    'Exploration & Appraisal Management',
    'Exploration & Production',
    'Finance & Accounting Management',
    'Fix Asset Management',
    'Governance, Risk & Compliance Management',
    'HSSE Management',
    'Human Resource Management',
    'ICT Management',
    'Operation Management',
    'Processing and Treatment',
    'Refinery Business Planning and Performance Management',
    'Refinery Development & Project Management',
    'Refinery Production & Operation',
    'Reliability & Maintenance Management',
    'Product Management',
    'Renewable Energy Project Development',
    'Renewable Energy Technicians and Engineers',
    'Research & Knowledge Management',
    'Supply Chain Management',
    'Sustainability and Environment',
    'Technology Research and Innovation Dev.',
    'Transportation',
];

const businessCapabilityRows = computed(() => props.businessCapabilities);
const subSubGroupSearch = ref('');
const selectedGroupBusiness = ref('');
const selectedGroupFunction = ref('');
const selectedSubGroupFunction = ref('');
const showCreateRow = ref(false);
const editingId = ref(null);
const showDeleteModal = ref(false);
const deleteTarget = ref(null);

const createForm = useForm({
    group_business: '',
    group_function: '',
    subGroup_function: '',
    subSubGroup_function: '',
});

const editForm = useForm({
    group_business: '',
    group_function: '',
    subGroup_function: '',
    subSubGroup_function: '',
});

const deleteForm = useForm({});

const buildFilterOptions = (...optionGroups) => [...new Set(
    optionGroups
        .flat()
        .filter(option => option !== null && option !== undefined && String(option).trim() !== ''),
)].sort((left, right) => String(left).localeCompare(String(right)));

const groupBusinessFilterOptions = computed(() => buildFilterOptions(
    groupBusinessOptions,
    businessCapabilityRows.value.map(item => item.group_business),
));

const groupFunctionFilterOptions = computed(() => buildFilterOptions(
    groupFunctionOptions,
    businessCapabilityRows.value.map(item => item.group_function),
));

const subGroupFunctionFilterOptions = computed(() => buildFilterOptions(
    subGroupFunctionOptions,
    businessCapabilityRows.value.map(item => item.subGroup_function),
));

const normalizeText = value => String(value ?? '').trim().toLowerCase();

const filteredRows = computed(() => {
    const subSubGroupKeyword = normalizeText(subSubGroupSearch.value);

    return businessCapabilityRows.value.filter((item) => {
        if (selectedGroupBusiness.value && item.group_business !== selectedGroupBusiness.value) return false;
        if (selectedGroupFunction.value && item.group_function !== selectedGroupFunction.value) return false;
        if (selectedSubGroupFunction.value && item.subGroup_function !== selectedSubGroupFunction.value) return false;
        if (!subSubGroupKeyword) return true;

        return normalizeText(item.subSubGroup_function).includes(subSubGroupKeyword);
    });
});

const resetCreate = () => {
    createForm.reset();
    createForm.clearErrors();
};

const toggleCreateRow = () => {
    showCreateRow.value = !showCreateRow.value;
    if (!showCreateRow.value) resetCreate();
};

const resetFilters = () => {
    subSubGroupSearch.value = '';
    selectedGroupBusiness.value = '';
    selectedGroupFunction.value = '';
    selectedSubGroupFunction.value = '';
};

const createRow = () => {
    createForm.post(route('itom.business-process.business-capability.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetCreate();
            showCreateRow.value = false;
        },
    });
};

const startEdit = (item) => {
    editingId.value = item.id;
    editForm.group_business = item.group_business ?? '';
    editForm.group_function = item.group_function ?? '';
    editForm.subGroup_function = item.subGroup_function ?? '';
    editForm.subSubGroup_function = item.subSubGroup_function ?? '';
    editForm.clearErrors();
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const saveEdit = () => {
    if (!editingId.value) return;

    editForm.put(route('itom.business-process.business-capability.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
};

const confirmDelete = (item) => {
    deleteTarget.value = item;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;

    deleteForm.delete(route('itom.business-process.business-capability.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteTarget.value = null;
        },
    });
};

const isEditing = item => editingId.value === item.id;

const displayVal = (value) => {
    if (value === null || value === undefined || String(value).trim() === '') return '-';
    return value;
};

const deleteMessage = computed(() => {
    if (!deleteTarget.value) return 'Apakah Anda yakin ingin menghapus data ini?';

    const label =
        deleteTarget.value.group_business ||
        deleteTarget.value.group_function ||
        deleteTarget.value.subGroup_function ||
        deleteTarget.value.subSubGroup_function ||
        'data';

    return `Apakah Anda yakin ingin menghapus "${label}"?`;
});
</script>
