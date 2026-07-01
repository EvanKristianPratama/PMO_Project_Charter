<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: {
        type: Object,
        required: true,
    },
});

const charter = computed(() => props.initiative?.charter ?? null);

const displayValue = (value) => {
    const normalizedValue = String(value ?? '').trim();
    return normalizedValue === '' ? '-' : normalizedValue;
};

const statusLabel = computed(() => {
    return displayValue(props.initiative?.statusRef?.name ?? props.initiative?.status);
});

const ownerName = computed(() => {
    return displayValue(props.initiative?.owner_name ?? charter.value?.owner);
});

const charterField = (fieldName) => {
    return displayValue(charter.value?.[fieldName]);
};

const documentDate = computed(() => {
    const rawValue = charter.value?.tgl_dokumen;

    if (!rawValue) {
        return '-';
    }

    const parsedDate = new Date(rawValue);

    if (Number.isNaN(parsedDate.getTime())) {
        return displayValue(rawValue);
    }

    return parsedDate.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
});

const narrativeSections = computed(() => {
    return [
        { key: 'objectives', label: 'Objectives', value: charterField('objectives') },
        { key: 'background', label: 'Background', value: charterField('background') },
        { key: 'impact_value', label: 'Impact Value', value: charterField('impact_value') },
        { key: 'key_personnel', label: 'Key Personnel', value: charterField('key_personnel') },
        { key: 'key_items', label: 'Key Items', value: charterField('key_items') },
        { key: 'budget', label: 'Budget', value: charterField('budget') },
        { key: 'risks_identified', label: 'Risks Identified', value: charterField('risks_identified') },
        { key: 'risk_mitigation', label: 'Risk Mitigation', value: charterField('risk_mitigation') },
    ];
});
</script>

<template>
    <article class="mx-auto w-full max-w-5xl bg-white text-slate-900 shadow-sm print:shadow-none">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-lg font-bold text-slate-900">
                    Digital Initiative: {{ displayValue(initiative.code) }}
                </h1>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                    {{ statusLabel }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-600">
                {{ displayValue(initiative.name) }}
            </p>
        </div>

        <div class="grid gap-6 px-6 py-5 md:grid-cols-2">
            <section class="space-y-3">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">General Information</h2>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Code</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.code) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Initiative Name</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.name) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Project Owner</span>
                        <span class="font-medium text-slate-800">{{ ownerName }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Project Status</span>
                        <span class="font-medium text-slate-800">{{ statusLabel }}</span>
                    </div>
                </div>
            </section>

            <section class="space-y-3">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Charter Information</h2>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Category</span>
                        <span class="font-medium text-slate-800">{{ charterField('category') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Version</span>
                        <span class="font-medium text-slate-800">{{ charterField('version_label') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Document Date</span>
                        <span class="font-medium text-slate-800">{{ documentDate }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Duration</span>
                        <span class="font-medium text-slate-800">{{ charterField('duration') }}</span>
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-4 px-6 pb-6">
            <section v-for="section in narrativeSections" :key="section.key"
                class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    {{ section.label }}
                </h2>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-700">
                    {{ section.value }}
                </p>
            </section>
        </div>
    </article>
</template>
