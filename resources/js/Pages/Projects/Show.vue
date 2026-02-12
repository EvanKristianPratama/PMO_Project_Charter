<template>
    <div class="min-h-screen bg-slate-100 dark:bg-[#0f0f0f] print:bg-white">
        <!-- Navigation Bar (Hidden in Print) -->
        <nav class="bg-white dark:bg-[#1a1a1a] border-b border-slate-200 dark:border-white/10 sticky top-0 z-30 print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-4">
                        <Link href="/projects" class="text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white truncate">{{ project.name }} <span class="text-slate-400 dark:text-slate-500 font-normal">({{ project.code }})</span></h1>
                        <StatusBadge :status="project.status" />
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="toggleDarkMode"
                            class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-white/5"
                            title="Toggle dark mode"
                        >
                            <svg v-if="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                        <button
                            v-if="!isEditing"
                            @click="isEditing = true"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg"
                        >
                            Edit Charter
                        </button>
                        <template v-else>
                            <button
                                @click="cancelEdit"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg"
                            >
                                Cancel
                            </button>
                            <button
                                @click="saveCharter"
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg flex items-center gap-2"
                            >
                                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Save Changes
                            </button>
                        </template>
                        <button
                            @click="printCharter"
                            class="p-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg"
                            title="Print PDF"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-8 print:py-0 print:m-0">
            <CharterDocument
                :project="project"
                :form="form"
                :editable="isEditing"
            />
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import CharterDocument from './Partials/CharterDocument.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { useDarkMode } from '@/Composables/useDarkMode';

const props = defineProps({
    project: Object,
});

const { isDark, toggleDarkMode } = useDarkMode();
const isEditing = ref(false);

const form = useForm({
    category: props.project.charter?.category || '',
    duration: props.project.charter?.duration || '',
    background: props.project.charter?.background || '',
    objectives: props.project.charter?.objectives || '',
    scope: props.project.charter?.scope || '',
    impact_value: props.project.charter?.impact_value || '',
    key_personnel: props.project.charter?.key_personnel || '',
    key_items: props.project.charter?.key_items || '',
    budget: props.project.charter?.budget || '',
    risks_identified: props.project.charter?.risks_identified || '',
    risk_mitigation: props.project.charter?.risk_mitigation || '',
});

const saveCharter = () => {
    form.post(`/projects/${props.project.id}/charter`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
};

const printCharter = () => {
    window.print();
};
</script>
