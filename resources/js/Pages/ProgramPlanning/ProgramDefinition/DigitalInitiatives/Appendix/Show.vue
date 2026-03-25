<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <Link
                        :href="route('program-planning.program-definition.digital-initiatives.appendix.index')"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>

                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10" />

                    <label for="appendix-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih Use Case</label>
                    <select
                        id="appendix-nav"
                        v-model="selectedAppendixId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="" disabled>-- Pilih Use Case --</option>
                        <option v-for="option in appendixOptions" :key="`appendix-opt-${option.id}`" :value="String(option.id)">
                            {{ formatAppendixLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            v-if="canStartEdit"
                            type="button"
                            @click="startEdit"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-700 transition-all hover:bg-amber-100"
                        >
                            Edit Scope Charter
                        </button>

                        <button
                            v-if="showCancelEdit"
                            type="button"
                            @click="cancelEdit"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-transparent dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            Batal
                        </button>

                        <button
                            v-if="canSubmit"
                            type="button"
                            @click="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#0f63b5] px-6 py-2 text-xs font-bold text-white shadow-md transition-all active:scale-95 hover:bg-[#0c4e8f] disabled:opacity-50"
                        >
                            {{ submitLabel }}
                        </button>
                    </div>
                </div>
            </section>

            <section
                v-if="isEditing"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <div class="mb-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Master Initiative Mapping</h4>
                    <p class="mt-1 text-xs text-slate-500">Pilih atau sesuaikan inisiatif yang terhubung ke Appendix.</p>
                </div>

                <div class="space-y-4">
                    <div class="max-w-xl">
                        <select
                            @change="onSelectInitiative"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                        >
                            <option value="">+ Pilih Initiative untuk dimapping...</option>
                            <option
                                v-for="opt in initiativeOptions"
                                :key="`appendix-initiative-opt-${opt.id}`"
                                :value="opt.id"
                                :disabled="form.initiative_ids.includes(Number(opt.id))"
                            >
                                {{ opt.code ? `[${String(opt.code).replace(/#/g, '')}] ` : '' }}{{ opt.name }}
                            </option>
                        </select>
                    </div>

                    <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                        <div class="flex flex-wrap gap-2">
                            <div
                                v-for="item in selectedInitiatives"
                                :key="`appendix-tag-${item.id}`"
                                class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                            >
                                <span class="max-w-[280px] truncate">{{ initiativeTagLabel(item).replace(/#/g, '') }}</span>
                                <button
                                    type="button"
                                    @click="removeInitiative(item.id)"
                                    class="text-slate-400 transition-colors hover:text-rose-500"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <p v-if="selectedInitiatives.length === 0" class="flex items-center text-xs italic text-slate-400">
                            Belum ada inisiatif yang dipilih.
                        </p>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-100 pt-6 dark:border-white/5">
                    <div class="mb-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Compendium Mapping</h4>
                        <p class="mt-1 text-xs text-slate-500">Pilih compendium yang terhubung ke Appendix.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="max-w-xl">
                            <select
                                @change="onSelectCompendium"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">+ Pilih Compendium untuk dimapping...</option>
                                <option
                                    v-for="opt in compendiumOptions"
                                    :key="`appendix-compendium-opt-${opt.id}`"
                                    :value="opt.id"
                                    :disabled="form.compendium_ids.includes(Number(opt.id))"
                                >
                                    {{ formatCompendiumLabel(opt) }}
                                </option>
                            </select>
                        </div>

                        <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="item in selectedCompendiums"
                                    :key="`appendix-compendium-tag-${item.id}`"
                                    class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                                >
                                    <span class="max-w-[280px] truncate">{{ item.label }}</span>
                                    <button
                                        type="button"
                                        @click="removeCompendium(item.id)"
                                        class="text-slate-400 transition-colors hover:text-rose-500"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <p v-if="selectedCompendiums.length === 0" class="flex items-center text-xs italic text-slate-400">
                                Belum ada compendium yang dipilih.
                            </p>
                        </div>
                    </div>

                    <div v-if="form.errors.compendium_ids" class="mt-2 text-xs italic text-rose-500">* {{ form.errors.compendium_ids }}</div>
                </div>
            </section>

            <AppendixCharterDocument
                :initiative="appendixData"
                :form="form"
                :editable="isEditing"
                :coe-options="coeOptions"
                :theme-options="themeOptions"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';

const props = defineProps({
    appendix: { type: Object, required: true },
    appendixOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    compendiumOptions: { type: Array, default: () => [] },
    coeOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
});

const route = useRouteHelper();

const toNumber = (value, fallback = null) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
};

const normalizeIdList = (values) => {
    if (!Array.isArray(values)) return [];
    return values.map((value) => toNumber(value, 0)).filter((value) => value > 0);
};

const parseSignBy = (value) => {
    if (Array.isArray(value)) {
        return value.map((item) => String(item ?? '').trim()).filter((item) => item !== '');
    }
    if (typeof value === 'string') {
        try {
            const parsed = JSON.parse(value);
            if (Array.isArray(parsed)) {
                return parsed.map((item) => String(item ?? '').trim()).filter((item) => item !== '');
            }
        } catch {
            // fall through
        }
        const trimmed = value.trim();
        return trimmed ? [trimmed] : [];
    }
    return [];
};

const buildAppendixFormPayload = (appendix = {}) => {
    const signBy = parseSignBy(appendix.sign_by ?? []);
    const primary = signBy[0] ?? '';
    const others = signBy.slice(1).join(', ');
    const initiativeIds = Array.isArray(appendix.initiative_ids)
        ? appendix.initiative_ids
        : (appendix.initiative_id ? [appendix.initiative_id] : []);
    const compendiumIds = Array.isArray(appendix.compendium_ids)
        ? appendix.compendium_ids
        : (appendix.compendium_id ? [appendix.compendium_id] : []);

    return {
        initiative_ids: normalizeIdList(initiativeIds),
        compendium_ids: normalizeIdList(compendiumIds),
        owner: String(appendix.owner ?? ''),
        coe: String(appendix.coe ?? ''),
        usecase: String(appendix.usecase ?? ''),
        description: String(appendix.description ?? ''),
        value: appendix.value === null || appendix.value === undefined ? null : toNumber(appendix.value, null),
        urgency: appendix.urgency === null || appendix.urgency === undefined ? null : toNumber(appendix.urgency, null),
        status: appendix.status ?? null,
        rjpp_tagging_ids: normalizeIdList(appendix.rjpp_tagging_ids),
        organization: String(appendix.organization ?? ''),
        update_doc: String(appendix.update_doc ?? ''),
        situation: String(appendix.situation ?? ''),
        key_functionalities: String(appendix.key_functionalities ?? ''),
        value_rationale: String(appendix.value_rationale ?? ''),
        value_matrics: String(appendix.value_matrics ?? ''),
        urgency_rationale: String(appendix.urgency_rationale ?? ''),
        urgency_expected: String(appendix.urgency_expected ?? ''),
        ease: appendix.ease === null || appendix.ease === undefined ? null : toNumber(appendix.ease, null),
        ease_rationale: String(appendix.ease_rationale ?? ''),
        ease_detail: String(appendix.ease_detail ?? ''),
        resource: appendix.resource === null || appendix.resource === undefined ? null : toNumber(appendix.resource, null),
        resource_rationale: String(appendix.resource_rationale ?? ''),
        resource_detail: String(appendix.resource_detail ?? appendix.resource_retionale ?? ''),
        predecessor: String(appendix.predecessor ?? ''),
        successor: String(appendix.successor ?? ''),
        otherBU: String(appendix.otherBU ?? ''),
        sign_by: [primary, ...signBy.slice(1)],
        sign_others_raw: others,
    };
};

const form = useForm(buildAppendixFormPayload(props.appendix ?? {}));

const isEditing = ref(false);

const APPENDIX_CREATE_VALUE = '__create__';
const appendixId = computed(() => toNumber(props.appendix?.id, 0));
const selectedAppendixId = computed({
    get: () => (appendixId.value > 0 ? String(appendixId.value) : ''),
    set: (value) => {
        const selectedValue = String(value ?? '').trim();
        if (!selectedValue) return;
        if (selectedValue === APPENDIX_CREATE_VALUE) {
            router.visit(route('program-planning.program-definition.digital-initiatives.appendix.create'));
            return;
        }
        if (selectedValue === String(appendixId.value)) return;
        router.visit(route('program-planning.program-definition.digital-initiatives.appendix.edit', selectedValue));
    },
});

const formatAppendixLabel = (option) => {
    const text = String(option?.label ?? '').trim();
    return text !== '' ? text : `Appendix #${option?.id ?? '-'}`;
};

const pageTitle = computed(() => 'Program Definition Digital Initiatives - Appendix Detail');
const canStartEdit = computed(() => !isEditing.value);
const showCancelEdit = computed(() => isEditing.value);
const canSubmit = computed(() => isEditing.value);

const submitLabel = computed(() => (form.processing ? 'Menyimpan...' : 'Simpan Appendix'));

const startEdit = () => {
    isEditing.value = true;
};

const cancelEdit = () => {
    form.defaults(buildAppendixFormPayload(props.appendix ?? {}));
    form.reset();
    form.clearErrors();
    isEditing.value = false;
};

const buildSignByPayload = (data) => {
    const primary = String(data.sign_by?.[0] ?? '').trim();
    const others = String(data.sign_others_raw ?? '')
        .split(',')
        .map((item) => item.trim())
        .filter((item) => item !== '');

    return [primary, ...others].filter((item) => item !== '');
};

const formatCompendiumLabel = (option) => {
    const text = String(option?.label ?? '').trim();
    return text !== '' ? text : `Compendium #${option?.id ?? '-'}`;
};

const submit = () => {
    form.transform((data) => {
        const initiativeIds = normalizeIdList(data.initiative_ids);
        const compendiumIds = normalizeIdList(data.compendium_ids);
        return {
            ...data,
            initiative_ids: initiativeIds.length ? initiativeIds : null,
            compendium_ids: compendiumIds,
            rjpp_tagging_ids: normalizeIdList(data.rjpp_tagging_ids),
            owner: String(data.owner ?? '').trim(),
            coe: String(data.coe ?? '').trim(),
            usecase: String(data.usecase ?? '').trim(),
            description: String(data.description ?? '').trim(),
            organization: String(data.organization ?? '').trim(),
            update_doc: data.update_doc ? String(data.update_doc).trim() : null,
            situation: String(data.situation ?? '').trim(),
            key_functionalities: String(data.key_functionalities ?? '').trim(),
            value_rationale: String(data.value_rationale ?? '').trim(),
            value_matrics: String(data.value_matrics ?? '').trim(),
            urgency_rationale: String(data.urgency_rationale ?? '').trim(),
            urgency_expected: String(data.urgency_expected ?? '').trim(),
            ease_rationale: String(data.ease_rationale ?? '').trim(),
            ease_detail: String(data.ease_detail ?? '').trim(),
            resource_rationale: String(data.resource_rationale ?? '').trim(),
            resource_detail: String(data.resource_detail ?? '').trim(),
            predecessor: String(data.predecessor ?? '').trim(),
            successor: String(data.successor ?? '').trim(),
            otherBU: String(data.otherBU ?? '').trim(),
            value: data.value === null || data.value === '' ? null : toNumber(data.value, null),
            urgency: data.urgency === null || data.urgency === '' ? null : toNumber(data.urgency, null),
            ease: data.ease === null || data.ease === '' ? null : toNumber(data.ease, null),
            resource: data.resource === null || data.resource === '' ? null : toNumber(data.resource, null),
            sign_by: buildSignByPayload(data),
        };
    });

    form.put(route('program-planning.program-definition.digital-initiatives.appendix.update', props.appendix.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const selectedInitiatives = computed(() => {
    const optionsById = new Map(
        (props.initiativeOptions ?? []).map((option) => [toNumber(option.id, 0), option])
    );
    return normalizeIdList(form.initiative_ids).map((id) => {
        const option = optionsById.get(id);
        return {
            id,
            code: option?.code ?? '',
            name: option?.name ?? `Initiative ${id}`,
        };
    });
});

const initiativeTagLabel = (item) => {
    if (!item) return '-';
    if (item.code) return `[${item.code}] ${item.name}`;
    return item.name;
};

const selectedCompendiums = computed(() => {
    const optionsById = new Map(
        (props.compendiumOptions ?? []).map((option) => [toNumber(option.id, 0), option])
    );
    return normalizeIdList(form.compendium_ids).map((id) => {
        const option = optionsById.get(id);
        return {
            id,
            label: option ? formatCompendiumLabel(option) : `Compendium #${id}`,
        };
    });
});

const addInitiative = (id) => {
    const numericId = toNumber(id, 0);
    if (!numericId) return;
    if (!form.initiative_ids.includes(numericId)) {
        form.initiative_ids.push(numericId);
    }
};

const removeInitiative = (id) => {
    const numericId = toNumber(id, 0);
    form.initiative_ids = normalizeIdList(form.initiative_ids).filter((item) => item !== numericId);
};

const onSelectInitiative = (event) => {
    const selectedId = toNumber(event.target.value, 0);
    addInitiative(selectedId);
    event.target.value = '';
};

const addCompendium = (id) => {
    const numericId = toNumber(id, 0);
    if (!numericId) return;
    if (!form.compendium_ids.includes(numericId)) {
        form.compendium_ids.push(numericId);
    }
};

const removeCompendium = (id) => {
    const numericId = toNumber(id, 0);
    form.compendium_ids = normalizeIdList(form.compendium_ids).filter((item) => item !== numericId);
};

const onSelectCompendium = (event) => {
    const selectedId = toNumber(event.target.value, 0);
    addCompendium(selectedId);
    event.target.value = '';
};

const appendixData = computed(() => {
    const a = props.appendix ?? {};
    const getLabel = (val) => {
        if (val === 1) return 'High';
        if (val === 2) return 'Medium';
        if (val === 3) return 'Low';
        return '-';
    };

    const signBy = parseSignBy(a.sign_by ?? []);
    const themeMap = new Map((props.themeOptions ?? []).map((theme) => [Number(theme.id), theme]));
    const rjppThemes = (a.rjpp_tagging_ids ?? []).map((id) => themeMap.get(Number(id))).filter(Boolean);

    return {
        usecase: a.usecase ?? '-',
        description: a.description ?? '-',
        owner: a.owner ?? '-',
        coe: a.coe ?? '-',
        value_label: getLabel(a.value),
        urgency_label: getLabel(a.urgency),
        organization: a.organization ?? '-',
        update_doc: a.update_doc ?? '-',
        situation: a.situation ?? '-',
        key_functionalities: a.key_functionalities ?? '-',
        value_rationale: a.value_rationale ?? '-',
        value_matrics: a.value_matrics ?? '-',
        urgency_rationale: a.urgency_rationale ?? '-',
        urgency_expected: a.urgency_expected ?? '-',
        ease_label: getLabel(a.ease),
        ease_rationale: a.ease_rationale ?? '-',
        ease_detail: a.ease_detail ?? '-',
        resource_label: getLabel(a.resource),
        resource_rationale: a.resource_rationale ?? '-',
        resource_detail: a.resource_detail ?? a.resource_retionale ?? '-',
        predecessor: a.predecessor ?? '-',
        successor: a.successor ?? '-',
        otherBU: a.otherBU ?? '-',
        sign_by: signBy,
        rjppThemes,
    };
});

watch(
    () => props.appendix,
    () => {
        if (!isEditing.value) {
            form.defaults(buildAppendixFormPayload(props.appendix ?? {}));
            form.reset();
            form.clearErrors();
        }
    },
    { deep: true },
);
</script>
