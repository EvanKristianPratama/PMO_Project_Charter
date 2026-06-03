<template>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="bg-[#0b2545] px-4 py-3 flex items-center justify-between">
            <h2 class="text-xs font-semibold tracking-wider text-white uppercase">IT Steering Commitee</h2>
            
            <button 
                @click="isManageMode = !isManageMode"
                class="inline-flex items-center gap-1 rounded bg-white/15 hover:bg-white/25 text-white px-2.5 py-1 text-xs font-semibold shadow-sm transition-all focus:outline-none border border-white/10"
            >
                <svg v-if="isManageMode" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ isManageMode ? 'Lihat Bagan' : 'Kelola Data' }}</span>
            </button>
        </div>

        <!-- Empty State (Non-Manage Mode) -->
        <div v-if="!isManageMode && (!steeringRows || steeringRows.length === 0)" class="px-2 py-8 text-center bg-[#e8edf2] dark:bg-[#1e2530]">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a5.97 5.97 0 00-.94 3.197M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Data IT Steering Committee</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada data IT Steering Committee.</p>
        </div>

        <!-- 1. VIEW MODE: Diagram Canvas -->
        <div v-else-if="!isManageMode" class="w-full overflow-x-auto bg-[#e8edf2] dark:bg-[#1e2530] px-4 py-6 flex justify-center">
            <div class="relative w-[800px] h-[300px] flex-shrink-0 select-none">
                
                <!-- SVG Connector Lines -->
                <svg class="absolute inset-0 w-full h-full pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Main Vertical Line from Ketua to Anggota Tetap -->
                    <line x1="210" y1="80" x2="210" y2="180" stroke="#0b3350" stroke-width="2" class="dark:stroke-slate-400" />
                    
                    <!-- Horizontal Line to Sekretaris -->
                    <line x1="210" y1="95" x2="440" y2="95" stroke="#0b3350" stroke-width="2" class="dark:stroke-slate-400" />
                    
                    <!-- Dotted Blue Line to Anggota Ad Hoc -->
                    <path d="M 210,145 L 580,145 L 580,180" stroke="#009fe3" stroke-width="2" stroke-dasharray="3,3" fill="none" />
                </svg>

                <!-- 1. Ketua Komite Card -->
                <div class="absolute left-[115px] top-[20px] w-[190px] h-[60px] bg-gradient-to-b from-[#114e7a] to-[#0b3452] rounded-lg shadow-md flex flex-col justify-center items-center text-white px-2 text-center border border-white/10">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider mb-0.5">Ketua Komite</h3>
                    <div class="text-[9.5px] font-light leading-snug">
                        <div v-for="member in ketuaMembers" :key="member.code">
                            {{ member.organization_name }}
                        </div>
                        <div v-if="ketuaMembers.length === 0" class="italic opacity-60">Direktur Utama</div>
                    </div>
                </div>

                <!-- 2. Sekretaris Komite Card -->
                <div class="absolute left-[440px] top-[62px] w-[280px] h-[66px] bg-gradient-to-b from-[#114e7a] to-[#0b3452] rounded-lg shadow-md flex flex-col justify-center items-center text-white px-3 text-center border border-white/10">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider mb-0.5">Sekretaris Komite</h3>
                    <div class="text-[9.5px] font-light leading-normal space-y-0.5 w-full">
                        <template v-if="sekretarisMembers.length > 0">
                            <div v-for="member in sekretarisMembers" :key="member.code" class="truncate w-full">
                                {{ member.organization_name }}
                            </div>
                        </template>
                        <template v-else>
                            <div class="truncate">SVP Enterprise IT</div>
                            <div class="truncate">VP Enterprise IT SARM</div>
                        </template>
                    </div>
                </div>

                <!-- 3. Anggota Tetap Komite Card -->
                <div class="absolute left-[65px] top-[180px] w-[290px] min-h-[75px] h-auto bg-gradient-to-b from-[#114e7a] to-[#0b3452] rounded-xl shadow-lg flex flex-col justify-start text-white p-3 border border-white/10">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-center mb-2">Anggota Tetap Komite</h3>
                    <div class="text-[9.5px] font-light space-y-1">
                        <template v-if="anggotaTetapMembers.length > 0">
                            <div v-for="(member, idx) in anggotaTetapMembers" :key="member.code" class="flex items-start gap-1">
                                <span class="font-semibold">{{ idx + 1 }}.</span>
                                <span>{{ member.organization_name }}</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-start gap-1"><span class="font-semibold">1.</span> <span>Direktur Penunjang Bisnis</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">2.</span> <span>Direktur Manajemen Risiko</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">3.</span> <span>Direktur Keuangan</span></div>
                        </template>
                    </div>
                </div>

                <!-- 4. Anggota Ad Hoc Card -->
                <div class="absolute left-[435px] top-[180px] w-[290px] min-h-[75px] h-auto bg-gradient-to-b from-[#009fe3] to-[#0082c3] border border-[#009fe3] rounded-xl shadow-lg flex flex-col justify-start text-white p-3">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-center mb-2">
                        Anggota Ad Hoc <sup class="text-[8px]">1</sup>
                    </h3>
                    <div class="text-[9.5px] font-light space-y-1">
                        <template v-if="anggotaAdHocMembers.length > 0">
                            <div v-for="(member, idx) in anggotaAdHocMembers" :key="member.code" class="flex items-start gap-1">
                                <span class="font-semibold">{{ idx + 1 }}.</span>
                                <span>{{ member.organization_name }}</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-start gap-1"><span class="font-semibold">1.</span> <span>Direktur Holding</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">2.</span> <span>Direktur Utama Subholding</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">3.</span> <span>Direktur Utama APFS</span></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MANAGE MODE: Table CRUD view -->
        <div v-else class="p-4 bg-slate-50 dark:bg-[#1f2530] space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-semibold text-slate-800 dark:text-white">{{ sortedRows.length }}</span> anggota steering committee.
                </p>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#0b2545] hover:bg-[#114e7a] text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Anggota
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white dark:border-white/10 dark:bg-[#171717]">
                <table class="w-full divide-y divide-slate-200 text-xs dark:divide-white/10">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider text-slate-500 w-12">No</th>
                            <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider text-slate-500">Jabatan / Nama</th>
                            <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider text-slate-500 w-32">Kode LLDDSSNN</th>
                            <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider text-slate-500 w-44">Tipe Peran</th>
                            <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr v-for="(row, idx) in sortedRows" :key="row.id" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 font-mono">{{ idx + 1 }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-800 dark:text-white">{{ row.organization_name }}</td>
                            <td class="px-4 py-2.5 font-mono text-slate-600 dark:text-slate-400">{{ row.code }}</td>
                            <td class="px-4 py-2.5 text-slate-600 dark:text-slate-400">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium"
                                    :class="getRoleBadgeClass(row.code)">
                                    {{ getRoleLabel(row.code) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center space-x-2">
                                <button
                                    @click="openEditModal(row)"
                                    class="text-blue-600 hover:text-blue-800 font-semibold dark:text-blue-400 dark:hover:text-blue-300 transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteModal(row)"
                                    class="text-red-600 hover:text-red-800 font-semibold dark:text-red-400 dark:hover:text-red-300 transition"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="sortedRows.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                Belum ada data anggota. Klik tombol "Tambah Anggota" untuk menambahkan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Form Input & Edit Modal (ConfirmationModal) -->
        <ConfirmationModal
            :show="isModalOpen"
            :title="modalMode === 'create' ? 'Tambah Anggota Steering Committee' : 'Edit Anggota Steering Committee'"
            message="Silakan lengkapi formulir di bawah ini dengan memilih organisasi serta menentukan perannya."
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            :loading="form.processing"
            @close="isModalOpen = false"
            @confirm="submitForm"
        >
            <div class="mt-4 space-y-4">
                <!-- Organization Dropdown -->
                <div class="flex flex-col gap-1.5">
                    <label for="organization_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Organisasi / Jabatan
                    </label>
                    <select
                        id="organization_id"
                        v-model="form.organization_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        required
                    >
                        <option value="" disabled>Pilih Organisasi...</option>
                        <option v-for="org in organizationOptions" :key="org.id" :value="org.id">
                            {{ org.jabatan || org.name }} ({{ org.name }})
                        </option>
                    </select>
                    <span v-if="form.errors.organization_id" class="text-xs text-red-500 font-medium">
                        {{ form.errors.organization_id }}
                    </span>
                </div>

                <!-- Role Selector -->
                <div class="flex flex-col gap-1.5">
                    <label for="role_prefix" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Peran Komite
                    </label>
                    <select
                        id="role_prefix"
                        v-model="selectedRolePrefix"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        required
                    >
                        <option value="100100">Ketua Komite (100100xx)</option>
                        <option value="100201">Sekretaris Komite (100201xx)</option>
                        <option value="100202">Anggota Tetap Komite (100202xx)</option>
                        <option value="100203">Anggota Ad Hoc (100203xx)</option>
                    </select>
                </div>

                <!-- Sequence Number (2-Digit) -->
                <div class="flex flex-col gap-1.5">
                    <label for="seq_num" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Nomor Urut (2 Digit, Contoh: 01, 02)
                    </label>
                    <input
                        id="seq_num"
                        v-model="seqNumber"
                        type="text"
                        maxlength="2"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white font-mono"
                        placeholder="01"
                        required
                    />
                    <span v-if="form.errors.code" class="text-xs text-red-500 font-medium">
                        {{ form.errors.code }}
                    </span>
                </div>

                <!-- Combined Code Live Preview -->
                <div class="rounded-lg bg-slate-100 dark:bg-white/5 p-3 flex justify-between items-center text-xs">
                    <span class="font-semibold text-slate-500">Live Preview Kode:</span>
                    <span class="font-mono text-sm font-bold text-blue-600 dark:text-blue-400">
                        {{ generatedCode }}
                    </span>
                </div>
            </div>
        </ConfirmationModal>

        <!-- 4. Delete Confirmation Modal -->
        <ConfirmationModal
            :show="isDeleteModalOpen"
            title="Hapus Anggota Steering Committee"
            :message="`Apakah Anda yakin ingin menghapus '${selectedItem?.organization_name}' dari kepengurusan IT Steering Committee?`"
            confirm-text="Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="form.processing"
            @close="isDeleteModalOpen = false"
            @confirm="submitDelete"
        />
    </section>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

defineOptions({
    name: 'ITSteeringComitte',
});

const props = defineProps({
    steeringRows: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

// Manage vs View State
const isManageMode = ref(false);

// Modal state variables
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const selectedItem = ref(null);

// Form prefix controls
const selectedRolePrefix = ref('100202'); // Default to Anggota Tetap
const seqNumber = ref('01'); // Default to 01

const form = useForm({
    organization_id: '',
    code: '',
});

// Live Preview code combination
const generatedCode = computed(() => {
    const rawSeq = seqNumber.value.trim().padEnd(2, '0').substring(0, 2);
    return `${selectedRolePrefix.value}${rawSeq}`;
});

// Sync compiled code to form value on change
watch(generatedCode, (newVal) => {
    form.code = newVal;
}, { immediate: true });

/**
 * Parse code LLDDSSNN:
 * LL = Level Organisasi (e.g. 10 = Komite IT)
 * DD = Level Ketua/Member (01 = Ketua, 02 = Member)
 * SS = Sub Struktur (01 = Sekretaris, 02 = Anggota Tetap, 03 = Anggota Ad Hoc)
 * NN = Nomor Urut (01-99)
 */
const parseCode = (code) => {
    const c = String(code ?? '').trim().padEnd(8, '0');
    return {
        ll: c.substring(0, 2),
        dd: c.substring(2, 4),
        ss: c.substring(4, 6),
        nn: c.substring(6, 8),
    };
};

const sortedRows = computed(() => {
    if (!props.steeringRows || props.steeringRows.length === 0) return [];
    return [...props.steeringRows].sort((a, b) => {
        const codeA = String(a.code ?? '').trim();
        const codeB = String(b.code ?? '').trim();
        return codeA.localeCompare(codeB);
    });
});

// DD=01 → Ketua
const ketuaMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd } = parseCode(row.code);
        return dd === '01';
    });
});

// DD=02, SS=01 → Sekretaris
const sekretarisMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd, ss } = parseCode(row.code);
        return dd === '02' && ss === '01';
    });
});

// DD=02, SS=02 → Anggota Tetap
const anggotaTetapMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd, ss } = parseCode(row.code);
        return dd === '02' && ss === '02';
    });
});

// DD=02, SS=03 → Anggota Ad Hoc
const anggotaAdHocMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd, ss } = parseCode(row.code);
        return dd === '02' && ss === '03';
    });
});

// Helpers to get roles info
const getRoleLabel = (code) => {
    const { dd, ss } = parseCode(code);
    if (dd === '01') return 'Ketua Komite';
    if (dd === '02') {
        if (ss === '01') return 'Sekretaris Komite';
        if (ss === '02') return 'Anggota Tetap Komite';
        if (ss === '03') return 'Anggota Ad Hoc';
    }
    return 'Lainnya';
};

const getRoleBadgeClass = (code) => {
    const { dd, ss } = parseCode(code);
    if (dd === '01') return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
    if (dd === '02') {
        if (ss === '01') return 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300';
        if (ss === '02') return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300';
        if (ss === '03') return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
    }
    return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300';
};

// CRUD handlers
const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    selectedRolePrefix.value = '100202';
    seqNumber.value = '01';
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    modalMode.value = 'edit';
    selectedItem.value = item;
    form.clearErrors();
    form.organization_id = item.organization_id || '';
    
    // Deconstruct code into prefix and sequence number
    const codeStr = String(item.code ?? '').trim().padEnd(8, '0');
    selectedRolePrefix.value = codeStr.substring(0, 6);
    seqNumber.value = codeStr.substring(6, 8);
    
    isModalOpen.value = true;
};

const openDeleteModal = (item) => {
    selectedItem.value = item;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('policy.organization.steering.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('policy.organization.steering.update', selectedItem.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('policy.organization.steering.destroy', selectedItem.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>