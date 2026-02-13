<template>
    <UserLayout title="Edit IT Initiative">
        <div class="mx-auto max-w-2xl animate-fade-in">
            <div class="mb-8">
                <Link href="/it-initiatives" class="mb-2 flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to IT Initiatives
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Edit IT Initiative</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                    Update IT initiative master data. Charter and roadmap are managed separately.
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <div class="md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">IT Initiative Code</label>
                            <input
                                v-model="form.code"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="PRJ-001"
                            />
                            <p v-if="form.errors.code" class="mt-1 text-xs text-red-500">{{ form.errors.code }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">IT Initiative Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="e.g., UI/UX Standardization"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Company Owner</label>
                            <input
                                v-model="form.owner_name"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="e.g., PT Example Indonesia"
                            />
                            <p v-if="form.errors.owner_name" class="mt-1 text-xs text-red-500">{{ form.errors.owner_name }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                            >
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 dark:border-white/5">
                        <Link href="/it-initiatives" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Save IT Initiative
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
    itInitiative: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    code: props.itInitiative.code ?? '',
    name: props.itInitiative.name ?? '',
    owner_name: props.itInitiative.owner_name ?? '',
    status: props.itInitiative.status ?? 'draft',
});

const submit = () => {
    form.put(`/it-initiatives/${props.itInitiative.id}`);
};
</script>
