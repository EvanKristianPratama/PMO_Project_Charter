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

const showScope = () => !props.editable && Boolean(String(props.form.scope || '').trim());

const stripBulletPrefix = (value) => String(value || '')
    .replace(/^[\u2022\u2023\u25E6\u2043\u2219â€¢\-\*\u00B7\u2013\u2014]+\s*/u, '')
    .trim();
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

const formatVersionLabel = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? null : trimmed;
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

const displayOwner = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] bg-white text-slate-900 shadow-sm print:shadow-none">

        <!-- Title -->
        <div class="px-5 pt-5 pb-3 border-b border-slate-200">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                    Project Charter:
                    {{ itInitiative.name || '-' }}
                </h1>
                <span v-if="formatVersionLabel(form.version_label || itInitiative.charter?.version_label)"
                    class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-300">
                    {{ formatVersionLabel(form.version_label || itInitiative.charter?.version_label) }}
                </span>
                <span v-if="statusTimeline !== null && statusTimeline !== ''"
                    :class="statusTimelineBadgeClass(statusTimeline)"
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold">
                    {{ statusTimelineLabel(statusTimeline) }}
                </span>
            </div>
            <p class="mt-1 text-[13px] text-slate-600">
                <template v-if="form.objectives">{{ stripBulletPrefix(lineItems(form.objectives)[0] || '') }}</template>
                <template v-else>{{ itInitiative.description || '' }}</template>
            </p>
        </div>

        <!-- Info Bar: Kategori | Durasi | Tanggal Dokumen | Project Owner -->
        <div class="info-bar">
            <div class="info-cell">
                <span class="info-label">Kategori</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <input v-if="editable" v-model="form.category" type="text" class="info-input"
                        placeholder="e.g. Integration and Automation" />
                    <template v-else>{{ form.category || '-' }}</template>
                </span>
            </div>
            <div class="info-cell info-cell-compact">
                <span class="info-label">Durasi</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <input v-if="editable" v-model="form.duration" type="text" class="info-input"
                        placeholder="e.g. 2 tahun (2026-2027)" />
                    <template v-else>{{ form.duration || '-' }}</template>
                </span>
            </div>
            <div class="info-cell info-cell-compact info-cell-date">
                <span class="info-label">Tanggal Dokumen</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <input v-if="editable" v-model="form.tgl_dokumen" type="date" class="info-input" />
                    <template v-else>{{ formatDateLong(form.tgl_dokumen) }}</template>
                </span>
            </div>
            <div class="info-cell info-cell-last">
                <span class="info-label info-label-dark">Project Owner</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <input v-if="editable" v-model="form.owner" type="text" class="info-input"
                        placeholder="Nama project owner" />
                    <template v-else>{{ displayOwner(form.owner) }}</template>
                </span>
            </div>
        </div>

        <!-- Informasi Proyek -->
        <div class="charter-section">
            <div class="bar-main">Informasi proyek</div>

            <div class="grid-2col">
                <!-- Background -->
                <article class="panel">
                    <div class="bar-sub">Latar belakang - Gap/peluang saat ini</div>
                    <div class="panel-body">
                        <textarea v-if="editable" v-model="form.background" class="field-area"
                            placeholder="Satu poin per baris..."></textarea>
                        <ul v-else-if="lineItems(form.background).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.background)" :key="`bg-${idx}`">{{ line }}</li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>

                <!-- Tujuan -->
                <article class="panel">
                    <div class="bar-sub">Tujuan</div>
                    <div class="panel-body">
                        <textarea v-if="editable" v-model="form.objectives" class="field-area"
                            placeholder="Satu poin per baris..."></textarea>
                        <ul v-else-if="lineItems(form.objectives).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.objectives)" :key="`obj-${idx}`">{{ line }}</li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>
            </div>

            <!-- Scope (optional) -->
            <article v-if="showScope()" class="panel mt-0 border-t-0">
                <div class="bar-sub">Scope</div>
                <div class="panel-body">
                    <textarea v-if="editable" v-model="form.scope" class="field-area"
                        placeholder="Satu poin per baris..."></textarea>
                    <ul v-else-if="lineItems(form.scope).length" class="bullet-list">
                        <li v-for="(line, idx) in lineItems(form.scope)" :key="`scope-${idx}`">{{ line }}</li>
                    </ul>
                    <p v-else class="empty">-</p>
                </div>
            </article>
        </div>

        <!-- Dampak & Kebutuhan Sumber Daya -->
        <div class="charter-section grid-2col-impact">
            <!-- Dampak -->
            <article class="panel">
                <div class="bar-main bar-sub-lg">Dampak dan nilai bagi Pertamina</div>
                <div class="panel-body min-h-[140px]">
                    <textarea v-if="editable" v-model="form.impact_value" class="field-area"
                        placeholder="Satu poin per baris..."></textarea>
                    <ul v-else-if="lineItems(form.impact_value).length" class="bullet-list">
                        <li v-for="(line, idx) in lineItems(form.impact_value)" :key="`impact-${idx}`">{{ line }}</li>
                    </ul>
                    <p v-else class="empty">-</p>
                </div>
            </article>

            <!-- Kebutuhan Sumber Daya -->
            <div class="impact-resources">
                <div class="bar-main mb-0">Kebutuhan sumber daya</div>
                <div class="grid-3col">
                    <div class="panel">
                        <div class="bar-sub bar-sub-sm">Personel utama</div>
                        <div class="panel-body panel-body-sm">
                            <textarea v-if="editable" v-model="form.key_personnel" class="field-area field-area-sm"
                                placeholder="Satu poin per baris..."></textarea>
                            <ul v-else-if="lineItems(form.key_personnel).length" class="bullet-list">
                                <li v-for="(line, idx) in lineItems(form.key_personnel)" :key="`kp-${idx}`">{{ line }}
                                </li>
                            </ul>
                            <p v-else class="empty">-</p>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="bar-sub bar-sub-sm">Item utama</div>
                        <div class="panel-body panel-body-sm">
                            <textarea v-if="editable" v-model="form.key_items" class="field-area field-area-sm"
                                placeholder="Satu poin per baris..."></textarea>
                            <ul v-else-if="lineItems(form.key_items).length" class="bullet-list">
                                <li v-for="(line, idx) in lineItems(form.key_items)" :key="`ki-${idx}`">{{ line }}</li>
                            </ul>
                            <p v-else class="empty">-</p>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="bar-sub bar-sub-sm">Indikatif kebutuhan budget</div>
                        <div class="panel-body panel-body-sm">
                            <input v-if="editable" v-model="form.budget" type="text" class="field-input"
                                placeholder="~ 3 - 8 mn USD" />
                            <p v-else class="text-[12px] text-slate-900">{{ form.budget || '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Potensi Risiko -->
        <div class="charter-section">
            <div class="bar-main">Potensi risiko</div>

            <div class="grid-2col">
                <article class="panel">
                    <div class="bar-sub">Risiko teridentifikasi</div>
                    <div class="panel-body min-h-[120px]">
                        <textarea v-if="editable" v-model="form.risks_identified" class="field-area"
                            placeholder="Satu poin per baris..."></textarea>
                        <ul v-else-if="lineItems(form.risks_identified).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.risks_identified)" :key="`risk-${idx}`">{{ line }}
                            </li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>

                <article class="panel">
                    <div class="bar-sub">Mitigasi risiko</div>
                    <div class="panel-body min-h-[120px]">
                        <textarea v-if="editable" v-model="form.risk_mitigation" class="field-area"
                            placeholder="Satu poin per baris..."></textarea>
                        <ul v-else-if="lineItems(form.risk_mitigation).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.risk_mitigation)" :key="`mit-${idx}`">{{ line }}
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

/* --- INFO BAR --- */
.info-bar {
    display: flex;
    border-bottom: 1px solid #ccc;
    background: #f8f8f8;
}

.info-cell {
    display: flex;
    align-items: stretch;
    border-right: 1px solid #ccc;
    flex: 1;
}

.info-cell-compact {
    flex: 0 0 18%;
    min-width: 170px;
}

.info-cell-date {
    flex-basis: 22%;
    min-width: 200px;
}

.info-cell-last {
    border-right: none;
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
}

.info-input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 13px;
    background: transparent;
}

/* --- SECTION --- */
.charter-section {
    padding: 0;
    border-top: 1px solid #ddd;
}

/* --- BARS --- */
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

.bar-sub-lg {
    font-size: 13px;
    font-weight: 700;
    padding: 7px 10px;
}

.bar-sub-sm {
    font-size: 11px;
    padding: 4px 8px;
}

/* --- PANEL --- */
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

.panel-body-sm {
    padding: 7px;
}

/* --- GRIDS --- */
.grid-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-top: 1px solid #1e4f8f;
    gap: 0;
}

.grid-2col>* {
    border-right: 1px solid #1e4f8f;
}

.grid-2col>*:last-child {
    border-right: 1px solid #1e4f8f;
}

.grid-2col-impact {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border-top: 1px solid #1e4f8f;
    align-items: stretch;
}

.grid-2col-impact>*:first-child {
    border-right: 1px solid #1e4f8f;
}

.grid-3col {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 0;
    border-top: none;
}

.grid-3col>* {
    border-right: 1px solid #1e4f8f;
}

.grid-3col>*:last-child {
    border-right: 1px solid #1e4f8f;
}

.impact-resources {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.impact-resources .grid-3col {
    flex: 1;
    align-items: stretch;
}

.impact-resources .grid-3col>.panel {
    display: flex;
    flex-direction: column;
}

.impact-resources .grid-3col .panel-body {
    flex: 1;
}

/* --- CONTENT --- */
.bullet-list {
    margin: 0;
    padding-left: 0;
    list-style: none;
    font-size: 12px;
    line-height: 1.55;
    color: #1a1a1a;
}

.bullet-list li+li {
    margin-top: 2px;
}

.field-area {
    width: 100%;
    min-height: 100px;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px;
    resize: vertical;
    background: #fff;
    font-size: 12px;
    line-height: 1.4;
    outline: none;
    font-family: inherit;
}

.field-area-sm {
    min-height: 60px;
    font-size: 11px;
}

.field-input {
    width: 100%;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 600;
    background: #fff;
    outline: none;
    font-family: inherit;
}

.empty {
    color: #9ca3af;
    font-size: 12px;
}

/* --- PRINT --- */
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
</style>
