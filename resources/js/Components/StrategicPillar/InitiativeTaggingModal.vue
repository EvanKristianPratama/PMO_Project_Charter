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
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/30">
                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                    </svg>
                                </div>
                                <div class="flex-1 w-full">
                                    <DialogTitle as="h3" class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
                                        Add Initiative Tagging
                                    </DialogTitle>

                                    <form @submit.prevent="submit" class="mt-4 space-y-4">
                                        <!-- 1. Initiative -->
                                        <div>
                                            <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">IT Initiative</label>
                                            <select
                                                v-model="form.initiative_id"
                                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                required
                                            >
                                                <option value="" disabled>Pilih Initiative</option>
                                                <option v-for="init in sortedInitiatives" :key="init.id" :value="init.id">
                                                    {{ init.code ? `[${init.code}]` : '' }} {{ init.name }}{{ init.organization?.name ? ` · ${init.organization.name}` : '' }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.initiative_id" class="mt-1 text-sm text-red-600">{{ form.errors.initiative_id }}</div>
                                        </div>

                                        <!-- 2. Goal / Blok -->
                                        <div>
                                            <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Blok / Strategic Pillar</label>
                                            <select
                                                v-model="form.goal"
                                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white"
                                                required
                                            >
                                                <option value="" disabled>Pilih Blok / Goal</option>
                                                <option v-for="goal in goals" :key="goal.id" :value="goal.code">
                                                    {{ goal.code }} - {{ goal.title }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.goal" class="mt-1 text-sm text-red-600">{{ form.errors.goal }}</div>
                                        </div>

                                        <!-- 3. Theme (Dependent on Goal) -->
                                        <div>
                                            <label class="block text-[13px] font-medium text-gray-700 dark:text-gray-300">Theme (Opsional)</label>
                                            <select
                                                v-model="form.themes_id"
                                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-white/10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white dark:bg-[#1a1a1a] text-gray-900 dark:text-white disabled:opacity-50"
                                                :disabled="!form.goal || availableThemes.length === 0"
                                            >
                                                <option value="">-- Hanya Map ke Blok / Tidak ada Theme --</option>
                                                <option v-for="theme in availableThemes" :key="theme.id" :value="theme.id">
                                                    Theme {{ theme.theme_number }}. {{ theme.name }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.themes_id" class="mt-1 text-sm text-red-600">{{ form.errors.themes_id }}</div>
                                            <div v-if="form.goal && availableThemes.length === 0" class="mt-1 text-[11px] text-amber-600">Blok ini tidak memiliki data theme.</div>
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
                                                class="inline-flex justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all disabled:opacity-75 disabled:cursor-wait"
                                                :disabled="form.processing"
                                            >
                                                <span v-if="form.processing" class="flex items-center">
                                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Processing...
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
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    show: Boolean,
    initiatives: {
        type: Array,
        default: () => [],
    },
    goals: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    initiative_id: '',
    goal: '',
    themes_id: '',
});

// Sort initiatives by code numerically
const sortedInitiatives = computed(() =>
    [...props.initiatives].sort((a, b) => Number(a.code ?? 99999) - Number(b.code ?? 99999))
);

// Calculate available themes based on selected Goal
const availableThemes = computed(() => {
    if (!form.goal || !props.goals) return [];
    
    // Find the selected goal object
    const selectedGoal = props.goals.find(g => g.code === form.goal);
    
    // Return its themes if they exist
    return selectedGoal && selectedGoal.themes ? selectedGoal.themes : [];
});

// Watch for goal change to reset theme
watch(() => form.goal, (newGoal, oldGoal) => {
    if (newGoal !== oldGoal) {
        form.themes_id = ''; // reset theme when goal changes
    }
});

const closeModal = () => {
    emit('close');
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.post('/strategic-pillars/tagging', {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>
