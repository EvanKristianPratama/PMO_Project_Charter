<script setup>
import { computed } from 'vue';

const props = defineProps({
    current: { type: Object, required: true },
    previous: { type: Object, required: true },
    projectName: { type: String, default: '' },
});

const fields = [
    { key: 'sponsor', label: 'Project Sponsor' },
    { key: 'owner', label: 'Project Owner' },
    { key: 'leader', label: 'Project Leader' },
    { key: 'duration', label: 'Duration' },
    { key: 'tgl_dokumen', label: 'Document Date', format: 'date' },
    { key: 'background', label: 'Background', multiline: true },
    { key: 'objectives', label: 'Objectives', multiline: true },
    { key: 'target_kpi', label: 'Target KPI', multiline: true },
    { key: 'impact_value', label: 'Impact Value', multiline: true },
    { key: 'key_personnel', label: 'Cross Function Involvement', multiline: true },
    { key: 'key_items', label: 'Required Resources', multiline: true },
    { key: 'budget', label: 'Budget' },
    { key: 'risks_identified', label: 'Risks Identified', multiline: true },
    { key: 'risk_mitigation', label: 'Risk Mitigation', multiline: true },
    { key: 'key_milestone', label: 'Key Milestone & Due Date', multiline: true },
    { key: 'notes', label: 'Notes', multiline: true },
];

const resolveFieldSourceValue = (charter, field) => {
    if (field.key === 'target_kpi') {
        return charter?.target_kpi
            ?? charter?.metadata?.target_kpi
            ?? charter?.metadata?.targetKpi
            ?? charter?.metadata?.kpi_target
            ?? charter?.metadata?.kpi
            ?? '';
    }

    return charter?.[field.key];
};

const normalizeLineValue = (value) => String(value ?? '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean)
    .join('\n');

const normalizeDateValue = (value) => {
    const raw = String(value ?? '').trim();
    if (!raw) return '';

    const parsed = new Date(raw);
    if (Number.isNaN(parsed.getTime())) {
        return raw;
    }

    return parsed.toISOString().slice(0, 10);
};

const formatDateValue = (value) => {
    const normalized = normalizeDateValue(value);
    if (!normalized) return '-';

    const parsed = new Date(normalized);
    if (Number.isNaN(parsed.getTime())) {
        return normalized;
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(parsed);
};

const normalizeFieldValue = (value, field) => {
    if (field.format === 'date') {
        return normalizeDateValue(value);
    }

    if (field.multiline) {
        return normalizeLineValue(value);
    }

    return String(value ?? '').trim();
};

const displayFieldValue = (value, field) => {
    if (field.format === 'date') {
        return formatDateValue(value);
    }

    const normalized = normalizeFieldValue(value, field);
    return normalized === '' ? '-' : normalized;
};

const resolveVersionLabel = (charter, fallbackText) => {
    const versionLabel = String(charter?.version_label ?? '').trim();
    if (versionLabel !== '') {
        return versionLabel;
    }

    const documentDate = formatDateValue(charter?.tgl_dokumen);
    if (documentDate !== '-') {
        return documentDate;
    }

    return fallbackText;
};

const previousVersionLabel = computed(() => resolveVersionLabel(props.previous, 'Previous Version'));
const currentVersionLabel = computed(() => resolveVersionLabel(props.current, 'Current Version'));

const rows = computed(() => fields.map((field) => {
    const previousSourceValue = resolveFieldSourceValue(props.previous, field);
    const currentSourceValue = resolveFieldSourceValue(props.current, field);
    const previousValue = normalizeFieldValue(previousSourceValue, field);
    const currentValue = normalizeFieldValue(currentSourceValue, field);

    return {
        ...field,
        changed: previousValue !== currentValue,
        previousText: displayFieldValue(previousSourceValue, field),
        currentText: displayFieldValue(currentSourceValue, field),
    };
}));

const headlineTitle = computed(() => {
    const name = String(props.projectName ?? '').trim();
    return name !== '' ? `Project Charter Comparison: ${name}` : 'Project Charter Comparison';
});
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] bg-white text-slate-900 shadow-sm print:shadow-none">
        <div class="border-b border-slate-200 px-5 pb-3 pt-5">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                    {{ headlineTitle }}
                </h1>
            </div>
        </div>

        <div class="compare-overview">
            <article class="panel">
                <div class="bar-sub">Previous Version</div>
                <div class="panel-body">
                    <p class="summary-title">{{ previousVersionLabel }}</p>
                </div>
            </article>

            <article class="panel">
                <div class="bar-sub">Current Version</div>
                <div class="panel-body panel-body-highlight">
                    <p class="summary-title">{{ currentVersionLabel }}</p>
                </div>
            </article>
        </div>

        <div class="charter-section">
            <div class="comparison-shell">
                <div class="comparison-grid comparison-grid-head">
                    <div class="comparison-head-cell comparison-head-field">Field</div>
                    <div class="comparison-head-cell">Previous</div>
                    <div class="comparison-head-cell">Current</div>
                </div>

                <div
                    v-for="row in rows"
                    :key="row.key"
                    class="comparison-grid comparison-row"
                >
                    <div class="comparison-field-cell">
                        <div class="comparison-field-content">{{ row.label }}</div>
                    </div>

                    <div
                        class="comparison-value-cell"
                        :class="row.changed ? 'comparison-value-cell-changed' : ''"
                    >
                        <p class="compare-text">{{ row.previousText }}</p>
                    </div>

                    <div
                        class="comparison-value-cell"
                        :class="row.changed ? 'comparison-value-cell-changed' : ''"
                    >
                        <p class="compare-text">{{ row.currentText }}</p>
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>

<style scoped>
.charter-sheet {
    font-family: "Segoe UI", Arial, sans-serif;
    font-size: 13px;
    color: #1a1a1a;
    border: 1px solid #ccc;
}

.compare-overview {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 0;
    border-bottom: 1px solid #ddd;
}

.compare-overview > .panel:first-child {
    border-right: none;
}

.charter-section {
    padding: 0;
    border-top: 1px solid #ddd;
}

.bar-main {
    background: #1e4f8f;
    color: #fff;
    padding: 7px 12px;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
}

.bar-sub {
    background: #2e6ea2;
    color: #fff;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
}

.bar-sub-sm {
    font-size: 11px;
    padding: 4px 8px;
}

.panel {
    border: 1px solid #1e4f8f;
    border-radius: 0;
    background: transparent;
}

.panel-body {
    padding: 10px;
    background: #fff;
    font-size: 12px;
}

.panel-body-highlight {
    background: #f8fbff;
}

.summary-title {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}

.comparison-shell {
    overflow-x: auto;
}

.comparison-grid {
    display: grid;
    grid-template-columns: 0.32fr 1.34fr 1.34fr;
    width: 100%;
}

.comparison-grid-head {
    border-bottom: 1px solid #1e4f8f;
}

.comparison-head-cell {
    background: #2e6ea2;
    color: #fff;
    padding: 8px 10px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    border-right: 1px solid #1e4f8f;
}

.comparison-head-cell:last-child {
    border-right: none;
}

.comparison-head-field {
    background: #1e4f8f;
}

.comparison-row {
    border-bottom: 1px solid #ddd;
}

.comparison-field-cell {
    background: #f8f8f8;
    border-right: 1px solid #ccc;
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
}

.comparison-field-content {
    display: block;
}

.comparison-value-cell {
    background: #fff;
    border-right: 1px solid #1e4f8f;
    padding: 10px 12px;
}

.comparison-value-cell:last-child {
    border-right: none;
}

.comparison-value-cell-changed {
    background: #f8fbff;
}

.compare-text {
    margin: 0;
    white-space: pre-line;
    line-height: 1.55;
    color: #1a1a1a;
    word-break: break-word;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    .charter-sheet {
        width: 100%;
        max-width: none;
        border: none;
        box-shadow: none;
    }

    .panel-body {
        background: #fff !important;
    }
}

@media (max-width: 900px) {
    .compare-overview {
        grid-template-columns: 1fr;
    }

    .compare-overview > .panel:first-child {
        border-right: 1px solid #1e4f8f;
        border-bottom: none;
    }
}
</style>
