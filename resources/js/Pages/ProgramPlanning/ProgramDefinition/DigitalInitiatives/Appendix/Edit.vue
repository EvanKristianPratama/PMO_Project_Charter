<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <Link
                        href="/program-planning/program-definition/digital-initiatives/appendix"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>

                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10" />

                    <div class="text-xs font-medium text-slate-700 dark:text-slate-200">
                        Appendix Detail
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            v-if="canStartEdit"
                            type="button"
                            @click="startEdit"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-700 transition-all hover:bg-amber-100"
                        >
                            Edit Use Case
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
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';

const props = defineProps({
    appendix: { type: Object, required: true },
    coeOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
});

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

    return {
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

const submit = () => {
    form.transform((data) => ({
        ...data,
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
    }));

    form.put(`/program-planning/program-definition/digital-initiatives/appendix/${props.appendix.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
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
