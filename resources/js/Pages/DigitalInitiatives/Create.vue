<template>
    <UserLayout title="Create Digital Initiative">
        <div class="mx-auto max-w-2xl animate-fade-in">
            <div class="mb-8">
                <Link href="/digital-initiatives" class="mb-2 flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Digital Initiatives
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">New Digital Initiative</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Create a new digital initiative entry.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <form class="space-y-6" @submit.prevent="submit">
                    <!-- No & Type -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">No</label>
                            <input
                                v-model="form.no"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="DI-001"
                            />
                            <p v-if="form.errors.no" class="mt-1 text-xs text-red-500">{{ form.errors.no }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Type</label>
                            <select
                                v-model="form.type"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                            >
                                <option value="">Select Type</option>
                                <option value="strategic">Strategic</option>
                                <option value="operational">Operational</option>
                                <option value="tactical">Tactical</option>
                            </select>
                            <p v-if="form.errors.type" class="mt-1 text-xs text-red-500">{{ form.errors.type }}</p>
                        </div>
                    </div>

                    <!-- Use Case & Project Owner -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Use Case</label>
                            <input
                                v-model="form.useCase"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="e.g., Automation of reporting"
                            />
                            <p v-if="form.errors.useCase" class="mt-1 text-xs text-red-500">{{ form.errors.useCase }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Project Owner</label>
                            <input
                                v-model="form.projectOwner"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="e.g., VP Digital"
                            />
                            <p v-if="form.errors.projectOwner" class="mt-1 text-xs text-red-500">{{ form.errors.projectOwner }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                        <textarea
                            v-model="form.desc"
                            rows="3"
                            class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            placeholder="Brief description of this initiative..."
                        ></textarea>
                        <p v-if="form.errors.desc" class="mt-1 text-xs text-red-500">{{ form.errors.desc }}</p>
                    </div>

                    <!-- Value -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Value</label>
                        <textarea
                            v-model="form.value"
                            rows="2"
                            class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            placeholder="Expected value or benefit..."
                        ></textarea>
                        <p v-if="form.errors.value" class="mt-1 text-xs text-red-500">{{ form.errors.value }}</p>
                    </div>

                    <!-- Urgency, RJJP, COE -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Urgency</label>
                            <select
                                v-model="form.urgency"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                            >
                                <option value="">Select Urgency</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                            <p v-if="form.errors.urgency" class="mt-1 text-xs text-red-500">{{ form.errors.urgency }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">RJJP</label>
                            <input
                                v-model="form.rjjp"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="RJJP"
                            />
                            <p v-if="form.errors.rjjp" class="mt-1 text-xs text-red-500">{{ form.errors.rjjp }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">COE</label>
                            <input
                                v-model="form.coe"
                                type="text"
                                class="w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="COE"
                            />
                            <p v-if="form.errors.coe" class="mt-1 text-xs text-red-500">{{ form.errors.coe }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 dark:border-white/5">
                        <Link href="/digital-initiatives" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Create Initiative
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
    type: '',
    no: '',
    projectOwner: '',
    useCase: '',
    desc: '',
    value: '',
    urgency: '',
    rjjp: '',
    coe: '',
});

const submit = () => {
    form.post('/digital-initiatives');
};
</script>
