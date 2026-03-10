<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] p-6 text-left align-middle shadow-xl transition-all border border-gray-200/80 dark:border-white/5">
                            <DialogTitle as="h2" class="text-lg font-medium text-slate-900 dark:text-slate-100">
                                {{ isEditing ? 'Edit Status Implementation' : 'Add Status Implementation' }}
                            </DialogTitle>

                            <form @submit.prevent="submit" class="mt-6 space-y-4 text-left">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Month / Year</label>
                                    <input
                                        v-model="form.month_year"
                                        type="month"
                                        class="mt-1 block w-full rounded-md border text-slate-700 border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                        required
                                    />
                                    <div v-if="form.errors.month_year" class="mt-1 text-sm text-red-600">{{ form.errors.month_year }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Review Status</label>
                                    <select
                                        v-model="form.review_status"
                                        class="mt-1 block w-full rounded-md border text-slate-700 border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                        required
                                    >
                                        <option value="On Track">On Track</option>
                                        <option value="At Risk">At Risk</option>
                                        <option value="Not Started">Not Started</option>
                                        <option value="Not Signed">Not Signed</option>
                                    </select>
                                    <div v-if="form.errors.review_status" class="mt-1 text-sm text-red-600">{{ form.errors.review_status }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status / Notes</label>
                                    <textarea
                                        v-model="form.status"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border text-slate-700 border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                                </div>

                                <div class="mt-6 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        @click="closeModal"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                        :disabled="form.processing"
                                    >
                                        Save
                                    </button>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    show: Boolean,
    statusData: {
        type: Object,
        default: null,
    },
    projectId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    month_year: '',
    review_status: 'On Track',
    status: '',
});

const isEditing = computed(() => !!props.statusData);

watch(() => props.show, (show) => {
    if (show) {
        if (props.statusData) {
            form.month_year = props.statusData.date ? props.statusData.date.substring(0, 7) : '';
            form.review_status = props.statusData.review_status || 'On Track';
            form.status = props.statusData.status || '';
        } else {
            form.reset();
        }
    }
});

const closeModal = () => {
    emit('close');
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (isEditing.value) {
        form.put(`/implementation-status/${props.statusData.id}`, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(`/it-initiatives/${props.projectId}/implementation-status`, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};
</script>
