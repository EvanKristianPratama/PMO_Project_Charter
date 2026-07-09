<template>
    <div class="space-y-6">
        <!-- Single Card containing both Controls Header and Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Header/Controls Section -->
            <div class="flex flex-row items-center justify-between gap-3 px-5 py-3 border-b border-slate-200 dark:border-white/10 flex-wrap">
                <!-- Left Section: Search and Filter dropdown -->
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[180px] max-w-xs">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="pointer-events-none absolute inset-y-0 left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                            />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari kebijakan umum..."
                            class="w-full appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-8 pr-8 py-1.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            type="button"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="w-3.5 h-3.5"
                            >
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Right Section: Add Action -->
                <div class="flex items-center gap-2 shrink-0">
                    <button
                        @click="openAddModal"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95 cursor-pointer"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3.5 h-3.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                        Tambah Kebijakan
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/5 dark:bg-[#1f1f1f]/50 dark:text-slate-400">
                        <tr class="divide-x divide-slate-200 dark:divide-white/10">
                            <th class="px-5 py-3 w-16 text-center">No</th>
                            <th class="px-5 py-3">Kebijakan Umum</th>
                            <th class="px-5 py-3 w-32 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300 font-medium">
                        <tr v-if="filteredPolicies.length === 0">
                            <td colspan="3" class="px-5 py-8 text-center text-slate-400 font-medium dark:text-slate-500">
                                Tidak ada data kebijakan umum ditemukan.
                            </td>
                        </tr>
                        <tr
                            v-for="(policy, index) in filteredPolicies"
                            :key="policy.id"
                            class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition duration-150 divide-x divide-slate-200 dark:divide-white/10"
                        >
                            <td class="px-5 py-3.5 text-center align-middle font-mono font-bold text-slate-900 dark:text-white w-16">
                                {{ policy.number }}
                            </td>
                            <td class="px-5 py-3.5 align-middle text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">
                                {{ formatDescription(policy.description) }}
                            </td>
                            <td class="px-5 py-2 align-middle text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        @click="openEditModal(policy)"
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                        title="Edit Kebijakan"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deletePolicy(policy)"
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:border-red-300 active:scale-95 dark:border-red-500/20 dark:bg-[#1a1a1a] dark:text-red-400 dark:hover:bg-red-500/10"
                                        title="Hapus Kebijakan"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dialog Modal Form (Add & Edit) -->
        <ConfirmationModal
            :show="isModalOpen"
            :title="editingId ? 'Edit Kebijakan Umum' : 'Tambah Kebijakan Umum'"
            :message="editingId ? 'Perbarui detail butir kebijakan umum di bawah ini.' : 'Isi formulir di bawah ini untuk menambahkan kebijakan umum baru.'"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            :loading="form.processing"
            @close="closeModal"
            @confirm="submitForm"
        >
            <div class="mt-4 space-y-4 text-left">
                <!-- Number Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="number" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Nomor Urut Kebijakan <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.number"
                        type="number"
                        id="number"
                        required
                        min="1"
                        placeholder="Contoh: 1"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                    />
                    <div v-if="form.errors.number" class="text-xs text-rose-500 font-medium">{{ form.errors.number }}</div>
                </div>

                <!-- Description Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="description" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Deskripsi Kebijakan <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.description"
                        id="description"
                        rows="5"
                        required
                        placeholder="Tuliskan isi butir kebijakan di sini..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition leading-relaxed"
                    ></textarea>
                    <div v-if="form.errors.description" class="text-xs text-rose-500 font-medium">{{ form.errors.description }}</div>
                </div>
            </div>
        </ConfirmationModal>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    policies: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: [Number, String],
        default: null,
    },
});

const searchQuery = ref('');

// Form & Modal State
const isModalOpen = ref(false);
const editingId = ref(null);

const form = useForm({
    regulation_id: props.selectedRegulationId || '',
    number: '',
    description: '',
});

// Update form regulation_id when selectedRegulationId changes
watch(
    () => props.selectedRegulationId,
    (newVal) => {
        if (newVal) {
            form.regulation_id = newVal;
        }
    },
    { immediate: true }
);

// Filtering policies locally by search query
const filteredPolicies = computed(() => {
    let result = props.policies || [];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(
            (item) =>
                (item.number || '').toString().includes(q) ||
                (item.description || '').toLowerCase().includes(q)
        );
    }
    return result;
});

// Add Modal Operations
function openAddModal() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.regulation_id = props.selectedRegulationId || '';
    // Pre-fill next number logically
    let nextNum = 1;
    if (props.policies.length > 0) {
        nextNum = Math.max(...props.policies.map((p) => parseInt(p.number) || 0)) + 1;
    }
    form.number = nextNum;
    isModalOpen.value = true;
}

// Edit Modal Operations
function openEditModal(policy) {
    editingId.value = policy.id;
    form.regulation_id = policy.regulation_id || props.selectedRegulationId || '';
    form.number = policy.number;
    form.description = policy.description;
    form.clearErrors();
    isModalOpen.value = true;
}

// Close Modal
function closeModal() {
    isModalOpen.value = false;
    editingId.value = null;
    form.reset();
}

// Submit Form
function submitForm() {
    if (editingId.value) {
        form.put(route('itom.policy.general.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kebijakan Umum berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#2563eb',
                    timer: 2000,
                    timerProgressBar: true,
                });
            },
        });
    } else {
        form.post(route('itom.policy.general.store'), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kebijakan Umum berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#2563eb',
                    timer: 2000,
                    timerProgressBar: true,
                });
            },
        });
    }
}

// Delete General Policy
function deletePolicy(policy) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Kebijakan Umum No. ${policy.number}. Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('itom.policy.general.destroy', policy.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Kebijakan Umum berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb',
                        timer: 2000,
                        timerProgressBar: true,
                    });
                },
            });
        }
    });
}



// Format description sub-bullets helper
function formatDescription(text) {
    if (!text) return '';
    return text.replace(/\s+([a-z])([\.\)])\s+/g, '\n   $1$2 ');
}
</script>

<style scoped>
/* Scoped styles */
</style>
