<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    AdjustmentsHorizontalIcon,
    ArrowPathIcon,
    ArrowRightOnRectangleIcon,
    ClipboardDocumentListIcon,
    FunnelIcon,
    MagnifyingGlassIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    TrashIcon,
    UserIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    logs: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    users: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    events: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();

const search    = ref(props.filters.search    ?? '');
const event     = ref(props.filters.event     ?? '');
const userId    = ref(props.filters.user_id   ?? '');
const dateFrom  = ref(props.filters.date_from ?? '');
const dateTo    = ref(props.filters.date_to   ?? '');
const showFilters = ref(false);

const applyFilters = () => {
    router.get(
        route('admin.activity-log.index'),
        {
            search:    search.value    || undefined,
            event:     event.value     || undefined,
            user_id:   userId.value    || undefined,
            date_from: dateFrom.value  || undefined,
            date_to:   dateTo.value    || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value   = '';
    event.value    = '';
    userId.value   = '';
    dateFrom.value = '';
    dateTo.value   = '';
    applyFilters();
};

const hasActiveFilters = computed(
    () => search.value || event.value || userId.value || dateFrom.value || dateTo.value,
);

/* ── Badge helpers ─────────────────────────────────────────────── */
const eventConfig = {
    login:   { label: 'Login',       bg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300', icon: ArrowRightOnRectangleIcon },
    logout:  { label: 'Logout',      bg: 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',             icon: ArrowRightOnRectangleIcon },
    created: { label: 'Tambah Data', bg: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',             icon: PlusCircleIcon },
    updated: { label: 'Ubah Data',   bg: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',         icon: PencilSquareIcon },
    deleted: { label: 'Hapus Data',  bg: 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',             icon: TrashIcon },
};

const getEventCfg = (evt) => eventConfig[evt] ?? { label: evt, bg: 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300', icon: ClipboardDocumentListIcon };

const formatDate = (v) => {
    if (!v) return '-';
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
    }).format(new Date(v));
};

const subjectClass = (type) => {
    if (!type) return null;
    return type.split('\\').pop();
};

const formatProperties = (props) => {
    if (!props) return null;
    try {
        return typeof props === 'string' ? JSON.parse(props) : props;
    } catch {
        return null;
    }
};

/* expansion */
const expanded = ref(null);
const toggle   = (id) => { expanded.value = expanded.value === id ? null : id; };
</script>

<template>
    <AdminLayout title="Log Aktivitas">
        <div class="space-y-6">

            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#111827]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-500 dark:text-indigo-300">Audit Trail</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Log Aktivitas</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Rekam jejak semua aktivitas user — login, logout, penambahan, perubahan, dan penghapusan data.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10"
                            @click="showFilters = !showFilters"
                        >
                            <FunnelIcon class="h-4 w-4" />
                            Filter
                            <span
                                v-if="hasActiveFilters"
                                class="inline-flex h-2 w-2 rounded-full bg-indigo-500"
                            />
                        </button>
                    </div>
                </div>
            </section>

            <!-- Filter Panel -->
            <section
                v-if="showFilters"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827]"
            >
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    <!-- Search -->
                    <div class="relative xl:col-span-2">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama, email, deskripsi…"
                            class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-white dark:placeholder-slate-500"
                            @keyup.enter="applyFilters"
                        />
                    </div>

                    <!-- Event -->
                    <select
                        v-model="event"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-white"
                    >
                        <option value="">Semua Event</option>
                        <option v-for="evt in events" :key="evt" :value="evt">
                            {{ getEventCfg(evt).label }}
                        </option>
                    </select>

                    <!-- User -->
                    <select
                        v-model="userId"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-white"
                    >
                        <option value="">Semua User</option>
                        <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>

                    <!-- Dates -->
                    <div class="flex gap-2">
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-white"
                        />
                        <input
                            v-model="dateTo"
                            type="date"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-white"
                        />
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        @click="applyFilters"
                    >
                        <AdjustmentsHorizontalIcon class="h-4 w-4" />
                        Terapkan Filter
                    </button>
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        @click="resetFilters"
                    >
                        <ArrowPathIcon class="h-4 w-4" />
                        Reset
                    </button>
                </div>
            </section>

            <!-- Table -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#111827]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            {{ logs.meta?.total ?? 0 }} record ditemukan
                        </p>
                    </div>
                </div>

                <div v-if="logs.data?.length" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-[#0f172a]">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">Event</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">IP Address</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <template v-for="log in logs.data" :key="log.id">
                                <tr class="transition-colors hover:bg-slate-50/70 dark:hover:bg-white/[0.03]">
                                    <!-- Waktu -->
                                    <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                        {{ formatDate(log.created_at) }}
                                    </td>

                                    <!-- User -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-[10px] font-bold text-white">
                                                {{ (log.user_name ?? '?').slice(0, 2).toUpperCase() }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ log.user_name ?? '(dihapus)' }}</p>
                                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ log.user_email ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Event badge -->
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.07em]"
                                            :class="getEventCfg(log.event).bg"
                                        >
                                            <component :is="getEventCfg(log.event).icon" class="h-3.5 w-3.5" />
                                            {{ getEventCfg(log.event).label }}
                                        </span>
                                    </td>

                                    <!-- Deskripsi -->
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                        {{ log.description ?? '-' }}
                                    </td>

                                    <!-- Subject -->
                                    <td class="px-4 py-3">
                                        <div v-if="log.subject_type" class="text-xs">
                                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ subjectClass(log.subject_type) }}</span>
                                            <span class="ml-1 text-slate-400 dark:text-slate-500">#{{ log.subject_id }}</span>
                                            <p v-if="log.subject_label" class="mt-0.5 text-slate-500 dark:text-slate-400">{{ log.subject_label }}</p>
                                        </div>
                                        <span v-else class="text-xs text-slate-400">-</span>
                                    </td>

                                    <!-- IP -->
                                    <td class="whitespace-nowrap px-4 py-3 text-xs font-mono text-slate-500 dark:text-slate-400">
                                        {{ log.ip_address ?? '-' }}
                                    </td>

                                    <!-- Toggle detail -->
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            v-if="log.properties"
                                            type="button"
                                            class="rounded-lg p-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                                            @click="toggle(log.id)"
                                        >
                                            {{ expanded === log.id ? 'Tutup' : 'Lihat' }}
                                        </button>
                                        <span v-else class="text-xs text-slate-400">-</span>
                                    </td>
                                </tr>

                                <!-- Expanded detail: diff viewer -->
                                <tr
                                    v-if="expanded === log.id && log.properties"
                                    :key="`${log.id}-detail`"
                                    class="bg-slate-50/80 dark:bg-[#0f172a]/70"
                                >
                                    <td colspan="7" class="px-6 pb-4 pt-2">
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <!-- Old values -->
                                            <div v-if="formatProperties(log.properties)?.old">
                                                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.1em] text-rose-500">Sebelum</p>
                                                <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 dark:border-rose-500/20 dark:bg-rose-500/5">
                                                    <dl class="space-y-1">
                                                        <div
                                                            v-for="(val, key) in formatProperties(log.properties).old"
                                                            :key="key"
                                                            class="flex gap-2 text-xs"
                                                        >
                                                            <dt class="w-32 shrink-0 font-semibold text-rose-600 dark:text-rose-400">{{ key }}</dt>
                                                            <dd class="break-all text-slate-700 dark:text-slate-300">{{ val ?? '(null)' }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            </div>

                                            <!-- New values -->
                                            <div v-if="formatProperties(log.properties)?.new">
                                                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.1em] text-emerald-500">Sesudah</p>
                                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-500/20 dark:bg-emerald-500/5">
                                                    <dl class="space-y-1">
                                                        <div
                                                            v-for="(val, key) in formatProperties(log.properties).new"
                                                            :key="key"
                                                            class="flex gap-2 text-xs"
                                                        >
                                                            <dt class="w-32 shrink-0 font-semibold text-emerald-600 dark:text-emerald-400">{{ key }}</dt>
                                                            <dd class="break-all text-slate-700 dark:text-slate-300">{{ val ?? '(null)' }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div v-else class="px-6 py-16 text-center">
                    <ClipboardDocumentListIcon class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" />
                    <p class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-400">Belum ada log aktivitas.</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Log akan muncul setelah ada aktivitas user.</p>
                </div>

                <!-- Pagination -->
                <div
                    v-if="logs.meta?.last_page > 1"
                    class="flex items-center justify-between border-t border-slate-200 px-5 py-4 dark:border-white/10"
                >
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Halaman {{ logs.meta.current_page }} dari {{ logs.meta.last_page }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="logs.links?.prev"
                            :href="logs.links.prev"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            Sebelumnya
                        </Link>
                        <Link
                            v-if="logs.links?.next"
                            :href="logs.links.next"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            Berikutnya
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
