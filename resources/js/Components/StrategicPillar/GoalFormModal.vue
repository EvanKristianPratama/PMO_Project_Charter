<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" class="relative z-50" @close="closeModal">
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
                        <DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-left align-middle shadow-xl transition-all dark:border-white/5 dark:bg-[#1a1a1a]">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5M3.75 12h16.5M3.75 18.75h16.5" />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <DialogTitle as="h3" class="text-lg font-bold text-slate-900 dark:text-white">
                                        {{ modalTitle }}
                                    </DialogTitle>

                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Pilar aktif: <span class="font-semibold text-slate-900 dark:text-white">{{ pilarLabel }}</span>
                                    </p>

                                    <form class="mt-5 space-y-4" @submit.prevent="submit">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kode Blok</label>
                                            <input
                                                v-model="form.code"
                                                type="text"
                                                maxlength="255"
                                                placeholder="Contoh: A"
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                required
                                            >
                                            <div v-if="form.errors.code" class="mt-1 text-sm text-rose-600">{{ form.errors.code }}</div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Blok</label>
                                            <input
                                                v-model="form.title"
                                                type="text"
                                                maxlength="255"
                                                placeholder="Masukkan judul blok"
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                required
                                            >
                                            <div v-if="form.errors.title" class="mt-1 text-sm text-rose-600">{{ form.errors.title }}</div>
                                        </div>

                                        <div v-if="form.errors.general" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                                            {{ form.errors.general }}
                                        </div>

                                        <div class="flex justify-end gap-3 pt-2">
                                            <button
                                                type="button"
                                                class="inline-flex justify-center rounded-lg px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5"
                                                @click="closeModal"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                class="inline-flex justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 disabled:cursor-wait disabled:opacity-75"
                                                :disabled="form.processing"
                                            >
                                                {{ form.processing ? 'Menyimpan...' : submitLabel }}
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
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

const route = useRouteHelper();

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    pilar: {
        type: [String, Number],
        default: 2,
    },
    pilarLabel: {
        type: String,
        default: 'Pilar 2',
    },
    goal: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const isEditing = computed(() => Boolean(props.goal?.id));
const modalTitle = computed(() => (isEditing.value ? 'Edit Blok Pilar' : 'Tambah Blok Pilar'));
const submitLabel = computed(() => (isEditing.value ? 'Simpan Perubahan' : 'Simpan Blok'));

const form = useForm({
    code: '',
    title: '',
    pilar: String(props.pilar ?? 2),
});

const syncForm = () => {
    form.code = String(props.goal?.code ?? '');
    form.title = String(props.goal?.title ?? '');
    form.pilar = String(props.pilar ?? 2);
    form.clearErrors();
};

watch(
    () => [props.show, props.goal, props.pilar],
    ([show]) => {
        if (show) {
            syncForm();
        }
    },
    { deep: true }
);

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const submit = () => {
    form.transform((data) => ({
        code: String(data.code ?? '').trim(),
        title: String(data.title ?? '').trim(),
        pilar: String(props.pilar ?? data.pilar ?? 2),
    }));

    if (isEditing.value) {
        form.put(route('strategic-pillars.goals.update', props.goal.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });

        return;
    }

    form.post(route('strategic-pillars.goals.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>
