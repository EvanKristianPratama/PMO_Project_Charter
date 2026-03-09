<script setup>
const props = defineProps({
    initiative: { type: Object, required: false, default: () => ({}) },
    form: { type: Object, required: true },
    editable: { type: Boolean, default: false },
    sourceOptions: { type: Array, default: () => [] },
});

const lineItems = (value) => String(value || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

const scoreOptions = [
    { value: 1, label: 'Low' },
    { value: 2, label: 'Medium' },
    { value: 3, label: 'High' },
    { value: 4, label: 'TBC' },
];

const getScoreLabel = (val) => {
    return scoreOptions.find(opt => opt.value === Number(val))?.label || '-';
};

const displayValue = (val) => {
    const trimmed = String(val ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] bg-white text-slate-900 shadow-sm print:shadow-none">
        
        <!-- Header / Title Section -->
        <div class="px-5 pt-5 pb-3 border-b border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-[18px] font-extrabold leading-tight text-slate-900 uppercase tracking-tight">
                        Scope Charter: {{ displayValue(form.usecase) }}
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Score Value</span>
                        <template v-if="editable">
                            <select v-model="form.value" class="score-select bg-emerald-50 text-emerald-700 ring-emerald-200">
                                <option v-for="opt in scoreOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </template>
                        <span v-else class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-300">
                            {{ getScoreLabel(form.value) }}
                        </span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Urgency</span>
                        <template v-if="editable">
                            <select v-model="form.urgency" class="score-select bg-rose-50 text-rose-700 ring-rose-200">
                                <option v-for="opt in scoreOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </template>
                        <span v-else class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 ring-1 ring-rose-300">
                            {{ getScoreLabel(form.urgency) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Bar -->
        <div class="info-bar">
            <div class="info-cell">
                <span class="info-label">Project Owner</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <input v-if="editable" v-model="form.owner" type="text" class="status-select border-none !bg-transparent" placeholder="Input Project Owner..." />
                    <span v-else class="font-bold">{{ displayValue(form.owner) }}</span>
                </span>
            </div>
            <div class="info-cell info-cell-last">
                <span class="info-label info-label-dark">Data Source</span>
                <span class="info-sep"></span>
                <span class="info-value">
                    <select v-if="editable" v-model="form.source_id" class="status-select font-sans">
                        <option value="">- Pilih Source -</option>
                        <option v-for="opt in sourceOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                    </select>
                    <span v-else>{{ sourceOptions.find(o => o.id === form.source_id)?.name || '-' }}</span>
                </span>
            </div>
        </div>

        <!-- Main Content Sections -->
        <div class="charter-section pb-5">
            <div class="bar-main uppercase tracking-wider">Scope Definition</div>
            
            <div class="grid-2col border-b border-[#1e4f8f]">
                <!-- Use Case Name -->
                <article class="panel border-b-0">
                    <div class="bar-sub">Use Case Name / Usecase</div>
                    <div class="panel-body">
                        <input v-if="editable" v-model="form.usecase" type="text" class="field-input" placeholder="Nama Use Case untuk Scope Charter..." />
                        <p v-else class="font-bold text-slate-800">{{ displayValue(form.usecase) }}</p>
                    </div>
                </article>

                <!-- Description -->
                <article class="panel border-b-0">
                    <div class="bar-sub">General Description</div>
                    <div class="panel-body">
                        <textarea v-if="editable" v-model="form.description" class="field-area min-h-[100px]" placeholder="Deskripsi umum inisiatif..."></textarea>
                        <p v-else class="text-slate-700 leading-relaxed">{{ displayValue(form.description) }}</p>
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

.score-select {
    font-size: 11px;
    font-weight: 800;
    padding: 2px 10px;
    border-radius: 9999px;
    border: none;
    outline: none;
    appearance: none;
    cursor: pointer;
    text-align: center;
    min-width: 80px;
}

.status-select {
    font-size: 11px;
    font-weight: 700;
    padding: 0 4px;
    border: 1px dashed #aac4e0;
    background: transparent;
    color: #1e4f8f;
    outline: none;
    width: 100%;
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

.info-cell-last {
    border-right: none;
}

.info-label {
    background: #2563a8;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 10px;
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
    padding: 6px 10px;
    font-size: 11px;
    display: flex;
    align-items: center;
    flex: 1;
    color: #334155;
    font-weight: 600;
    line-height: 1.2;
}

.charter-section {
    padding: 0;
    border-top: 1px solid #ddd;
}

.bar-main {
    background: #1e4f8f;
    color: #fff;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
}

.bar-sub {
    background: #2e6ea2;
    color: #fff;
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
}

.panel {
    border: 1px solid #1e4f8f;
    border-radius: 0;
    background: transparent;
}

.panel-body {
    padding: 12px;
    background: #fff;
    font-size: 12px;
}

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

.field-area {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px;
    resize: vertical;
    background: #fff;
    font-size: 12px;
    line-height: 1.5;
    outline: none;
    font-family: inherit;
    transition: border-color 0.2s;
}

.field-area:focus {
    border-color: #2e6ea2;
}

.field-input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 600;
    background: #fff;
    outline: none;
    font-family: inherit;
}

.field-input:focus {
    border-color: #2e6ea2;
}

@media print {
    @page {
        size: A4 portrait;
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
    .field-area, .field-input, .score-select, .status-select {
        border: none !important;
        appearance: none !important;
    }
}
</style>
