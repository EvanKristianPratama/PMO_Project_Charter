<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 px-4 py-3 gap-3 dark:border-white/10">
            <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-xs font-bold tracking-wider text-slate-700 dark:text-slate-200">
                    Employee Structure
                </h3>

                <!-- Search Input -->
                <div class="relative w-full sm:w-48">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari jabatan, pejabat..."
                        class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>
                <!-- Show BoD Toggle -->
                <label class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-slate-600 dark:text-slate-300 cursor-pointer select-none bg-slate-100 dark:bg-white/5 px-2 py-1 rounded-lg">
                    <input
                        type="checkbox"
                        v-model="showBod"
                        class="rounded border-slate-300 bg-white text-blue-500 focus:border-blue-500 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a]"
                    />
                    BoD
                </label>
            </div>
            <div class="flex items-center gap-2">
                <button
                    @click="openEmployeeModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-emerald-400 dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Employee
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-fixed">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-12">No</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Company</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Nama Organisasi</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-20">Alias</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Nama Jabatan</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-32">Regulation</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Parent</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Pejabat</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-24">Tipe</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-48">Sumber</th>
                        <th class="px-4 py-2 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(employee, index) in filteredEmployees"
                        :key="employee.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-left w-12">{{ index + 1 }}</td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ employee.company_name }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ employee.name }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left w-20">
                            {{ employee.alias || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ employee.nama_jabatan || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white text-left font-medium w-32" :title="getRegulationName(employee.regulation_id) || '-'">
                            <div class="break-words whitespace-normal">
                                {{ getRegulationName(employee.regulation_id) || '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ getParentName(employee.parent_id) || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white text-left font-medium">
                            {{ employee.pejabat || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 text-left">
                            {{ employee.tipe || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white text-left font-medium w-48" :title="employee.sumber || '-'">
                            <div class="break-words whitespace-normal">
                                {{ employee.sumber || '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center w-40">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditEmployeeModal(employee)"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteEmployeeModal(employee)"
                                    class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredEmployees.length === 0">
                        <td colspan="11" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 mb-4 border border-slate-200 dark:border-white/10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Employee Not Available
                                </h4>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>

    <!-- Create/Edit Employee Modal -->
    <ConfirmationModal
        :show="isEmployeeModalOpen"
        :title="employeeModalMode === 'create' ? 'Tambah Employee' : 'Edit Employee'"
        message=""
        confirm-text="Save"
        cancel-text="Cancel"
        type="info"
        :loading="employeeForm.processing"
        @close="isEmployeeModalOpen = false"
        @confirm="submitEmployeeForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Company Selection -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_company" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company</label>
                <select
                    id="employee_company"
                    v-model="employeeForm.company_id"
                    :disabled="!!selectedCompanyId"
                    :class="[
                        'w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-1',
                        selectedCompanyId
                            ? 'border-slate-300 bg-slate-100 text-slate-500 cursor-not-allowed dark:border-white/10 dark:bg-[#1a1a1a]/50 dark:text-slate-400'
                            : 'border-slate-300 bg-white text-slate-900 focus:border-slate-500 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white'
                    ]"
                    required
                >
                    <option value="" disabled>Pilih Company...</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
                    </option>
                </select>
                <span v-if="employeeForm.errors.company_id" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.company_id }}</span>
            </div>

            <!-- Parent Employee Selection -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_parent" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Parent Jabatan <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select
                    id="employee_parent"
                    v-model="employeeForm.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">-- Tidak ada parent (jabatan tertinggi) --</option>
                    <option
                        v-for="parentEmployee in availableParents"
                        :key="parentEmployee.id"
                        :value="parentEmployee.id"
                    >
                        [{{ getCompanyName(parentEmployee.company_id) }}] {{ parentEmployee.name }}{{ parentEmployee.alias ? ` (${parentEmployee.alias})` : '' }}
                    </option>
                </select>
                <span v-if="employeeForm.errors.parent_id" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.parent_id }}</span>
            </div>

            <!-- Regulation Selection -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_regulation" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Regulation <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select
                    id="employee_regulation"
                    v-model="employeeForm.regulation_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">-- Tanpa Regulation --</option>
                    <option
                        v-for="reg in regulations"
                        :key="reg.id"
                        :value="reg.id"
                    >
                        {{ reg.nomor ? `${reg.nomor} - ${reg.judul}` : reg.judul }}
                    </option>
                </select>
                <span v-if="employeeForm.errors.regulation_id" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.regulation_id }}</span>
            </div>

            <!-- Employee Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Struktur Organisasi</label>
                <input
                    id="employee_name"
                    v-model="employeeForm.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Manager Hulu / Supervisor IT"
                    required
                />
                <span v-if="employeeForm.errors.name" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.name }}</span>
            </div>

            <!-- Employee Nama Jabatan Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_nama_jabatan" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Jabatan</label>
                <input
                    id="employee_nama_jabatan"
                    v-model="employeeForm.nama_jabatan"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Manager Hulu / Supervisor IT"
                />
                <span v-if="employeeForm.errors.nama_jabatan" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.nama_jabatan }}</span>
            </div>

            <!-- Employee Alias Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_alias" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Alias</label>
                <input
                    id="employee_alias"
                    v-model="employeeForm.alias"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: MGR-HULU"
                />
                <span v-if="employeeForm.errors.alias" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.alias }}</span>
            </div>

            <!-- Employee Tipe Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_tipe" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tipe</label>
                <input
                    id="employee_tipe"
                    v-model="employeeForm.tipe"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-500 cursor-not-allowed dark:border-white/10 dark:bg-[#1a1a1a]/50 dark:text-slate-400"
                    disabled
                />
                <span v-if="employeeForm.errors.tipe" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.tipe }}</span>
            </div>

            <!-- Employee Role Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_role_function" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Role</label>
                <select
                    id="employee_role_function"
                    v-model="employeeForm.role_function"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">-- Tidak ada --</option>
                    <option value="wakil">Wakil</option>
                    <option value="fungsi">Fungsi</option>
                </select>
                <span v-if="employeeForm.errors.role_function" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.role_function }}</span>
            </div>

            <!-- Employee Pejabat Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_pejabat" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Pejabat (Official)</label>
                <input
                    id="employee_pejabat"
                    v-model="employeeForm.pejabat"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Ir. Budi Santoso"
                />
                <span v-if="employeeForm.errors.pejabat" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.pejabat }}</span>
            </div>

            <!-- Employee Sumber Input -->
            <div class="flex flex-col gap-1.5">
                <label for="employee_sumber" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Sumber</label>
                <input
                    id="employee_sumber"
                    v-model="employeeForm.sumber"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: SK Direksi / Internal"
                />
                <span v-if="employeeForm.errors.sumber" class="text-xs text-red-500 font-medium">{{ employeeForm.errors.sumber }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Employee Confirmation Modal -->
    <ConfirmationModal
        :show="isEmployeeDeleteModalOpen"
        title="Hapus Employee"
        :message="`Apakah Anda yakin ingin menghapus '${selectedEmployee?.name}${selectedEmployee?.alias ? ` (${selectedEmployee.alias})` : ''}' dari Employee Structure?`"
        confirm-text="Delete"
        cancel-text="Cancel"
        type="danger"
        :loading="employeeForm.processing"
        @close="isEmployeeDeleteModalOpen = false"
        @confirm="submitDeleteEmployee"
    />
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
    initialCompanyId: {
        type: [String, Number],
        default: '',
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

const selectedCompanyId = ref('');
const showBod = ref(true);
const searchQuery = ref('');

watch(() => props.initialCompanyId, (newVal) => {
    selectedCompanyId.value = newVal || '';
}, { immediate: true });

const employeesOnly = computed(() => {
    return props.bods.filter(bod => {
        const tipeLower = bod.tipe ? String(bod.tipe).toLowerCase() : '';
        if (tipeLower === 'employee') return true;
        if (showBod.value && tipeLower === 'bod') return true;
        return false;
    });
});

const filteredEmployees = computed(() => {
    let result = employeesOnly.value;

    if (selectedCompanyId.value) {
        result = result.filter(employee => Number(employee.company_id) === Number(selectedCompanyId.value));
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        result = result.filter(employee => {
            return (
                (employee.name || '').toLowerCase().includes(query) ||
                (employee.pejabat || '').toLowerCase().includes(query) ||
                (employee.company_name || '').toLowerCase().includes(query) ||
                (employee.tipe || '').toLowerCase().includes(query) ||
                (employee.sumber || '').toLowerCase().includes(query)
            );
        });
    }

    return result;
});

/**
 * Kandidat parent: Semua Employee dari semua company, exclude diri sendiri (saat edit).
 * Ditampilkan dengan informasi nama company untuk konteks.
 */
const availableParents = computed(() => {
    return employeesOnly.value.filter(employee => {
        // Hanya tampilkan data MstBod pada company yg dipilih
        if (selectedCompanyId.value && Number(employee.company_id) !== Number(selectedCompanyId.value)) return false;
        // Saat edit, exclude diri sendiri dari pilihan parent
        if (employeeModalMode.value === 'edit' && selectedEmployee.value && employee.id === selectedEmployee.value.id) return false;
        return true;
    });
});

/**
 * Dapatkan nama company berdasarkan company_id.
 */
const getCompanyName = (companyId) => {
    if (!companyId) return '-';
    const company = props.companies.find(c => Number(c.id) === Number(companyId));
    return company ? company.name : '-';
};

/**
 * Dapatkan nama jabatan parent berdasarkan parent_id.
 */
const getParentName = (parentId) => {
    if (!parentId) return null;
    const parent = employeesOnly.value.find(b => b.id === Number(parentId));
    return parent ? (parent.alias ? `${parent.name} (${parent.alias})` : parent.name) : null;
};

/**
 * Dapatkan nama/nomor regulation berdasarkan regulation_id.
 */
const getRegulationName = (regulationId) => {
    if (!regulationId) return null;
    const reg = props.regulations.find(r => Number(r.id) === Number(regulationId));
    return reg ? (reg.nomor ? `${reg.nomor} - ${reg.judul}` : reg.judul) : null;
};

const isEmployeeModalOpen = ref(false);
const isEmployeeDeleteModalOpen = ref(false);
const employeeModalMode = ref('create'); // 'create' or 'edit'
const selectedEmployee = ref(null);

const employeeForm = useForm({
    company_id: '',
    parent_id: '',
    name: '',
    nama_jabatan: '',
    alias: '',
    sumber: '',
    pejabat: '',
    tipe: '',
    role_function: '',
    regulation_id: '',
});

const openEmployeeModal = () => {
    employeeModalMode.value = 'create';
    selectedEmployee.value = null;
    employeeForm.clearErrors();
    employeeForm.reset();
    employeeForm.tipe = 'employee';
    employeeForm.role_function = '';
    employeeForm.regulation_id = '';
    if (selectedCompanyId.value) {
        employeeForm.company_id = selectedCompanyId.value;
    }
    isEmployeeModalOpen.value = true;
};

const openEditEmployeeModal = (employee) => {
    employeeModalMode.value = 'edit';
    selectedEmployee.value = employee;
    employeeForm.clearErrors();
    employeeForm.company_id = employee.company_id;
    employeeForm.parent_id = employee.parent_id ?? '';
    employeeForm.name = employee.name;
    employeeForm.nama_jabatan = employee.nama_jabatan || '';
    employeeForm.alias = employee.alias || '';
    employeeForm.sumber = employee.sumber || '';
    employeeForm.pejabat = employee.pejabat || '';
    employeeForm.tipe = employee.tipe || 'employee';
    employeeForm.role_function = employee.role_function || '';
    employeeForm.regulation_id = employee.regulation_id ?? '';
    isEmployeeModalOpen.value = true;
};

const openDeleteEmployeeModal = (employee) => {
    selectedEmployee.value = employee;
    isEmployeeDeleteModalOpen.value = true;
};

const submitEmployeeForm = () => {
    if (employeeModalMode.value === 'create') {
        employeeForm.post(route('itom.business-process.organization-structure.bod.store'), {
            onSuccess: () => {
                isEmployeeModalOpen.value = false;
                employeeForm.reset();
            },
        });
    } else {
        employeeForm.put(route('itom.business-process.organization-structure.bod.update', selectedEmployee.value.id), {
            onSuccess: () => {
                isEmployeeModalOpen.value = false;
                employeeForm.reset();
            },
        });
    }
};

const submitDeleteEmployee = () => {
    employeeForm.delete(route('itom.business-process.organization-structure.bod.destroy', selectedEmployee.value.id), {
        onSuccess: () => {
            isEmployeeDeleteModalOpen.value = false;
        },
    });
};
</script>
