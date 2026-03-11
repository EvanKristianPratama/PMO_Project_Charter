<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    compendium: { type: Object, default: null },
    appendix: { type: Object, default: null },
    compendiumOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'success']);

const toNumber = (value, fallback = null) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
};

const normalizeIdList = (values) => {
    if (!Array.isArray(values)) return [];
    return values.map((value) => toNumber(value, 0)).filter((value) => value > 0);
};

const appendixForm = useForm({
    // Initiative Fields
    owner: '',
    coe: '',
    usecase: '',
    description: '',
    source_id: '',
    value: null,
    urgency: null,
    status: 1,
    initiative_ids: [],
    rjpp_tagging_ids: [],

    // Appendix Fields
    sc_id: [],
    current_situation: '',
    key_functionalities: '',
    value_rationale: '',
    value_matrics: '',
    value_detail: '',
    urgency_detail: '',
    ease_implementation: null,
    ease_detail: '',
    resource_requirement: null,
    resource_detail: '',
    interpendencies: '',
    sign_by: '',
});

// Sync form when props change or modal opens
watch(() => props.show, (isShowing) => {
    if (isShowing) {
        appendixForm.owner = '';
        appendixForm.coe = '';
        appendixForm.usecase = '';
        appendixForm.description = '';
        appendixForm.source_id = '';
        appendixForm.value = null;
        appendixForm.urgency = null;
        appendixForm.status = 1;
        appendixForm.initiative_ids = [];
        appendixForm.rjpp_tagging_ids = [];

        appendixForm.sc_id = props.appendix?.sc_id ? [props.appendix.sc_id] : [];
        appendixForm.current_situation = props.appendix?.current_situation ?? '';
        appendixForm.key_functionalities = props.appendix?.key_functionalities ?? '';
        appendixForm.value_rationale = props.appendix?.value_rationale ?? '';
        appendixForm.value_matrics = props.appendix?.value_matrics ?? '';
        appendixForm.value_detail = props.appendix?.value_detail ?? '';
        appendixForm.urgency_detail = props.appendix?.urgency_detail ?? '';
        appendixForm.ease_implementation = props.appendix?.ease_implementation ?? null;
        appendixForm.ease_detail = props.appendix?.ease_detail ?? '';
        appendixForm.resource_requirement = props.appendix?.resource_requirement ?? null;
        appendixForm.resource_detail = props.appendix?.resource_detail ?? '';
        appendixForm.interpendencies = props.appendix?.interpendencies ?? '';
        appendixForm.sign_by = props.appendix?.sign_by ?? '';
    }
});

const scoreOptions = [
    { value: 1, label: 'High' },
    { value: 2, label: 'Medium' },
    { value: 3, label: 'Low' },
    { value: null, label: 'TBC' },
];

const themeDisplayLabel = (theme) => {
    if (!theme) return '-';
    const strategicPillar = String(theme?.strategic_pillar_title ?? theme?.strategic_pillar ?? '').trim();
    const themeNumber = String(theme?.theme_number ?? theme?.code ?? '').trim().replace(/#/g, '');
    const themeName = String(theme?.theme_name ?? theme?.name ?? '').trim();
    if (strategicPillar === '' && themeNumber === '') return themeName || '-';
    const prefix = strategicPillar !== '' ? `[${strategicPillar}]` : '';
    const number = themeNumber !== '' ? ` ${themeNumber}` : '';
    const suffix = themeName !== '' ? ` - ${themeName}` : '';
    return `${prefix}${number}${suffix}`.trim() || '-';
};

const sourceDisplayLabel = (source) => {
    if (!source) return '-';
    const name = String(source?.name ?? '').trim();
    const month = String(source?.month ?? '').trim();
    const year = String(source?.year ?? '').trim();
    if (name === '') return '-';
    let datePart = '';
    if (month !== '' && year !== '') {
        datePart = ` (${month} ${year})`;
    } else if (year !== '') {
        datePart = ` (${year})`;
    }
    return `${name}${datePart}`;
};

const addInitiative = (id) => {
    const numericId = toNumber(id, 0);
    if (numericId && !appendixForm.initiative_ids.includes(numericId)) {
        appendixForm.initiative_ids.push(numericId);
    }
};

const removeInitiative = (id) => {
    appendixForm.initiative_ids = appendixForm.initiative_ids.filter((item) => item !== id);
};

const addRjpp = (id) => {
    const numericId = toNumber(id, 0);
    if (numericId && !appendixForm.rjpp_tagging_ids.includes(numericId)) {
        appendixForm.rjpp_tagging_ids.push(numericId);
    }
};

const removeRjpp = (id) => {
    appendixForm.rjpp_tagging_ids = appendixForm.rjpp_tagging_ids.filter((item) => item !== id);
};

const getInitiativeLabel = (id) => {
    const selected = props.initiativeOptions.find((item) => toNumber(item.id) === id);
    if (!selected) return String(id);
    return selected.code ? `[${String(selected.code).replace(/#/g, '')}] ${selected.name}` : selected.name;
};

const getThemeLabel = (id) => {
    const theme = props.themeOptions.find((item) => toNumber(item.id) === id);
    return themeDisplayLabel(theme) || String(id);
};

const formatCompendiumLabel = (option) => {
    const text = String(option?.label ?? '').trim();
    return text !== '' ? text : `Compendium #${option?.id ?? '-'}`;
};

const submit = () => {
    appendixForm.post(`/program-planning/program-definition/digital-initiatives/compendium/${props.compendium?.id}/appendix`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('success');
            emit('close');
        },
    });
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-4"
        @click.self="$emit('close')"
    >
        <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#171717]">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-white/10">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">
                    Appendix Details
                </h2>
                <button
                    type="button"
                    class="rounded-md px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-slate-200"
                    @click="$emit('close')"
                >
                    Close
                </button>
            </div>

            <form class="flex-1 space-y-8 overflow-y-auto px-6 py-6" @submit.prevent="submit">
                <!-- SECTION: INITIATIVE INFO -->
                <div class="space-y-4 rounded-xl border border-blue-100 bg-blue-50/30 p-4 dark:border-white/5 dark:bg-white/5">
                    <div class="flex items-center justify-between border-b border-blue-100 pb-2 dark:border-white/5">
                         <h3 class="text-[11px] font-bold uppercase tracking-wider text-[#0f63b5]">Initiative Information</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                         <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-[#0f63b5]">Compendium Selection - <span class="font-normal normal-case italic">Opsional</span></label>
                            <div class="space-y-2">
                                <select
                                    @change="(e) => { 
                                        const val = toNumber(e.target.value);
                                        if (val && !(appendixForm.sc_id || []).includes(val)) {
                                            if (!Array.isArray(appendixForm.sc_id)) appendixForm.sc_id = [];
                                            appendixForm.sc_id.push(val);
                                        }
                                        e.target.value = '';
                                    }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Initiative (Compendium)...</option>
                                    <option
                                        v-for="option in compendiumOptions"
                                        :key="`appendix-sc-opt-${option.id}`"
                                        :value="option.id"
                                        :disabled="(appendixForm.sc_id || []).includes(toNumber(option.id))"
                                    >
                                        {{ formatCompendiumLabel(option) }}
                                    </option>
                                </select>
                                <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                    <template v-if="(appendixForm.sc_id || []).length">
                                        <span
                                            v-for="id in (appendixForm.sc_id || [])"
                                            :key="`appendix-sc-tag-${id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                        >
                                            {{ formatCompendiumLabel(compendiumOptions.find(opt => toNumber(opt.id) === id)) }}
                                            <button type="button" class="text-blue-700/70 hover:text-rose-500 dark:text-blue-300/80" @click="appendixForm.sc_id = appendixForm.sc_id.filter(item => item !== id)">x</button>
                                        </span>
                                    </template>
                                    <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada initiative dipilih.</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Project Owner</label>
                            <input v-model="appendixForm.owner" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Project owner name...">
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">CoE</label>
                            <select v-model="appendixForm.coe" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                                <option value="">Pilih CoE...</option>
                                <option v-for="coe in coeOptions" :key="`appendix-coe-${coe.id}`" :value="coe.name">{{ coe.name }}</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Use Case Name</label>
                            <input v-model="appendixForm.usecase" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Enter use case name...">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Description</label>
                            <textarea v-model="appendixForm.description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Initiative description..."></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Source</label>
                            <select
                                v-model="appendixForm.source_id"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">Pilih Source...</option>
                                <option v-for="source in sourceOptions" :key="`appendix-source-${source.id}`" :value="source.id">
                                    {{ sourceDisplayLabel(source) }}
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-[#0f63b5]">RJPP Tagging</label>
                            <div class="space-y-2">
                                <select
                                    @change="(e) => { addRjpp(e.target.value); e.target.value = ''; }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih RJPP Tagging...</option>
                                    <option
                                        v-for="opt in themeOptions"
                                        :key="`appendix-theme-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="appendixForm.rjpp_tagging_ids.includes(toNumber(opt.id))"
                                    >
                                        {{ themeDisplayLabel(opt) }}
                                    </option>
                                </select>
                                <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                    <template v-if="appendixForm.rjpp_tagging_ids.length">
                                        <span
                                            v-for="id in appendixForm.rjpp_tagging_ids"
                                            :key="`appendix-rjpp-tag-${id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-300"
                                        >
                                            {{ getThemeLabel(id) }}
                                            <button type="button" class="text-amber-700/70 hover:text-rose-500 dark:text-amber-300/80" @click="removeRjpp(id)">x</button>
                                        </span>
                                    </template>
                                    <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada RJPP dipilih.</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Value</label>
                            <select v-model="appendixForm.value" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                                <option v-for="opt in scoreOptions" :key="`appendix-val-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Urgency</label>
                            <select v-model="appendixForm.urgency" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                                <option v-for="opt in scoreOptions" :key="`appendix-urg-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION: APPENDIX DETAILS -->
                <div class="space-y-4">
                    <div class="flex items-center border-b border-slate-100 pb-2 dark:border-white/5">
                         <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Appendix Details</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Row 1: Situation & Functionalities -->
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Current Situation</label>
                                <textarea v-model="appendixForm.current_situation" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Current situation/ frictions addressed..."></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Key Functionalities</label>
                                <textarea v-model="appendixForm.key_functionalities" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Key functionalities/scope..."></textarea>
                            </div>

                            <!-- Row 2: Value Rationale & Value Metrics -->
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Value Rationale</label>
                                <textarea v-model="appendixForm.value_rationale" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Rationale for the value indication..."></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Value Metrics</label>
                                <textarea v-model="appendixForm.value_matrics" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Metrics impacted..."></textarea>
                            </div>

                            <!-- Row 3: Urgency Rationale & (empty space for expected go-live) -->
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Urgency Rationale</label>
                                <textarea v-model="appendixForm.urgency_detail" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Rationale for urgency..."></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Expected Go-Live</label>
                                <input v-model="appendixForm.value_detail" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Expected date or timeframe...">
                            </div>

                            <!-- Row 4: Ease Level & Ease Detail -->
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Ease Level</label>
                                <select v-model="appendixForm.ease_implementation" class="w-full mb-3 rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                                    <option :value="null">Pilih Level...</option>
                                    <option :value="1">High</option>
                                    <option :value="2">Medium</option>
                                    <option :value="3">Low</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Ease Detail / Notes</label>
                                <textarea v-model="appendixForm.ease_detail" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Complexity details..."></textarea>
                            </div>

                            <!-- Row 5: Resource Req. & Resource Detail -->
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Resource Req.</label>
                                <select v-model="appendixForm.resource_requirement" class="w-full mb-3 rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                                    <option :value="null">Pilih Level...</option>
                                    <option :value="1">High</option>
                                    <option :value="2">Medium</option>
                                    <option :value="3">Low</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Resource Detail</label>
                                <textarea v-model="appendixForm.resource_detail" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Resource requirement details..."></textarea>
                            </div>

                            <!-- Row 6: Interdependencies -->
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Interdependencies</label>
                                <textarea v-model="appendixForm.interpendencies" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Predecessor, Successor, etc..."></textarea>
                            </div>
                        </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Sign By</label>
                        <input v-model="appendixForm.sign_by" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100" placeholder="Signatory name...">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-5 dark:border-white/10">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/10"
                        @click="$emit('close')"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="appendixForm.processing"
                        class="rounded-lg bg-[#0f63b5] px-6 py-2 text-xs font-bold text-white shadow-md transition-all active:scale-95 hover:bg-[#0c4e8f] disabled:opacity-50"
                    >
                        {{ appendixForm.processing ? 'Menyimpan...' : 'Simpan Appendix' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
