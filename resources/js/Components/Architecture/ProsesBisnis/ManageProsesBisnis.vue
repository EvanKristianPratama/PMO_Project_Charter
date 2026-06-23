<template>
    <Teleport to="body">
        <transition name="fade">
            <div v-if="isModalOpen" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl dark:bg-[#1a1a1a] border border-slate-200 dark:border-white/10 overflow-y-auto max-h-[85vh] animate-scale-up">
                    <!-- Modal Header -->
                    <div class="bg-[#821f44] p-5 text-white flex items-center justify-between sticky top-0 z-10 shrink-0">
                        <h3 class="text-base font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            {{ editingId ? 'Edit Business Process' : 'Add Business Process' }}
                        </h3>
                        <button @click="closeModal" class="text-white/80 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Form Body -->
                    <form @submit.prevent="submitForm">
                        <div class="p-6 space-y-4">
                            <!-- Organisasi -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Organisasi:</label>
                                <select 
                                    v-model="form.organization_id"
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#111111] dark:text-white dark:border-white/10 cursor-pointer"
                                    required
                                >
                                    <option value="" disabled>Pilih Organisasi...</option>
                                    <option v-for="org in organizations" :key="org.id" :value="org.id">
                                        {{ org.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.organization_id" class="text-xs text-rose-500 font-medium">{{ form.errors.organization_id }}</div>
                            </div>

                            <!-- No & Status -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">No:</label>
                                    <input 
                                        type="text" 
                                        v-model="form.no" 
                                        placeholder="Contoh: 1, 1.1..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#111111] dark:text-white dark:border-white/10"
                                        required
                                    />
                                    <div v-if="form.errors.no" class="text-xs text-rose-500 font-medium">{{ form.errors.no }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status:</label>
                                    <input 
                                        type="text" 
                                        v-model="form.status" 
                                        placeholder="Contoh: Aktif, Draft..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#111111] dark:text-white dark:border-white/10"
                                    />
                                    <div v-if="form.errors.status" class="text-xs text-rose-500 font-medium">{{ form.errors.status }}</div>
                                </div>
                            </div>

                            <!-- Proses Bisnis -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Proses Bisnis:</label>
                                <input 
                                    type="text" 
                                    v-model="form.proses_bisnis" 
                                    placeholder="Masukkan judul/nama proses bisnis..." 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#111111] dark:text-white dark:border-white/10"
                                    required
                                />
                                <div v-if="form.errors.proses_bisnis" class="text-xs text-rose-500 font-medium">{{ form.errors.proses_bisnis }}</div>
                            </div>

                            <!-- Tugas -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tugas:</label>
                                <textarea 
                                    v-model="form.tugas" 
                                    rows="3"
                                    placeholder="Masukkan deskripsi tugas..." 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#111111] dark:text-white dark:border-white/10"
                                    required
                                ></textarea>
                                <div v-if="form.errors.tugas" class="text-xs text-rose-500 font-medium">{{ form.errors.tugas }}</div>
                            </div>

                            <!-- Hasil -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Hasil:</label>
                                <textarea 
                                    v-model="form.hasil" 
                                    rows="3"
                                    placeholder="Masukkan hasil/output..." 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#111111] dark:text-white dark:border-white/10"
                                    required
                                ></textarea>
                                <div v-if="form.errors.hasil" class="text-xs text-rose-500 font-medium">{{ form.errors.hasil }}</div>
                            </div>
                        </div>

                        <!-- Modal Footer Actions -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5 sticky bottom-0 z-10 shrink-0">
                            <button 
                                type="button" 
                                @click="closeModal"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

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

const isModalOpen = ref(false);
const editingId = ref(null);

const form = useForm({
    organization_id: '',
    no: '',
    proses_bisnis: '',
    tugas: '',
    hasil: '',
    status: '',
});

function openAddModal() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
}

function openEditModal(item) {
    editingId.value = item.id;
    form.organization_id = item.organization_id;
    form.no = item.no;
    form.proses_bisnis = item.proses_bisnis;
    form.tugas = item.tugas;
    form.hasil = item.hasil;
    form.status = item.status || '';
    form.clearErrors();
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
}

function submitForm() {
    if (editingId.value) {
        form.put(route('architecture.proses-bisnis.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Proses Bisnis berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true
                });
            },
        });
    } else {
        form.post(route('architecture.proses-bisnis.store'), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Proses Bisnis berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true
                });
            },
        });
    }
}

defineExpose({
    openAddModal,
    openEditModal
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-scale-up {
    animation: scaleUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
