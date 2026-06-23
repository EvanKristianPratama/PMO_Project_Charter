<template>
    <div class="space-y-6">
        <!-- Table Content -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Business Process List</h2>
                <div class="flex items-center gap-3">
                    <!-- Filter by Status -->
                    <div class="relative">
                        <select
                            v-model="selectedStatus"
                            class="appearance-none bg-white text-slate-900 border border-slate-300 rounded-lg pl-3 pr-8 py-1.5 text-xs focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[130px]"
                        >
                            <option value="">Semua Status</option>
                            <option
                                v-for="status in uniqueStatuses"
                                :key="status"
                                :value="status"
                            >
                                {{ status }}
                            </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Add button -->
                    <Link
                        :href="route('architecture.proses-bisnis.manage')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] hover:bg-[#9c2552] text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Kelola Proses Bisnis
                    </Link>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                        <tr>
                            <th scope="col" class="px-6 py-4">Probis</th>
                            <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                            <th scope="col" class="px-6 py-4">Proses Bisnis</th>
                            <th scope="col" class="px-6 py-4">Tugas</th>
                            <th scope="col" class="px-6 py-4">Hasil</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4 text-center w-36 print:hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr v-if="filteredProsesBisnis.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Belum ada data proses bisnis.
                            </td>
                        </tr>
                        <tr v-for="item in filteredProsesBisnis" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                            <td class="px-6 py-4 text-slate-900 dark:text-white font-semibold">
                                {{ item.organization?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                {{ item.no }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                {{ item.proses_bisnis }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 whitespace-pre-line">
                                {{ item.tugas }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300 whitespace-pre-line">
                                {{ item.hasil }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border" :class="item.status === 'Aktif' ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400' : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'">
                                    {{ item.status || 'Draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-b border-slate-200 dark:border-white/10 print:hidden">
                                <div class="flex items-center justify-center gap-1.5">
                                    <Link 
                                        :href="route('architecture.proses-bisnis.manage', { edit_id: item.id })"
                                        class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                        title="Edit Proses Bisnis"
                                    >
                                        Edit
                                    </Link>
                                    <button 
                                        @click="deleteProsesBisnis(item)"
                                        class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                        title="Hapus Proses Bisnis"
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

        <!-- ManageModal Component removed (now loaded in a separate page) -->
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
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

// Filters & Search
const selectedStatus = ref('');

const uniqueStatuses = computed(() => {
    const statusSet = new Set();
    props.prosesBisnis.forEach(item => {
        if (item.status) {
            statusSet.add(item.status);
        }
    });
    return Array.from(statusSet).sort((a, b) => a.localeCompare(b));
});

const filteredProsesBisnis = computed(() => {
    let result = props.prosesBisnis;

    if (selectedStatus.value) {
        result = result.filter(item => item.status === selectedStatus.value);
    }

    return result;
});

function deleteProsesBisnis(item) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Proses Bisnis: "${item.proses_bisnis}". Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('architecture.proses-bisnis.destroy', item.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Proses Bisnis berhasil dihapus.',
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
</script>
