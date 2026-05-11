<template>
    <UserLayout title="Master Data — Business Capability">
        <div class="animate-fade-in-up space-y-3">
            <section class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Business Capability Maps</h1>
                </div>
            </section>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-white/10 dark:bg-[#171717]">
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
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
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
                                        <option v-for="option in groupFunctionFilterOptions" :key="option" :value="option">
                                            {{ option }}
                                        </option>
                                    </select>
                                </th>
                                <th class="px-3 py-1.5 align-top">
                                    <select
                                        v-model="selectedSubGroupFunction"
                                        class="w-full min-w-[170px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-normal text-slate-700 outline-none focus:border-[#1C75BC] focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    >
                                        <option value="">All</option>
                                        <option v-for="option in subGroupFunctionFilterOptions" :key="option" :value="option">
                                            {{ option }}
                                        </option>
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
                                        <option v-for="option in groupBusinessFilterOptions" :key="option" :value="option">
                                            {{ option }}
                                        </option>
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
                        <!-- Create row (WYSIWYG) -->
                        <tr v-if="showCreateRow" class="bg-slate-50/60 dark:bg-white/5">
                            <td class="px-3 py-2 align-top">
                                <select
                                    v-model="createForm.group_function"
                                    class="w-full min-w-[160px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                >
                                    <option value="" disabled>Group Function (Level 1)</option>
                                    <option v-for="opt in groupFunctionOptions" :key="opt" :value="opt">{{ opt }}</option>
                                </select>
                                <p v-if="createForm.errors.group_function" class="mt-1 text-[10px] text-rose-500">
                                    {{ createForm.errors.group_function }}
                                </p>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <select
                                    v-model="createForm.subGroup_function"
                                    class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                >
                                    <option value="" disabled>SubGroup Function</option>
                                    <option v-for="opt in subGroupFunctionOptions" :key="opt" :value="opt">{{ opt }}</option>
                                </select>
                                <p v-if="createForm.errors.subGroup_function" class="mt-1 text-[10px] text-rose-500">
                                    {{ createForm.errors.subGroup_function }}
                                </p>
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
                                <select
                                    v-model="createForm.group_business"
                                    class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                >
                                    <option value="" disabled>Group Business Function</option>
                                    <option v-for="opt in groupBusinessOptions" :key="opt" :value="opt">{{ opt }}</option>
                                </select>
                                <p v-if="createForm.errors.group_business" class="mt-1 text-[10px] text-rose-500">
                                    {{ createForm.errors.group_business }}
                                </p>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <div class="flex items-center gap-1">
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
                                <template v-if="isEditing(item)">
                                    <select
                                        v-model="editForm.group_function"
                                        class="w-full min-w-[160px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    >
                                        <option value="" disabled>Group Function</option>
                                        <option v-for="opt in groupFunctionOptions" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                    <p v-if="editForm.errors.group_function" class="mt-1 text-[10px] text-rose-500">
                                        {{ editForm.errors.group_function }}
                                    </p>
                                </template>
                                <template v-else>{{ displayVal(item.group_function) }}</template>
                            </td>
                            <td class="px-3 py-2.5 align-top text-slate-700 dark:text-slate-200">
                                <template v-if="isEditing(item)">
                                    <select
                                        v-model="editForm.subGroup_function"
                                        class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    >
                                        <option value="" disabled>SubGroup Function</option>
                                        <option v-for="opt in subGroupFunctionOptions" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                    <p v-if="editForm.errors.subGroup_function" class="mt-1 text-[10px] text-rose-500">
                                        {{ editForm.errors.subGroup_function }}
                                    </p>
                                </template>
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
                                <template v-if="isEditing(item)">
                                    <select
                                        v-model="editForm.group_business"
                                        class="w-full min-w-[180px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    >
                                        <option value="" disabled>Group Business Function</option>
                                        <option v-for="opt in groupBusinessOptions" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                    <p v-if="editForm.errors.group_business" class="mt-1 text-[10px] text-rose-500">
                                        {{ editForm.errors.group_business }}
                                    </p>
                                </template>
                                <template v-else>{{ displayVal(item.group_business) }}</template>
                            </td>
                            <td class="px-3 py-2.5 align-top">
                                <div v-if="isEditing(item)" class="flex items-center gap-1">
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
                                <div v-else class="flex items-center gap-1">
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
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

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
    'Refinery Development & Project Management  ',
    'Refinery Production & Operation',
    'Reliability & Maintenance Management',
    'Product Management',
    'Operation Management',
    'Renewable Energy Project Development',
    'Renewable Energy Technicians and Engineers',
    'Research & Knowledge Management',
    'Supply Chain Management',
    'Sustainability and Environment',
    'Technology Research and Innovation Dev.',
    'Transportation',
];

const route = useRouteHelper();

const props = defineProps({
    businessCapabilities: { type: Array, default: () => [] },
});

const businessCapabilityRows = computed(() => props.businessCapabilities);
const subSubGroupSearch = ref('');
const selectedGroupBusiness = ref('');
const selectedGroupFunction = ref('');
const selectedSubGroupFunction = ref('');
const showCreateRow = ref(false);

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

const editingId = ref(null);
const showDeleteModal = ref(false);
const deleteTarget = ref(null);
const deleteForm = useForm({});

const buildFilterOptions = (...optionGroups) => {
    return [...new Set(
        optionGroups
            .flat()
            .filter((option) => option !== null && option !== undefined && String(option).trim() !== ''),
    )].sort((left, right) => String(left).localeCompare(String(right)));
};

const groupBusinessFilterOptions = computed(() => {
    return buildFilterOptions(
        groupBusinessOptions,
        businessCapabilityRows.value.map((item) => item.group_business),
    );
});

const groupFunctionFilterOptions = computed(() => {
    return buildFilterOptions(
        groupFunctionOptions,
        businessCapabilityRows.value.map((item) => item.group_function),
    );
});

const subGroupFunctionFilterOptions = computed(() => {
    return buildFilterOptions(
        subGroupFunctionOptions,
        businessCapabilityRows.value.map((item) => item.subGroup_function),
    );
});

const normalizeText = (value) => String(value ?? '').trim().toLowerCase();

const filteredRows = computed(() => {
    const subSubGroupKeyword = normalizeText(subSubGroupSearch.value);

    return businessCapabilityRows.value.filter((item) => {
        if (selectedGroupBusiness.value && item.group_business !== selectedGroupBusiness.value) {
            return false;
        }

        if (selectedGroupFunction.value && item.group_function !== selectedGroupFunction.value) {
            return false;
        }

        if (selectedSubGroupFunction.value && item.subGroup_function !== selectedSubGroupFunction.value) {
            return false;
        }

        if (!subSubGroupKeyword) {
            return true;
        }

        return normalizeText(item.subSubGroup_function).includes(subSubGroupKeyword);
    });
});

const resetCreate = () => {
    createForm.reset();
    createForm.clearErrors();
};

const toggleCreateRow = () => {
    showCreateRow.value = !showCreateRow.value;

    if (!showCreateRow.value) {
        resetCreate();
    }
};

const resetFilters = () => {
    subSubGroupSearch.value = '';
    selectedGroupBusiness.value = '';
    selectedGroupFunction.value = '';
    selectedSubGroupFunction.value = '';
};

const createRow = () => {
    createForm.post(route('master-data.business-capabilities.store'), {
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

    editForm.put(route('master-data.business-capabilities.update', editingId.value), {
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

    deleteForm.delete(route('master-data.business-capabilities.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteTarget.value = null;
        },
    });
};

const isEditing = (item) => editingId.value === item.id;
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
