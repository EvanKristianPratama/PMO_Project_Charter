<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: { type: Object, required: true },
    editable: { type: Boolean, default: false },
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
});

const scoreOptions = [
    { value: 1, label: 'High' },
    { value: 2, label: 'Medium' },
    { value: 3, label: 'Low' },
    { value: null, label: 'TBC' },
];

const displayValue = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};

const getScoreLabel = (value) => {
    if (value === null || value === undefined || value === '') return 'TBC';
    return scoreOptions.find((option) => option.value === Number(value))?.label ?? 'TBC';
};

const toNumber = (value) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : 0;
};

const normalizedThemeIds = computed(() => {
    if (!Array.isArray(props.form.rjpp_tagging_ids)) return [];

    return props.form.rjpp_tagging_ids
        .map((value) => toNumber(value))
        .filter((value) => value > 0);
});

const themeMap = computed(() => {
    return new Map((props.themeOptions ?? []).map((option) => [toNumber(option.id), option]));
});

const selectedThemes = computed(() => {
    const list = normalizedThemeIds.value.map((id) => {
        const option = themeMap.value.get(id);

        return {
            id,
            code: String(option?.code ?? '-').replace(/#/g, ''),
            strategicPillar: option?.strategic_pillar ?? '-',
            themeCode: String(option?.theme_code ?? '-').replace(/#/g, ''),
            name: option?.name ?? `Theme ${id}`,
        };
    });

    const result = [];
    for (let i = 0; i < list.length; i++) {
        const current = list[i];
        let span = 1;
        
        let nextIdx = i + 1;
        while (nextIdx < list.length && 
               list[nextIdx].code === current.code && 
               list[nextIdx].strategicPillar === current.strategicPillar) {
            span++;
            nextIdx++;
        }

        result.push({ ...current, rowSpan: span });

        for (let k = 1; k < span; k++) {
            result.push({ ...list[i + k], rowSpan: 0 });
        }

        i = nextIdx - 1;
    }
    return result;
});

const coeCoverageLabel = computed(() => {
    return displayValue(props.form.coe);
});

const themeOptionLabel = (option) => {
    if (!option) return '-';

    const strategicPillar = String(option?.strategic_pillar_title ?? option?.strategic_pillar ?? '').trim();
    const themeNumber = String(option?.theme_number ?? option?.code ?? '').trim().replace(/#/g, '');
    const themeName = String(option?.theme_name ?? option?.name ?? '').trim();

    if (strategicPillar === '' && themeNumber === '') {
        return themeName || '-';
    }

    const prefix = strategicPillar !== '' ? `[${strategicPillar}]` : '';
    const number = themeNumber !== '' ? ` ${themeNumber}` : '';
    const suffix = themeName !== '' ? ` - ${themeName}` : '';

    return `${prefix}${number}${suffix}`.trim() || '-';
};

const addTheme = (value) => {
    const id = toNumber(value);
    if (!id) return;

    if (!Array.isArray(props.form.rjpp_tagging_ids)) {
        props.form.rjpp_tagging_ids = [];
    }

    if (!props.form.rjpp_tagging_ids.includes(id)) {
        props.form.rjpp_tagging_ids.push(id);
    }
};

const removeTheme = (id) => {
    props.form.rjpp_tagging_ids = normalizedThemeIds.value.filter((item) => item !== toNumber(id));
};

const onThemeSelect = (event) => {
    addTheme(event.target.value);
    event.target.value = '';
};

const sourceOptionLabel = (option) => {
    if (!option) return '-';

    const name = String(option?.name ?? '').trim();
    const month = String(option?.month ?? '').trim();
    const year = String(option?.year ?? '').trim();

    if (name === '') return '-';

    let datePart = '';
    if (month !== '' && year !== '') {
        datePart = ` (${month} ${year})`;
    } else if (year !== '') {
        datePart = ` (${year})`;
    }

    return `${name}${datePart}`;
};

const sourceLabel = computed(() => {
    const option = props.sourceOptions.find((o) => toNumber(o.id) === toNumber(props.form.source_id));
    return sourceOptionLabel(option);
});
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] bg-white text-slate-900 shadow-sm print:shadow-none">
        <div class="border-b border-slate-200 px-5 pb-3 pt-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                            <span class="shrink-0 text-[#3b5e96]">Use Case</span>
                            <span class="mx-2 shrink-0 text-slate-400">|</span>
                            <span class="">{{ displayValue(form.usecase) }}</span>
                        </h1>
                    </div>
                    <p class="mt-1 text-[13px] text-slate-600">
                        {{ displayValue(form.description) }}
                    </p>
                </div>

                <div class="score-panel">
                    <div class="score-column border-r border-[#1e4f8f]">
                        <div class="bar-sub text-center !py-1">Value</div>
                        <div class="panel-body text-center !py-1.5 !px-2.5">
                            <template v-if="editable">
                                <select v-model="form.value" class="score-input-simple text-center">
                                    <option v-for="option in scoreOptions" :key="option.value" :value="option.value">{{
                                        option.label }}</option>
                                </select>
                            </template>
                            <div v-else class="text-[13px] text-slate-900">{{ getScoreLabel(form.value) }}</div>
                        </div>
                    </div>
                    <div class="score-column">
                        <div class="bar-sub text-center !py-1">Urgency</div>
                        <div class="panel-body text-center !py-1.5 !px-2.5">
                            <template v-if="editable">
                                <select v-model="form.urgency" class="score-input-simple text-center">
                                    <option v-for="option in scoreOptions" :key="option.value" :value="option.value">{{
                                        option.label }}</option>
                                </select>
                            </template>
                            <div v-else class="text-[13px] text-slate-900">{{ getScoreLabel(form.urgency) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-bar">
            <div class="info-cell info-cell-compact">
                <span class="info-label info-label">Project Owner</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <input v-if="editable" v-model="form.owner" type="text" class="info-input"
                        placeholder="Nama project owner">
                    <template v-else>{{ displayValue(form.owner) }}</template>
                </span>
            </div>
            <div class="info-cell info-cell-coe">
                <span class="info-label">CoE</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <select v-if="editable" v-model="form.coe" class="info-input">
                        <option value="">- Pilih CoE -</option>
                        <option v-for="option in coeOptions" :key="option.id" :value="option.name">{{ option.name }}
                        </option>
                    </select>
                    <template v-else>{{ coeCoverageLabel }}</template>
                </span>
            </div>
            <div class="info-cell info-cell-last">
                <span class="info-label">Data Source</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <select v-if="editable" v-model="form.source_id" class="info-input">
                        <option value="">- Pilih Source -</option>
                        <option v-for="option in sourceOptions" :key="option.id" :value="option.id">{{
                            sourceOptionLabel(option)
                        }}</option>
                    </select>
                    <template v-else>{{ sourceLabel }}</template>
                </span>
            </div>
        </div>

        <div class="charter-section">
            <article class="panel border-t-0">
                <div class="bar-sub">RJPP Tagging</div>
                <div class="panel-body space-y-3">
                    <div v-if="editable" class="max-w-xl">
                        <select @change="onThemeSelect" class="field-select">
                            <option value="">+ Pilih RJPP Tagging...</option>
                            <option v-for="option in themeOptions" :key="option.id" :value="option.id"
                                :disabled="normalizedThemeIds.includes(toNumber(option.id))">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div class="table-wrap">
                        <table class="initiative-table">
                            <thead>
                                <tr>
                                    <th class="w-[45px] text-center">Code</th>
                                    <th class="text-center">Strategic Pillar Title</th>
                                    <th colspan="2" class="text-center">Themes</th>
                                    <th v-if="editable" class="w-[80px] text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!selectedThemes.length">
                                    <td :colspan="editable ? 5 : 4" class="empty-row text-center">Belum ada RJPP tagging yang dimapping.</td>
                                </tr>
                                <tr v-for="(theme, index) in selectedThemes" :key="`compendium-theme-${index}`">
                                    <td v-if="theme.rowSpan > 0" :rowspan="theme.rowSpan" class="cell-center">{{ theme.code }}</td>
                                    <td v-if="theme.rowSpan > 0" :rowspan="theme.rowSpan">{{ theme.strategicPillar }}</td>
                                    <td class="cell-center">{{ theme.themeCode }}</td>
                                    <td>{{ theme.name }}</td>
                                    <td v-if="editable" class="cell-center">
                                        <button type="button" class="text-red-600 hover:text-red-800 font-bold"
                                            @click="removeTheme(theme.id)">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
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

.title-input {
    min-width: min(520px, 100%);
    border: none;
    border-bottom: 1px solid #1e4f8f;
    background: transparent;
    font: inherit;
    font-weight: 800;
    color: #0f172a;
    outline: none;
    padding: 0 0 2px;
}

.score-panel {
    display: flex;
    border: 1px solid #1e4f8f;
    min-width: 160px;
    background: #fff;
}

.score-column {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.score-input-simple {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 13px;
    outline: none;
    cursor: pointer;
    appearance: none;
    padding: 0;
}

.score-input {
    padding: 0;
    appearance: none;
    cursor: pointer;
}

.score-input-emerald {
    color: #047857;
}

.score-input-rose {
    color: #be123c;
}

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
    flex: 0.45;
}

.info-cell-coe {
    flex: 0.34;
}

.info-cell-last {
    border-right: none;
    flex: 0.8;
}

.info-label {
    background: #2563a8;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    flex-shrink: 0;
}

.info-label-dark {
    background: #2e6ea2;
}

.info-sep {
    width: 0;
    border-right: 1px solid #aac4e0;
}

.info-value {
    padding: 6px 10px;
    font-size: 13px;
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.info-input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 13px;
    background: transparent;
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

.field-select {
    width: 100%;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px;
    background: #fff;
    font-size: 12px;
    outline: none;
    font-family: inherit;
}

.table-wrap {
    overflow-x: auto;
    border: 1px solid #cbd5e1;
}

.initiative-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.initiative-table th,
.initiative-table td {
    border: 1px solid #cbd5e1;
    padding: 8px 10px;
    vertical-align: middle;
}

.initiative-table th {
    background: #eff6ff;
    color: #1e3a8a;
    font-size: 11px;
    font-weight: 700;
    text-align: left;
}

.cell-center {
    text-align: center;
    white-space: nowrap;
}

.empty-row {
    text-align: center;
    color: #94a3b8;
    font-style: italic;
}

.tag-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: flex-start;
}

.tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #1e4f8f;
    background: #eff6ff;
    color: #1e3a8a;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 600;
}

.tag-remove {
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    font-size: 12px;
    line-height: 1;
    padding: 0;
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

    .table-wrap {
        overflow: visible;
    }

    .title-input,
    .info-input,
    .field-select,
    .score-input {
        border: none !important;
        appearance: none !important;
        background: transparent !important;
    }
}

@media (max-width: 768px) {
    .score-grid {
        grid-template-columns: 1fr;
        min-width: 100%;
    }

    .info-bar {
        flex-direction: column;
    }

    .info-cell {
        border-right: none;
        border-bottom: 1px solid #ccc;
    }

    .info-cell-last {
        border-bottom: none;
    }
}
</style>
