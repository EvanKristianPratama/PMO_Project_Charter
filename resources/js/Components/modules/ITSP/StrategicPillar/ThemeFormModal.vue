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
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 6h9m-9 6h9m-9 6h9M4.5 6h.008v.008H4.5V6zm0 6h.008v.008H4.5V12zm0 6h.008v.008H4.5V18z" />
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
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Blok / Goal</label>
                                            <select
                                                v-model="form.idGoal"
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                :disabled="sortedGoals.length === 0"
                                                required
                                            >
                                                <option value="" disabled>Pilih blok</option>
                                                <option v-for="goal in sortedGoals" :key="goal.id" :value="String(goal.id)">
                                                    {{ goal.code }} - {{ goal.title }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.idGoal" class="mt-1 text-sm text-rose-600">{{ form.errors.idGoal }}</div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Theme</label>
                                            <input
                                                v-model="form.theme_number"
                                                type="number"
                                                min="1"
                                                placeholder="Contoh: 1"
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                required
                                            >
                                            <div v-if="form.errors.theme_number" class="mt-1 text-sm text-rose-600">{{ form.errors.theme_number }}</div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Theme</label>
                                            <input
                                                v-model="form.name"
                                                type="text"
                                                maxlength="255"
                                                placeholder="Masukkan nama theme"
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                required
                                            >
                                            <div v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</div>
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
                                                class="inline-flex justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-amber-700 disabled:cursor-wait disabled:opacity-75"
                                                :disabled="form.processing || sortedGoals.length === 0"
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
    goals: {
        type: Array,
        default: () => [],
    },
    theme: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const isEditing = computed(() => Boolean(props.theme?.id));
const modalTitle = computed(() => (isEditing.value ? 'Edit Theme' : 'Tambah Theme'));
const submitLabel = computed(() => (isEditing.value ? 'Simpan Perubahan' : 'Simpan Theme'));
const sortedGoals = computed(() =>
    [...props.goals].sort((left, right) => String(left.code ?? '').localeCompare(String(right.code ?? '')))
);

const form = useForm({
    idGoal: '',
    theme_number: '',
    name: '',
    pilar: String(props.pilar ?? 2),
});

const syncForm = () => {
    form.idGoal = props.theme?.idGoal ? String(props.theme.idGoal) : '';
    form.theme_number = props.theme?.theme_number ? String(props.theme.theme_number) : '';
    form.name = String(props.theme?.name ?? '');
    form.pilar = String(props.pilar ?? 2);
    form.clearErrors();
};

watch(
    () => [props.show, props.theme, props.pilar],
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
        idGoal: Number(data.idGoal),
        theme_number: Number(data.theme_number),
        name: String(data.name ?? '').trim(),
        pilar: String(props.pilar ?? data.pilar ?? 2),
    }));

    if (isEditing.value) {
        form.put(route('itsp.strategic-pillars.themes.update', props.theme.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });

        return;
    }

    form.post(route('itsp.strategic-pillars.themes.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>
