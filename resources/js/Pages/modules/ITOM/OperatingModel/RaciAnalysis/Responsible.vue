<template>
    <ModulLayout title="Kelola Master Responsible">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Master Responsible</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('itom.operating-model.raci-analysis.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke RACI Analisis
                        </Link>

                        <button
                            @click="openAddForm"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Master
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

            <!-- Inline Responsible Form (Shown when isFormOpen is true) -->
            <transition name="slide-down">
                <div v-if="isFormOpen" class="max-w-4xl mx-auto overflow-hidden rounded-2xl border-2 border-dashed border-[#821f44]/30 bg-slate-50/50 p-6 dark:bg-white/5">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-white/10 mb-4">
                        <h3 class="text-base font-bold text-[#821f44] dark:text-[#a83262] flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#821f44] animate-ping"></span>
                            {{ editingId ? 'Edit Master Responsible' : 'Buat Master Responsible Baru' }}
                        </h3>
                        <button @click="closeForm" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="editingId ? submitUpdate() : submitCreate()" class="space-y-4 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden dark:border-white/15 dark:bg-[#1a1a1a]">
                        <div class="bg-[#821f44] p-5 text-white">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-white/80">Tanggung Jawab (Responsible):</label>
                                <textarea 
                                    v-model="responsibleForm.responsible" 
                                    rows="3" 
                                    placeholder="Contoh: Menetapkan tata kelola TIK Pertamina secara berkala..." 
                                    class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                    required
                                ></textarea>
                                <div v-if="responsibleForm.errors.responsible" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ responsibleForm.errors.responsible }}</div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                            <button 
                                type="button" 
                                @click="closeForm"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="rounded-xl bg-[#821f44] px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60 flex items-center gap-1.5 active:scale-95"
                                :disabled="responsibleForm.processing"
                            >
                                <span v-if="responsibleForm.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                {{ editingId ? 'Simpan Perubahan' : 'Simpan Data' }}
                            </button>
                        </div>
                    </form>
                </div>
            </transition>

            <!-- Full-Width Spreadsheet-Style Table -->
            <Deferred data="responsibles">
                <template #fallback>
                    <TableSkeleton />
                </template>
                <div class="w-full">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="bg-slate-50/70 border-b border-slate-200 px-5 py-4 dark:bg-white/5 dark:border-white/10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#821f44]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Master List Responsibilities ({{ processedResponsibles.length }})
                        </h3>

                        <!-- Filtering & Sorting Controls -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search Field -->
                            <div class="relative w-full sm:w-64">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                                    </svg>
                                </span>
                                <input 
                                    v-model="searchQuery" 
                                    type="text" 
                                    placeholder="Cari tanggung jawab..." 
                                    class="w-full pl-9 pr-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:border-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                />
                            </div>

                            <!-- Sort Options Button Group -->
                            <div class="flex items-center gap-1 bg-slate-100 dark:bg-white/5 p-1 rounded-xl border border-slate-200 dark:border-white/10">
                                <button 
                                    @click="sortBy = 'default'" 
                                    type="button"
                                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition active:scale-95"
                                    :class="sortBy === 'default' ? 'bg-[#821f44] text-white shadow' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/50 dark:hover:bg-white/5'"
                                >
                                    Default
                                </button>
                                <button 
                                    @click="sortBy = 'alphabetical'" 
                                    type="button"
                                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition active:scale-95 flex items-center gap-1"
                                    :class="sortBy === 'alphabetical' ? 'bg-[#821f44] text-white shadow' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/50 dark:hover:bg-white/5'"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25" />
                                    </svg>
                                    A-Z
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Spreadsheet-Style Grid Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                            <thead class="bg-slate-100 dark:bg-white/5 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                                <tr class="divide-x divide-slate-200 dark:divide-white/10">
                                    <th scope="col" class="px-4 py-3 w-16 text-center border border-slate-200 dark:border-white/10">NO</th>
                                    <th scope="col" class="px-5 py-3 border border-slate-200 dark:border-white/10">Tanggung Jawab (Responsible)</th>
                                    <th scope="col" class="px-4 py-3 w-28 text-center border border-slate-200 dark:border-white/10">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                <tr v-if="processedResponsibles.length === 0" class="hover:bg-slate-50/30 dark:hover:bg-white/5">
                                    <td colspan="3" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500 font-medium border border-slate-200 dark:border-white/10">
                                        Belum ada data Master Responsible. Klik "+ Tambah Master" di atas untuk menambahkan data.
                                    </td>
                                </tr>
                                <tr 
                                    v-for="(resp, index) in processedResponsibles" 
                                    :key="resp.id" 
                                    class="group hover:bg-slate-50/80 dark:hover:bg-white/5 transition duration-150 divide-x divide-slate-200 dark:divide-white/10"
                                >
                                    <!-- NO -->
                                    <td class="px-4 py-3.5 text-center font-mono text-xs text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10 bg-slate-50/40 dark:bg-white/5 font-semibold">
                                        {{ index + 1 }}
                                    </td>
                                    <!-- Content -->
                                    <td class="px-5 py-3.5 text-slate-900 dark:text-white font-medium whitespace-pre-line leading-relaxed border border-slate-200 dark:border-white/10">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-2">
                                                <span 
                                                    v-if="resp.isDuplicate" 
                                                    class="inline-flex items-center gap-1 rounded-md bg-amber-50 dark:bg-amber-950/30 px-2 py-0.5 text-[10px] font-extrabold text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30 shrink-0 select-none"
                                                    title="Ada data tanggung jawab lain yang sama persis di master"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                    </svg>
                                                    Duplikat
                                                </span>
                                            </div>
                                            <span>{{ resp.responsible }}</span>
                                        </div>
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-4 py-3.5 border border-slate-200 dark:border-white/10">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button 
                                                @click="openEditForm(resp)"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#821f44] hover:bg-[#821f44]/5 hover:border-[#821f44]/20 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-[#db588c] dark:hover:bg-[#db588c]/10"
                                                title="Edit Data"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="deleteResponsible(resp)"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-rose-400"
                                                title="Hapus Data"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5">
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
            </div>
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { usePage, useForm, router, Link, Deferred } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';

const props = defineProps({
    responsibles: {
        type: Array,
        required: true,
    },
});

// Search & Sort States
const searchQuery = ref('');
const sortBy = ref('default');

// Computed list with search, alphabetical sorting, and duplicate highlighting
const processedResponsibles = computed(() => {
    // 1. Detect duplicates across all entries
    const counts = {};
    props.responsibles.forEach(r => {
        const text = (r.responsible || '').trim().toLowerCase();
        if (text) {
            counts[text] = (counts[text] || 0) + 1;
        }
    });

    let list = props.responsibles.map(r => {
        const text = (r.responsible || '').trim().toLowerCase();
        return {
            ...r,
            isDuplicate: text ? (counts[text] > 1) : false
        };
    });

    // 2. Filter by search query
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        list = list.filter(r => (r.responsible || '').toLowerCase().includes(query));
    }

    // 3. Sort alphabetically (A-Z) if selected
    if (sortBy.value === 'alphabetical') {
        list.sort((a, b) => (a.responsible || '').localeCompare(b.responsible || '', 'id'));
    }

    return list;
});

const page = usePage();

// Alert notifications
const localSuccess = ref(page.props.flash?.success || null);
const localError = ref(page.props.flash?.error || null);

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
// RESPONSIBLE: FORM STATE & CRUD OPERATIONS INLINE
// ---------------------------------------------------
const isFormOpen = ref(false);
const editingId = ref(null);
const responsibleForm = useForm({
    responsible: '',
});

function openAddForm() {
    editingId.value = null;
    responsibleForm.reset();
    responsibleForm.clearErrors();
    isFormOpen.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function openEditForm(resp) {
    editingId.value = resp.id;
    responsibleForm.responsible = resp.responsible;
    responsibleForm.clearErrors();
    isFormOpen.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function closeForm() {
    isFormOpen.value = false;
    editingId.value = null;
    responsibleForm.reset();
    responsibleForm.clearErrors();
}

function submitCreate() {
    responsibleForm.post(route('itom.policy.responsible.store'), {
        onSuccess: () => {
            closeForm();
            localSuccess.value = 'Master Responsible berhasil dibuat!';
        },
        onError: () => {
            localError.value = 'Mohon periksa kembali kesalahan input Anda.';
        }
    });
}

function submitUpdate() {
    responsibleForm.put(route('itom.policy.responsible.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => {
            closeForm();
            localSuccess.value = 'Master Responsible berhasil diperbarui!';
        },
        onError: () => {
            localError.value = 'Gagal menyimpan perubahan Master Responsible.';
        }
    });
}

function deleteResponsible(resp) {
    if (confirm(`Apakah Anda yakin ingin menghapus data Master Responsible (ID: ${resp.id}) ini?`)) {
        router.delete(route('itom.policy.responsible.destroy', resp.id), {
            preserveScroll: true,
            onSuccess: () => {
                // If we are currently editing the deleted record, close the form
                if (editingId.value === resp.id) {
                    closeForm();
                }
                localSuccess.value = 'Master Responsible berhasil dihapus.';
            },
            onError: () => {
                localError.value = 'Gagal menghapus Master Responsible.';
            }
        });
    }
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.4s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease-out;
    max-height: 500px;
}
.slide-down-enter-from,
.slide-down-leave-to {
    max-height: 0;
    opacity: 0;
    padding-top: 0;
    padding-bottom: 0;
    margin-top: 0;
    margin-bottom: 0;
}
</style>

