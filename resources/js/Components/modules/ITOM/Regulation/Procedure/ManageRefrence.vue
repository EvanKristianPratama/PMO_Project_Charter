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
                            placeholder="Cari nomor atau judul regulasi..."
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
                </div>

                <!-- Right Section: Actions -->
                <div v-if="!readonly" class="flex items-center gap-2 shrink-0">
                    <button
                        @click="openMapModal"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 dark:border-white/10 bg-white dark:bg-[#1a1a1a] px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-white/5 active:scale-95 cursor-pointer"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3.5 h-3.5 text-[#821f44]"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"
                            />
                        </svg>
                        Map Existing Regulation
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-[11px]">
                    <thead
                        class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300"
                    >
                        <tr class="divide-x divide-slate-200 dark:divide-white/10">
                            <th class="px-1 py-3 w-10 text-center">No</th>
                            <th class="px-1 py-3 w-32">Tipe</th>
                            <th class="px-1 py-3 w-40">Nomor</th>
                            <th class="px-1 py-3">Judul Regulasi</th>
                            <th class="px-1 py-3 w-48">Pemilik Dokumen</th>
                            <th class="px-1 py-3 w-24 text-center">Status</th>
                            <th v-if="!readonly" class="px-1 py-3 w-28 text-center print:hidden">Action</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-200 dark:divide-white/10"
                    >
                        <tr v-if="filteredRelatedRegulations.length === 0">
                            <td
                                :colspan="readonly ? 6 : 7"
                                class="px-1 py-8 text-center text-slate-400 font-medium"
                            >
                                Tidak ada data referensi regulasi ditemukan.
                            </td>
                        </tr>
                        <tr
                            v-for="(item, index) in filteredRelatedRegulations"
                            :key="item.id"
                            class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition-all duration-500 divide-x divide-slate-200 dark:divide-white/10"
                        >
                            <td
                                class="px-1 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400"
                            >
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-1 py-3 align-middle font-medium text-slate-900 dark:text-white"
                            >
                                {{ item.tipe }}
                            </td>
                            <td
                                class="px-1 py-3 align-middle font-medium text-slate-700 dark:text-slate-300"
                            >
                                {{ item.nomor || '-' }}
                            </td>
                            <td
                                class="px-1 py-3 align-middle text-slate-700 dark:text-slate-300 font-semibold"
                            >
                                {{ item.judul }}
                            </td>
                            <td
                                class="px-1 py-3 align-middle text-slate-655 dark:text-slate-400 leading-normal"
                            >
                                {{ item.organization?.name || item.owner || '-' }}
                            </td>
                            <td
                                class="px-1 py-3 align-middle text-center"
                            >
                                <span 
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider border"
                                    :class="{
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30': item.status?.toLowerCase() === 'aktif',
                                        'bg-slate-50 text-slate-700 border-slate-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700/50': item.status?.toLowerCase() !== 'aktif'
                                    }"
                                >
                                    {{ item.status || 'Draft' }}
                                </span>
                            </td>
                            <td v-if="!readonly" class="px-1 py-2 align-middle text-center print:hidden">
                                <button
                                    @click="confirmUnmap(item)"
                                    class="inline-flex items-center justify-center rounded-full border border-amber-200 bg-white px-3 py-1 text-[10px] font-bold text-amber-700 transition hover:bg-amber-50 hover:border-amber-300 dark:border-amber-500/30 dark:bg-[#1a1a1a] dark:text-amber-400 dark:hover:bg-amber-500/10 active:scale-95 cursor-pointer"
                                >
                                    Unmap
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dialog Modal: Map Existing Regulation -->
        <ConfirmationModal
            :show="isMapModalOpen"
            title="Map Existing Regulation Reference"
            message="Pilih regulasi yang sudah tersedia untuk dipetakan sebagai dokumen referensi."
            confirm-text="Map Regulation"
            cancel-text="Cancel"
            type="info"
            :loading="mapForm.processing"
            @close="closeMapModal"
            @confirm="submitMapForm"
        >
            <div class="mt-4 space-y-4 text-left">
                <!-- Selected Regulation (Disabled / Informational) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                        Prosedur Tujuan
                    </label>
                    <input
                        type="text"
                        disabled
                        :value="activeProcedureName"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-white/5 px-3 py-2 text-xs text-slate-500 transition font-medium"
                    />
                </div>

                <!-- Search and Select Existing Regulation -->
                <div class="flex flex-col gap-2">
                    <label class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                        Pilih Regulasi Existing <span class="text-red-500">*</span>
                    </label>
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="mapRegSearchQuery"
                                type="text"
                                placeholder="Cari nomor atau judul regulasi..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400 font-medium"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-48 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5">
                            <li v-if="filteredMappableRegulations.length === 0"
                                class="px-3 py-4 text-[11px] text-slate-400 dark:text-slate-500 text-center italic">
                                Tidak ada regulasi existing yang belum dipetakan
                            </li>
                            <li
                                v-for="reg in filteredMappableRegulations"
                                :key="reg.id"
                                @click="mapForm.related_id = reg.id"
                                class="flex flex-col cursor-pointer py-2 px-3 text-[11px] transition select-none"
                                :class="mapForm.related_id === reg.id
                                    ? 'bg-blue-50 dark:bg-blue-500/10 border-l-4 border-blue-500'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5 border-l-4 border-transparent'"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        <span v-if="reg.nomor" class="text-blue-600 dark:text-blue-400 mr-1">[{{ reg.nomor }}]</span>
                                        {{ reg.judul }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-semibold">
                                        {{ reg.tipe }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate leading-relaxed">
                                    Owner: {{ reg.organization?.name || reg.owner || '-' }} | Status: {{ reg.status || 'Draft' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div v-if="mapForm.errors.related_id" class="text-xs text-red-500 mt-1">
                        {{ mapForm.errors.related_id }}
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
import Swal from 'sweetalert2';

const props = defineProps({
    relatedRegulations: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    activeRegulationId: {
        type: Number,
        default: null,
    },
});

// Search State
const searchQuery = ref('');

// Modal Form State (Map Existing Regulation)
const isMapModalOpen = ref(false);
const mapRegSearchQuery = ref('');

const mapForm = useForm({
    related_id: '',
    regulation_id: null,
});

// Form for Unmapping
const unmapForm = useForm({
    related_id: null,
    regulation_id: null,
});

// Active Procedure (Regulation) Name computed property
const activeProcedureName = computed(() => {
    const reg = (props.regulations || []).find((r) => r.id === Number(props.activeRegulationId));
    return reg ? `${reg.nomor ? reg.nomor + ' - ' : ''}${reg.judul}` : 'Prosedur Aktif';
});

// Filtered related regulations displayed in main table
const filteredRelatedRegulations = computed(() => {
    let result = props.relatedRegulations || [];

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(
            (item) =>
                (item.judul || '').toLowerCase().includes(q) ||
                (item.nomor || '').toLowerCase().includes(q) ||
                (item.tipe || '').toLowerCase().includes(q)
        );
    }

    return result;
});

// Filtered available regulations that can be mapped to active procedure in modal
const filteredMappableRegulations = computed(() => {
    const activeRegId = Number(props.activeRegulationId);
    
    // Filter out:
    // 1. The active regulation itself
    // 2. Regulations already mapped to the active regulation
    let result = (props.regulations || []).filter((reg) => {
        if (reg.id === activeRegId) return false;
        const alreadyMapped = (props.relatedRegulations || []).some((r) => r.id === reg.id);
        return !alreadyMapped;
    });

    if (mapRegSearchQuery.value) {
        const q = mapRegSearchQuery.value.toLowerCase();
        result = result.filter(
            (reg) => 
                (reg.judul || '').toLowerCase().includes(q) ||
                (reg.nomor || '').toLowerCase().includes(q)
        );
    }

    return result;
});

// Map Modal Operations
function openMapModal() {
    mapForm.reset();
    mapForm.clearErrors();
    mapForm.regulation_id = Number(props.activeRegulationId);
    mapRegSearchQuery.value = '';
    isMapModalOpen.value = true;
}

function closeMapModal() {
    isMapModalOpen.value = false;
    mapForm.reset();
}

function submitMapForm() {
    if (!mapForm.related_id) {
        mapForm.setError('related_id', 'Silakan pilih salah satu regulasi referensi.');
        return;
    }
    
    mapForm.post(route('itom.policy.regulation.procedure.regulation.map'), {
        onSuccess: () => {
            closeMapModal();
            Swal.fire({
                title: 'Success!',
                text: 'Regulasi referensi berhasil dipetakan.',
                icon: 'success',
                confirmButtonColor: '#821f44',
                timer: 2000,
                timerProgressBar: true,
            });
        },
    });
}

// Unmap confirmation
function confirmUnmap(item) {
    Swal.fire({
        title: 'Hapus Pemetaan?',
        text: `Apakah Anda yakin ingin melepas regulasi referensi "${item.judul}" dari prosedur ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Lepas!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            unmapForm.related_id = item.id;
            unmapForm.regulation_id = Number(props.activeRegulationId);
            unmapForm.post(route('itom.policy.regulation.procedure.regulation.unmap'), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Pemetaan regulasi referensi berhasil dilepas.',
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
