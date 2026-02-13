<template>
    <UserLayout title="Create IT Initiative">
        <div class="max-w-2xl mx-auto animate-fade-in">
            <div class="mb-8">
                <Link href="/it-initiatives" class="text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to IT Initiatives
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">New IT Initiative</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Create a new IT initiative entry to start drafting its charter.</p>
            </div>

            <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 p-6 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Code & Name -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IT Initiative Code</label>
                            <input
                                v-model="form.code"
                                type="text"
                                class="w-full rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-900 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="PRJ-001"
                            />
                            <p v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IT Initiative Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-900 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="e.g., UI/UX Standardization"
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                    </div>

                    <!-- Owner & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IT Initiative Owner</label>
                            <input
                                v-model="form.owner_name"
                                type="text"
                                class="w-full rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-900 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="e.g., VP EIT SARM"
                            />
                            <p v-if="form.errors.owner_name" class="text-red-500 text-xs mt-1">{{ form.errors.owner_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                            <select v-model="form.status" class="w-full rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-700 dark:text-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                             <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status (Mock Text)</label>
                             <input
                                 type="text"
                                 class="w-full rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-900 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500"
                                 placeholder="Enter status text (e.g., On Track)"
                             />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/5">
                        <Link href="/it-initiatives" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg">Cancel</Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg disabled:opacity-50"
                        >
                            Create IT Initiative
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const form = useForm({
    code: '',
    name: '',
    owner_name: '',
    status: 'draft',
});

const submit = () => {
    form.post('/it-initiatives');
};
</script>
