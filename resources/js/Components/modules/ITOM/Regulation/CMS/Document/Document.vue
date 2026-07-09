<template>
    <div class="space-y-6">
        <!-- Table Card -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Table Header / Search & Filter -->
            <div class="flex flex-row items-center justify-between gap-3 border-b border-slate-200 px-5 py-3 dark:border-white/10 flex-wrap">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Content Management System - Document List
                    </h2>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <MagnifyingGlassIcon class="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search documents..."
                            class="w-56 rounded-lg border border-slate-300 bg-white py-1.5 pl-8 pr-3 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white placeholder-slate-400 transition"
                        />
                    </div>
                    <button
                        type="button"
                        @click="openCreateModal"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#a02854]"
                    >
                        <PlusIcon class="h-3 w-3" />
                        Add Document
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/5 dark:bg-[#1f1f1f]/50 dark:text-slate-400">
                            <th class="px-2 py-3 w-8 text-center">No</th>
                            <th class="px-2 py-3 w-[25%]">Document Name</th>
                            <th class="px-2 py-3 w-[25%]">URL</th>
                            <th class="px-2 py-3 w-[30%]">Regulations</th>
                            <th class="px-2 py-3 text-center w-64">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700 dark:divide-white/5 dark:text-slate-300">
                        <tr
                            v-for="(doc, index) in filteredDocuments"
                            :key="doc.id"
                            class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition duration-150"
                        >
                            <td class="px-2 py-3.5 text-center text-slate-400 w-8">{{ index + 1 }}</td>
                            <td class="px-2 py-3.5 font-semibold text-slate-900 dark:text-white break-words">
                                {{ doc.name }}
                            </td>
                            <td class="px-2 py-3.5 break-all">
                                <a
                                    v-if="doc.url"
                                    :href="doc.url"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline underline-offset-2"
                                >
                                    {{ doc.url }}
                                </a>
                                <span v-else class="text-slate-400 italic">—</span>
                            </td>
                            <td class="px-2 py-3.5">
                                <ul v-if="doc.regulations && doc.regulations.length > 0" class="space-y-1.5">
                                    <li
                                        v-for="reg in doc.regulations"
                                        :key="reg.id"
                                        class="flex items-start gap-1.5 text-[10px] text-slate-600 dark:text-slate-300 leading-snug"
                                    >
                                        <span class="shrink-0 mt-[3px] h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                        <span>
                                            <span class="font-semibold text-slate-900 dark:text-white">{{ reg.tipe }}: {{ reg.judul }}</span>
                                            <span class="text-slate-400 dark:text-slate-500 block font-mono text-[9px]">{{ reg.nomor }}</span>
                                        </span>
                                    </li>
                                </ul>
                                <span v-else class="text-[10px] text-slate-400 italic pl-1">—</span>
                            </td>
                            <td class="px-2 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="openMapModal(doc)"
                                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-[#821f44] hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-[#db588c] dark:hover:bg-white/5"
                                    >
                                        Mapping
                                    </button>
                                    <button
                                        type="button"
                                        @click="openEditModal(doc)"
                                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="openDeleteModal(doc)"
                                        class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:border-red-300 active:scale-95 dark:border-red-500/20 dark:bg-[#1a1a1a] dark:text-red-400 dark:hover:bg-red-500/10"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredDocuments.length === 0">
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <DocumentTextIcon class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                        {{ searchQuery ? 'No documents match your search' : 'No documents available' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create / Edit Document Modal -->
        <ConfirmationModal
            :show="formModal.show"
            :title="formModal.isEditing ? 'Edit Document' : 'Add New Document'"
            :message="formModal.isEditing ? 'Update the document details below.' : 'Fill in the document details below.'"
            :confirm-text="formModal.isEditing ? 'Save' : 'Add'"
            cancel-text="Cancel"
            type="info"
            :loading="form.processing"
            @close="closeFormModal"
            @confirm="submitForm"
        >
            <div class="mt-4 space-y-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Document Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Enter document name"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                    />
                    <p v-if="form.errors.name" class="text-[10px] text-red-500">{{ form.errors.name }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Document URL
                    </label>
                    <input
                        v-model="form.url"
                        type="url"
                        placeholder="https://example.com/document.pdf"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                    />
                    <p v-if="form.errors.url" class="text-[10px] text-red-500">{{ form.errors.url }}</p>
                </div>
            </div>
        </ConfirmationModal>

        <!-- Mapping Regulation Modal -->
        <ConfirmationModal
            :show="mapModal.show"
            title="Map Document to Regulation"
            message="Pilih regulasi yang akan dihubungkan ke dokumen ini."
            confirm-text="Map Regulation"
            cancel-text="Cancel"
            type="info"
            :loading="mapForm.processing"
            @close="closeMapModal"
            @confirm="submitMapForm"
        >
            <div class="mt-4 space-y-4 text-left">
                <!-- Autocomplete Selection List -->
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">
                        Pilih Regulasi <span class="text-red-500">*</span>
                    </label>
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Cari regulasi..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-44 overflow-y-auto">
                            <li
                                v-for="item in filteredRegulationsForSelect"
                                :key="item.id"
                                @click="selectRegulation(item.id)"
                                class="flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none hover:bg-slate-50 dark:hover:bg-white/5"
                                :class="mapForm.regulation_id === item.id ? 'bg-blue-50 dark:bg-blue-500/10' : ''"
                            >
                                <span class="mr-1.5 shrink-0 font-medium text-slate-400 dark:text-slate-500">—</span>
                                <span :class="mapForm.regulation_id === item.id ? 'font-semibold text-blue-700 dark:text-blue-300' : 'text-slate-600 dark:text-slate-400'">
                                    [{{ item.tipe }}] {{ item.judul }} <span v-if="item.nomor">({{ item.nomor }})</span>
                                </span>
                            </li>
                            <li v-if="filteredRegulationsForSelect.length === 0" class="px-3 py-3 text-slate-400 dark:text-slate-500 italic text-[11px] text-center select-none">
                                Tidak ada regulasi yang cocok atau belum dimapping.
                            </li>
                        </ul>
                    </div>
                    <!-- Selected label -->
                    <p v-if="mapForm.regulation_id" class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">
                        ✓ Dipilih: {{ selectedRegulationName }}
                    </p>
                    <p v-if="mapForm.errors.regulation_id" class="text-[10px] text-red-500">{{ mapForm.errors.regulation_id }}</p>
                </div>

                <!-- Selected Items (Currently Mapped Regulations for this Document) -->
                <div v-if="currentMapDocument?.regulations && currentMapDocument.regulations.length > 0" class="flex flex-col gap-2 pt-3 border-t border-slate-200 dark:border-white/10 mt-3">
                    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">
                        Regulasi Terhubung ({{ currentMapDocument.regulations.length }})
                    </label>
                    <div class="space-y-1 max-h-32 overflow-y-auto pr-1">
                        <div
                            v-for="reg in currentMapDocument.regulations"
                            :key="'sel-' + reg.id"
                            class="flex items-center justify-between px-3 py-1.5 rounded-lg border border-slate-100 bg-slate-50 dark:border-white/5 dark:bg-[#1a1a1a] hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            <div class="flex items-center gap-2">
                                <span class="flex h-1.5 w-1.5 rounded-full bg-[#821f44] shrink-0"></span>
                                <span class="text-[11px] font-medium text-slate-800 dark:text-slate-200">
                                    [{{ reg.tipe }}] {{ reg.judul }} <span v-if="reg.nomor" class="text-slate-400 dark:text-slate-500 font-mono text-[9px]">({{ reg.nomor }})</span>
                                </span>
                            </div>
                            <button
                                type="button"
                                @click.stop="openUnmapConfirm(currentMapDocument, reg)"
                                class="inline-flex items-center justify-center rounded-md p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition shrink-0"
                                title="Unmap"
                            >
                                <TrashIcon class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </ConfirmationModal>

        <!-- Delete Document Confirmation Modal -->
        <ConfirmationModal
            :show="deleteModal.show"
            title="Delete Document"
            :message="'Are you sure you want to delete document ' + (deleteModal.document?.name ? '\u201C' + deleteModal.document.name + '\u201D' : '') + '?'"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            type="danger"
            :loading="formDelete.processing"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />

        <!-- Unmap Regulation Confirmation Modal -->
        <ConfirmationModal
            :show="unmapModal.show"
            title="Remove Regulation Mapping"
            :message="'Are you sure you want to remove the link to regulation \u201C' + (unmapModal.regulation?.judul || '') + '\u201D?'"
            confirm-text="Yes, Unmap"
            cancel-text="Cancel"
            type="danger"
            :loading="unmapForm.processing"
            @close="closeUnmapModal"
            @confirm="confirmUnmap"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {
    DocumentTextIcon,
    PlusIcon,
    MagnifyingGlassIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    documents: {
        type: Array,
        default: () => [],
    },
    regulationOptions: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');

const filteredDocuments = computed(() => {
    if (!searchQuery.value) return props.documents;
    const q = searchQuery.value.toLowerCase();
    return props.documents.filter(
        (doc) =>
            (doc.name && doc.name.toLowerCase().includes(q)) ||
            (doc.url && doc.url.toLowerCase().includes(q)) ||
            (doc.regulations && doc.regulations.some(reg => 
                (reg.judul && reg.judul.toLowerCase().includes(q)) ||
                (reg.nomor && reg.nomor.toLowerCase().includes(q)) ||
                (reg.tipe && reg.tipe.toLowerCase().includes(q))
            ))
    );
});

// ---------- Create / Edit Document ----------
const formModal = ref({
    show: false,
    isEditing: false,
    editingId: null,
});

const form = useForm({
    name: '',
    url: '',
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    formModal.value = { show: true, isEditing: false, editingId: null };
};

const openEditModal = (doc) => {
    form.reset();
    form.clearErrors();
    form.name = doc.name;
    form.url = doc.url || '';
    formModal.value = { show: true, isEditing: true, editingId: doc.id };
};

const closeFormModal = () => {
    formModal.value = { show: false, isEditing: false, editingId: null };
    form.reset();
};

const submitForm = () => {
    if (formModal.value.isEditing) {
        form.put(route('itom.policy.CMS.update', formModal.value.editingId), {
            preserveScroll: true,
            onSuccess: () => closeFormModal(),
        });
    } else {
        form.post(route('itom.policy.CMS.store'), {
            preserveScroll: true,
            onSuccess: () => closeFormModal(),
        });
    }
};

// ---------- Delete Document ----------
const deleteModal = ref({
    show: false,
    document: null,
});

const formDelete = useForm({});

const openDeleteModal = (doc) => {
    deleteModal.value = { show: true, document: doc };
};

const closeDeleteModal = () => {
    deleteModal.value = { show: false, document: null };
};

const confirmDelete = () => {
    if (!deleteModal.value.document) return;
    formDelete.delete(route('itom.policy.CMS.destroy', deleteModal.value.document.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};

// ---------- Mapping Regulation ----------
const mapModal = ref({
    show: false,
    documentId: null,
});

const currentMapDocument = computed(() => {
    if (!mapModal.value.documentId) return null;
    return props.documents.find(d => d.id === mapModal.value.documentId) || null;
});

const mapForm = useForm({
    document_id: '',
    regulation_id: '',
});

const searchTerm = ref('');

const availableRegulationsForDoc = computed(() => {
    if (!currentMapDocument.value) return props.regulationOptions;
    
    // Filter out already mapped regulations
    const mappedIds = new Set(
        currentMapDocument.value.regulations ? currentMapDocument.value.regulations.map(r => r.id) : []
    );
    
    return props.regulationOptions.filter(r => !mappedIds.has(r.id));
});

const filteredRegulationsForSelect = computed(() => {
    const list = availableRegulationsForDoc.value;
    if (!searchTerm.value) return list;
    const q = searchTerm.value.toLowerCase();
    return list.filter(r => 
        (r.judul && r.judul.toLowerCase().includes(q)) ||
        (r.nomor && r.nomor.toLowerCase().includes(q)) ||
        (r.tipe && r.tipe.toLowerCase().includes(q))
    );
});

const selectedRegulationName = computed(() => {
    if (!mapForm.regulation_id) return '';
    const reg = props.regulationOptions.find(r => r.id === mapForm.regulation_id);
    return reg ? `[${reg.tipe}] ${reg.judul}` : '';
});

const selectRegulation = (id) => {
    mapForm.regulation_id = id;
};

const openMapModal = (doc) => {
    mapForm.reset();
    mapForm.clearErrors();
    mapForm.document_id = doc.id;
    mapForm.regulation_id = '';
    searchTerm.value = '';
    mapModal.value = { show: true, documentId: doc.id };
};

const closeMapModal = () => {
    mapModal.value = { show: false, documentId: null };
    mapForm.reset();
    searchTerm.value = '';
};

const submitMapForm = () => {
    if (!mapForm.regulation_id) {
        mapForm.setError('regulation_id', 'Regulasi wajib dipilih.');
        return;
    }
    mapForm.post(route('itom.policy.CMS.regulation.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Keep the modal open but reset selection to allow mapping another regulation if needed.
            mapForm.regulation_id = '';
            searchTerm.value = '';
        },
    });
};

// ---------- Unmap Regulation ----------
const unmapModal = ref({
    show: false,
    document: null,
    regulation: null,
});

const unmapForm = useForm({});

const openUnmapConfirm = (doc, reg) => {
    unmapModal.value = { show: true, document: doc, regulation: reg };
};

const closeUnmapModal = () => {
    unmapModal.value = { show: false, document: null, regulation: null };
};

const confirmUnmap = () => {
    if (!unmapModal.value.document || !unmapModal.value.regulation) return;
    
    unmapForm.delete(
        route('itom.policy.CMS.regulation.destroy', {
            document_id: unmapModal.value.document.id,
            regulation_id: unmapModal.value.regulation.id,
        }), 
        {
            preserveScroll: true,
            onSuccess: () => closeUnmapModal(),
        }
    );
};
</script>

<style scoped>
/* No leak CSS */
</style>
