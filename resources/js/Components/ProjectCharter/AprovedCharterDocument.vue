<script setup>
const props = defineProps({
    itInitiative: { type: Object, required: true },
    form: { type: Object, required: true },
    editable: { type: Boolean, default: false },
    statusTimeline: { type: [String, Number], default: null },
});

const lineItems = (value) => String(value || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

const stripBulletPrefix = (value) => String(value || '')
    .replace(/^\s*([*-]|\d+\.)\s*/, '')
    .trim();

const normalizedLineItems = (value) => lineItems(value)
    .map((line) => stripBulletPrefix(line))
    .filter(Boolean);

const statusMap = {
    1: { label: 'Draft', cls: 'bg-slate-100 text-slate-600 ring-1 ring-slate-300' },
    2: { label: 'Propose', cls: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300' },
    3: { label: 'Review', cls: 'bg-amber-100 text-amber-700 ring-1 ring-amber-300' },
    5: { label: 'Baseline', cls: 'bg-purple-100 text-purple-700 ring-1 ring-purple-300' },
    4: { label: 'Approved', cls: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300' },
};

const statusTimelineBadgeClass = (status) => {
    const key = Number(status);
    return statusMap[key]?.cls ?? 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
};

const statusTimelineLabel = (status) => {
    const key = Number(status);
    return statusMap[key]?.label ?? String(status ?? '');
};

const formatDateLong = (value) => {
    const raw = String(value || '').trim();
    if (!raw) return '-';
    const parsed = new Date(raw);
    if (Number.isNaN(parsed.getTime())) return raw;
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(parsed);
};

const displayValue = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};

const displayMultilineValue = (value) => {
    const items = normalizedLineItems(value);

    if (items.length) {
        return items.join('\n');
    }

    return displayValue(value);
};

const resolveTargetKpi = () => {
    const metadata = props.form?.metadata ?? {};
    const candidates = [
        props.form?.target_kpi,
        metadata?.target_kpi,
        metadata?.targetKpi,
        metadata?.kpi_target,
        metadata?.kpi,
    ];

    for (const value of candidates) {
        if (Array.isArray(value)) {
            const items = value
                .map((item) => String(item ?? '').trim())
                .filter(Boolean);

            if (items.length) {
                return items.join('\n');
            }

            continue;
        }

        const trimmed = String(value ?? '').trim();
        if (trimmed !== '') {
            return trimmed;
        }
    }

    return '-';
};

const headlineSummary = () => {
    const firstObjective = normalizedLineItems(props.form?.objectives)[0];

    if (firstObjective) {
        return firstObjective;
    }

    return String(props.itInitiative?.description ?? '').trim();
};
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] bg-white text-slate-900 shadow-sm print:shadow-none">
        <div class="px-5 pt-5 pb-3 border-b border-slate-200">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                    Project Charter:
                    {{ itInitiative.name || '-' }}
                </h1>
                <span
                    v-if="statusTimeline !== null && statusTimeline !== ''"
                    :class="statusTimelineBadgeClass(statusTimeline)"
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                >
                    {{ statusTimelineLabel(statusTimeline) }}
                </span>
            </div>
            <p class="mt-1 text-[13px] text-slate-600">
                {{ headlineSummary() || '-' }}
            </p>
        </div>

        <div class="info-paired-stack">
            <div class="info-paired-row">
                <div class="info-paired-cell">
                    <span class="info-label">Project Sponsor</span>
                    <span class="info-sep"></span>
                    <span class="info-value">
                        <input
                            v-if="editable"
                            v-model="form.sponsor"
                            type="text"
                            class="info-input"
                            placeholder="Project sponsor"
                        >
                        <template v-else>{{ displayValue(form.sponsor) }}</template>
                    </span>
                </div>
                <div class="info-paired-cell info-paired-cell-subgroup">
                    <div class="info-subcell">
                        <span class="info-label">Project Duration</span>
                        <span class="info-sep"></span>
                        <span class="info-value">
                            <input
                                v-if="editable"
                                v-model="form.duration"
                                type="text"
                                class="info-input"
                                placeholder="Project duration"
                            >
                            <template v-else>{{ displayValue(form.duration) }}</template>
                        </span>
                    </div>
                    <div class="info-subcell info-subcell-compact">
                        <span class="info-label info-label-dark">Document Date</span>
                        <span class="info-sep"></span>
                        <span class="info-value">
                            <input
                                v-if="editable"
                                v-model="form.tgl_dokumen"
                                type="date"
                                class="info-input"
                            >
                            <template v-else>{{ formatDateLong(form.tgl_dokumen) }}</template>
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-paired-row">
                <div class="info-paired-cell">
                    <span class="info-label">Project Owner</span>
                    <span class="info-sep"></span>
                    <span class="info-value">
                        <input
                            v-if="editable"
                            v-model="form.owner"
                            type="text"
                            class="info-input"
                            placeholder="Project owner"
                        >
                        <template v-else>{{ displayValue(form.owner) }}</template>
                    </span>
                </div>
                <div class="info-paired-cell">
                    <span class="info-label">Required Resources</span>
                    <span class="info-sep"></span>
                    <span class="info-value info-value-multiline">
                        <textarea
                            v-if="editable"
                            v-model="form.key_items"
                            class="info-textarea"
                            placeholder="Satu poin per baris..."
                        ></textarea>
                        <template v-else>{{ displayMultilineValue(form.key_items) }}</template>
                    </span>
                </div>
            </div>

            <div class="info-paired-row">
                <div class="info-paired-cell">
                    <span class="info-label">Project Leader</span>
                    <span class="info-sep"></span>
                    <span class="info-value">
                        <input
                            v-if="editable"
                            v-model="form.leader"
                            type="text"
                            class="info-input"
                            placeholder="Project leader"
                        >
                        <template v-else>{{ displayValue(form.leader) }}</template>
                    </span>
                </div>
                <div class="info-paired-cell">
                    <span class="info-label">Cost</span>
                    <span class="info-sep"></span>
                    <span class="info-value">
                        <input
                            v-if="editable"
                            v-model="form.budget"
                            type="text"
                            class="info-input"
                            placeholder="Cost"
                        >
                        <template v-else>{{ displayValue(form.budget) }}</template>
                    </span>
                </div>
            </div>

            <div class="info-paired-row info-paired-row-match">
                <div class="info-paired-cell">
                    <span class="info-label info-label-dark">Cross Function Involvement</span>
                    <span class="info-sep"></span>
                    <span class="info-value info-value-multiline">
                        <textarea
                            v-if="editable"
                            v-model="form.key_personnel"
                            class="info-textarea"
                            placeholder="Satu poin per baris..."
                        ></textarea>
                        <template v-else>{{ displayMultilineValue(form.key_personnel) }}</template>
                    </span>
                </div>
                <div class="info-paired-cell info-paired-cell-vertical">
                    <div class="info-subcell info-subcell-static">
                        <span class="info-label">Business Objectives</span>
                        <span class="info-sep"></span>
                        <span class="info-value info-value-multiline">
                            <textarea
                                v-if="editable"
                                v-model="form.objectives"
                                class="info-textarea"
                                placeholder="Satu poin per baris..."
                            ></textarea>
                            <template v-else>{{ displayMultilineValue(form.objectives) }}</template>
                        </span>
                    </div>
                    <div class="info-subcell info-subcell-fill">
                        <span class="info-label">Target KPI</span>
                        <span class="info-sep"></span>
                        <span class="info-value info-value-multiline">
                            <textarea
                                v-if="editable"
                                v-model="form.target_kpi"
                                class="info-textarea"
                                placeholder="Satu poin per baris..."
                            ></textarea>
                            <template v-else>{{ resolveTargetKpi() }}</template>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="charter-section">
            <article class="panel">
                <div class="bar-sub">Project Background</div>
                <div class="panel-body">
                    <textarea
                        v-if="editable"
                        v-model="form.background"
                        class="field-area"
                        placeholder="Satu poin per baris..."
                    ></textarea>
                    <ul v-else-if="normalizedLineItems(form.background).length" class="bullet-list">
                        <li v-for="(line, idx) in normalizedLineItems(form.background)" :key="`bg-${idx}`">
                            {{ line }}
                        </li>
                    </ul>
                    <p v-else class="empty">-</p>
                </div>
            </article>
        </div>

        <div class="dual-section-layout">
            <div class="dual-section-cell dual-section-cell-left dual-section-cell-match">
                <article class="panel stretch-panel">
                    <div class="bar-sub">Key Milestone & Due Date</div>
                    <div class="panel-body">
                        <textarea
                            v-if="editable"
                            v-model="form.key_milestone"
                            class="field-area"
                            placeholder="Satu poin per baris..."
                        ></textarea>
                        <ul v-else-if="normalizedLineItems(form.key_milestone).length" class="bullet-list">
                            <li v-for="(line, idx) in normalizedLineItems(form.key_milestone)" :key="`km-${idx}`">
                                {{ line }}
                            </li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>
            </div>

            <div class="dual-section-cell">
                <section class="risk-section">
                    <div class="bar-main">Potential Risk</div>

                    <div class="grid-2col">
                        <article class="panel stretch-panel">
                            <div class="bar-sub">Risk</div>
                            <div class="panel-body">
                                <textarea
                                    v-if="editable"
                                    v-model="form.risks_identified"
                                    class="field-area"
                                    placeholder="Satu poin per baris..."
                                ></textarea>
                                <ul v-else-if="normalizedLineItems(form.risks_identified).length" class="bullet-list">
                                    <li v-for="(line, idx) in normalizedLineItems(form.risks_identified)" :key="`risk-${idx}`">
                                        {{ line }}
                                    </li>
                                </ul>
                                <p v-else class="empty">-</p>
                            </div>
                        </article>

                        <article class="panel stretch-panel">
                            <div class="bar-sub">Mitigation</div>
                            <div class="panel-body">
                                <textarea
                                    v-if="editable"
                                    v-model="form.risk_mitigation"
                                    class="field-area"
                                    placeholder="Satu poin per baris..."
                                ></textarea>
                                <ul v-else-if="normalizedLineItems(form.risk_mitigation).length" class="bullet-list">
                                    <li v-for="(line, idx) in normalizedLineItems(form.risk_mitigation)" :key="`mit-${idx}`">
                                        {{ line }}
                                    </li>
                                </ul>
                                <p v-else class="empty">-</p>
                            </div>
                        </article>
                    </div>
                </section>
            </div>

            <div class="dual-section-cell dual-section-cell-left dual-section-cell-match">
                <article class="panel stretch-panel">
                    <div class="bar-sub">Notes</div>
                    <div class="panel-body">
                        <textarea
                            v-if="editable"
                            v-model="form.notes"
                            class="field-area"
                            placeholder="Notes..."
                        ></textarea>
                        <p v-else-if="displayValue(form.notes) !== '-'" class="whitespace-pre-line text-[12px] leading-[1.55] text-slate-900">
                            {{ form.notes }}
                        </p>
                        <p v-else class="empty">-</p>
                    </div>
                </article>
            </div>

            <div class="dual-section-cell dual-section-cell-match">
                <article class="panel stretch-panel">
                    <div class="bar-sub">Impact and Value for Pertamina</div>
                    <div class="panel-body">
                        <textarea
                            v-if="editable"
                            v-model="form.impact_value"
                            class="field-area"
                            placeholder="Satu poin per baris..."
                        ></textarea>
                        <ul v-else-if="normalizedLineItems(form.impact_value).length" class="bullet-list">
                            <li v-for="(line, idx) in normalizedLineItems(form.impact_value)" :key="`impact-${idx}`">
                                {{ line }}
                            </li>
                        </ul>
                        <p v-else class="empty">-</p>
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

.info-paired-stack {
    border-bottom: 1px solid #ccc;
    background: #f8f8f8;
}

.info-paired-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    border-bottom: 1px solid #ccc;
}

.info-paired-row:last-child {
    border-bottom: none;
}

.info-paired-row-match {
    align-items: stretch;
}

.info-paired-cell {
    display: flex;
    align-items: stretch;
    min-width: 0;
}

.info-paired-cell:first-child {
    border-right: 1px solid #ccc;
}

.info-paired-cell-subgroup {
    padding: 0;
}

.info-paired-cell-vertical {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.info-paired-cell-vertical .info-subcell + .info-subcell {
    border-left: none;
    border-top: 1px solid #ccc;
}

.info-subcell {
    display: flex;
    align-items: stretch;
    flex: 1 1 0;
    min-width: 0;
}

.info-subcell + .info-subcell {
    border-left: 1px solid #ccc;
}

.info-subcell-compact {
    flex: 0 0 260px;
}

.info-subcell-static {
    flex: 0 0 auto;
}

.info-subcell-fill {
    flex: 1 1 auto;
}

.info-label {
    background: #2563a8;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    flex-shrink: 0;
}

.info-paired-cell .info-label,
.info-subcell .info-label {
    flex: 0 0 170px;
    white-space: normal;
    line-height: 1.25;
}

.info-subcell-compact .info-label {
    flex-basis: 120px;
}

.info-label-dark {
    background: #1e4f8f;
}

.info-sep {
    width: 0;
    border-right: 1px solid #aac4e0;
}

.info-value {
    padding: 6px 12px;
    font-size: 13px;
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.info-value-multiline {
    white-space: pre-line;
    align-items: flex-start;
}

.info-input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 13px;
    background: transparent;
    color: inherit;
}

.info-textarea {
    width: 100%;
    min-height: 72px;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px;
    resize: vertical;
    background: #fff;
    font-size: 12px;
    line-height: 1.45;
    outline: none;
    font-family: inherit;
    color: inherit;
}

.charter-section {
    padding: 0;
    border-top: 1px solid #ddd;
}

.dual-section-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 0;
    align-items: stretch;
}

.dual-section-cell {
    display: flex;
    min-width: 0;
}

.dual-section-cell-left {
    border-right: 1px solid #1e4f8f;
}

.dual-section-cell-match {
    align-self: stretch;
}

.risk-section,
.stretch-panel {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
}

.risk-section .grid-2col {
    flex: 1;
}

.risk-section .grid-2col > .panel {
    display: flex;
    flex-direction: column;
}

.stretch-panel .panel-body,
.risk-section .panel-body {
    flex: 1;
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

.field-area {
    width: 100%;
    min-height: 110px;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px;
    resize: vertical;
    background: #fff;
    font-size: 12px;
    line-height: 1.45;
    outline: none;
    font-family: inherit;
    color: inherit;
}

.panel-body-sm {
    padding: 7px;
}

.grid-2col {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    border-top: 1px solid #1e4f8f;
    gap: 0;
    align-items: stretch;
}

.grid-2col > * {
    border-right: 1px solid #1e4f8f;
}

.grid-2col > *:last-child {
    border-right: 1px solid #1e4f8f;
}

.bullet-list {
    margin: 0;
    padding-left: 0;
    list-style: none;
    font-size: 12px;
    line-height: 1.55;
    color: #1a1a1a;
}

.bullet-list li + li {
    margin-top: 2px;
}

.empty {
    color: #9ca3af;
    font-size: 12px;
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

@media (max-width: 680px) {
    .info-paired-row {
        grid-template-columns: 1fr;
    }

    .info-paired-cell:first-child {
        border-right: none;
        border-bottom: 1px solid #ccc;
    }

    .info-paired-cell-subgroup {
        flex-direction: column;
    }

    .info-subcell + .info-subcell {
        border-left: none;
        border-top: 1px solid #ccc;
    }

    .info-subcell-compact {
        flex-basis: auto;
    }

    .info-paired-row .info-paired-cell:last-child {
        border-bottom: none;
    }

}

@media (max-width: 900px) {
    .dual-section-layout {
        grid-template-columns: 1fr;
    }

    .dual-section-cell-left {
        border-right: none;
    }
}
</style>
