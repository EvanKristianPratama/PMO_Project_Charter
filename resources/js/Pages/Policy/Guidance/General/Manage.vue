<template>
    <ModulLayout title="Kelola Kebijakan Umum">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Dokumen</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('policy.general.index', { regulation_id: props.selectedRegulationId || activeRegulation?.id })"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lihat Dokumen
                        </Link>
                        <button
                            @click="openAddModal"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Kebijakan Umum
                        </button>
                    </div>
                </div>
            </section>


            <!-- Floating Alerts / Feedback at Bottom-Left -->
            <div class="fixed bottom-6 left-6 z-[9999] max-w-sm space-y-3 pointer-events-none">
                <transition name="fade">
                    <div v-if="localSuccess" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-emerald-200 bg-white p-4 text-sm text-[#065f46] shadow-2xl backdrop-blur-sm dark:border-emerald-500/30 dark:bg-[#1a1a1a] dark:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.807-9.617a.9.9 0 111.386 1.134l-4.5 5.5a.9.9 0 01-1.302.08l-2.5-2.5a.9.9 0 011.272-1.272l1.782 1.782 3.862-4.724z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localSuccess }}</span>
                    </div>
                </transition>

                <transition name="fade">
                    <div v-if="localError" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-rose-200 bg-white p-4 text-sm text-[#991b1b] shadow-2xl backdrop-blur-sm dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localError }}</span>
                    </div>
                </transition>
            </div>

            <!-- Standard Vue Table Component -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="w-16 px-6 py-4 text-center">No.</th>
                                <th scope="col" class="w-20 px-6 py-4 text-center">Item</th>
                                <th scope="col" class="px-6 py-4">Kebijakan Umum</th>
                                <th scope="col" class="w-32 px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                            <tr v-if="policies.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data Kebijakan Umum. Klik "+ Tambah Kebijakan Umum" untuk memasukkan data pertama.
                                </td>
                            </tr>
                            <tr 
                                v-for="(policy, index) in policies" 
                                :key="policy.id" 
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                            >
                                <!-- No Column -->
                                <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                    {{ index + 1 }}
                                </td>
                                <!-- Number Column -->
                                <td class="px-6 py-4 text-center font-mono font-bold text-slate-900 dark:text-white">
                                    {{ policy.number }}
                                </td>
                                <!-- Description Column -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line">
                                    {{ formatDescription(policy.description) }}
                                </td>
                                <!-- Actions Column -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            @click="openEditModal(policy)"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#821f44] hover:bg-[#821f44]/5 hover:border-[#821f44]/20 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-[#db588c] dark:hover:bg-[#db588c]/10"
                                            title="Edit Kebijakan Umum"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button 
                                            @click="deletePolicy(policy)"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-rose-400"
                                            title="Hapus Kebijakan Umum"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sleek Modal for Create / Edit -->
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
                                {{ editingId ? 'Edit Kebijakan Umum' : 'Tambah Kebijakan Umum Baru' }}
                            </h3>
                            <button @click="closeModal" class="text-white/80 hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Form -->
                        <form @submit.prevent="submitForm">
                            <div class="p-6 space-y-4">
                                <!-- Number Input -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nomor Urut Kebijakan:</label>
                                    <input 
                                        type="number" 
                                        v-model="form.number" 
                                        placeholder="Contoh: 1" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        required
                                        min="1"
                                    />
                                    <div v-if="form.errors.number" class="text-xs text-rose-500 font-medium">{{ form.errors.number }}</div>
                                </div>

                                <!-- Description Input -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Deskripsi Kebijakan Umum:</label>
                                    <textarea 
                                        v-model="form.description" 
                                        rows="4" 
                                        placeholder="Masukkan butir kebijakan lengkap..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-xs text-rose-500 font-medium">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <!-- Modal Footer Actions -->
                            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5 sticky bottom-0 z-10 shrink-0">
                                <button 
                                    type="button" 
                                    @click="closeModal"
                                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit" 
                                    class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                    Simpan Kebijakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { usePage, useForm, router, Link } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
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
        default: null
    }
});

const activeRegulation = computed(() => {
    if (!props.selectedRegulationId) return props.regulations[0] || null;
    return props.regulations.find(r => r.id === props.selectedRegulationId) || null;
});

const page = usePage();

// Success and Error local states to handle notification flashes
const localSuccess = ref(page.props.flash?.success || null);
const localError = ref(page.props.flash?.error || null);

// Watch for inertia flash prop updates
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            localSuccess.value = flash.success;
            setTimeout(() => { localSuccess.value = null; }, 5000);
        }
        if (flash?.error) {
            localError.value = flash.error;
            setTimeout(() => { localError.value = null; }, 5000);
        }
    },
    { deep: true, immediate: true }
);

// ---------------------------------------------------
// MODAL & FORM STATE
// ---------------------------------------------------
const isModalOpen = ref(false);
const editingId = ref(null);

const form = useForm({
    regulation_id: activeRegulation.value?.id || '',
    number: '',
    description: '',
});

// Watch activeRegulation changes to update form's regulation_id
watch(
    () => activeRegulation.value,
    (reg) => {
        if (reg) {
            form.regulation_id = reg.id;
        }
    },
    { immediate: true }
);


function openAddModal() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.regulation_id = activeRegulation.value?.id || '';
    // Pre-fill next number logically
    let nextNum = 1;
    if (props.policies.length > 0) {
        nextNum = Math.max(...props.policies.map(p => parseInt(p.number) || 0)) + 1;
    }
    form.number = nextNum;
    isModalOpen.value = true;
}

function openEditModal(policy) {
    editingId.value = policy.id;
    form.regulation_id = policy.regulation_id || activeRegulation.value?.id || '';
    form.number = policy.number;
    form.description = policy.description;
    form.clearErrors();
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
}

function submitForm() {
    if (editingId.value) {
        form.put(route('policy.general.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kebijakan Umum berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true
                });
            },
            onError: () => {
                localError.value = 'Gagal menyimpan perubahan Kebijakan Umum.';
            }
        });
    } else {
        form.post(route('policy.general.store'), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kebijakan Umum berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true
                });
            },
            onError: () => {
                localError.value = 'Gagal menambahkan Kebijakan Umum baru.';
            }
        });
    }
}

function deletePolicy(policy) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Kebijakan Umum No. ${policy.number}. Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('policy.general.destroy', policy.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Kebijakan Umum berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#821f44',
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal menghapus data.',
                        icon: 'error',
                        confirmButtonColor: '#821f44'
                    });
                }
            });
        }
    });
}

function formatDescription(text) {
    if (!text) return '';
    return text.replace(/\s+([a-z])([\.\)])\s+/g, '\n   $1$2 ');
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

.animate-scale-up {
    animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
