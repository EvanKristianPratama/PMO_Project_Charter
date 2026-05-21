<template>
    <UserLayout title="Master Data">
        <div class="animate-fade-in-up space-y-4">
            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Master Data Center</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Kelola dan lihat semua tabel master (<span class="font-semibold">mst_</span>).
                        </p>
                    </div>
                    <div class="w-full sm:w-80">
                        <input
                            v-model="tableSearch"
                            type="text"
                            placeholder="Cari nama tabel..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                        />
                    </div>
                </div>
            </section>

            <!-- Main grid -->
            <section class="grid grid-cols-1 gap-4 lg:grid-cols-[220px_minmax(0,1fr)]">
                <!-- Sidebar -->
                <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Navigasi CRUD</p>

                    <div class="mt-3 space-y-1">
                        <a v-for="nav in crudNav" :key="nav.href" :href="nav.href"
                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-semibold transition-colors text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="nav.icon" />
                            </svg>
                            <span>{{ nav.label }}</span>
                        </a>
                    </div>

                    <p class="mt-5 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Tabel Viewer</p>

                    <div class="mt-3 space-y-1">
                        <button
                            v-for="table in sidebarTables"
                            :key="table.name"
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-left text-[11px] transition-colors"
                            :class="activeTable?.name === table.name
                                ? 'bg-[#1C75BC]/10 font-semibold text-[#1C75BC] dark:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                                : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5'"
                            @click="activeTableName = table.name"
                        >
                            <span class="truncate">{{ table.name }}</span>
                            <span class="ml-1 shrink-0 text-[9px] text-slate-400">{{ table.total_rows }}</span>
                        </button>
                    </div>

                    <div class="mt-4 rounded-xl border border-slate-200 p-3 text-xs dark:border-white/10">
                        <p class="font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Ringkasan</p>
                        <p class="mt-2 text-slate-600 dark:text-slate-300">
                            <span class="font-semibold">{{ tables.length }}</span> tabel master
                        </p>
                    </div>
                </aside>

                <!-- Content -->
                <div class="space-y-4">
                    <!-- Raw table preview -->
                    <template v-if="activeTable">
                        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <article class="rounded-2xl border border-[#1C75BC] bg-[#1C75BC] p-4 shadow-md">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-white">Tabel</p>
                                <p class="mt-1 text-lg font-bold text-white">{{ activeTable.name }}</p>
                            </article>
                            <article class="rounded-2xl border border-slate-700 bg-slate-700 p-4 shadow-md">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-white">Total Baris</p>
                                <p class="mt-1 text-3xl font-bold text-white">{{ activeTable.total_rows ?? 0 }}</p>
                            </article>
                        </section>

                        <section v-if="error"
                            class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                            {{ error }}
                        </section>

                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                            <div class="border-b border-slate-200 px-4 py-3 dark:border-white/10">
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Preview {{ previewLimit }} baris dari {{ activeTable.total_rows }} total
                                </p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10">
                                    <thead class="bg-slate-50 dark:bg-white/5">
                                        <tr>
                                            <th v-for="col in activeTable.columns" :key="col"
                                                class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ col }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                        <tr v-for="(row, ri) in activeTable.rows" :key="ri" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                            <td v-for="col in activeTable.columns" :key="col"
                                                class="max-w-[240px] whitespace-nowrap px-3 py-2 text-slate-700 dark:text-slate-200"
                                                :title="fullCell(row[col])">{{ shortCell(row[col]) }}</td>
                                        </tr>
                                        <tr v-if="activeTable.rows.length === 0">
                                            <td :colspan="Math.max(activeTable.columns.length, 1)" class="px-4 py-6 text-center text-xs text-slate-500">Belum ada data.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </template>

                    <section v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 dark:border-white/10 dark:bg-[#171717]">
                        Pilih tabel dari sidebar, atau masuk ke halaman CRUD di atas.
                    </section>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    tables:       { type: Array,  default: () => [] },
    previewLimit: { type: Number, default: 10 },
    error:        { type: String, default: null },
});

const route = useRouteHelper();

// ── CRUD sidebar links ──
const crudNav = [
    { label: 'Master Initiative', href: route('master-data.mst-initiatives.index'), icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Business Capability', href: route('master-data.business-capabilities.index'), icon: 'M4 6h16M4 12h16M4 18h16' },
    { label: 'Scope Charter', href: route('master-data.scope-charter.index'), icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { label: 'Project Charter', href: route('master-data.project-charter.index'), icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { label: 'PIC Project', href: route('master-data.pic-projects.index'), icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { label: 'Log Aktivitas', href: route('master-data.activity-log.index'), icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
    { label: 'Sinkronisasi Data', href: route('sync.index'), icon: 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z M12 12v6m0 0l-3-3m3 3l3-3' },
];

// ── Table viewer sidebar ──
const tableSearch = ref('');
const activeTableName = ref(null);

const sidebarTables = computed(() => {
    const q = tableSearch.value.trim().toLowerCase();
    if (!q) return props.tables;
    return props.tables.filter(t => t.name.toLowerCase().includes(q));
});

const activeTable = computed(() => {
    if (!activeTableName.value) return null;
    return props.tables.find(t => t.name === activeTableName.value) ?? null;
});

// ── Cell helpers ──
const fullCell = (value) => {
    if (value === null || value === undefined) return '-';
    if (typeof value === 'object') { try { return JSON.stringify(value); } catch { return String(value); } }
    return String(value);
};
const shortCell = (value) => { const t = fullCell(value); return t.length <= 80 ? t : `${t.slice(0, 80)}...`; };
</script>
