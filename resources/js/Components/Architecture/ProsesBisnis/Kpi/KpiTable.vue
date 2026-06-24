<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">KPI List</h2>
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari KPI..."
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48"
                />
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add KPI
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-16">No</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Description</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(kpi, index) in filteredKpis"
                        :key="'kpi-' + kpi.id"
                        class="group transition duration-150 hover:bg-slate-50/50 dark:hover:bg-white/5 animate-fade-in"
                    >
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 text-xs">
                            {{ index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-slate-900 dark:text-white text-xs whitespace-pre-wrap break-words font-medium">
                            {{ kpi.deskripsi || '-' }}
                        </td>
                        <td class="px-4 py-3 text-center print:hidden">
                            <div class="flex items-center justify-center gap-1.5">
                                <button
                                    @click="openEditModal(kpi)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteModal(kpi)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredKpis.length === 0">
                        <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data KPI tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah KPI' : 'Edit KPI'"
        :message="modalMode === 'create' ? 'Silakan isi deskripsi untuk menambahkan KPI baru.' : 'Silakan sesuaikan data KPI di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Deskripsi Input -->
            <div class="flex flex-col gap-1.5">
                <label for="kpi_deskripsi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi KPI</label>
                <textarea
                    id="kpi_deskripsi"
                    v-model="form.deskripsi"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Meningkatkan efisiensi sistem TI sebesar 20%"
                    required
                ></textarea>
                <span v-if="form.errors.deskripsi" class="text-xs text-red-500 font-medium">{{ form.errors.deskripsi }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus KPI"
        :message="`Apakah Anda yakin ingin menghapus KPI ini? Tindakan ini tidak dapat dibatalkan.`"
        confirm-text="Hapus"
        cancel-text="Batal"
        type="danger"
        :loading="form.processing"
        @close="isDeleteModalOpen = false"
        @confirm="submitDelete"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    kpiList: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedKpi = ref(null);
const modalMode = ref('create');

const form = useForm({
    deskripsi: '',
});

const filteredKpis = computed(() => {
    if (!searchQuery.value) return props.kpiList;
    const q = searchQuery.value.toLowerCase().trim();
    return props.kpiList.filter(kpi => 
        (kpi.deskripsi || '').toLowerCase().includes(q)
    );
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (kpi) => {
    modalMode.value = 'edit';
    selectedKpi.value = kpi;
    form.clearErrors();
    form.deskripsi = kpi.deskripsi || '';
    isModalOpen.value = true;
};

const openDeleteModal = (kpi) => {
    selectedKpi.value = kpi;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('architecture.kpi.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('architecture.kpi.update', selectedKpi.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('architecture.kpi.destroy', selectedKpi.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
