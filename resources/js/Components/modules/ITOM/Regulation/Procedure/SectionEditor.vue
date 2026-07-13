<template>
    <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 md:p-10 rounded-2xl font-sans animate-fade-in-up">
        <PertaminaDocumentHeader v-if="isHeaderVisible" :activeRegulation="activeRegulation" />

        <div class="mt-10 flex items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                Editor Section Dokumen
            </h3>
            <div class="flex items-center gap-3">
                <!-- Save status indicator -->
                <span class="text-[11px] flex items-center gap-1.5 select-none print:hidden mr-2">
                    <span v-if="saveStatus === 'saving'" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                        <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else-if="saveStatus === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Tersimpan
                    </span>
                    <span v-else-if="saveStatus === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                        Gagal menyimpan
                    </span>
                    <span v-else-if="modifiedSections.size > 0" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Belum disimpan
                    </span>
                    <span v-else class="text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-emerald-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tersimpan
                    </span>
                </span>

                <!-- Manual Save Button -->
                <button
                    @click="saveAll"
                    :disabled="isSaving || modifiedSections.size === 0"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50 print:hidden mr-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    Simpan
                </button>

                <button
                    @click="addSectionDirectly"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 print:hidden"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Section
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3 w-20 text-center">No</th>
                        <th class="px-6 py-3">Nama Section</th>
                        <th class="px-6 py-3 w-28 text-center">Urutan</th>
                        <th class="px-6 py-3 w-24 text-center print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-if="filteredTkoSections.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada section.</td>
                    </tr>
                    <tr v-for="(sec, index) in filteredTkoSections" :key="sec.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5">
                        <td class="px-6 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-6 py-2 align-middle">
                            <input
                                v-if="sectionLocal[sec.id]"
                                v-model="sectionLocal[sec.id].name"
                                @input="handleSectionChange(sec.id)"
                                type="text"
                                class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10 font-bold"
                                placeholder="Nama Section (Contoh: TUJUAN)"
                            />
                        </td>
                        <td class="px-6 py-2 align-middle text-center">
                            <input
                                v-if="sectionLocal[sec.id]"
                                v-model="sectionLocal[sec.id].order"
                                @input="handleSectionChange(sec.id)"
                                type="number"
                                min="1"
                                class="w-20 bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10 text-center"
                            />
                        </td>
                        <td class="px-6 py-2 align-middle text-center print:hidden">
                            <button
                                @click="deleteSectionDirectly(sec)"
                                class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-white/5 p-4 rounded-xl">
            <p class="font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Pengaturan Section:</p>
            <ul class="list-disc pl-4 space-y-1">
                <li>Secara default, Bab IV (Fungsi Terkait) diisi otomatis oleh data Aktor di Urutan 4.</li>
                <li>Bab V (Prosedur) diisi otomatis oleh data SOP & Diagram di Urutan 5.</li>
                <li>Secara default, Bab IX (Pengertian / Glossary) diisi otomatis oleh data Glossary di Urutan 9.</li>
                <li>Anda bebas mengubah nama dan urutan section lainnya (seperti Tujuan, Ruang Lingkup, Lampiran, dll).</li>
                <li>Perubahan nama section atau urutan section akan secara otomatis memperbarui tata letak halaman dan menu navigasi.</li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import PertaminaDocumentHeader from '@/Components/modules/ITOM/Regulation/PertaminaDocumentHeader.vue';

// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
    tkoSections: {
        type: Array,
        default: () => [],
    },
    activeRegulation: {
        type: Object,
        default: null,
    },
    isHeaderVisible: {
        type: Boolean,
        default: true,
    },
});

// ─── State ────────────────────────────────────────────────────────────────────
const sectionLocal = ref({});
const modifiedSections = ref(new Set());
const isSaving = ref(false);
const saveStatus = ref(null); // null | 'saving' | 'saved' | 'error'

// ─── Computed ─────────────────────────────────────────────────────────────────
const hasUnsavedChanges = computed(() => modifiedSections.value.size > 0);
const filteredTkoSections = computed(() => {
    return (props.tkoSections || []).filter(s => s.name.trim().toLowerCase() !== 'pengertian');
});

// ─── Init local state ─────────────────────────────────────────────────────────
function initLocal() {
    props.tkoSections.forEach(sec => {
        if (!sectionLocal.value[sec.id]) {
            sectionLocal.value[sec.id] = {
                name: sec.name || '',
                order: sec.order || 1
            };
        } else if (!modifiedSections.value.has(sec.id)) {
            sectionLocal.value[sec.id].name = sec.name || '';
            sectionLocal.value[sec.id].order = sec.order || 1;
        }
    });
}

initLocal();

watch(() => props.tkoSections, () => {
    initLocal();
}, { deep: true });

// ─── Actions ──────────────────────────────────────────────────────────────────
function handleSectionChange(secId) {
    modifiedSections.value.add(secId);
}

function addSectionDirectly() {
    const maxOrder = props.tkoSections.reduce((max, s) => Math.max(max, s.order || 0), 0);
    let nextOrder = maxOrder + 1;
    if (nextOrder === 4 || nextOrder === 5) {
        nextOrder = 6;
    }
    if (nextOrder === 9) {
        nextOrder = 10;
    }
    
    router.post(
        route('itom.policy.regulation.procedure.section.store'),
        {
            name: 'Section Baru',
            order: nextOrder,
        },
        { preserveScroll: true }
    );
}

function deleteSectionDirectly(sec) {
    Swal.fire({
        title: 'Hapus Section',
        text: `Apakah Anda yakin ingin menghapus section '${sec.name}' beserta seluruh konten di dalamnya?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('itom.policy.regulation.procedure.section.destroy', sec.id), { preserveScroll: true });
        }
    });
}

async function saveAll() {
    if (modifiedSections.value.size === 0) return;
    isSaving.value = true;
    saveStatus.value = 'saving';

    try {
        const promises = [];
        for (const secId of modifiedSections.value) {
            const secData = sectionLocal.value[secId];
            promises.push(
                axios.put(
                    route('itom.policy.regulation.procedure.section.update', secId),
                    { name: secData.name, order: secData.order },
                    { headers: { 'Accept': 'application/json' } }
                )
            );
        }
        await Promise.all(promises);
        modifiedSections.value.clear();
        saveStatus.value = 'saved';
        // Reload after delay so the "Tersimpan" notification is visible first
        setTimeout(() => {
            saveStatus.value = null;
            router.reload({ preserveScroll: true });
        }, 2000);
    } catch (error) {
        console.error('Gagal menyimpan section:', error);
        saveStatus.value = 'error';
    } finally {
        isSaving.value = false;
    }
}

// ─── Expose for parent tab-switch guard ───────────────────────────────────────
defineExpose({ hasUnsavedChanges, saveAll });
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
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
</style>
