<template>
    <div class="space-y-6">
        <!-- Single Card containing both Controls Header and Table -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
        >
            <!-- Header/Controls Section -->
            <div
                class="flex flex-row items-center justify-between gap-3 px-5 py-3 border-b border-slate-200 dark:border-white/10 flex-wrap"
            >
                <!-- Left Section: Filters grouped horizontally -->
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
                            placeholder="Cari istilah atau definisi..."
                            class="w-full appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10"
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

                    <!-- Filter Regulation Select -->
                    <div class="relative shrink-0">
                        <select
                            v-model="selectedRegulationId"
                            class="appearance-none bg-white text-slate-700 border border-slate-200 rounded-xl pl-2.5 pr-7 py-1.5 text-[11px] font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer w-40 truncate"
                        >
                            <option value="">Semua Regulasi</option>
                            <option
                                v-for="reg in regulations"
                                :key="reg.id"
                                :value="reg.id"
                            >
                                {{ reg.nomor ? reg.nomor + ' - ' : '' }}{{ reg.judul }}
                            </option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2.5"
                                stroke="currentColor"
                                class="w-3 h-3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Add Button -->
                <div class="flex items-center shrink-0">
                    <button
                        @click="openAddModal"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#6b1937] active:scale-95"
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
                        Add Definition
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/5 dark:bg-[#1f1f1f]/50 dark:text-slate-400"
                        >
                            <th class="px-5 py-3 w-16 text-center">No</th>
                            <th class="px-5 py-3 w-64">Term</th>
                            <th class="px-5 py-3">Definition</th>
                            <th class="px-5 py-3 w-72">Regulation</th>
                            <th class="px-5 py-3 w-28 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-300"
                    >
                        <tr v-if="filteredDefinitions.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-slate-400 dark:text-slate-500 font-medium"
                            >
                                Tidak ada data definisi ditemukan.
                            </td>
                        </tr>
                        <tr
                            v-for="(item, index) in filteredDefinitions"
                            :key="item.id"
                            class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition duration-150"
                        >
                            <td
                                class="px-5 py-3.5 text-center text-slate-400 font-semibold"
                            >
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-5 py-3.5 font-bold text-slate-900 dark:text-white"
                            >
                                {{ item.name }}
                            </td>
                            <td
                                class="px-5 py-3.5 whitespace-pre-wrap leading-relaxed text-slate-600 dark:text-slate-300"
                            >
                                {{ item.definition }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    v-if="!item.regulations || item.regulations.length === 0"
                                    class="text-[10px] text-slate-400 italic"
                                >—</span>
                                <ul v-else class="space-y-0.5">
                                    <li
                                        v-for="reg in item.regulations"
                                        :key="reg.id"
                                        class="flex items-start gap-1.5 text-[10px] text-slate-600 dark:text-slate-300 leading-snug"
                                    >
                                        <span class="shrink-0 mt-[3px] h-1 w-1 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                        <span>{{ reg.judul || reg.nomor || 'Regulation #' + reg.id }}</span>
                                    </li>
                                </ul>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div
                                    class="flex items-center justify-center gap-1.5"
                                >
                                    <button
                                        @click="openEditModal(item)"
                                        class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="confirmDelete(item)"
                                        class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                    >
                                        Delete
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
            :title="isEditing ? 'Edit Definition' : 'Add Definition'"
            :message="isEditing ? 'Update the definition details below.' : 'Fill in the form below to add a new definition.'"
            confirm-text="Save"
            cancel-text="Cancel"
            type="info"
            :loading="form.processing"
            @close="closeModal"
            @confirm="submitForm"
        >
            <div class="mt-4 space-y-4 text-left">
                <!-- Name Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Istilah / Term <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        id="name"
                        required
                        placeholder="Contoh: IT Governance"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                    />
                </div>

                <!-- Definition Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="definition" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Definisi / Arti <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.definition"
                        id="definition"
                        rows="4"
                        required
                        placeholder="Tuliskan pengertian lengkap istilah di sini..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition leading-relaxed"
                    ></textarea>
                </div>

                <!-- Mapping Regulations Multi-Select -->
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">
                        Petakan ke Regulasi
                    </label>
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="modalRegQuery"
                                type="text"
                                placeholder="Cari regulasi..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-44 overflow-y-auto">
                            <li v-if="filteredModalRegulations.length === 0"
                                class="px-3 py-2 text-[11px] text-slate-400 dark:text-slate-500 text-center italic">
                                Tidak ada regulasi yang cocok
                            </li>
                            <li
                                v-for="reg in filteredModalRegulations"
                                :key="reg.id"
                                @click="toggleRegulation(reg.id)"
                                class="flex items-center cursor-pointer py-1.5 px-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
                                :class="form.regulation_ids.includes(reg.id)
                                    ? 'bg-blue-50 dark:bg-blue-500/10'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                <span class="mr-1.5 shrink-0 font-medium text-slate-400 dark:text-slate-500">—</span>
                                <div class="flex-1 leading-tight">
                                    <span
                                        v-if="reg.nomor"
                                        class="text-[10px] block mb-0.5"
                                        :class="form.regulation_ids.includes(reg.id)
                                            ? 'text-blue-500 dark:text-blue-400'
                                            : 'text-slate-400 dark:text-slate-500'"
                                    >{{ reg.nomor }}</span>
                                    <span
                                        :class="form.regulation_ids.includes(reg.id)
                                            ? 'font-semibold text-blue-700 dark:text-blue-300'
                                            : 'text-slate-600 dark:text-slate-400'"
                                    >{{ reg.judul }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Selected Regulations Tags -->
                    <div v-if="selectedRegulations.length > 0" class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">
                                ✓ {{ selectedRegulations.length }} regulasi dipilih
                            </p>
                        </div>
                        <div class="space-y-1 max-h-32 overflow-y-auto pr-1">
                            <div
                                v-for="reg in selectedRegulations"
                                :key="'sel-' + reg.id"
                                class="flex items-center justify-between px-3 py-1.5 rounded-lg border border-slate-100 bg-white dark:border-white/5 dark:bg-[#1a1a1a] hover:bg-slate-50 dark:hover:bg-white/5 transition"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="flex h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                    <div class="text-[11px] font-medium text-slate-800 dark:text-slate-200">
                                        {{ reg.judul }}
                                        <span v-if="reg.nomor" class="ml-1 text-[10px] text-slate-400 dark:text-slate-500 font-normal">
                                            ({{ reg.nomor }})
                                        </span>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="removeRegulation(reg.id)"
                                    class="inline-flex items-center justify-center rounded-md p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition shrink-0"
                                    title="Hapus Regulasi"
                                >
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ConfirmationModal>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    definitions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

// Search & Filter State
const searchQuery = ref('');
const selectedRegulationId = ref('');

// Modal Form State
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const modalRegQuery = ref('');

const form = useForm({
    name: '',
    definition: '',
    regulation_ids: [],
});

// Computed filtering for definitions
const filteredDefinitions = computed(() => {
    let result = props.definitions || [];

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(
            (item) =>
                (item.name || '').toLowerCase().includes(q) ||
                (item.definition || '').toLowerCase().includes(q)
        );
    }

    if (selectedRegulationId.value) {
        const regId = Number(selectedRegulationId.value);
        result = result.filter((item) =>
            item.regulations && item.regulations.some((r) => r.id === regId)
        );
    }

    return result;
});

// Computed filtering for regulations checklist in modal
const filteredModalRegulations = computed(() => {
    let regs = props.regulations || [];
    if (modalRegQuery.value) {
        const q = modalRegQuery.value.toLowerCase();
        regs = regs.filter(
            (reg) =>
                (reg.judul || '').toLowerCase().includes(q) ||
                (reg.nomor || '').toLowerCase().includes(q)
        );
    }
    return regs;
});

// Computed: selected regulation objects for display below the field
const selectedRegulations = computed(() => {
    const regs = props.regulations || [];
    return form.regulation_ids
        .map((id) => regs.find((r) => r.id === id))
        .filter(Boolean);
});

// Toggle a regulation id in the selection list
function toggleRegulation(id) {
    const idx = form.regulation_ids.indexOf(id);
    if (idx >= 0) {
        form.regulation_ids.splice(idx, 1);
    } else {
        form.regulation_ids.push(id);
    }
}

// Remove a regulation from the selected list
function removeRegulation(id) {
    const idx = form.regulation_ids.indexOf(id);
    if (idx >= 0) {
        form.regulation_ids.splice(idx, 1);
    }
}

// Modal Operations
function openAddModal() {
    isEditing.value = false;
    editingId.value = null;
    modalRegQuery.value = '';
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
}

function openEditModal(item) {
    isEditing.value = true;
    editingId.value = item.id;
    modalRegQuery.value = '';
    form.clearErrors();

    form.name = item.name;
    form.definition = item.definition;
    form.regulation_ids = item.regulations ? item.regulations.map((r) => r.id) : [];

    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    form.reset();
}

function submitForm() {
    if (isEditing.value) {
        form.put(route('itom.policy.definition.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Success!',
                    text: 'Definition updated successfully.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true,
                });
            },
        });
    } else {
        form.post(route('itom.policy.definition.store'), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Success!',
                    text: 'Definition added successfully.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true,
                });
            },
        });
    }
}

function confirmDelete(item) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the term: "${item.name}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('itom.policy.definition.destroy', item.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Definition deleted successfully.',
                        icon: 'success',
                        confirmButtonColor: '#821f44',
                        timer: 2000,
                        timerProgressBar: true,
                    });
                },
            });
        }
    });
}
</script>