<template>
    <UserLayout :title="`Digital Initiative - ${initiative.no}`">
        <div class="mx-auto max-w-3xl animate-fade-in">
            <!-- Header -->
            <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            href="/digital-initiatives"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                            </svg>
                            Back
                        </Link>

                        <h1 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ initiative.no }}
                        </h1>

                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                            :class="typeBadgeClass(initiative.type)"
                        >
                            {{ initiative.type }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <Link
                            :href="`/digital-initiatives/${initiative.id}/edit`"
                            class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/10"
                            @click="confirmDelete"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </section>

            <!-- Detail Cards -->
            <div class="space-y-6">
                <!-- Main Info -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 dark:border-white/5 dark:bg-[#1a1a1a]">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">General Information</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">No</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{{ initiative.no || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">Type</dt>
                            <dd class="mt-0.5 text-sm font-medium capitalize text-slate-900 dark:text-white">{{ initiative.type || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">Project Owner</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{{ initiative.projectOwner || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">Use Case</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{{ initiative.useCase || '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Description & Value -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 dark:border-white/5 dark:bg-[#1a1a1a]">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Details</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">Description</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300">{{ initiative.desc || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">Value</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300">{{ initiative.value || '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Urgency, RJJP, COE -->
                <div class="rounded-xl border border-slate-200 bg-white p-6 dark:border-white/5 dark:bg-[#1a1a1a]">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Classification</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">Urgency</dt>
                            <dd class="mt-1">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                    :class="urgencyBadgeClass(initiative.urgency)"
                                >
                                    {{ initiative.urgency || '-' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">RJJP</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{{ initiative.rjjp || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">COE</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{{ initiative.coe || '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    initiative: Object,
});

const typeBadgeClass = (type) => {
    const t = String(type || '').toLowerCase();
    if (t === 'strategic') return 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400';
    if (t === 'operational') return 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400';
    if (t === 'tactical') return 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

const urgencyBadgeClass = (urgency) => {
    const u = String(urgency || '').toLowerCase();
    if (u === 'high' || u === 'tinggi') return 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400';
    if (u === 'medium' || u === 'sedang') return 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
    if (u === 'low' || u === 'rendah') return 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete initiative "${props.initiative.no}"?`)) {
        router.delete(`/digital-initiatives/${props.initiative.id}`);
    }
};
</script>
