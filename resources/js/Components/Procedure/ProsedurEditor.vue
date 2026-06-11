<template>
    <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
        <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

        <!-- V. PROSEDUR -->
        <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                V. PROSEDUR
            </h3>
            <div class="flex items-center gap-3 print:hidden">
                <!-- Save status indicator -->
                <span class="text-[11px] flex items-center gap-1.5 select-none">
                    <span v-if="isSaving" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
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
                    <span v-else-if="hasUnsavedChanges" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
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
                    :disabled="isSaving || !hasUnsavedChanges"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    Simpan
                </button>

                <button
                    @click="addCategory"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#821f44] bg-transparent px-3 py-1.5 text-xs font-bold text-[#821f44] hover:bg-[#821f44]/5 transition-all focus:ring-2 focus:ring-[#821f44]/20 active:scale-95 dark:border-[#a83262] dark:text-[#a83262] print:hidden"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori
                </button>
            </div>
        </div>

        <!-- SOP List by Category -->
        <div class="mt-6 space-y-8 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100">
            <div v-if="categories.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">
                Belum ada data SOP untuk regulasi ini.
            </div>
            <div v-for="cat in categories" :key="cat.id" class="space-y-4">
                <!-- Category Title (Editable) -->
                <div class="flex items-center justify-between gap-4 group/cat">
                    <div class="flex-1">
                        <input
                            v-if="categoryLocal[cat.id]"
                            v-model="categoryLocal[cat.id].tipe"
                            @input="markCategoryModified(cat.id)"
                            class="font-bold text-slate-950 dark:text-white bg-transparent border border-transparent rounded hover:border-slate-300 dark:hover:border-white/10 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 focus:bg-white dark:focus:bg-[#1e1e1e] text-sm md:text-base w-full px-2 py-1"
                            placeholder="Nama Kategori (Contoh: A. Penyusunan RSTI)"
                        />
                    </div>
                    <button
                        @click="deleteCategory(cat)"
                        class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition-colors ml-4 opacity-0 group-hover/cat:opacity-100 focus:opacity-100 print:hidden animate-fade-in-up"
                    >
                        Hapus Kategori
                    </button>
                </div>

                <!-- SOP Items -->
                <div class="space-y-3">
                    <div
                        v-for="(item, index) in getSopsForCategory(cat.id)"
                        :key="item.id"
                        class="flex gap-3 items-start group/sop"
                    >
                        <span class="font-bold min-w-[20px] text-right select-none pt-2 text-[15px] text-slate-950 dark:text-white">
                            {{ index + 1 }}.
                        </span>
                        <div class="flex-1">
                            <textarea
                                v-if="sopLocal[item.id]"
                                v-model="sopLocal[item.id].description"
                                @input="markSopModified(item.id)"
                                rows="2"
                                class="w-full bg-transparent px-3 py-2 text-justify font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 border border-transparent rounded hover:border-slate-300 dark:hover:border-white/10 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 focus:bg-white dark:focus:bg-[#1e1e1e]"
                                placeholder="Tulis deskripsi aktivitas SOP..."
                            ></textarea>
                        </div>
                        <button
                            @click="deleteSop(item)"
                            class="text-xs text-rose-600 hover:text-rose-800 transition-colors pt-2 opacity-0 group-hover/sop:opacity-100 focus:opacity-100 print:hidden"
                        >
                            Hapus
                        </button>
                    </div>
                    <div
                        v-if="getSopsForCategory(cat.id).length === 0"
                        class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400"
                    >
                        Belum ada data SOP untuk kategori ini.
                    </div>
                </div>

                <!-- Add SOP -->
                <div class="pl-8">
                    <button
                        @click="addSop(cat.id)"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors mt-1 print:hidden"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah SOP
                    </button>
                </div>
            </div>
        </div>

        <!-- VI. DIAGRAM ALIR -->
        <div class="mt-12 pt-10 border-t border-slate-200 dark:border-white/10">
            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                VI. DIAGRAM ALIR
            </h3>
            <div class="mt-6 space-y-6">
                <div
                    v-if="categories.length === 0"
                    class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400"
                >
                    Belum ada data diagram untuk regulasi ini.
                </div>
                <div
                    v-for="cat in categories"
                    :key="cat.id"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div class="border-b border-slate-200 px-5 py-3 dark:border-white/10">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">{{ cat.tipe }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="min-w-[1200px] p-4">
                            <FlowChart :actors="actors" :sops="flowChartSops" :flow-type="String(cat.id)" :categories="[cat]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';
import FlowChart from '@/Components/Procedure/FlowChart.vue';

// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
    flowChartSops: {
        type: Array,
        default: () => [],
    },
    actors: {
        type: Array,
        default: () => [],
    },
    activeRegulation: {
        type: Object,
        default: null,
    },
});

// ─── State ────────────────────────────────────────────────────────────────────
const categoryLocal = ref({});
const sopLocal = ref({});
const modifiedCategories = ref(new Set());
const modifiedSops = ref(new Set());
const isSaving = ref(false);
const saveStatus = ref(null); // null | 'saved' | 'error'

// ─── Computed ─────────────────────────────────────────────────────────────────
const hasUnsavedChanges = computed(
    () => modifiedCategories.value.size > 0 || modifiedSops.value.size > 0
);

// ─── Init local state ─────────────────────────────────────────────────────────
function initLocal() {
    props.categories.forEach(cat => {
        if (!categoryLocal.value[cat.id]) {
            categoryLocal.value[cat.id] = { tipe: cat.tipe || '' };
        } else if (!modifiedCategories.value.has(cat.id)) {
            categoryLocal.value[cat.id].tipe = cat.tipe || '';
        }
    });

    props.sop.forEach(item => {
        if (!sopLocal.value[item.id]) {
            sopLocal.value[item.id] = {
                category_id: item.category_id,
                description: item.description || '',
            };
        } else if (!modifiedSops.value.has(item.id)) {
            sopLocal.value[item.id].category_id = item.category_id;
            sopLocal.value[item.id].description = item.description || '';
        }
    });
}

initLocal();

watch(() => [props.categories, props.sop], () => { initLocal(); }, { deep: true });

// ─── Helpers ──────────────────────────────────────────────────────────────────
function getSopsForCategory(categoryId) {
    return props.sop.filter(s => Number(s.category_id) === Number(categoryId));
}

function shortDescription(text) {
    if (!text) return '-';
    const clean = String(text).replace(/\r?\n/g, ' ').trim();
    return clean.length > 80 ? `${clean.slice(0, 80)}...` : clean;
}

// ─── Mark modified ────────────────────────────────────────────────────────────
function markCategoryModified(catId) {
    modifiedCategories.value.add(catId);
}

function markSopModified(sopId) {
    modifiedSops.value.add(sopId);
}

// ─── CRUD Actions ─────────────────────────────────────────────────────────────
function addCategory() {
    router.post(
        route('policy.procedure.category.store'),
        {
            regulation_id: props.activeRegulation?.id || '',
            tipe: 'Kategori Baru',
        },
        { preserveScroll: true }
    );
}

function deleteCategory(cat) {
    Swal.fire({
        title: 'Hapus Kategori',
        text: `Apakah Anda yakin ingin menghapus kategori '${cat.tipe}' beserta seluruh SOP di dalamnya?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.category.destroy', cat.id), { preserveScroll: true });
        }
    });
}

function addSop(categoryId) {
    router.post(
        route('policy.procedure.sop.store'),
        {
            category_id: categoryId,
            description: 'Aktivitas SOP Baru',
        },
        { preserveScroll: true }
    );
}

function deleteSop(item) {
    Swal.fire({
        title: 'Hapus SOP',
        text: `Apakah Anda yakin ingin menghapus SOP '${shortDescription(item.description)}'?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.sop.destroy', item.id), { preserveScroll: true });
        }
    });
}

// ─── Save All ─────────────────────────────────────────────────────────────────
async function saveAll() {
    if (!hasUnsavedChanges.value) return;
    isSaving.value = true;
    saveStatus.value = null;

    try {
        const promises = [];

        for (const catId of modifiedCategories.value) {
            promises.push(
                axios.put(
                    route('policy.procedure.category.update', catId),
                    { tipe: categoryLocal.value[catId].tipe },
                    { headers: { 'Accept': 'application/json' } }
                )
            );
        }

        for (const sopId of modifiedSops.value) {
            const data = sopLocal.value[sopId];
            promises.push(
                axios.put(
                    route('policy.procedure.sop.update', sopId),
                    { category_id: data.category_id, description: data.description },
                    { headers: { 'Accept': 'application/json' } }
                )
            );
        }

        await Promise.all(promises);
        modifiedCategories.value.clear();
        modifiedSops.value.clear();
        saveStatus.value = 'saved';
        // Reload after delay so the "Tersimpan" notification is visible first
        setTimeout(() => {
            saveStatus.value = null;
            router.reload({ preserveScroll: true });
        }, 2000);
    } catch (error) {
        console.error('Gagal menyimpan prosedur:', error);
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
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
