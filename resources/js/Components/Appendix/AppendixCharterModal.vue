<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed, watch, ref } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    compendium: { type: Object, default: null },
    appendix: { type: Object, default: null },
    compendiumOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(["close", "success"]);

const toNumber = (value, fallback = null) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
};

const normalizeIdList = (values) => {
    if (!Array.isArray(values)) return [];
    return values
        .map((value) => toNumber(value, 0))
        .filter((value) => value > 0);
};

const appendixForm = useForm({
    // Initiative Fields
    compendium_id: null,
    owner: "",
    coe: "",
    usecase: "",
    description: "",
    source_id: "",
    value: null,
    urgency: null,
    status: 1,
    initiative_ids: [],
    rjpp_tagging_ids: [],

    // Details Fields
    organization: "",
    update_doc: "",
    situation: "",
    key_functionalities: "",
    value_rationale: "",
    value_matrics: "",
    urgency_rationale: "",
    urgency_expected: "",
    ease: null,
    ease_rationale: "",
    ease_detail: "",
    resource: null,
    resource_rationale: "",
    resource_retionale: "",
    predecessor: "",
    successor: "",
    otherBU: "",
    sign_by: [],
});

const signByInput = ref("");
const addSignBy = () => {
    const val = signByInput.value.trim();
    if (val && !appendixForm.sign_by.includes(val)) {
        appendixForm.sign_by.push(val);
    }
    signByInput.value = "";
};
const removeSignBy = (name) => {
    appendixForm.sign_by = appendixForm.sign_by.filter((item) => item !== name);
};

// Sync form when props change or modal opens
watch(
    () => props.show,
    (isShowing) => {
        if (isShowing) {
            // Always reset all fields — we always create a new initiative
            appendixForm.compendium_id = props.compendium?.id ?? null;
            appendixForm.owner = "";
            appendixForm.coe = "";
            appendixForm.usecase = "";
            appendixForm.description = "";
            appendixForm.source_id = "";
            appendixForm.value = null;
            appendixForm.urgency = null;
            appendixForm.status = 1;
            appendixForm.initiative_ids = [];
            appendixForm.rjpp_tagging_ids = [];
            appendixForm.organization = "";
            appendixForm.update_doc = "";
            appendixForm.situation = "";
            appendixForm.key_functionalities = "";
            appendixForm.value_rationale = "";
            appendixForm.value_matrics = "";
            appendixForm.urgency_rationale = "";
            appendixForm.urgency_expected = "";
            appendixForm.ease = null;
            appendixForm.ease_rationale = "";
            appendixForm.ease_detail = "";
            appendixForm.resource = null;
            appendixForm.resource_rationale = "";
            appendixForm.resource_retionale = "";
            appendixForm.predecessor = "";
            appendixForm.successor = "";
            appendixForm.otherBU = "";
            appendixForm.sign_by = [];
            signByInput.value = "";
        }
    },
);

const scoreOptions = [
    { value: 1, label: "High" },
    { value: 2, label: "Medium" },
    { value: 3, label: "Low" },
    { value: null, label: "TBC" },
];

const rjppDisplayLabel = (theme) => {
    if (!theme) return "-";

    const code = String(theme?.code ?? theme?.goal_code ?? "")
        .trim()
        .replace(/#/g, "");
    const goal = String(
        theme?.goal ?? theme?.strategic_pillar ?? theme?.strategic_pillar_title ?? "",
    ).trim();
    const themeNumber = String(theme?.theme_number ?? theme?.theme_code ?? "")
        .trim()
        .replace(/#/g, "");
    const themeName = String(theme?.themes ?? theme?.theme_name ?? theme?.name ?? "")
        .trim();

    const parts = [
        code !== "" ? code : "-",
        goal !== "" ? goal : "-",
        themeNumber !== "" ? themeNumber : "-",
        themeName !== "" ? themeName : "-",
    ];

    return parts.join(" - ");
};

const sourceDisplayLabel = (source) => {
    if (!source) return "-";
    const name = String(source?.name ?? "").trim();
    const month = String(source?.month ?? "").trim();
    const year = String(source?.year ?? "").trim();
    if (name === "") return "-";
    let datePart = "";
    if (month !== "" && year !== "") {
        datePart = ` (${month} ${year})`;
    } else if (year !== "") {
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
    appendixForm.initiative_ids = appendixForm.initiative_ids.filter(
        (item) => item !== id,
    );
};

const addRjpp = (id) => {
    const numericId = toNumber(id, 0);
    if (numericId && !appendixForm.rjpp_tagging_ids.includes(numericId)) {
        appendixForm.rjpp_tagging_ids.push(numericId);
    }
};

const removeRjpp = (id) => {
    appendixForm.rjpp_tagging_ids = appendixForm.rjpp_tagging_ids.filter(
        (item) => item !== id,
    );
};

const stripInitiativePrefix = (name, code) => {
    const rawName = String(name ?? "").trim();
    const rawCode = String(code ?? "").trim().replace(/#/g, "");
    if (!rawName || !rawCode) return rawName;
    const escaped = rawCode.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const pattern = new RegExp(
        `^\\s*(\\[\\s*)?${escaped}(\\s*\\])?\\s*[-.:)]?\\s*`,
        "i",
    );
    const cleaned = rawName.replace(pattern, "").trim();
    return cleaned !== "" ? cleaned : rawName;
};

const initiativeDisplayLabel = (initiative) => {
    if (!initiative) return "-";
    const code = String(initiative?.code ?? "").trim().replace(/#/g, "");
    const name = stripInitiativePrefix(initiative?.name ?? "", code);
    if (code && name) return `[${code}] - ${name}`;
    if (code) return `[${code}]`;
    return name || "-";
};

const getInitiativeLabel = (id) => {
    const selected = props.initiativeOptions.find(
        (item) => toNumber(item.id) === id,
    );
    if (!selected) return String(id);
    return initiativeDisplayLabel(selected) || String(id);
};

const getThemeLabel = (id) => {
    const theme = props.themeOptions.find((item) => toNumber(item.id) === id);
    return rjppDisplayLabel(theme) || String(id);
};

const formatCompendiumLabel = (option) => {
    const text = String(option?.label ?? "").trim();
    return text !== "" ? text : `Compendium #${option?.id ?? "-"}`;
};

const submit = () => {
    appendixForm
        .transform((data) => ({
            ...data,
            initiative_ids: Array.isArray(data.initiative_ids) && data.initiative_ids.length
                ? data.initiative_ids
                : null,
        }))
        .post(
            `/program-planning/program-definition/digital-initiatives/appendix`,
            {
                preserveScroll: true,
                onSuccess: () => {
                    emit("success");
                    emit("close");
                },
                onError: () => {
                    alert("Gagal menyimpan data Appendix. Silakan periksa kembali isian Anda.");
                },
            },
        );
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-4"
        @click.self="$emit('close')"
    >
        <div
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#171717]"
        >
            <div
                class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-white/10"
            >
                <h2
                    class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white"
                >
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

            <form
                class="flex-1 space-y-8 overflow-y-auto px-6 py-6"
                @submit.prevent="submit"
            >
                <!-- SECTION: INITIATIVE INFO -->
                <div
                    class="space-y-4 rounded-xl border border-blue-100 bg-blue-50/30 p-4 dark:border-white/5 dark:bg-white/5"
                >
                    <div
                        class="flex items-center justify-between border-b border-blue-100 pb-2 dark:border-white/5"
                    >
                        <h3
                            class="text-[11px] font-bold uppercase tracking-wider text-[#0f63b5]"
                        >
                            Initiative Information
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="md:col-span-3">
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-[#0f63b5]"
                                >Compendium Selection -
                                <span class="font-normal normal-case italic"
                                    >Opsional</span
                                ></label
                            >
                            <div class="space-y-2">
                                <select
                                    v-model="appendixForm.compendium_id"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option :value="null">
                                        + Pilih Use Case (Compendium)...
                                    </option>
                                    <option
                                        v-for="option in compendiumOptions"
                                        :key="`appendix-sc-opt-${option.id}`"
                                        :value="toNumber(option.id)"
                                    >
                                        {{ formatCompendiumLabel(option) }}
                                    </option>
                                </select>
                                <p v-if="appendixForm.errors.compendium_id" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.compendium_id }}</p>
                                <div
                                    class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5"
                                >
                                    <template v-if="appendixForm.compendium_id">
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                        >
                                            {{
                                                formatCompendiumLabel(
                                                    compendiumOptions.find(
                                                        (opt) =>
                                                            toNumber(opt.id) ===
                                                            appendixForm.compendium_id,
                                                    ),
                                                )
                                            }}
                                            <button
                                                type="button"
                                                class="text-blue-700/70 hover:text-rose-500 dark:text-blue-300/80"
                                                @click="
                                                    appendixForm.compendium_id =
                                                        null
                                                "
                                            >
                                                x
                                            </button>
                                        </span>
                                    </template>
                                    <span
                                        v-else
                                        class="text-[10px] italic text-slate-500 dark:text-slate-400"
                                        >Belum ada compendium dipilih.</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >Master Initiative</label
                            >
                            <div class="space-y-2">
                                <select
                                    @change="(e) => { addInitiative(Number(e.target.value)); e.target.value = ''; }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Initiative...</option>
                                    <option
                                        v-for="opt in initiativeOptions"
                                        :key="`appendix-initiative-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="appendixForm.initiative_ids.includes(toNumber(opt.id, 0))"
                                    >
                                        {{ initiativeDisplayLabel(opt) }}
                                    </option>
                                </select>
                                <div
                                    class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5"
                                >
                                    <template v-if="appendixForm.initiative_ids.length">
                                        <span
                                            v-for="id in appendixForm.initiative_ids"
                                            :key="`appendix-initiative-tag-${id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                        >
                                            {{ getInitiativeLabel(id) }}
                                            <button
                                                type="button"
                                                class="text-blue-700/70 hover:text-rose-500 dark:text-blue-300/80"
                                                @click="removeInitiative(id)"
                                            >
                                                x
                                            </button>
                                        </span>
                                    </template>
                                    <span
                                        v-else
                                        class="text-[10px] italic text-slate-500 dark:text-slate-400"
                                        >Belum ada initiative dipilih.</span
                                    >
                                </div>
                            </div>
                            <p v-if="appendixForm.errors.initiative_ids" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.initiative_ids }}</p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >Project Owner</label
                            >
                            <select
                                v-model="appendixForm.owner"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">Pilih Project Owner...</option>
                                <option
                                    v-for="org in organizationOptions"
                                    :key="`appendix-org-${org.id}`"
                                    :value="org.name"
                                >
                                    {{ org.name }}
                                </option>
                            </select>
                            <p v-if="appendixForm.errors.owner" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.owner }}</p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >PIC</label
                            >
                            <input
                                v-model="appendixForm.organization"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="PIC name..."
                            />
                            <p v-if="appendixForm.errors.organization" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.organization }}</p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >Update Dokumen</label
                            >
                            <input
                                v-model="appendixForm.update_doc"
                                type="date"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            />
                            <p v-if="appendixForm.errors.update_doc" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.update_doc }}</p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >CoE</label
                            >
                            <select
                                v-model="appendixForm.coe"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">Pilih CoE...</option>
                                <option
                                    v-for="coe in coeOptions"
                                    :key="`appendix-coe-${coe.id}`"
                                    :value="coe.name"
                                >
                                    {{ coe.name }}
                                </option>
                            </select>
                            <p v-if="appendixForm.errors.coe" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.coe }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >Source</label
                            >
                            <select
                                v-model="appendixForm.source_id"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">Pilih Source...</option>
                                <option
                                    v-for="source in sourceOptions"
                                    :key="`appendix-source-${source.id}`"
                                    :value="source.id"
                                >
                                    {{ sourceDisplayLabel(source) }}
                                </option>
                            </select>
                            <p v-if="appendixForm.errors.source_id" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.source_id }}</p>
                        </div>

                        <div class="md:col-span-3">
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >Scope Charter Name</label
                            >
                            <input
                                v-model="appendixForm.usecase"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Enter scope charter name..."
                            />
                            <p v-if="appendixForm.errors.usecase" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.usecase }}</p>
                        </div>

                        <div class="md:col-span-3">
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >Description</label
                            >
                            <textarea
                                v-model="appendixForm.description"
                                rows="2"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Scope charter description..."
                            ></textarea>
                            <p v-if="appendixForm.errors.description" class="mt-1 text-[10px] text-rose-500">{{ appendixForm.errors.description }}</p>
                        </div>

                        <div class="md:col-span-3">
                            <label
                                class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-[#0f63b5]"
                                >RJPP Tagging</label
                            >
                            <div class="space-y-2">
                                <select
                                    @change="
                                        (e) => {
                                            addRjpp(e.target.value);
                                            e.target.value = '';
                                        }
                                    "
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">
                                        + Pilih RJPP Tagging...
                                    </option>
                                    <option
                                        v-for="opt in themeOptions"
                                        :key="`appendix-theme-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="
                                            appendixForm.rjpp_tagging_ids.includes(
                                                toNumber(opt.id),
                                            )
                                        "
                                    >
                                        {{ rjppDisplayLabel(opt) }}
                                    </option>
                                </select>
                                <div
                                    class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5"
                                >
                                    <template
                                        v-if="
                                            appendixForm.rjpp_tagging_ids.length
                                        "
                                    >
                                        <span
                                            v-for="id in appendixForm.rjpp_tagging_ids"
                                            :key="`appendix-rjpp-tag-${id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-300"
                                        >
                                            {{ getThemeLabel(id) }}
                                            <button
                                                type="button"
                                                class="text-amber-700/70 hover:text-rose-500 dark:text-amber-300/80"
                                                @click="removeRjpp(id)"
                                            >
                                                x
                                            </button>
                                        </span>
                                    </template>
                                    <span
                                        v-else
                                        class="text-[10px] italic text-slate-500 dark:text-slate-400"
                                        >Belum ada RJPP dipilih.</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: APPENDIX DETAILS -->
                <div class="space-y-4">
                    <div
                        class="flex items-center border-b border-slate-100 pb-2 dark:border-white/5"
                    >
                        <h3
                            class="text-[11px] font-bold uppercase tracking-wider text-slate-500"
                        >
                            Appendix Details
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Row 1: Situation & Functionalities -->
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider"
                                >Current Situation</label
                            >
                            <textarea
                                v-model="appendixForm.situation"
                                rows="3"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Current situation/ frictions addressed..."
                            ></textarea>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider"
                                >Key Functionalities</label
                            >
                            <textarea
                                v-model="appendixForm.key_functionalities"
                                rows="3"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Key functionalities/scope..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Card: Value Indication -->
                    <div
                        class="rounded-lg border border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/[0.02]"
                    >
                        <div
                            class="border-b border-slate-200 px-3 py-1.5 dark:border-white/5"
                        >
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >Value Indication</span
                            >
                        </div>
                        <div class="grid grid-cols-[120px_1fr_1fr] gap-3 p-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Level</label
                                >
                                <select
                                    v-model="appendixForm.value"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option
                                        v-for="opt in scoreOptions"
                                        :key="`appendix-val-${opt.value}`"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Rationale</label
                                >
                                <textarea
                                    v-model="appendixForm.value_rationale"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Rationale..."
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Metrics Impacted</label
                                >
                                <textarea
                                    v-model="appendixForm.value_matrics"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Metrics impacted..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Urgency -->
                    <div
                        class="rounded-lg border border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/[0.02]"
                    >
                        <div
                            class="border-b border-slate-200 px-3 py-1.5 dark:border-white/5"
                        >
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >Urgency</span
                            >
                        </div>
                        <div class="grid grid-cols-[120px_1fr_1fr] gap-3 p-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Level</label
                                >
                                <select
                                    v-model="appendixForm.urgency"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option
                                        v-for="opt in scoreOptions"
                                        :key="`appendix-urg-${opt.value}`"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Rationale</label
                                >
                                <textarea
                                    v-model="appendixForm.urgency_rationale"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Rationale for urgency..."
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Expected Go-Live</label
                                >
                                <textarea
                                    v-model="appendixForm.urgency_expected"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Expected date or timeframe..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Ease of Implementation -->
                    <div
                        class="rounded-lg border border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/[0.02]"
                    >
                        <div
                            class="border-b border-slate-200 px-3 py-1.5 dark:border-white/5"
                        >
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >Ease of Implementation</span
                            >
                        </div>
                        <div class="grid grid-cols-[120px_1fr_1fr] gap-3 p-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Level</label
                                >
                                <select
                                    v-model="appendixForm.ease"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option :value="null">
                                        Pilih Level...
                                    </option>
                                    <option :value="1">High</option>
                                    <option :value="2">Medium</option>
                                    <option :value="3">Low</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Rationale</label
                                >
                                <textarea
                                    v-model="appendixForm.ease_rationale"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Rationale for ease level..."
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Detail</label
                                >
                                <textarea
                                    v-model="appendixForm.ease_detail"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Complexity details..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Resource Requirement -->
                    <div
                        class="rounded-lg border border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/[0.02]"
                    >
                        <div
                            class="border-b border-slate-200 px-3 py-1.5 dark:border-white/5"
                        >
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >Resource Requirement</span
                            >
                        </div>
                        <div class="grid grid-cols-[120px_1fr_1fr] gap-3 p-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Level</label
                                >
                                <select
                                    v-model="appendixForm.resource"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option :value="null">
                                        Pilih Level...
                                    </option>
                                    <option :value="1">High</option>
                                    <option :value="2">Medium</option>
                                    <option :value="3">Low</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Rationale</label
                                >
                                <textarea
                                    v-model="appendixForm.resource_rationale"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Rationale for resource requirement..."
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-semibold text-slate-500 uppercase tracking-wider"
                                    >Detail</label
                                >
                                <textarea
                                    v-model="appendixForm.resource_retionale"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Resource requirement details..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Interdependencies -->
                    <div
                        class="rounded-lg border border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/[0.02]"
                    >
                        <div
                            class="border-b border-slate-200 px-3 py-1.5 dark:border-white/5"
                        >
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                >Interdependencies</span
                            >
                        </div>
                        <div class="grid grid-cols-1 gap-3 p-3 md:grid-cols-3">
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-medium text-slate-500 dark:text-slate-400"
                                    >Predecessor</label
                                >
                                <textarea
                                    v-model="appendixForm.predecessor"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Predecessor..."
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-medium text-slate-500 dark:text-slate-400"
                                    >Successor</label
                                >
                                <textarea
                                    v-model="appendixForm.successor"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Successor..."
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-medium text-slate-500 dark:text-slate-400"
                                    >Other BUs implementing the same use
                                    cases</label
                                >
                                <textarea
                                    v-model="appendixForm.otherBU"
                                    rows="2"
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Other BU..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sign By -->
                    <div>
                        <label
                            class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider"
                            >Sign By</label
                        >
                        <div class="space-y-2">
                            <input
                                v-model="signByInput"
                                type="text"
                                @keydown.enter.prevent="addSignBy"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Ketik nama lalu tekan Enter..."
                            />
                            <div
                                class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5"
                            >
                                <template v-if="appendixForm.sign_by.length">
                                    <span
                                        v-for="(
                                            name, index
                                        ) in appendixForm.sign_by"
                                        :key="`appendix-sign-${index}`"
                                        class="inline-flex items-center gap-2 rounded-full bg-slate-200 px-2.5 py-1 text-[10px] font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-300"
                                    >
                                        {{ name }}
                                        <button
                                            type="button"
                                            class="text-slate-500 hover:text-rose-500 dark:text-slate-400"
                                            @click="removeSignBy(name)"
                                        >
                                            x
                                        </button>
                                    </span>
                                </template>
                                <span
                                    v-else
                                    class="text-[10px] italic text-slate-500 dark:text-slate-400"
                                    >Belum ada penanda tangan.</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-2 border-t border-slate-200 pt-5 dark:border-white/10"
                >
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
                        {{
                            appendixForm.processing
                                ? "Menyimpan..."
                                : "Simpan Appendix"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
