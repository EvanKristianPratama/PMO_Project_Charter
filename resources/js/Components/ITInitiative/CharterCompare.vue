<script setup>
import { computed, nextTick, onMounted, watch } from 'vue';

const props = defineProps({
    current: { type: Object, required: true },
    previous: { type: Object, required: true },
    analysis: { type: Object, default: null },
    analysisForm: { type: Object, default: null },
    editable: { type: Boolean, default: false },
    projectName: { type: String, default: '' },
});

const fields = [
    { key: 'sponsor', fieldLabel: 'Project Sponsor', previousLabel: 'Project Sponsor', currentLabel: 'Project Sponsor', blockPreviousValue: true },
    { key: 'owner', fieldLabel: 'Project Owner', previousLabel: 'Project Owner', currentLabel: 'Project Owner' },
    { key: 'leader', fieldLabel: 'Project Leader', previousLabel: 'Project Leader', currentLabel: 'Project Leader', blockPreviousValue: true },
    { key: 'duration', fieldLabel: 'Duration', previousLabel: 'Durasi', currentLabel: 'Duration' },
    { key: 'tgl_dokumen', fieldLabel: 'Document Date', previousLabel: 'Tanggal Dokumen', currentLabel: 'Document Date', format: 'date' },
    { key: 'background', fieldLabel: 'Latar belakang - Gap/peluang saat ini / Background', previousLabel: 'Latar belakang - Gap/peluang saat ini', currentLabel: 'Background', multiline: true },
    { key: 'objectives', fieldLabel: 'Tujuan / Objectives', previousLabel: 'Tujuan', currentLabel: 'Objectives', multiline: true },
    { key: 'target_kpi', fieldLabel: 'Target KPI', previousLabel: 'Target KPI', currentLabel: 'Target KPI', multiline: true, showTerminologyRow: true, blockPreviousValue: true },
    { key: 'impact_value', fieldLabel: 'Dampak dan nilai bagi Pertamina / Impact Value', previousLabel: 'Dampak dan nilai bagi Pertamina', currentLabel: 'Impact Value', multiline: true },
    { key: 'key_personnel', fieldLabel: 'Personel Utama / Cross Functional Involvement', previousLabel: 'Personel Utama', currentLabel: 'Cross Functional Involvement', multiline: true },
    { key: 'key_items', fieldLabel: 'Item Utama / Key Items', previousLabel: 'Item Utama', currentLabel: 'Required Resources', multiline: true },
    { key: 'budget', fieldLabel: 'Indikatif Kebutuhan Budget / Budget', previousLabel: 'Indikatif Kebutuhan Budget', currentLabel: 'Budget' },
    { key: 'risks_identified', fieldLabel: 'Resiko Teridentifikasi / Risks Identified', previousLabel: 'Resiko Teridentifikasi', currentLabel: 'Risks Identified', multiline: true },
    { key: 'risk_mitigation', fieldLabel: 'Mitigasi Resiko / Risk Mitigation / ', previousLabel: 'Mitigasi Resiko', currentLabel: 'Risk Mitigation', multiline: true },
    { key: 'key_milestone', fieldLabel: 'Key Milestone & Due Date', previousLabel: 'Key Milestone & Due Date', currentLabel: 'Key Milestone & Due Date', multiline: true, blockPreviousValue: true },
    { key: 'notes', fieldLabel: 'Notes', previousLabel: 'Notes', currentLabel: 'Notes', multiline: true, blockPreviousValue: true },
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

const normalizeAnalysisValue = (value) => normalizeLineValue(value ?? '');

const resolveAnalysisSourceValue = (field) => {
    const key = field.analysisKey ?? field.key;

    if (props.editable && props.analysisForm) {
        return props.analysisForm[key] ?? '';
    }

    return props.analysis?.[key] ?? '';
};

const resolveStatusLabel = (charter, fallbackText) => {
    const resolvedStatusLabel = String(charter?.resolved_status_label ?? '').trim();
    if (resolvedStatusLabel !== '') {
        return resolvedStatusLabel;
    }

    const numericStatus = Number(charter?.status);
    if (numericStatus === 1) return 'Drafting';
    if (numericStatus === 2) return 'Propose';
    if (numericStatus === 3) return 'Review';
    if (numericStatus === 4) return 'Approved';
    if (numericStatus === 5) return 'Baseline';

    const rawStatus = String(charter?.status ?? '').trim();
    return rawStatus !== '' ? rawStatus : fallbackText;
};

const previousColumnLabel = computed(() => resolveStatusLabel(props.previous, 'Previous'));
const currentColumnLabel = computed(() => resolveStatusLabel(props.current, 'Current'));

const cards = computed(() => fields.map((field) => {
    const previousSourceValue = resolveFieldSourceValue(props.previous, field);
    const currentSourceValue = resolveFieldSourceValue(props.current, field);
    const previousValue = normalizeFieldValue(previousSourceValue, field);
    const currentValue = normalizeFieldValue(currentSourceValue, field);
    const analysisSourceValue = resolveAnalysisSourceValue(field);
    const analysisValue = normalizeAnalysisValue(analysisSourceValue);
    const hasAnalysis = analysisValue !== '';
    const changed = previousValue !== currentValue;
    const previousBlocked = field.blockPreviousValue === true && previousValue === '';
    const currentBlocked = field.blockCurrentValue === true && currentValue === '';
    const hasDistinctFieldLabels = String(field.previousLabel ?? field.label ?? '').trim() !== String(field.currentLabel ?? field.label ?? '').trim();
    const shouldSplitTerminology = field.showTerminologyRow === true && hasDistinctFieldLabels;
    const cardRows = shouldSplitTerminology
        ? [
            {
                rowKey: `${field.key}-previous`,
                fieldText: field.previousLabel ?? field.fieldLabel ?? field.label ?? '-',
                previousText: displayFieldValue(previousSourceValue, field),
                currentText: '-',
                previousBlocked,
                currentBlocked: false,
            },
            {
                rowKey: `${field.key}-current`,
                fieldText: field.currentLabel ?? field.fieldLabel ?? field.label ?? '-',
                previousText: '-',
                currentText: displayFieldValue(currentSourceValue, field),
                previousBlocked: false,
                currentBlocked,
            },
        ]
        : [
            {
                rowKey: `${field.key}-single`,
                fieldText: field.fieldLabel ?? field.label ?? '-',
                previousText: displayFieldValue(previousSourceValue, field),
                currentText: displayFieldValue(currentSourceValue, field),
                previousBlocked,
                currentBlocked,
            },
        ];

    return {
        ...field,
        analysisFieldKey: field.analysisKey ?? field.key,
        previousLabel: field.previousLabel ?? field.label ?? '-',
        currentLabel: field.currentLabel ?? field.label ?? '-',
        cardRows,
        changed,
        analysisText: hasAnalysis ? analysisValue : 'Belum ada notes analysis untuk field ini.',
        hasAnalysis,
        supportsAnalysis: field.showAnalysis !== false,
    };
}));

const headlineTitle = computed(() => {
    const name = String(props.projectName ?? '').trim();
    return name !== '' ? `Project Charter Comparison: ${name}` : 'Project Charter Comparison';
});

const resizeTextarea = (element) => {
    if (!element) return;
    element.style.height = 'auto';
    element.style.height = `${Math.max(element.scrollHeight, 30)}px`;
};

const resizeAllTextareas = () => {
    if (typeof window === 'undefined') return;

    nextTick(() => {
        document
            .querySelectorAll('.analysis-textarea')
            .forEach((element) => resizeTextarea(element));
    });
};

onMounted(() => {
    resizeAllTextareas();
});

watch(cards, () => {
    resizeAllTextareas();
}, { deep: true });

watch(() => props.analysisForm, () => {
    resizeAllTextareas();
}, { deep: true });
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

        <div class="charter-section">
            <div class="comparison-card-list">
                <div class="comparison-header-grid">
                    <div class="bar-main comparison-header-item comparison-header-item-field">Field</div>
                    <div class="bar-main comparison-header-item">{{ previousColumnLabel }}</div>
                    <div class="bar-main comparison-header-item">{{ currentColumnLabel }}</div>
                    <div class="bar-main comparison-header-item">Notes</div>
                </div>

                <article
                    v-for="card in cards"
                    :key="card.key"
                    class="comparison-card"
                    :class="card.changed ? 'comparison-card-changed' : ''"
                >
                    <div class="comparison-card-body">
                        <div
                            class="comparison-columns"
                            :style="{ '--comparison-row-count': String(card.cardRows.length) }"
                        >
                            <div
                                v-for="cardRow in card.cardRows"
                                :key="cardRow.rowKey"
                                class="comparison-grid-row"
                            >
                                <div class="comparison-grid-cell comparison-grid-cell-field">
                                    <div class="comparison-field-body">
                                        <p class="comparison-field-title">{{ cardRow.fieldText }}</p>
                                    </div>
                                </div>
                                <div class="comparison-grid-cell">
                                    <div
                                        class="comparison-column-body"
                                        :class="cardRow.previousBlocked ? 'comparison-column-body-blocked' : ''"
                                    >
                                        <p
                                            class="compare-text"
                                            :class="cardRow.previousBlocked ? 'compare-text-blocked' : ''"
                                        >
                                            {{ cardRow.previousBlocked ? '' : cardRow.previousText }}
                                        </p>
                                    </div>
                                </div>
                                <div class="comparison-grid-cell">
                                    <div
                                        class="comparison-column-body"
                                        :class="cardRow.currentBlocked ? 'comparison-column-body-blocked' : ''"
                                    >
                                        <p
                                            class="compare-text"
                                            :class="cardRow.currentBlocked ? 'compare-text-blocked' : ''"
                                        >
                                            {{ cardRow.currentBlocked ? '' : cardRow.currentText }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="comparison-notes-cell"
                                :class="card.hasAnalysis ? '' : 'comparison-notes-cell-empty'"
                                :style="{ gridRow: `1 / span ${card.cardRows.length}` }"
                            >
                                <template v-if="editable && analysisForm">
                                    <div class="analysis-editor-wrap">
                                        <textarea
                                            v-model="analysisForm[card.analysisFieldKey]"
                                            class="analysis-textarea"
                                            rows="1"
                                            placeholder="Tulis notes analysis..."
                                            @input="resizeTextarea($event.target)"
                                        ></textarea>
                                    </div>
                                    <p
                                        v-if="analysisForm.errors?.[card.analysisFieldKey]"
                                        class="analysis-error"
                                    >
                                        {{ analysisForm.errors[card.analysisFieldKey] }}
                                    </p>
                                </template>
                                <template v-else-if="card.supportsAnalysis">
                                    <p class="compare-text">{{ card.analysisText }}</p>
                                </template>
                                <p
                                    v-else
                                    class="compare-text compare-text-muted"
                                >
                                    Notes analysis tidak tersedia untuk field ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </article>
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

.charter-section {
    padding: 0;
}

.bar-main {
    background: #1e4f8f;
    color: #fff;
    padding: 7px 12px;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
}

.comparison-card-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 0;
    background: #fff;
}

.comparison-card {
    border: 1px solid #1e4f8f;
    border-top: 0;
    background: #fff;
}

.comparison-card-changed {
    box-shadow: inset 4px 0 0 #2e6ea2;
}

.comparison-card-body {
    background: #fff;
}

.comparison-header-grid {
    display: grid;
    grid-template-columns: minmax(180px, 0.42fr) minmax(0, 1fr) minmax(0, 1fr) minmax(220px, 0.85fr);
    border-bottom: 1px solid #1e4f8f;
}

.comparison-header-item {
    min-width: 0;
}

.comparison-header-item-field {
    border-right: 1px solid #d0d9e5;
}

.comparison-header-item:nth-child(2) {
    border-right: 1px solid #d0d9e5;
}

.comparison-header-item:nth-child(3) {
    border-right: 1px solid #d0d9e5;
}

.comparison-columns {
    display: grid;
    grid-template-columns: minmax(180px, 0.42fr) minmax(0, 1fr) minmax(0, 1fr) minmax(220px, 0.85fr);
}

.comparison-grid-row {
    display: contents;
}

.comparison-grid-cell {
    min-width: 0;
    display: flex;
}

.comparison-grid-cell:not(:last-child) {
    border-right: 1px solid #1e4f8f;
}

.comparison-grid-row + .comparison-grid-row .comparison-grid-cell {
    border-top: 1px solid #d8e1ec;
}

.comparison-grid-cell-field {
    background: #f8fafc;
}

.comparison-field-body {
    padding: 10px 12px;
    background: #f8fafc;
}

.comparison-field-body-secondary {
    background: #eef4fb;
}

.comparison-field-title {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.35;
    color: #0f172a;
}

.comparison-column-body {
    flex: 1;
    padding: 10px 12px;
    background: #fff;
}

.comparison-column-body-blocked {
    position: relative;
    background:
        repeating-linear-gradient(
            -45deg,
            #eef3f9 0,
            #eef3f9 10px,
            #e3ebf5 10px,
            #e3ebf5 20px
        );
}

.comparison-column-body-blocked::before {
    content: '';
    display: block;
    min-height: 1.55em;
}

.comparison-notes-cell {
    grid-column: 4;
    padding: 6px 8px;
    background: #f8fbff;
    color: #0f172a;
    border-left: 1px solid #1e4f8f;
}

.comparison-notes-cell-empty {
    background: #f8fafc;
    color: #64748b;
}

.analysis-editor-wrap {
    display: flex;
    width: 100%;
}

.analysis-textarea {
    width: 100%;
    min-height: 28px;
    resize: vertical;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    padding: 5px 8px;
    font-family: inherit;
    font-size: 12px;
    line-height: 1.35;
    color: #0f172a;
    outline: none;
    overflow: hidden;
}

.analysis-textarea:focus {
    border-color: #1d4f91;
    box-shadow: 0 0 0 1px #1d4f91;
}

.analysis-error {
    margin: 6px 0 0;
    font-size: 11px;
    color: #dc2626;
}

.compare-text {
    margin: 0;
    white-space: pre-line;
    line-height: 1.55;
    color: #1a1a1a;
    word-break: break-word;
}

.compare-text-blocked {
    color: #64748b;
    font-weight: 600;
}

.compare-text-muted {
    color: #64748b;
}

@media (max-width: 760px) {
    .comparison-header-grid,
    .comparison-columns {
        min-width: 980px;
    }

    .comparison-card-body {
        overflow-x: auto;
    }
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
}
</style>
