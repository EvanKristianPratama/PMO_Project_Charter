<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArchiveBoxIcon,
    ArrowPathIcon,
    ArrowDownTrayIcon,
    CalendarDaysIcon,
    CircleStackIcon,
    ClockIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    backups: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
    settings: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const route = useRouteHelper();

const flash = computed(() => page.props.flash ?? {});

const backupForm = useForm({});

const retentionForm = useForm({
    retention_days: props.settings.retention_days ?? 14,
});

const statCards = computed(() => [
    {
        key: 'total',
        label: 'Total Backup',
        value: props.stats.total_backups ?? 0,
        icon: ArchiveBoxIcon,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-300',
    },
    {
        key: 'retention',
        label: 'Retention',
        value: `${props.stats.retention_days ?? 14} hari`,
        icon: ShieldCheckIcon,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
    },
    {
        key: 'latest',
        label: 'Backup Terbaru',
        value: props.stats.latest_backup_at ? formatDate(props.stats.latest_backup_at) : 'Belum ada',
        icon: CalendarDaysIcon,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
    },
    {
        key: 'size',
        label: 'Total Size',
        value: props.stats.total_size ?? '0 B',
        icon: CircleStackIcon,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
    },
]);

function formatDate(value) {
    return new Date(value).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatTime(value) {
    return new Date(value).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });
}

function submitBackup() {
    backupForm.post(route('admin.backup.run'), {
        preserveScroll: true,
    });
}

function submitRetention() {
    retentionForm.put(route('admin.backup.settings.update'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout title="Backup Database">
        <div class="space-y-6 animate-fade-in">
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-indigo-500/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-cyan-400/10 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-400">Admin Control</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Backup Database</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Monitoring file backup database, download arsip ZIP, dan atur retensi cleanup harian dari satu halaman admin.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-[#111827]">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Storage Path</p>
                            <p class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-200">{{ stats.storage_path }}</p>
                        </div>

                        <form @submit.prevent="submitBackup">
                            <button
                                type="submit"
                                class="inline-flex h-full items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="backupForm.processing"
                            >
                                <ArrowPathIcon class="h-5 w-5" :class="{ 'animate-spin': backupForm.processing }" />
                                {{ backupForm.processing ? 'Memproses backup...' : 'Backup Sekarang' }}
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                {{ flash.success }}
            </div>

            <div v-if="flash.error" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                {{ flash.error }}
            </div>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="card in statCards"
                    :key="card.key"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ card.label }}</p>
                            <p class="mt-2 truncate text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ card.value }}</p>
                        </div>
                        <span :class="card.iconWrapClass">
                            <component :is="card.icon" class="h-5 w-5" />
                        </span>
                    </div>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717] xl:col-span-3">
                    <div class="flex items-center gap-2">
                        <ArchiveBoxIcon class="h-5 w-5 text-indigo-500 dark:text-indigo-300" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Ringkasan Sistem</h2>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-[#111827]">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Frekuensi</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">Harian</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Scheduler menjalankan backup database setiap hari.</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-[#111827]">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Format</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">ZIP</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Arsip berisi dump database hasil Spatie backup.</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-[#111827]">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Cleanup</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">Otomatis</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">File lebih tua dari retensi akan dibersihkan setiap hari.</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-dashed border-slate-300 px-4 py-3 text-xs text-slate-500 dark:border-white/15 dark:text-slate-400">
                        Tombol <span class="font-semibold text-slate-700 dark:text-slate-200">Backup Sekarang</span> akan menjalankan backup database saat ini tanpa menunggu jadwal otomatis server.
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717] xl:col-span-2">
                    <div class="flex items-center gap-2">
                        <ClockIcon class="h-5 w-5 text-indigo-500 dark:text-indigo-300" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Pengaturan Retensi</h2>
                    </div>

                    <form class="mt-4 space-y-4" @submit.prevent="submitRetention">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Retention Days</label>
                            <input
                                v-model.number="retentionForm.retention_days"
                                type="number"
                                min="1"
                                max="3650"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#111827] dark:text-slate-200 dark:placeholder:text-slate-500"
                            />
                            <p v-if="retentionForm.errors.retention_days" class="mt-2 text-xs text-rose-500">
                                {{ retentionForm.errors.retention_days }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-xs text-slate-500 dark:border-white/15 dark:text-slate-400">
                            Cleanup mengikuti nilai retensi ini pada proses harian berikutnya.
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="retentionForm.processing"
                        >
                            <ShieldCheckIcon class="h-5 w-5" />
                            {{ retentionForm.processing ? 'Menyimpan...' : 'Simpan Retensi' }}
                        </button>
                    </form>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-white/10">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Daftar Backup</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">File arsip backup yang tersedia untuk diunduh oleh admin.</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50/70 dark:bg-[#1e1e1e]">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">No</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">File Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Tanggal</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Waktu</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Size</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            <tr
                                v-for="(backup, index) in backups.data"
                                :key="backup.file_name"
                                class="transition hover:bg-slate-50/70 dark:hover:bg-white/5"
                            >
                                <td class="px-5 py-4 font-medium text-slate-700 dark:text-slate-300">
                                    {{ (backups.from || 1) + index }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900 dark:text-white">{{ backup.file_name }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">ZIP backup database</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ formatDate(backup.created_at) }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ formatTime(backup.created_at) }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ backup.size }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end">
                                        <a
                                            :href="route('admin.backup.download', backup.file_name)"
                                            class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20"
                                        >
                                            <ArrowDownTrayIcon class="h-4 w-4" />
                                            Download
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!backups.data?.length">
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400">
                                            <ArchiveBoxIcon class="h-6 w-6" />
                                        </span>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Belum ada file backup</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Backup akan tampil di sini setelah scheduler atau command backup dijalankan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="backups.links?.length > 3" class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan {{ backups.from }}-{{ backups.to }} dari {{ backups.total }} backup
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in backups.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :preserve-scroll="true"
                            :class="[
                                'rounded-lg px-3 py-1.5 text-xs transition-colors',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : link.url
                                        ? 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5'
                                        : 'cursor-not-allowed text-slate-300 dark:text-slate-600'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
