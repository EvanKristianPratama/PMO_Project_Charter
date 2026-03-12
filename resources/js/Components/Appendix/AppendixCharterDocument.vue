<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: { type: Object, required: true },
    form: { type: Object, required: false, default: null },
    editable: { type: Boolean, default: false },
    coeOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
});

// Options for score selects
const scoreOptions = [
    { value: 1, label: 'High' },
    { value: 2, label: 'Medium' },
    { value: 3, label: 'Low' },
    { value: null, label: 'TBC' },
];

// First signer = index 0, rest go to second slot
const signFirst  = computed(() => {
    if (props.editable && props.form) return props.form.sign_by?.[0] ?? '-';
    return props.initiative.sign_by?.[0] ?? '-';
});
const signOthers = computed(() => {
    const source = (props.editable && props.form) ? props.form.sign_by : props.initiative.sign_by;
    const rest = (source ?? []).slice(1);
    return rest.length ? rest.join(', ') : '-';
});

const parseList = (text) => {
    if (!text || text === '-') return [];
    const lines = String(text).split(/\r?\n/).map(l => l.trim()).filter(Boolean);
    const hasBullet = lines.some(l => /^[-•*]/.test(l));
    if (hasBullet) {
        const result = lines.map(l => l.replace(/^[-•*]\s*/, '').trim()).filter(Boolean);
        return result.length > 0 ? result : [];
    }
    return [];
};

// RJPP Tagging Helpers for Edit Mode
const addRjppTagging = (id) => {
    if (!props.form) return;
    const numericId = Number(id);
    if (numericId && !props.form.rjpp_tagging_ids.includes(numericId)) {
        props.form.rjpp_tagging_ids.push(numericId);
    }
};

const removeRjppTagging = (id) => {
    if (!props.form) return;
    props.form.rjpp_tagging_ids = props.form.rjpp_tagging_ids.filter(item => item !== id);
};

const themeLabel = (id) => {
    const theme = props.themeOptions.find(t => Number(t.id) === id);
    if (!theme) return String(id);
    const code = (theme.theme_code ?? theme.code ?? '').replace(/#/g, '');
    return code ? `[${code}] ${theme.name}` : theme.name;
};

const currentRjppThemes = computed(() => {
    if (props.editable && props.form) {
        return props.form.rjpp_tagging_ids.map(id => props.themeOptions.find(t => Number(t.id) === id)).filter(Boolean);
    }
    return props.initiative.rjppThemes ?? [];
});
</script>

<template>
    <div class="charter-wrapper w-full border border-slate-200 bg-white text-slate-800 shadow-sm print:shadow-none"
        style="font-family: 'Segoe UI', sans-serif;">
        <!-- Header Section -->
        <div class="flex flex-wrap items-center justify-between border-b border-slate-200 px-5 py-2">
            <!-- Title Row -->
            <div class="min-w-0 flex-1">
                <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                    <span class="shrink-0 text-[#3b5e96]">Scope Charter Detail</span>
                    <span class="mx-2 shrink-0 text-slate-400">|</span>
                    <input v-if="editable" v-model="form.usecase" type="text" class="header-input w-full max-w-md" placeholder="Input Use Case Name...">
                    <span v-else>{{ initiative.usecase ?? '-' }}</span>
                </h1>
            </div>

            <!-- Score Panel -->
            <div class="score-panel">
                <div class="score-column border-r border-[#1e4f8f]">
                    <div class="bar-sub-mini text-center">Value</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        <select v-if="editable" v-model="form.value" class="score-select">
                            <option v-for="opt in scoreOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <template v-else>{{ initiative.value_label ?? '-' }}</template>
                    </div>
                </div>
                <div class="score-column border-r border-[#1e4f8f]">
                    <div class="bar-sub-mini text-center">Urgency</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        <select v-if="editable" v-model="form.urgency" class="score-select">
                            <option v-for="opt in scoreOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <template v-else>{{ initiative.urgency_label ?? '-' }}</template>
                    </div>
                </div>
                <div class="score-column border-r border-[#1e4f8f]">
                    <div class="bar-sub-mini text-center">Ease</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        <select v-if="editable" v-model="form.ease" class="score-select">
                            <option v-for="opt in scoreOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <template v-else>{{ initiative.ease_label ?? '-' }}</template>
                    </div>
                </div>
                <div class="score-column">
                    <div class="bar-sub-mini text-center">Resource</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        <select v-if="editable" v-model="form.resource" class="score-select">
                            <option v-for="opt in scoreOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                        <template v-else>{{ initiative.resource_label ?? '-' }}</template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metadata Bar -->
    <div class="grid grid-cols-1 overflow-hidden border-x border-b border-[#1e4f8f] lg:grid-cols-4">
        <!-- Project Owner -->
        <div class="flex border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="flex w-28 shrink-0 items-center justify-center bg-[#1e4f8f] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                Project Owner
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] font-semibold text-slate-900">
                <input v-if="editable" v-model="form.owner" type="text" class="meta-input" placeholder="...">
                <template v-else>{{ initiative.owner ?? '-' }}</template>
            </div>
        </div>

        <!-- H/SH -->
        <div class="flex border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                H/SH
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                <input v-if="editable" v-model="form.organization" type="text" class="meta-input" placeholder="...">
                <template v-else>{{ initiative.organization ?? '-' }}</template>
            </div>
        </div>

        <!-- CoE -->
        <div class="flex border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                CoE
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                <select v-if="editable" v-model="form.coe" class="meta-input">
                    <option value="">Pilih CoE...</option>
                    <option v-for="coe in coeOptions" :key="coe.id" :value="coe.name">{{ coe.name }}</option>
                </select>
                <template v-else>{{ initiative.coe ?? '-' }}</template>
            </div>
        </div>

        <!-- Updated -->
        <div class="flex">
            <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                Updated
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                <input v-if="editable" v-model="form.update_doc" type="text" class="meta-input" placeholder="...">
                <template v-else>{{ initiative.update_doc ?? '-' }}</template>
            </div>
        </div>
    </div>

    <!-- Main Content 3-Col -->
    <div class="grid grid-cols-1 border-x border-b border-[#1e4f8f] lg:grid-cols-3">
        <div class="flex flex-col border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Use Case Description
            </div>
            <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                <textarea v-if="editable" v-model="form.description" rows="5" class="content-textarea" placeholder="Input description..."></textarea>
                <template v-else>
                    <ul v-if="parseList(initiative.description).length" class="list-disc pl-5 space-y-1">
                        <li v-for="(item, idx) in parseList(initiative.description)" :key="idx">{{ item }}</li>
                    </ul>
                    <p v-else class="whitespace-pre-line">{{ initiative.description ?? '-' }}</p>
                </template>
            </div>
        </div>

        <div class="flex flex-col border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Current situation/ frictions addressed
            </div>
            <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                <textarea v-if="editable" v-model="form.situation" rows="5" class="content-textarea" placeholder="Input situation..."></textarea>
                <template v-else>
                    <ul v-if="parseList(initiative.situation).length" class="list-disc pl-5 space-y-1">
                        <li v-for="(item, idx) in parseList(initiative.situation)" :key="idx">{{ item }}</li>
                    </ul>
                    <p v-else class="whitespace-pre-line">{{ initiative.situation ?? '-' }}</p>
                </template>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Key functionalities/scope
            </div>
            <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                <textarea v-if="editable" v-model="form.key_functionalities" rows="5" class="content-textarea" placeholder="Input functionalities..."></textarea>
                <template v-else>
                    <ul v-if="parseList(initiative.key_functionalities).length" class="list-disc pl-5 space-y-1">
                        <li v-for="(item, idx) in parseList(initiative.key_functionalities)" :key="idx">{{ item }}</li>
                    </ul>
                    <p v-else class="whitespace-pre-line">{{ initiative.key_functionalities ?? '-' }}</p>
                </template>
            </div>
        </div>
    </div>

    <!-- RJPP Tagging Section -->
    <div class="border-x border-b border-[#1e4f8f]">
        <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
            RJPP Tagging
        </div>
        <div class="bg-white p-2.5">
            <div v-if="editable" class="mb-3 space-y-2">
                <select @change="(e) => { addRjppTagging(e.target.value); e.target.value = ''; }" class="rjpp-select w-full max-w-lg">
                    <option value="">+ Pilih RJPP Tagging...</option>
                    <option v-for="opt in themeOptions" :key="opt.id" :value="opt.id" :disabled="form.rjpp_tagging_ids.includes(Number(opt.id))">
                        {{ opt.theme_code ?? opt.code ?? '-' }} - {{ opt.name }}
                    </option>
                </select>
                <div class="flex flex-wrap gap-2">
                    <span v-for="id in form.rjpp_tagging_ids" :key="id" class="rjpp-tag">
                        {{ themeLabel(id) }}
                        <button type="button" @click="removeRjppTagging(id)" class="ml-1 font-bold hover:text-rose-500">×</button>
                    </span>
                </div>
            </div>

            <div class="table-wrap">
                <table class="initiative-table">
                    <thead>
                        <tr>
                            <th class="w-[45px] text-center">Code</th>
                            <th class="text-center">Strategic Pillar Title</th>
                            <th colspan="2" class="text-center">Themes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!currentRjppThemes || !currentRjppThemes.length">
                            <td class="cell-center h-8 empty-row" colspan="4">Belum ada RJPP tagging.</td>
                        </tr>
                        <tr v-for="theme in currentRjppThemes" :key="theme.id">
                            <td class="cell-center">{{ theme.code ?? '-' }}</td>
                            <td>{{ theme.strategic_pillar ?? '-' }}</td>
                            <td class="cell-center">{{ theme.theme_code ?? '-' }}</td>
                            <td>{{ theme.name ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Grid Row 1: Value / Urgency / Ease -->
    <div class="grid grid-cols-1 border-x border-b border-[#1e4f8f] lg:grid-cols-3">
        <!-- Value Indication -->
        <div class="flex flex-col border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Value Indication</div>
            <div class="flex-1 flex flex-col divide-y divide-[#1e4f8f] bg-white text-[11px] text-slate-600">
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Rationale</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.value_rationale" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.value_rationale).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.value_rationale)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.value_rationale ?? '-' }}</p>
                        </template>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Metrics Impacted</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.value_matrics" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.value_matrics).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.value_matrics)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.value_matrics ?? '-' }}</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Urgency -->
        <div class="flex flex-col border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Urgency</div>
            <div class="flex-1 flex flex-col divide-y divide-[#1e4f8f] bg-white text-[11px] text-slate-600">
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Rationale</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.urgency_rationale" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.urgency_rationale).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.urgency_rationale)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.urgency_rationale ?? '-' }}</p>
                        </template>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center text-[10px] leading-tight">Expected Go-Live</div>
                    <div class="px-2 py-1.5 flex-1">
                        <input v-if="editable" v-model="form.urgency_expected" type="text" class="meta-input" placeholder="...">
                        <template v-else>
                            <ul v-if="parseList(initiative.urgency_expected).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.urgency_expected)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.urgency_expected ?? '-' }}</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ease of Implementation -->
        <div class="flex flex-col">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Ease of Implementation</div>
            <div class="flex-1 flex flex-col divide-y divide-[#1e4f8f] bg-white text-[11px] text-slate-600">
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Rationale</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.ease_rationale" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.ease_rationale).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.ease_rationale)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.ease_rationale ?? '-' }}</p>
                        </template>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Detail</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.ease_detail" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.ease_detail).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.ease_detail)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.ease_detail ?? '-' }}</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grid Row 2: Resource / Interdependency / Sign By -->
    <div class="grid grid-cols-1 border-x border-b border-[#1e4f8f] lg:grid-cols-3">
        <!-- Resource Requirement -->
        <div class="flex flex-col border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Resource Requirement</div>
            <div class="flex-1 flex flex-col divide-y divide-[#1e4f8f] bg-white text-[11px] text-slate-600">
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Rationale</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.resource_rationale" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.resource_rationale).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.resource_rationale)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.resource_rationale ?? '-' }}</p>
                        </template>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Detail</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.resource_detail" rows="3" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.resource_detail).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.resource_detail)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.resource_detail ?? '-' }}</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interdependency -->
        <div class="flex flex-col border-b border-[#1e4f8f] lg:border-b-0 lg:border-r lg:border-r-[#1e4f8f]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Interdependency</div>
            <div class="flex-1 flex flex-col divide-y divide-[#1e4f8f] bg-white text-[11px] text-slate-600">
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Predecessor</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.predecessor" rows="2" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.predecessor).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.predecessor)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.predecessor ?? '-' }}</p>
                        </template>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Successor</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.successor" rows="2" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.successor).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.successor)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.successor ?? '-' }}</p>
                        </template>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center text-[10px] leading-tight">Other BUs Implement</div>
                    <div class="px-2 py-1.5 flex-1">
                        <textarea v-if="editable" v-model="form.otherBU" rows="2" class="content-textarea" placeholder="..."></textarea>
                        <template v-else>
                            <ul v-if="parseList(initiative.otherBU).length" class="list-disc pl-4 space-y-1">
                                <li v-for="(item, idx) in parseList(initiative.otherBU)" :key="idx">{{ item }}</li>
                            </ul>
                            <p v-else class="whitespace-pre-line break-words">{{ initiative.otherBU ?? '-' }}</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sign By -->
        <div class="flex flex-col">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Sign By</div>
            <div class="flex-1 flex flex-col divide-y divide-[#1e4f8f] bg-white text-[11px] text-slate-600">
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Primary</div>
                    <div class="flex-1 flex items-center justify-center p-2">
                        <input v-if="editable" v-model="form.sign_by[0]" type="text" class="meta-input text-center font-semibold" placeholder="Nama Penandatangan 1">
                        <p v-else class="text-center font-semibold text-slate-900">{{ signFirst }}</p>
                    </div>
                </div>
                <div class="flex flex-1">
                    <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">Others</div>
                    <div class="flex-1 flex items-center justify-center p-2">
                        <textarea v-if="editable" v-model="form.sign_others_raw" rows="2" class="content-textarea text-center font-semibold" placeholder="Nama-nama penandatangan lain, pisahkan dengan koma"></textarea>
                        <p v-else class="text-center font-semibold text-slate-900">{{ signOthers }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Edit mode inputs */
.header-input {
    background: transparent;
    border: none;
    border-bottom: 2px solid #3b5e96;
    outline: none;
    color: #0f172a;
    font-weight: 800;
}

.score-select {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
    text-align: center;
    cursor: pointer;
    font-weight: 600;
}

.meta-input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
    font-size: 12px;
}

.content-textarea {
    width: 100%;
    background: transparent;
    border: 1px dashed #cbd5e1;
    padding: 4px;
    font-size: 12px;
    resize: vertical;
    outline: none;
    color: #334155;
}

.content-textarea:focus {
    border-style: solid;
    border-color: #1e4f8f;
}

.rjpp-select {
    background: #fff;
    border: 1px solid #1e4f8f;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 12px;
    outline: none;
}

.rjpp-tag {
    display: inline-flex;
    align-items: center;
    background: #eff6ff;
    color: #1e4f8f;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid #bfdbfe;
}

.score-panel {
    display: flex;
    border: 1px solid #1e4f8f;
    min-width: 320px;
    background: #fff;
}

.score-column {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.bar-sub-mini {
    background: #2e6ea2;
    color: #fff;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
}

.panel-body-mini {
    padding: 6px;
    background: #fff;
    min-height: 32px;
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

/* Print overrides to ensure background colors print */
@media print {
    :deep(*) {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .charter-wrapper {
        max-width: none !important;
        width: 100% !important;
        padding: 0 !important;
    }

    .score-panel {
        border-color: #1e4f8f !important;
    }
}
</style>
