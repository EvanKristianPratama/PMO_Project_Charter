<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    itInitiative: { type: Object, required: true },
    form: { type: Object, required: true },
    editable: { type: Boolean, default: false },
    statusTimeline: { type: [String, Number], default: null },
    allOrganizations: { type: Array, default: () => [] },
});

const filteredOrgs = computed(() => {
    return props.allOrganizations.filter(org => org.jabatan && String(org.jabatan).trim() !== '');
});

const selectedNewCrossFunctionId = ref('');

const addCrossFunctionUnit = () => {
    if (!selectedNewCrossFunctionId.value) return;

    if (!Array.isArray(props.form.pic_cross_function_ids)) {
        props.form.pic_cross_function_ids = [];
    }

    const id = parseInt(selectedNewCrossFunctionId.value, 10);
    if (!props.form.pic_cross_function_ids.includes(id)) {
        props.form.pic_cross_function_ids.push(id);
    }

    selectedNewCrossFunctionId.value = '';
};

const removeCrossFunctionUnit = (id) => {
    props.form.pic_cross_function_ids = props.form.pic_cross_function_ids.filter(
        (existingId) => parseInt(existingId, 10) !== parseInt(id, 10)
    );
};

const getOrgName = (id) => {
    const org = props.allOrganizations.find(o => o.id === parseInt(id));
    return org ? org.jabatan : null;
};

const getOrgNames = (ids) => {
    if (!Array.isArray(ids)) return [];

    return ids
        .map(id => getOrgName(id))
        .filter(Boolean);
};

const lineItems = (value) => String(value || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

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
                    <div v-if="editable" class="flex flex-col gap-1 w-full">
                        <input v-model="form.owner" type="text" class="info-input"
                            placeholder="Nama project owner (manual)" />
                        <select
                            v-model="form.pic_owner_id"
                            class="info-select"
                        >
                            <option value="">-- Pilih Unit Owner --</option>
                            <option v-for="org in filteredOrgs" :key="org.id" :value="org.id">
                                {{ org.jabatan }}
                            </option>
                        </select>
                    </div>
                    <template v-else>
                        <div class="flex flex-col">
                            <span>{{ displayOwner(form.owner) }}</span>
                            <span v-if="getOrgName(form.pic_owner_id)" class="text-[10px] text-slate-500 font-semibold italic">
                                Organization: {{ getOrgName(form.pic_owner_id) }}
                            </span>
                        </div>
                    </template>
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

                            <div class="mt-3">
                                <label class="text-[10px] font-bold text-slate-500 uppercase">Pemetaan organisasi formal:</label>
                                <div v-if="editable" class="mt-1 flex flex-col gap-2">
                                    <div class="flex gap-1">
                                        <select v-model="selectedNewCrossFunctionId" class="info-select flex-1">
                                            <option value="">-- Pilih Unit untuk Ditambahkan --</option>
                                            <option v-for="org in filteredOrgs" :key="org.id" :value="org.id"
                                                :disabled="form.pic_cross_function_ids?.includes(org.id)">
                                                {{ org.jabatan }}
                                            </option>
                                        </select>
                                        <button type="button" @click="addCrossFunctionUnit"
                                            class="px-2 py-1 bg-blue-600 text-white text-[10px] font-bold rounded hover:bg-blue-700 transition-colors">
                                            Add
                                        </button>
                                    </div>

                                    <div v-if="form.pic_cross_function_ids?.length" class="space-y-1">
                                        <div v-for="id in form.pic_cross_function_ids" :key="id"
                                            class="flex items-center justify-between gap-2 px-2 py-1 bg-slate-50 border border-slate-100 rounded">
                                            <span class="text-[11px] text-slate-700 font-medium">{{ getOrgName(id) }}</span>
                                            <button type="button" @click="removeCrossFunctionUnit(id)"
                                                class="text-rose-500 hover:text-rose-700 p-0.5" title="Hapus Unit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p v-else class="text-[9px] text-slate-400 italic">Belum ada unit terpilih.</p>
                                </div>
                                <template v-else>
                                    <div v-if="getOrgNames(form.pic_cross_function_ids).length" class="mt-2 pt-2 border-t border-slate-100">
                                        <ul class="list-disc list-inside text-[11px] text-slate-500 font-semibold italic">
                                            <li v-for="name in getOrgNames(form.pic_cross_function_ids)" :key="name">
                                                {{ name }}
                                            </li>
                                        </ul>
                                    </div>
                                </template>
                            </div>
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
                    <div class="bar-sub">Resiko teridentifikasi</div>
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

.info-select {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 11px;
    background: #fff;
    outline: none;
    color: #444;
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
