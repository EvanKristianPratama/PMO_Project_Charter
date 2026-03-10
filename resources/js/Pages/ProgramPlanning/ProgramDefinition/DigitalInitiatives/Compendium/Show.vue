<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <Link
                        href="/program-planning/program-definition/digital-initiatives/compendium"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>

                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10" />

                    <label for="compendium-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih Compendium</label>
                    <select
                        id="compendium-nav"
                        v-model="selectedCompendiumId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option :value="COMPENDIUM_CREATE_VALUE">+ New Compendium</option>
                        <option v-for="option in compendiumOptions" :key="`compendium-opt-${option.id}`" :value="String(option.id)">
                            {{ formatCompendiumLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            v-if="canStartEdit"
                            type="button"
                            @click="startEdit"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-700 transition-all hover:bg-amber-100"
                        >
                            Edit Compendium
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
                <div class="mb-4 w-full sm:max-w-xs">
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Document Status</label>
                    <select
                        v-model="form.status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                    >
                        <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Master Initiative Mapping</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ mappingSubtitle }}</p>
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
                                :key="opt.id"
                                :value="opt.id"
                                :disabled="form.initiative_ids.includes(Number(opt.id))"
                            >
                                {{ opt.code ? `[${opt.code}] ` : '' }}{{ opt.name }}
                            </option>
                        </select>
                    </div>

                    <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                        <div class="flex flex-wrap gap-2">
                            <div
                                v-for="item in selectedInitiatives"
                                :key="`tag-${item.id}`"
                                class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                            >
                                <span class="max-w-[280px] truncate">{{ initiativeTagLabel(item) }}</span>
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

                <div v-if="form.errors.initiative_ids" class="mt-2 text-xs italic text-rose-500">* {{ form.errors.initiative_ids }}</div>
            </section>

            <CompendiumCharterDocument
                :form="form"
                :initiative-options="initiativeOptions"
                :source-options="sourceOptions"
                :theme-options="themeOptions"
                :editable="isEditing"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CompendiumCharterDocument from '@/Components/Compendium/CompendiumCharterDocument.vue';

const COMPENDIUM_CREATE_VALUE = '__create__';

const props = defineProps({
    compendium: { type: Object, default: null },
    compendiumOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
});

const statusOptions = [
    { value: '1', label: 'Drafting' },
    { value: '2', label: 'Propose' },
    { value: '3', label: 'Review' },
    { value: '4', label: 'Baseline' },
    { value: '5', label: 'Approved' },
];

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

const normalizeSourceId = (value) => {
    const num = toNumber(value, 0);
    return num > 0 ? num : '';
};

const buildFormPayload = (compendium = {}) => ({
    initiative_ids: normalizeIdList(compendium.initiative_ids),
    owner: String(compendium.owner ?? ''),
    usecase: String(compendium.usecase ?? ''),
    description: String(compendium.description ?? ''),
    source_id: normalizeSourceId(compendium.source_id),
    value: compendium.value === null || compendium.value === undefined ? null : toNumber(compendium.value, null),
    urgency: compendium.urgency === null || compendium.urgency === undefined ? null : toNumber(compendium.urgency, null),
    rjpp_tagging_ids: normalizeIdList(compendium.rjpp_tagging_ids),
    status: String(compendium.status ?? '1'),
});

const compendiumId = computed(() => toNumber(props.compendium?.id, 0));
const isExisting = computed(() => compendiumId.value > 0);
const isEditing = ref(!isExisting.value);

const form = useForm(buildFormPayload(props.compendium ?? {}));

const selectedCompendiumId = computed({
    get: () => (isExisting.value ? String(compendiumId.value) : COMPENDIUM_CREATE_VALUE),
    set: (value) => {
        const selectedValue = String(value ?? '').trim();

        if (!selectedValue) return;

        if (selectedValue === COMPENDIUM_CREATE_VALUE) {
            if (!isExisting.value) return;
            router.visit('/program-planning/program-definition/digital-initiatives/compendium/create');
            return;
        }

        if (isExisting.value && selectedValue === String(compendiumId.value)) return;

        router.visit(`/program-planning/program-definition/digital-initiatives/compendium/${selectedValue}/edit`);
    },
});

const formatCompendiumLabel = (option) => {
    const text = String(option?.label ?? '').trim();
    return text !== '' ? text : `Compendium #${option?.id ?? '-'}`;
};

const pageTitle = computed(() => (
    isExisting.value
        ? 'Program Definition Digital Initiatives - Compendium Detail'
        : 'Program Definition Digital Initiatives - New Compendium'
));

const mappingSubtitle = computed(() => (
    isEditing.value
        ? 'Pilih atau sesuaikan inisiatif yang dihubungkan ke dokumen compendium.'
        : 'Daftar inisiatif yang sudah terhubung ke dokumen compendium.'
));

const canStartEdit = computed(() => isExisting.value && !isEditing.value);
const showCancelEdit = computed(() => isExisting.value && isEditing.value);
const canSubmit = computed(() => isEditing.value);

const submitLabel = computed(() => {
    if (form.processing) {
        return isExisting.value ? 'Memperbarui...' : 'Menyimpan...';
    }

    return isExisting.value ? 'Simpan Perubahan' : 'Simpan Compendium';
});

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

const startEdit = () => {
    isEditing.value = true;
};

const cancelEdit = () => {
    form.defaults(buildFormPayload(props.compendium ?? {}));
    form.reset();
    form.clearErrors();
    isEditing.value = false;
};

const submit = () => {
    const endpointBase = '/program-planning/program-definition/digital-initiatives/compendium';

    form.transform((data) => ({
        ...data,
        initiative_ids: normalizeIdList(data.initiative_ids),
        rjpp_tagging_ids: normalizeIdList(data.rjpp_tagging_ids),
        source_id: data.source_id === '' ? null : toNumber(data.source_id, null),
        value: data.value === null || data.value === '' ? null : toNumber(data.value, null),
        urgency: data.urgency === null || data.urgency === '' ? null : toNumber(data.urgency, null),
        status: String(data.status ?? '1'),
    }));

    if (isExisting.value) {
        form.put(`${endpointBase}/${compendiumId.value}`, {
            preserveScroll: true,
        });
        return;
    }

    form.post(endpointBase, {
        preserveScroll: true,
    });
};
</script>

