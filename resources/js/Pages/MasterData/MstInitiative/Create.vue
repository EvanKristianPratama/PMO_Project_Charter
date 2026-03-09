<template>
    <UserLayout title="Master Data — Tambah Initiative">
        <div class="animate-fade-in-up space-y-4">
            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Master Initiative</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Isi data initiative baru.</p>
                    </div>
                    <a href="/master-data/master-initiatives"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                        ← Kembali
                    </a>
                </div>
            </section>

            <!-- ═══ MASTER DATA — TABLE STYLE ═══ -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-2.5 dark:border-white/10">
                    <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Data Initiative</h2>
                    <button type="button" @click="submitForm" :disabled="form.processing"
                        class="inline-flex items-center gap-1 rounded-lg bg-[#0f63b5] px-4 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#0c4e8f] disabled:opacity-50">
                        Simpan
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[8%]"><col class="w-[10%]"><col class="w-[12%]">
                            <col class="w-[12%]"><col class="w-[12%]"><col class="w-[20%]"><col class="w-[26%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th v-for="h in masterHeaders" :key="h" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ h }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-2.5">
                                    <input v-model="form.code" type="number" min="0" placeholder="1001" class="form-input-sm" />
                                </td>
                                <td class="px-3 py-2.5">
                                    <select v-model="form.tipe_initiative" class="form-input-sm">
                                        <option value="">—</option>
                                        <option v-for="t in tipeOptions" :key="t.id" :value="t.id">{{ t.label }}</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2.5">
                                    <select v-model="form.coe_id" class="form-input-sm">
                                        <option :value="null">—</option>
                                        <option v-for="opt in coeOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="max-h-28 space-y-1 overflow-y-auto rounded border border-slate-200 bg-white p-2 dark:border-white/10 dark:bg-[#131313]">
                                        <label
                                            v-for="opt in organizationOptions"
                                            :key="opt.id"
                                            class="flex items-start gap-2 text-[10px] text-slate-700 dark:text-slate-200"
                                        >
                                            <input
                                                v-model="form.organization_ids"
                                                type="checkbox"
                                                :value="opt.id"
                                                class="mt-0.5 rounded border-slate-300 text-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10"
                                            />
                                            <span>{{ opt.groub ? `${opt.groub} — ` : '' }}{{ opt.name }}</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <select v-model="form.source" class="form-input-sm">
                                        <option :value="null">—</option>
                                        <option v-for="opt in sourceOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2.5">
                                    <input v-model="form.name" type="text" placeholder="Nama initiative" class="form-input-sm" />
                                </td>
                                <td class="px-3 py-2.5">
                                    <input v-model="form.description" type="text" placeholder="Deskripsi singkat..." class="form-input-sm" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="Object.keys(form.errors).length" class="border-t border-slate-200 px-4 py-2 dark:border-white/10">
                    <p v-for="(msg, field) in form.errors" :key="field" class="text-[10px] text-rose-500">{{ field }}: {{ msg }}</p>
                </div>
            </div>

            <!-- ═══ STATUS — TABLE STYLE ═══ -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-2.5 dark:border-white/10">
                    <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Status
                        <span class="ml-1 font-normal text-slate-400">({{ localStatuses.length }})</span>
                    </h2>
                    <button v-if="!showNewRow" type="button" @click="openNewRow"
                        class="inline-flex items-center gap-1 rounded-lg bg-[#0f63b5] px-4 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#0c4e8f]">
                        + Tambah
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[5%]"><col class="w-[25%]"><col class="w-[18%]">
                            <col class="w-[38%]"><col class="w-[14%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th v-for="h in statusHeaders" :key="h" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ h }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(entry, idx) in localStatuses" :key="entry._id"
                                class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-2.5 text-slate-500">{{ idx + 1 }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium"
                                          :class="idx === 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300'">
                                        {{ entry.status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ entry.tanggal || '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ entry.notes || '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <button type="button" @click="removeStatus(entry._id)"
                                        class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-semibold text-rose-700 hover:bg-rose-200 opacity-0 group-hover:opacity-100 dark:bg-rose-500/20 dark:text-rose-300">
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- New row -->
                            <tr v-if="showNewRow" class="bg-emerald-50/40 dark:bg-emerald-500/5">
                                <td class="px-3 py-2.5 text-slate-400">+</td>
                                <td class="px-3 py-2.5">
                                    <select v-model="newRow.status" class="form-input-sm">
                                        <option value="">— Pilih Status —</option>
                                        <option v-for="opt in statusList" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                    <p v-if="newRowError" class="mt-0.5 text-[10px] text-rose-500">{{ newRowError }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    <input v-model="newRow.tanggal" type="date" class="form-input-sm" />
                                </td>
                                <td class="px-3 py-2.5">
                                    <input v-model="newRow.notes" type="text" placeholder="Catatan..." class="form-input-sm" />
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="addStatus"
                                            class="inline-flex rounded-full bg-emerald-500 px-2 py-0.5 text-[9px] font-semibold text-white hover:bg-emerald-600">
                                            Add
                                        </button>
                                        <button type="button" @click="showNewRow = false"
                                            class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[9px] font-semibold text-slate-600 hover:bg-slate-300 dark:bg-white/10 dark:text-slate-300">
                                            Batal
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!localStatuses.length && !showNewRow">
                                <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-500">Belum ada status.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    coeOptions:          { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    tipeOptions:         { type: Array, default: () => [] },
    sourceOptions:       { type: Array, default: () => [] },
});

const masterHeaders = ['Code', 'Tipe', 'CoE', 'Organisasi', 'Sumber Data', 'Nama', 'Deskripsi'];
const statusHeaders = ['No', 'Status', 'Tanggal', 'Notes', 'Action'];

const form = useForm({
    code: '',
    name: '',
    description: '',
    tipe_initiative: '',
    coe_id: null,
    business_unit: null,
    organization_ids: [],
    source: null,
});

// ── Local status management ──
const localStatuses = ref([]);
const showNewRow = ref(false);
const newRow = reactive({ status: '', tanggal: '', notes: '' });
const newRowError = ref('');
let _nextId = 1;

const statusList = ['Drafting', 'Propose', 'Review', 'Approved', 'Postpone'];

const openNewRow = () => {
    showNewRow.value = true;
    newRow.status = ''; newRow.tanggal = ''; newRow.notes = '';
    newRowError.value = '';
};

const addStatus = () => {
    newRowError.value = '';
    if (!newRow.status.trim()) { newRowError.value = 'Status wajib diisi.'; return; }
    localStatuses.value.push({ _id: _nextId++, status: newRow.status, tanggal: newRow.tanggal, notes: newRow.notes });
    showNewRow.value = false;
};

const removeStatus = (id) => {
    localStatuses.value = localStatuses.value.filter(s => s._id !== id);
};

const normalizeOrganizationIds = (ids) => {
    if (!Array.isArray(ids)) return [];

    return [...new Set(ids
        .map((id) => Number(id))
        .filter((id) => Number.isInteger(id) && id > 0))];
};

const submitForm = () => {
    form.transform((data) => {
        const organizationIds = normalizeOrganizationIds(data.organization_ids);

        return {
            ...data,
            organization_ids: organizationIds,
            business_unit: organizationIds[0] ?? null,
            statuses: localStatuses.value.map((s) => ({ status: s.status, tanggal: s.tanggal || null, notes: s.notes || null })),
        };
    }).post('/master-data/master-initiatives');
};
</script>

<style scoped>
@reference "tailwindcss";
.form-input-sm {
    @apply w-full rounded border-slate-300 bg-white px-2 py-1 text-[11px] shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100;
}
</style>
