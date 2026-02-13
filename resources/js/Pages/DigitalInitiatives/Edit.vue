<template>
    <UserLayout title="Edit Digital Initiative">
        <div class="mx-auto max-w-5xl animate-fade-in">
            <div class="mb-6">
                <Link href="/digital-initiatives" class="mb-2 flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Digital Initiatives
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Digital Initiative</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                    Update the initiative details below.
                </p>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <!-- Header -->
                <div class="bg-[#0f63b5] px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Edit Digital Initiative</h2>
                    <p class="text-sm text-blue-100">Update the initiative details below.</p>
                </div>

                <form class="space-y-6 p-6" @submit.prevent="submit">
                    <!-- General Information & Details Grid -->
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        
                        <!-- Left Column: General Info -->
                        <div class="space-y-4">
                            <h3 class="flex items-center text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <span class="mr-2 h-px w-8 bg-slate-300 dark:bg-slate-600"></span>
                                General Information
                                <span class="ml-2 h-px flex-1 bg-slate-300 dark:bg-slate-600"></span>
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                <div class="md:col-span-1">
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">No</label>
                                    <input
                                        v-model="form.no"
                                        type="text"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                        placeholder="DI-001"
                                    />
                                    <p v-if="form.errors.no" class="mt-1 text-xs text-red-500">{{ form.errors.no }}</p>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Type</label>
                                    <input
                                        v-model="form.type"
                                        type="text"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                        placeholder="e.g. Strategic"
                                    />
                                    <p v-if="form.errors.type" class="mt-1 text-xs text-red-500">{{ form.errors.type }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Project Owner</label>
                                    <input
                                        v-model="form.projectOwner"
                                        type="text"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                        placeholder="e.g., VP Digital"
                                    />
                                    <p v-if="form.errors.projectOwner" class="mt-1 text-xs text-red-500">{{ form.errors.projectOwner }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Use Case</label>
                                    <input
                                        v-model="form.useCase"
                                        type="text"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                        placeholder="e.g., Automation of reporting"
                                    />
                                    <p v-if="form.errors.useCase" class="mt-1 text-xs text-red-500">{{ form.errors.useCase }}</p>
                                </div>
                            </div>
                        </div>

                         <!-- Right Column: Details & Classification -->
                        <div class="space-y-4">
                            <h3 class="flex items-center text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <span class="mr-2 h-px w-8 bg-slate-300 dark:bg-slate-600"></span>
                                Details & Classification
                                <span class="ml-2 h-px flex-1 bg-slate-300 dark:bg-slate-600"></span>
                            </h3>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                                <textarea
                                    v-model="form.desc"
                                    rows="4"
                                    class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                    placeholder="Brief description of this initiative..."
                                ></textarea>
                                <p v-if="form.errors.desc" class="mt-1 text-xs text-red-500">{{ form.errors.desc }}</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Value</label>
                                    <select
                                        v-model="form.value"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-200 sm:text-sm"
                                    >
                                        <option value="">Select Value</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                        <option value="tbc">TBC</option>
                                    </select>
                                    <p v-if="form.errors.value" class="mt-1 text-xs text-red-500">{{ form.errors.value }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Urgency</label>
                                    <select
                                        v-model="form.urgency"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-200 sm:text-sm"
                                    >
                                        <option value="">Select Urgency</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                        <option value="tbc">TBC</option>
                                    </select>
                                    <p v-if="form.errors.urgency" class="mt-1 text-xs text-red-500">{{ form.errors.urgency }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">RJJP</label>
                                    <input
                                        v-model="form.rjjp"
                                        type="text"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                        placeholder="RJJP"
                                    />
                                    <p v-if="form.errors.rjjp" class="mt-1 text-xs text-red-500">{{ form.errors.rjjp }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">COE</label>
                                    <input
                                        v-model="form.coe"
                                        type="text"
                                        class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 sm:text-sm"
                                        placeholder="COE"
                                    />
                                    <p v-if="form.errors.coe" class="mt-1 text-xs text-red-500">{{ form.errors.coe }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                <select
                                    v-model="form.status"
                                    class="w-full rounded-md border-slate-300 bg-slate-50 shadow-sm focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-200 sm:text-sm"
                                >
                                    <option value="draft">Draft / Usulan</option>
                                    <option value="on_hold">On Hold / Review</option>
                                    <option value="active">Active / Persetujuan</option>
                                    <option value="completed">Completed / Selesai</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 rounded-b-xl border-t border-slate-100 pt-6 dark:border-white/5">
                        <Link href="/digital-initiatives" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-[#0f63b5] px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#0c4e8f] focus:outline-none focus:ring-2 focus:ring-[#0f63b5] focus:ring-offset-2 disabled:opacity-50"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    initiative: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    type: props.initiative.type ?? '',
    no: props.initiative.no ?? '',
    projectOwner: props.initiative.projectOwner ?? '',
    useCase: props.initiative.useCase ?? '',
    desc: props.initiative.desc ?? '',
    value: props.initiative.value ?? '',
    urgency: props.initiative.urgency ?? '',
    rjjp: props.initiative.rjjp ?? '',
    coe: props.initiative.coe ?? '',
    status: props.initiative.status ?? 'draft',
});

const submit = () => {
    form.put(`/digital-initiatives/${props.initiative.id}`);
};
</script>
