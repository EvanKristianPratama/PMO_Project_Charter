<template>
    <div class="space-y-6">
        <!-- Action Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Daftar Proses Bisnis</h2>
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Proses Bisnis
            </button>
        </div>

        <!-- Table -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                    <thead
                        class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                        <tr>
                            <th scope="col" class="px-6 py-4">Organisasi</th>
                            <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                            <th scope="col" class="px-6 py-4">Proses Bisnis</th>
                            <th scope="col" class="px-6 py-4">Tugas</th>
                            <th scope="col" class="px-6 py-4">Hasil</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr v-if="prosesBisnis.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Belum ada data proses bisnis.
                            </td>
                        </tr>
                        <tr v-for="item in prosesBisnis" :key="item.id"
                            class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                            <td class="px-6 py-4 text-slate-900 dark:text-white font-semibold">
                                {{ item.organization?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                {{ item.no }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                {{ item.proses_bisnis }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 whitespace-pre-line">
                                {{ item.tugas }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 whitespace-pre-line">
                                {{ item.hasil }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border"
                                    :class="item.status === 'Aktif' ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400' : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'">
                                    {{ item.status || 'Draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openEditModal(item)"
                                        class="rounded-lg p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/10 transition-colors"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(item)"
                                        class="rounded-lg p-2 text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10 transition-colors"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upsert Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeModal"></div>
            <div
                class="relative w-full max-w-lg animate-fade-in-up rounded-2xl bg-white p-6 shadow-2xl dark:bg-[#1a1a1a]">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">
                    {{ editingId ? 'Edit' : 'Tambah' }} Proses Bisnis
                </h3>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Organisasi</label>
                        <select v-model="form.organization_id"
                            class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-white"
                            required>
                            <option value="">Pilih Organisasi</option>
                            <option v-for="org in organizations" :key="org.id" :value="org.id">
                                {{ org.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.organization_id" class="mt-1 text-xs text-rose-500">{{
                            form.errors.organization_id }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">No</label>
                            <input v-model="form.no" type="text"
                                class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-white"
                                placeholder="e.g. 1" required />
                            <p v-if="form.errors.no" class="mt-1 text-xs text-rose-500">{{ form.errors.no }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                            <input v-model="form.status" type="text"
                                class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-white"
                                placeholder="e.g. Aktif" />
                            <p v-if="form.errors.status" class="mt-1 text-xs text-rose-500">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Proses
                            Bisnis</label>
                        <input v-model="form.proses_bisnis" type="text"
                            class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-white"
                            placeholder="Judul Proses Bisnis" required />
                        <p v-if="form.errors.proses_bisnis" class="mt-1 text-xs text-rose-500">{{
                            form.errors.proses_bisnis }}</p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Tugas</label>
                        <textarea v-model="form.tugas" rows="3"
                            class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-white"
                            placeholder="Deskripsi Tugas" required></textarea>
                        <p v-if="form.errors.tugas" class="mt-1 text-xs text-rose-500">{{ form.errors.tugas }}</p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Hasil</label>
                        <textarea v-model="form.hasil" rows="3"
                            class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-white"
                            placeholder="Hasil/Output" required></textarea>
                        <p v-if="form.errors.hasil" class="mt-1 text-xs text-rose-500">{{ form.errors.hasil }}</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeModal"
                            class="rounded-xl px-4 py-2 text-sm font-bold text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-6 py-2 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] focus:ring-2 focus:ring-[#821f44]/20 active:scale-95 disabled:opacity-50">
                            <span v-if="form.processing"
                                class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal :show="showDeleteModal" title="Hapus Proses Bisnis"
            message="Apakah Anda yakin ingin menghapus data proses bisnis ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Hapus" cancel-text="Batal" type="danger" :loading="form.processing"
            @close="showDeleteModal = false" @confirm="deleteItem" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    prosesBisnis: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        required: true,
    },
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const editingId = ref(null);
const itemToDelete = ref(null);

const form = useForm({
    organization_id: '',
    no: '',
    proses_bisnis: '',
    tugas: '',
    hasil: '',
    status: '',
});

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (item) => {
    editingId.value = item.id;
    form.organization_id = item.organization_id;
    form.no = item.no;
    form.proses_bisnis = item.proses_bisnis;
    form.tugas = item.tugas;
    form.hasil = item.hasil;
    form.status = item.status;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingId.value) {
        form.put(route('policy.proses-bisnis.update', editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('policy.proses-bisnis.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (item) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    form.delete(route('policy.proses-bisnis.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.4s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
