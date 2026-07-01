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
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/30">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </div>
                                <div class="flex-1 w-full">
                                    <DialogTitle as="h3" class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
                                        {{ isEditing ? 'Edit Status Implementation' : 'Add Status Implementation' }}
                                    </DialogTitle>
                                    
                                    <form @submit.prevent="submit" class="mt-4 space-y-4">
                                        <!-- Target & Progress -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Target (%)</label>
                                                <input
                                                    v-model.number="form.target"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    placeholder="0"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                />
                                                <div v-if="form.errors.target" class="mt-1 text-sm text-red-600">{{ form.errors.target }}</div>
                                            </div>
                                            <div>
                                                <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Progres (%)</label>
                                                <input
                                                    v-model.number="form.progress"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    placeholder="0"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                />
                                                <div v-if="form.errors.progress" class="mt-1 text-sm text-red-600">{{ form.errors.progress }}</div>
                                            </div>
                                        </div>

                                        <!-- Month & Year -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Bulan</label>
                                                <select
                                                    v-model="form.month"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                >
                                                    <option value="">Pilih Bulan</option>
                                                    <option v-for="m in monthOptions" :key="m" :value="m">{{ m }}</option>
                                                </select>
                                                <div v-if="form.errors.month" class="mt-1 text-sm text-red-600">{{ form.errors.month }}</div>
                                            </div>
                                            <div>
                                                <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                                                <input
                                                    v-model.number="form.year"
                                                    type="number"
                                                    min="2020"
                                                    max="2040"
                                                    :placeholder="currentYear"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                />
                                                <div v-if="form.errors.year" class="mt-1 text-sm text-red-600">{{ form.errors.year }}</div>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div>
                                            <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Status</label>
                                            <select
                                                v-model="form.status"
                                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                            >
                                                <option value="">Pilih Status</option>
                                                <option value="On Track">On Track</option>
                                                <option value="Done">Done</option>
                                                <option value="At Risk">At Risk</option>
                                                <option value="Delayed">Delayed</option>
                                                <option value="Not Started">Not Started</option>
                                            </select>
                                            <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Description</label>
                                            <textarea
                                                v-model="form.description"
                                                rows="3"
                                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                placeholder="Opsional"
                                            ></textarea>
                                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3 pt-2">
                                            <button
                                                type="button"
                                                class="inline-flex justify-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors"
                                                @click="closeModal"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                class="inline-flex justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-75 disabled:cursor-wait"
                                                :disabled="form.processing"
                                            >
                                                <span v-if="form.processing" class="flex items-center">
                                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Menyimpan...
                                                </span>
                                                <span v-else>Save</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
import Swal from 'sweetalert2';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

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
const route = useRouteHelper();

const currentYear = new Date().getFullYear();

const monthOptions = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const form = useForm({
    target: '',
    progress: '',
    month: '',
    year: '',
    status: '',
    description: '',
});

const isEditing = computed(() => !!props.statusData);

watch(() => props.show, (show) => {
    if (show) {
        if (props.statusData) {
            form.target = props.statusData.target ?? '';
            form.progress = props.statusData.progress ?? '';
            form.month = props.statusData.month || '';
            form.year = props.statusData.year ?? '';
            form.status = props.statusData.status || '';
            form.description = props.statusData.description || '';
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
    const requestOptions = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            Swal.fire({
                title: 'Berhasil!',
                text: isEditing.value 
                    ? 'Status implementation berhasil diperbarui.' 
                    : 'Status implementation berhasil ditambahkan.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
    };

    if (isEditing.value) {
        form.put(route('itsp.it-initiatives.implementation-status.update', props.statusData.id), requestOptions);
    } else {
        form.post(route('itsp.it-initiatives.implementation-status.store', props.projectId), requestOptions);
    }
};
</script>
