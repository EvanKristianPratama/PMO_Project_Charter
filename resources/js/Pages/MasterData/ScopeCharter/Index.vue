<template>
    <UserLayout title="Master Data - Scope Charter">
        <div class="animate-fade-in-up space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Scope Charter</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Kelola data Scope Charter untuk Digital Initiative.
                        </p>
                    </div>
                    <a
                        :href="route('master-data.index')"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        Kembali ke Master Data
                    </a>
                </div>
            </section>

            <article class="rounded-2xl border border-teal-600 bg-teal-600 p-4 shadow-md">
                <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-teal-100">Total Scope Charter</p>
                <p class="mt-1 text-3xl font-bold text-white">{{ initiatives.length }}</p>
            </article>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 dark:border-white/10">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari use case / value..."
                        class="rounded border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-700 shadow-sm focus:border-teal-400 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    />
                    <button
                        v-if="search"
                        type="button"
                        class="rounded border border-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:hover:bg-white/5"
                        @click="search = ''"
                    >
                        Reset
                    </button>
                    <div class="ml-auto">
                        <button
                            type="button"
                            class="rounded-lg bg-teal-600 px-3 py-1 text-[11px] font-semibold text-white shadow-sm hover:bg-teal-700"
                            @click="openCreateModal"
                        >
                            + Tambah
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th
                                    v-for="header in headers"
                                    :key="header"
                                    class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500"
                                >
                                    {{ header }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr
                                v-for="(item, idx) in filtered"
                                :key="item.id"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5"
                            >
                                <td class="px-3 py-2.5 text-slate-500">{{ idx + 1 }}</td>
                                <td class="px-3 py-2.5 text-slate-700 dark:text-slate-200">{{ item.category_fase ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-700 dark:text-slate-200">{{ item.use_case?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">
                                    <p class="line-clamp-2 break-words">{{ item.useCase_description ?? '-' }}</p>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.value ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.urgency ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.source?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                            @click="openEditModal(item)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-semibold text-rose-700 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300"
                                            @click="confirmDelete(item)"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filtered.length === 0">
                                <td colspan="8" class="px-6 py-8 text-center text-xs text-slate-500">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            v-if="showFormModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            @click.self="closeFormModal"
        >
            <div class="w-full max-w-2xl rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#1a1a1a]">
                <div class="rounded-t-xl bg-teal-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">{{ editingItem ? 'Edit' : 'Tambah' }} Scope Charter</h2>
                </div>
                <form class="space-y-5 p-6" @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <FormField label="Kategori Fase" :error="form.errors.category_fase">
                            <input v-model="form.category_fase" type="text" placeholder="e.g. Planning" class="form-input" />
                        </FormField>
                        <FormField label="Use Case" :error="form.errors.useCase_id">
                            <select v-model="form.useCase_id" class="form-input">
                                <option :value="null">- Pilih Use Case -</option>
                                <option v-for="useCase in useCaseOptions" :key="useCase.id" :value="useCase.id">
                                    {{ useCase.name }}
                                </option>
                            </select>
                        </FormField>
                        <FormField label="Value" :error="form.errors.value">
                            <input v-model="form.value" type="text" placeholder="Value" class="form-input" />
                        </FormField>
                        <FormField label="Urgency" :error="form.errors.urgency">
                            <input v-model="form.urgency" type="text" placeholder="Urgency" class="form-input" />
                        </FormField>
                        <FormField label="Sumber Data" :error="form.errors.source_id">
                            <select v-model="form.source_id" class="form-input">
                                <option :value="null">- Pilih Sumber -</option>
                                <option v-for="source in sourceOptions" :key="source.id" :value="source.id">
                                    {{ source.name }}
                                </option>
                            </select>
                        </FormField>
                    </div>

                    <FormField label="Deskripsi Use Case" :error="form.errors.useCase_description">
                        <textarea v-model="form.useCase_description" rows="3" placeholder="Deskripsi..." class="form-input"></textarea>
                    </FormField>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5 dark:border-white/5">
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="closeFormModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-teal-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 disabled:opacity-50"
                        >
                            {{ editingItem ? 'Simpan Perubahan' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <ConfirmDeleteModal
            v-if="deleteTarget"
            :name="deleteTarget.use_case?.name ?? deleteTarget.useCase_description ?? 'Scope Charter'"
            @cancel="deleteTarget = null"
            @confirm="doDelete"
        />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import FormField from '@/Components/MasterData/FormField.vue';
import ConfirmDeleteModal from '@/Components/MasterData/ConfirmDeleteModal.vue';

const props = defineProps({
    initiatives: { type: Array, default: () => [] },
    useCaseOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
});

const route = useRouteHelper();

const headers = ['No', 'Fase', 'Use Case', 'Deskripsi', 'Value', 'Urgency', 'Sumber', 'Action'];

const search = ref('');

const filtered = computed(() => {
    if (!search.value) {
        return props.initiatives;
    }

    const query = search.value.toLowerCase();

    return props.initiatives.filter((item) =>
        (item.use_case?.name ?? '').toLowerCase().includes(query) ||
        (item.useCase_description ?? '').toLowerCase().includes(query) ||
        (item.value ?? '').toLowerCase().includes(query)
    );
});

const showFormModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    category_fase: '',
    useCase_id: null,
    useCase_description: '',
    value: '',
    urgency: '',
    source_id: null,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.clearErrors();
    showFormModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    Object.assign(form, {
        category_fase: item.category_fase ?? '',
        useCase_id: item.useCase_id ?? null,
        useCase_description: item.useCase_description ?? '',
        value: item.value ?? '',
        urgency: item.urgency ?? '',
        source_id: item.source_id ?? null,
    });
    form.clearErrors();
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    editingItem.value = null;
};

const submitForm = () => {
    const options = {
        onSuccess: () => closeFormModal(),
    };

    if (editingItem.value) {
        form.put(route('master-data.scope-charter.update', editingItem.value.id), options);
        return;
    }

    form.post(route('master-data.scope-charter.store'), options);
};

const deleteTarget = ref(null);

const confirmDelete = (item) => {
    deleteTarget.value = item;
};

const doDelete = () => {
    if (!deleteTarget.value) {
        return;
    }

    router.delete(route('master-data.scope-charter.destroy', deleteTarget.value.id), {
        onFinish: () => {
            deleteTarget.value = null;
        },
    });
};
</script>

<style scoped>
@reference "tailwindcss";

.form-input {
    @apply w-full rounded-md border-slate-300 bg-slate-50 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100;
}
</style>
