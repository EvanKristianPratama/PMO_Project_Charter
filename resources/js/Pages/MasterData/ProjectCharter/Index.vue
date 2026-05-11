<template>
    <UserLayout title="Master Data — Project Charter">
        <div class="animate-fade-in-up space-y-4">
            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Project Charter</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Kelola data Project Charter &amp; PC Status Implementation.
                        </p>
                    </div>
                    <a :href="route('master-data.index')"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                        ← Kembali ke Master Data
                    </a>
                </div>
            </section>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <article class="rounded-2xl border border-indigo-600 bg-indigo-600 p-4 shadow-md">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-indigo-100">Total Projects</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ projects.length }}</p>
                </article>
                <article class="rounded-2xl border border-purple-500 bg-purple-500 p-4 shadow-md">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-purple-100">Dengan Charter</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ withCharterCount }}</p>
                </article>
            </div>

            <!-- Toolbar -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 dark:border-white/10">
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Status</label>
                        <select v-model="filterStatus" class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] text-slate-700 shadow-sm focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="">All</option>
                            <option v-for="s in statusOptions" :key="s.id" :value="s.id">{{ s.label }}</option>
                        </select>
                    </div>
                    <input v-model="search" type="text" placeholder="Cari nama / kode..."
                        class="rounded border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-700 shadow-sm focus:border-indigo-400 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200" />
                    <button v-if="search || filterStatus" type="button"
                        class="rounded border border-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:hover:bg-white/5"
                        @click="search = ''; filterStatus = ''">Reset</button>
                    <div class="ml-auto">
                        <button type="button"
                            class="rounded-lg bg-indigo-600 px-3 py-1 text-[11px] font-semibold text-white shadow-sm hover:bg-indigo-700"
                            @click="openCreateModal">+ Tambah</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th v-for="h in headers" :key="h" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ h }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(item, idx) in filtered" :key="item.id" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-2.5 text-slate-500">{{ idx + 1 }}</td>
                                <td class="px-3 py-2.5 font-medium text-slate-700 dark:text-slate-200">{{ item.code ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-medium text-slate-700 dark:text-slate-200"><p class="break-words">{{ item.name ?? '-' }}</p></td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.owner_name ?? item.owner?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <span v-if="item.status_ref"
                                        class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                        {{ item.status_ref.name }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">
                                    {{ item.charter?.version_label ?? '-' }}
                                </td>
                                <td class="px-3 py-2.5">
                                    <span v-if="item.latest_pc_status_implementation?.status"
                                        class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-white/10 dark:text-slate-300">
                                        {{ item.latest_pc_status_implementation.status }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    <ReviewBadge :status="item.latest_pc_status_implementation?.review_status" />
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="openEditModal(item)"
                                            class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300">Edit</button>
                                        <button type="button" @click="confirmDelete(item)"
                                            class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-semibold text-rose-700 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filtered.length === 0">
                                <td colspan="9" class="px-6 py-8 text-center text-xs text-slate-500">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══ CREATE / EDIT MODAL ═══ -->
        <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="closeFormModal">
            <div class="w-full max-w-2xl rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#1a1a1a]">
                <div class="rounded-t-xl bg-indigo-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">{{ editingItem ? 'Edit' : 'Tambah' }} Project Charter</h2>
                </div>
                <form class="space-y-5 p-6" @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <FormField label="Code" :error="form.errors.code">
                            <input v-model="form.code" type="text" placeholder="e.g. PRJ-001" class="form-input" />
                        </FormField>
                        <FormField label="Nama Project" :error="form.errors.name" required>
                            <input v-model="form.name" type="text" placeholder="Nama project" class="form-input" />
                        </FormField>
                        <FormField label="Owner" :error="form.errors.owner_name">
                            <input v-model="form.owner_name" type="text" placeholder="Nama owner" class="form-input" />
                        </FormField>
                        <FormField label="Status" :error="form.errors.status" required>
                            <select v-model="form.status" class="form-input">
                                <option value="">— Pilih Status —</option>
                                <option v-for="s in statusOptions" :key="s.id" :value="s.id">{{ s.label }}</option>
                            </select>
                        </FormField>
                        <FormField label="PC Status" :error="form.errors.pc_status">
                            <input v-model="form.pc_status" type="text" placeholder="Status implementasi" class="form-input" />
                        </FormField>
                        <FormField label="Review Status" :error="form.errors.pc_review_status">
                            <select v-model="form.pc_review_status" class="form-input">
                                <option value="">— Pilih —</option>
                                <option v-for="rs in reviewOptions" :key="rs" :value="rs">{{ rs }}</option>
                            </select>
                        </FormField>
                    </div>
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5 dark:border-white/5">
                        <button type="button" @click="closeFormModal"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5">Batal</button>
                        <button type="submit" :disabled="form.processing"
                            class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50">
                            {{ editingItem ? 'Simpan Perubahan' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ═══ DELETE MODAL ═══ -->
        <ConfirmDeleteModal v-if="deleteTarget" :name="deleteTarget.name ?? 'Project'" @cancel="deleteTarget = null" @confirm="doDelete" />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import FormField from '@/Components/MasterData/FormField.vue';
import ConfirmDeleteModal from '@/Components/MasterData/ConfirmDeleteModal.vue';
import ReviewBadge from '@/Components/MasterData/ReviewBadge.vue';

const props = defineProps({
    projects:             { type: Array, default: () => [] },
    statusOptions:        { type: Array, default: () => [] },
    masterItInitiatives:  { type: Array, default: () => [] },
});

const route = useRouteHelper();

const headers = ['No', 'Code', 'Nama', 'Owner', 'Status', 'Charter', 'PC Status', 'Review', 'Action'];
const reviewOptions = ['At Risk', 'On Track', 'Not Started', 'Not Signed'];

// ── Filters ──
const search = ref('');
const filterStatus = ref('');
const withCharterCount = computed(() => props.projects.filter(p => p.charter).length);

const filtered = computed(() => {
    return props.projects.filter(item => {
        if (filterStatus.value && item.status !== Number(filterStatus.value)) return false;
        if (search.value) {
            const q = search.value.toLowerCase();
            if (!(item.name ?? '').toLowerCase().includes(q) && !(item.code ?? '').toLowerCase().includes(q)) return false;
        }
        return true;
    });
});

// ── Create / Edit ──
const showFormModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    code: '', name: '', owner_name: '', status: '',
    pc_status: '', pc_review_status: '',
});

const openCreateModal = () => { editingItem.value = null; form.reset(); form.clearErrors(); showFormModal.value = true; };

const openEditModal = (item) => {
    editingItem.value = item;
    Object.assign(form, {
        code: item.code ?? '', name: item.name ?? '',
        owner_name: item.owner_name ?? '', status: item.status ?? '',
        pc_status: item.latest_pc_status_implementation?.status ?? '',
        pc_review_status: item.latest_pc_status_implementation?.review_status ?? '',
    });
    form.clearErrors();
    showFormModal.value = true;
};

const closeFormModal = () => { showFormModal.value = false; editingItem.value = null; };

const submitForm = () => {
    const opts = { onSuccess: () => closeFormModal() };
    if (editingItem.value) {
        form.put(route('master-data.project-charter.update', editingItem.value.id), opts);
    } else {
        form.post(route('master-data.project-charter.store'), opts);
    }
};

// ── Delete ──
const deleteTarget = ref(null);
const confirmDelete = (item) => { deleteTarget.value = item; };
const doDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('master-data.project-charter.destroy', deleteTarget.value.id), {
        onFinish: () => { deleteTarget.value = null; },
    });
};
</script>

<style scoped>
@reference "tailwindcss";
.form-input {
    @apply w-full rounded-md border-slate-300 bg-slate-50 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100;
}
</style>
