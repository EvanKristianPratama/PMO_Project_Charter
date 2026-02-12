<template>
    <UserLayout :title="`Project Charter - ${project.name}`">
        <div class="space-y-5 print:space-y-0">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            href="/projects"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                            </svg>
                            Back to Projects
                        </Link>

                        <h1 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ project.name }}
                        </h1>

                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ project.code }}</span>
                        <StatusBadge :status="project.status" />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-if="!isEditing"
                            type="button"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                            @click="isEditing = true"
                        >
                            Edit Charter
                        </button>

                        <template v-else>
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                                @click="cancelEdit"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-70"
                                @click="saveCharter"
                            >
                                <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4Zm2 5.29A7.95 7.95 0 0 1 4 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65Z"></path>
                                </svg>
                                Save Changes
                            </button>
                        </template>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="printCharter"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 17h12v3H6v-3Zm-2-2h16a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2Z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <span>
                        Roadmap dipisah di menu <strong>Roadmap</strong> dan otomatis mengambil data durasi/tanggal dari project yang sudah diinput.
                    </span>
                    <Link
                        :href="`/roadmap?project_id=${project.id}`"
                        class="inline-flex items-center rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200"
                    >
                        Buka Roadmap Module
                    </Link>
                </div>
            </section>

            <main class="print:m-0 print:p-0">
                <CharterDocument
                    :project="project"
                    :form="form"
                    :editable="isEditing"
                />
            </main>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CharterDocument from './Partials/CharterDocument.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    project: Object,
});

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
