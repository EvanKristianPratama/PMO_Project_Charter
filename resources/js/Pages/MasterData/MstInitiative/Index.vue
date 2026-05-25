<template>
    <UserLayout title="Master Data — Master Initiative">
        <div class="animate-fade-in-up space-y-4">
            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Master Initiative</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Kelola data master initiative (Digital &amp; IT).
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="route('master-data.index')"
                           class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                            ← Master Data
                        </a>
                        <a :href="route('master-data.mst-initiatives.create')"
                           class="inline-flex items-center gap-1 rounded-lg bg-[#0f63b5] px-4 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#0c4e8f]">
                            + Tambah
                        </a>
                    </div>
                </div>
            </section>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <article class="rounded-2xl border border-[#0f63b5] bg-[#0f63b5] p-4 shadow-md">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-blue-100">Total Initiative</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ mstInitiatives.length }}</p>
                </article>
                <article class="rounded-2xl border border-blue-500 bg-blue-500 p-4 shadow-md">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-blue-100">Digital</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ digitalCount }}</p>
                </article>
                <article class="rounded-2xl border border-violet-500 bg-violet-500 p-4 shadow-md">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-violet-100">IT</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ itCount }}</p>
                </article>
            </div>

            <!-- Filter bar + table -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 dark:border-white/10">
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Tipe</label>
                        <select v-model="filterTipe" class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] text-slate-700 shadow-sm focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="">All</option>
                            <option value="1">Digital</option>
                            <option value="2">IT</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">CoE</label>
                        <select v-model="filterCoe" class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] text-slate-700 shadow-sm focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="">All</option>
                            <option v-for="opt in coeFilterOptions" :key="opt" :value="opt">{{ opt }}</option>
                        </select>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <input v-model="search" type="text" placeholder="Cari nama / kode..."
                            class="rounded border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-700 shadow-sm focus:border-blue-400 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200" />
                        <button v-if="filterTipe || filterCoe || search" type="button"
                            class="rounded border border-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:hover:bg-white/5"
                            @click="resetFilters">Reset</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[3%]"><col class="w-[4%]"><col class="w-[6%]"><col class="w-[13%]">
                            <col class="w-[22%]"><col class="w-[10%]"><col class="w-[12%]"><col class="w-[8%]">
                            <col class="w-[12%]"><col class="w-[10%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th v-for="h in headers" :key="h" class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ h }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(item, idx) in filteredInitiatives" :key="item.id"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-2.5 text-slate-500">{{ idx + 1 }}</td>
                                <td class="px-3 py-2.5 font-medium text-slate-700 dark:text-slate-200">{{ item.code ?? '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium"
                                        :class="item.tipe_initiative === 1
                                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'
                                            : 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300'">
                                        {{ item.tipe_initiative === 1 ? 'Digital' : 'IT' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-slate-700 dark:text-slate-200">{{ item.coe?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-medium text-slate-700 dark:text-slate-200"><p class="break-words">{{ item.name ?? '-' }}</p></td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.organization?.groub?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.organization?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <span v-if="item.latest_status?.status"
                                        class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                        {{ item.latest_status.status }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ item.source_data?.name ?? '-' }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1">
                                        <a :href="route('master-data.mst-initiatives.edit', item.id)"
                                            class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300">Edit</a>
                                        <button type="button" @click="confirmDelete(item)"
                                            class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-semibold text-rose-700 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredInitiatives.length === 0">
                                <td colspan="10" class="px-6 py-8 text-center text-xs text-slate-500">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <ConfirmDeleteModal v-if="deleteTarget" :name="deleteTarget.name" @cancel="deleteTarget = null" @confirm="doDelete" />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import ConfirmDeleteModal from '@/Components/MasterData/ConfirmDeleteModal.vue';

const props = defineProps({
    mstInitiatives: { type: Array, default: () => [] },
});

const route = useRouteHelper();

const headers = ['No', 'Code', 'Tipe', 'CoE', 'Nama', 'Holding', 'Organisasi', 'Status', 'Sumber Data', 'Action'];

const filterTipe = ref('');
const filterCoe = ref('');
const search = ref('');
const resetFilters = () => { filterTipe.value = ''; filterCoe.value = ''; search.value = ''; };

const digitalCount = computed(() => props.mstInitiatives.filter(i => i.tipe_initiative === 1).length);
const itCount = computed(() => props.mstInitiatives.filter(i => i.tipe_initiative === 2).length);

const coeFilterOptions = computed(() => {
    const names = props.mstInitiatives.map(i => i.coe?.name).filter(Boolean);
    return [...new Set(names)].sort();
});

const filteredInitiatives = computed(() => {
    return props.mstInitiatives.filter(item => {
        if (filterTipe.value && String(item.tipe_initiative) !== filterTipe.value) return false;
        if (filterCoe.value && (item.coe?.name ?? '') !== filterCoe.value) return false;
        if (search.value) {
            const q = search.value.toLowerCase();
            if (!(item.name ?? '').toLowerCase().includes(q) && !(item.code ?? '').toLowerCase().includes(q)) return false;
        }
        return true;
    });
});

const deleteTarget = ref(null);
const confirmDelete = (item) => { deleteTarget.value = item; };
const doDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('master-data.mst-initiatives.destroy', deleteTarget.value.id), {
        onFinish: () => { deleteTarget.value = null; },
    });
};
</script>
