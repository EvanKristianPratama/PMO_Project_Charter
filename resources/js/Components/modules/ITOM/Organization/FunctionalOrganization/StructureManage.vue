<template>
    <!-- Kelola Structure Modal -->
    <ConfirmationModal
        :show="isFunctionModalOpen"
        :title="`Kelola Structure - ${selectedFunctional?.name}`"
        message="Berikut adalah daftar structure (struktur internal) yang dikaitkan dengan organisasi fungsional ini."
        confirm-text="Tutup"
        cancel-text=""
        type="info"
        :loading="functionForm.processing"
        max-width="lg"
        @close="isFunctionModalOpen = false"
        @confirm="isFunctionModalOpen = false"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Add Structure Form -->
            <form @submit.prevent="submitAddFunction" class="flex flex-col gap-3 p-3 bg-slate-50 rounded-xl dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Tambah Structure Baru</h4>
                <div class="flex flex-col gap-1.5">
                    <label for="function_name_input" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Nama Structure / Struktur Internal</label>
                    <div class="flex gap-2">
                        <input
                            id="function_name_input"
                            v-model="functionForm.name"
                            type="text"
                            placeholder="Contoh: Ketua, Sekretaris, Anggota..."
                            class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            required
                        />
                        <button
                            type="submit"
                            :disabled="functionForm.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                        >
                            {{ functionForm.processing ? 'Adding...' : 'Tambah' }}
                        </button>
                    </div>
                    <span v-if="functionForm.errors.name" class="text-[10px] text-red-500 font-medium">{{ functionForm.errors.name }}</span>
                </div>
                <!-- Parent Selector -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Parent Structure <span class="font-normal text-slate-400">(opsional)</span></label>
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="functionParentSearch"
                                type="text"
                                placeholder="Cari parent structure..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-44 overflow-y-auto">
                            <li
                                @click="functionForm.parent_id = null"
                                class="flex items-center cursor-pointer py-1.5 px-3 text-[11px] border-b border-slate-50 dark:border-white/5 transition select-none font-medium text-slate-600 dark:text-slate-400"
                                :class="functionForm.parent_id === null
                                    ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 font-semibold'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                — Tanpa Parent (Root) —
                            </li>
                            <li v-if="filteredParentFunctions.length === 0 && functionParentSearch"
                                class="px-3 py-2 text-[11px] text-slate-400 dark:text-slate-500 text-center italic">
                                Tidak ada structure tersedia
                            </li>
                            <li
                                v-for="fun in filteredParentFunctions"
                                :key="fun.structure_id"
                                @click="functionForm.parent_id = fun.structure_id"
                                class="flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
                                :style="{ paddingLeft: `${8 + (fun._level || 0) * 14}px` }"
                                :class="functionForm.parent_id === fun.structure_id
                                    ? 'bg-blue-50 dark:bg-blue-500/10'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                <span
                                    class="mr-1.5 shrink-0 font-medium"
                                    :class="fun._level === 0
                                        ? 'text-slate-600 dark:text-slate-400'
                                        : 'text-slate-400 dark:text-slate-500'"
                                >—</span>
                                <span
                                    :class="functionForm.parent_id === fun.structure_id
                                        ? 'font-semibold text-blue-700 dark:text-blue-300'
                                        : fun._level === 0
                                            ? 'font-medium text-slate-800 dark:text-slate-200'
                                            : 'text-slate-600 dark:text-slate-400'"
                                >{{ fun.name }}</span>
                            </li>
                        </ul>
                    </div>
                    <span v-if="functionForm.errors.parent_id" class="text-[10px] text-red-500 font-medium">{{ functionForm.errors.parent_id }}</span>
                </div>
            </form>

            <!-- List of Current Structures (tree) -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Structure Saat Ini</h4>
                <div v-if="selectedFunctional?.functions && selectedFunctional.functions.length > 0" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    <template v-for="fun in buildTree(selectedFunctional?.functions ?? [])" :key="fun.structure_id">
                        <!-- Root item -->
                        <div class="rounded-xl border border-slate-100 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a] transition hover:shadow-md">
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                    <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ fun.name }}</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 italic">root</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="manageMembers(fun)"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 px-2.5 py-1 text-xs font-semibold shadow-sm transition dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Anggota
                                    </button>
                                    <button type="button" @click="deleteFunction(fun.structure_id)"
                                        class="inline-flex items-center justify-center rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 transition"
                                        title="Hapus Structure">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Children -->
                            <div v-if="fun.children && fun.children.length > 0" class="border-t border-slate-100 dark:border-white/5 divide-y divide-slate-50 dark:divide-white/5">
                                <div v-for="child in fun.children" :key="child.structure_id"
                                    class="flex items-center justify-between px-3 py-2 pl-7 bg-slate-50/50 dark:bg-white/[0.02]">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-300 dark:text-slate-600 text-xs">└</span>
                                        <span class="flex h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        <span class="text-xs text-slate-700 dark:text-slate-300">{{ child.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="manageMembers(child)"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 px-2.5 py-1 text-xs font-semibold shadow-sm transition dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            Anggota
                                        </button>
                                        <button type="button" @click="deleteFunction(child.structure_id)"
                                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 transition"
                                            title="Hapus Sub-Structure">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div v-else class="text-xs text-slate-500 dark:text-slate-400 py-6 text-center border border-dashed border-slate-200 rounded-xl dark:border-white/10">
                    Belum ada structure. Silakan ketik nama structure di atas untuk menambahkan.
                </div>
            </div>
        </div>
    </ConfirmationModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    functionalOrganizations: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['open-member']);

const isFunctionModalOpen = ref(false);
const selectedFunctional = ref(null);
const functionParentSearch = ref('');

const functionForm = useForm({
    functional_org_id: '',
    name: '',
    parent_id: null,
});

watch(() => props.functionalOrganizations, (newVal) => {
    if (selectedFunctional.value) {
        const updated = newVal.find(f => f.id === selectedFunctional.value.id);
        if (updated) {
            selectedFunctional.value = updated;
        }
    }
}, { deep: true });

const open = (row) => {
    selectedFunctional.value = row;
    functionForm.clearErrors();
    functionForm.reset();
    functionForm.functional_org_id = row.id;
    functionForm.parent_id = null;
    functionParentSearch.value = '';
    isFunctionModalOpen.value = true;
};

const close = () => {
    isFunctionModalOpen.value = false;
};

const manageMembers = (fun) => {
    emit('open-member', {
        functional: selectedFunctional.value,
        structure: fun,
    });
};

const submitAddFunction = () => {
    functionForm.post(route('itom.business-process.organization-structure.functional.structure.store'), {
        onSuccess: () => {
            functionForm.reset('name');
            functionForm.parent_id = null;
            functionParentSearch.value = '';
        },
    });
};

const deleteFunction = (structureId) => {
    const delFuncForm = useForm({
        structure_id: structureId,
    });
    delFuncForm.delete(route('itom.business-process.organization-structure.functional.structure.destroy'), {
        data: {
            structure_id: structureId,
        },
    });
};

// ─── Hierarchy Helpers ───────────────────────────────────────────────────────
const flattenWithLevel = (items, idKey = 'id', parentKey = 'parent_id') => {
    if (!items || items.length === 0) return [];
    const map = {};
    const roots = [];
    items.forEach(item => { map[item[idKey]] = { ...item, _children: [], _level: 0 }; });
    items.forEach(item => {
        if (item[parentKey] && map[item[parentKey]]) {
            map[item[parentKey]]._children.push(map[item[idKey]]);
        } else {
            roots.push(map[item[idKey]]);
        }
    });
    const result = [];
    const walk = (node, level) => {
        node._level = level;
        result.push(node);
        node._children.forEach(child => walk(child, level + 1));
    };
    roots.forEach(r => walk(r, 0));
    return result;
};

const flatAvailableFunctions = computed(() => {
    return flattenWithLevel(selectedFunctional.value?.functions ?? [], 'structure_id', 'parent_id');
});

const filteredParentFunctions = computed(() => {
    const q = functionParentSearch.value.toLowerCase().trim();
    if (!q) return flatAvailableFunctions.value;
    return (selectedFunctional.value?.functions ?? [])
        .filter(f => f.name.toLowerCase().includes(q))
        .map(f => ({ ...f, _level: 0 }));
});

const buildTree = (functions) => {
    if (!functions || functions.length === 0) return [];
    const map = {};
    const roots = [];
    functions.forEach(fn => {
        map[fn.structure_id] = { ...fn, children: [] };
    });
    functions.forEach(fn => {
        if (fn.parent_id && map[fn.parent_id]) {
            map[fn.parent_id].children.push(map[fn.structure_id]);
        } else {
            roots.push(map[fn.structure_id]);
        }
    });
    return roots;
};

defineExpose({
    open,
    close,
});
</script>
