<template>
    <UserLayout title="Create Digital Initiative">
        <div class="mx-auto max-w-[1860px] animate-fade-in space-y-6">
            <div>
                <Link
                    :href="route('digital-initiatives.index')"
                    class="mb-2 flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">New Digital Initiative</h2>
            </div>

            <form @submit.prevent="submit">
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Data Utama</h3>
                    </div>

                    <div class="overflow-x-hidden">
                        <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                            <colgroup>
                                <col class="w-[10%]">
                                <col class="w-[22%]">
                                <col class="w-[16%]">
                                <col class="w-[14%]">
                                <col class="w-[14%]">
                                <col class="w-[14%]">
                                <col class="w-[10%]">
                            </colgroup>
                            <thead class="bg-slate-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Inisiatif</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Arsitektur Digital</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Status</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notes</th>
                                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                                <tr class="bg-slate-50/50 dark:bg-white/[0.03]">
                                    <td class="px-3 py-3 align-top">
                                        <input v-model="form.code" type="text" class="table-input" placeholder="DI-001">
                                        <p v-if="form.errors.code" class="mt-1 text-[10px] text-rose-600">{{ form.errors.code }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <input v-model="form.name" type="text" class="table-input" placeholder="Nama inisiatif digital">
                                        <p v-if="form.errors.name" class="mt-1 text-[10px] text-rose-600">{{ form.errors.name }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <input v-model="form.charter_category" type="text" class="table-input" placeholder="Contoh: Data Platform">
                                        <p v-if="form.errors.charter_category" class="mt-1 text-[10px] text-rose-600">{{ form.errors.charter_category }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <select v-model="form.status" class="table-input">
                                            <option v-for="statusOption in statusOptions" :key="statusOption.id" :value="statusOption.id">
                                                {{ statusOption.label }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.status" class="mt-1 text-[10px] text-rose-600">{{ form.errors.status }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <input v-model="form.project_status_changed_at" type="date" class="table-input">
                                        <p v-if="form.errors.project_status_changed_at" class="mt-1 text-[10px] text-rose-600">
                                            {{ form.errors.project_status_changed_at }}
                                        </p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <textarea
                                            v-model="form.project_status_notes"
                                            rows="3"
                                            class="table-input table-textarea"
                                            placeholder="Catatan status awal"
                                        />
                                        <p v-if="form.errors.project_status_notes" class="mt-1 text-[10px] text-rose-600">
                                            {{ form.errors.project_status_notes }}
                                        </p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <button
                                            type="submit"
                                            :disabled="form.processing"
                                            class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-[9px] font-semibold text-indigo-700 disabled:opacity-50 dark:bg-indigo-500/20 dark:text-indigo-300"
                                        >
                                            Create
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </form>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    statusOptions: {
        type: Array,
        default: () => [],
    },
    defaultStatusId: {
        type: Number,
        default: 1,
    },
});

const statusOptions = props.statusOptions.length > 0
    ? props.statusOptions
    : [{ id: 1, label: 'Drafting' }];

const currentDate = () => new Date().toISOString().slice(0, 10);

const route = useRouteHelper();

const form = useForm({
    code: '',
    name: '',
    owner_name: '',
    charter_category: '',
    status: statusOptions.some((statusOption) => statusOption.id === props.defaultStatusId)
        ? props.defaultStatusId
        : statusOptions[0].id,
    project_status_changed_at: currentDate(),
    project_status_notes: '',
});

const submit = () => {
    form.post(route('digital-initiatives.store'));
};
</script>

<style scoped>
.table-input {
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid rgb(203 213 225);
    background-color: rgb(255 255 255);
    padding: 0.25rem 0.5rem;
    font-size: 11px;
    color: rgb(15 23 42);
}

.table-input:focus {
    border-color: rgb(99 102 241);
    outline: none;
    box-shadow: 0 0 0 1px rgb(99 102 241 / 0.35);
}

.table-textarea {
    min-height: 64px;
    resize: vertical;
    line-height: 1.35;
}

:global(.dark) .table-input {
    border-color: rgb(255 255 255 / 0.1);
    background-color: rgb(19 19 19);
    color: rgb(226 232 240);
}
</style>
