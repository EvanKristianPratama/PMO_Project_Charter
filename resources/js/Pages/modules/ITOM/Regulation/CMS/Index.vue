<template>
    <ModulLayout title="CMS - Document Management">
        <div class="animate-fade-in-up space-y-6 -mx-4 sm:-mx-6 lg:-mx-8 -mt-5">
            <!-- Navigation Tabs -->
            <div
                class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto"
                >
                    <Link
                        :href="route('itom.policy.CMS.index')"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            route().current('itom.policy.CMS.index')
                                ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1A1A1A] dark:text-[#db588c]'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                    >
                        Document
                    </Link>
                    <Link
                        :href="route('itom.policy.CMS.regulation.index')"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            route().current('itom.policy.CMS.regulation.index')
                                ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1A1A1A] dark:text-[#db588c]'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                    >
                        Regulation
                    </Link>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Table with Deferred Loading -->
                <Deferred data="documents">
                    <template #fallback>
                        <TableSkeleton />
                    </template>

                    <!-- Table Card -->
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                        <!-- Table Header / Search & Filter -->
                        <div class="flex flex-row items-center justify-between gap-3 border-b border-slate-200 px-5 py-3 dark:border-white/10 flex-wrap">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                                    Content Management System
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
                                        <th class="px-5 py-3 w-12">No</th>
                                        <th class="px-5 py-3">Document</th>
                                        <th class="px-5 py-3">URL</th>
                                        <th class="px-5 py-3 text-center w-36">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700 dark:divide-white/5 dark:text-slate-300">
                                    <tr
                                        v-for="(doc, index) in filteredDocuments"
                                        :key="doc.id"
                                        class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition duration-150"
                                    >
                                        <td class="px-5 py-3.5 text-slate-400">{{ index + 1 }}</td>
                                        <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-white max-w-[200px] truncate">
                                            {{ doc.name }}
                                        </td>
                                        <td class="px-5 py-3.5 max-w-[250px] truncate">
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
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button
                                                    type="button"
                                                    @click="openEditModal(doc)"
                                                    class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="openDeleteModal(doc)"
                                                    class="inline-flex items-center justify-center rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:border-red-300 active:scale-95 dark:border-red-500/20 dark:bg-[#1a1a1a] dark:text-red-400 dark:hover:bg-red-500/10"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredDocuments.length === 0">
                                        <td colspan="4" class="px-5 py-12 text-center">
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
                </Deferred>

                <!-- Create / Edit Modal -->
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

                <!-- Delete Confirmation Modal -->
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
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Deferred, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    DocumentTextIcon,
    PlusIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    documents: {
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
            (doc.url && doc.url.toLowerCase().includes(q))
    );
});

// ---------- Create / Edit ----------
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

// ---------- Delete ----------
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
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.4s ease-out;
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
</style>