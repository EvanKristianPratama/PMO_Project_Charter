<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 px-4 py-3 gap-3 dark:border-white/10">
            <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-xs font-bold tracking-wider text-slate-700 dark:text-slate-200">
                    SK Organization
                </h3>

                <!-- Search Input -->
                <div class="relative w-full sm:w-48">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari SK, deskripsi..."
                        class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    @click="openSkModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-emerald-400 dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add SK
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-fixed">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-12">No</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-1/3">SK</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Deskripsi</th>
                        <th class="px-4 py-2 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(item, index) in filteredSks"
                        :key="item.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-left w-12">{{ index + 1 }}</td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left break-words whitespace-normal">
                            {{ item.sk }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 text-left break-words whitespace-normal">
                            {{ item.deskripsi || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-center w-40">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditSkModal(item)"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteSkModal(item)"
                                    class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredSks.length === 0">
                        <td colspan="4" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 mb-4 border border-slate-200 dark:border-white/10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    SK Not Available
                                </h4>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create/Edit SK Modal -->
    <ConfirmationModal
        :show="isSkModalOpen"
        :title="skModalMode === 'create' ? 'Tambah SK' : 'Edit SK'"
        message=""
        confirm-text="Save"
        cancel-text="Cancel"
        type="info"
        :loading="skForm.processing"
        @close="isSkModalOpen = false"
        @confirm="submitSkForm"
    >
        <div class="mt-4 space-y-4">
            <!-- SK Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="sk_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nomor / Nama SK</label>
                <input
                    id="sk_name"
                    v-model="skForm.sk"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: SK-001/DIR/2026"
                    required
                />
                <span v-if="skForm.errors.sk" class="text-xs text-red-500 font-medium">{{ skForm.errors.sk }}</span>
            </div>

            <!-- SK Deskripsi Input -->
            <div class="flex flex-col gap-1.5">
                <label for="sk_deskripsi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi</label>
                <textarea
                    id="sk_deskripsi"
                    v-model="skForm.deskripsi"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: SK Pembentukan Struktur Organisasi Divisi IT"
                ></textarea>
                <span v-if="skForm.errors.deskripsi" class="text-xs text-red-500 font-medium">{{ skForm.errors.deskripsi }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete SK Confirmation Modal -->
    <ConfirmationModal
        :show="isSkDeleteModalOpen"
        title="Hapus SK"
        :message="`Apakah Anda yakin ingin menghapus '${selectedSk?.sk}' dari daftar SK Organisasi?`"
        confirm-text="Delete"
        cancel-text="Cancel"
        type="danger"
        :loading="skForm.processing"
        @close="isSkDeleteModalOpen = false"
        @confirm="submitDeleteSk"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    skOrganizations: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');

const filteredSks = computed(() => {
    let result = props.skOrganizations;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        result = result.filter(item => {
            return (
                (item.sk || '').toLowerCase().includes(query) ||
                (item.deskripsi || '').toLowerCase().includes(query)
            );
        });
    }

    return result;
});

const isSkModalOpen = ref(false);
const isSkDeleteModalOpen = ref(false);
const skModalMode = ref('create'); // 'create' or 'edit'
const selectedSk = ref(null);

const skForm = useForm({
    sk: '',
    deskripsi: '',
});

const openSkModal = () => {
    skModalMode.value = 'create';
    selectedSk.value = null;
    skForm.clearErrors();
    skForm.reset();
    isSkModalOpen.value = true;
};

const openEditSkModal = (item) => {
    skModalMode.value = 'edit';
    selectedSk.value = item;
    skForm.clearErrors();
    skForm.sk = item.sk;
    skForm.deskripsi = item.deskripsi || '';
    isSkModalOpen.value = true;
};

const openDeleteSkModal = (item) => {
    selectedSk.value = item;
    isSkDeleteModalOpen.value = true;
};

const submitSkForm = () => {
    if (skModalMode.value === 'create') {
        skForm.post(route('itom.business-process.organization-structure.sk.store'), {
            onSuccess: () => {
                isSkModalOpen.value = false;
                skForm.reset();
            },
        });
    } else {
        skForm.put(route('itom.business-process.organization-structure.sk.update', selectedSk.value.id), {
            onSuccess: () => {
                isSkModalOpen.value = false;
                skForm.reset();
            },
        });
    }
};

const submitDeleteSk = () => {
    skForm.delete(route('itom.business-process.organization-structure.sk.destroy', selectedSk.value.id), {
        onSuccess: () => {
            isSkDeleteModalOpen.value = false;
        },
    });
};
</script>
