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

                    <label for="compendium-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih Use Case</label>
                    <select
                        id="compendium-nav"
                        v-model="selectedCompendiumId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="" disabled>-- Pilih Use Case --</option>
                        <option v-for="option in compendiumOptions" :key="`compendium-opt-${option.id}`" :value="String(option.id)">
                            {{ formatCompendiumLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            v-if="canStartEdit"
                            type="button"
                            @click="openAppendixModal"
                            class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 transition-all hover:bg-blue-100 dark:border-white/10 dark:bg-transparent dark:text-blue-300 dark:hover:bg-white/5"
                        >
                            Manage Appendix
                        </button>

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

            <section
                v-if="isEditing"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] space-y-8"
            >
                <!-- Group 1: General Info & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Use Case Name</label>
                            <input v-model="form.usecase" type="text"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Input use case name...">
                            <p v-if="form.errors.usecase" class="mt-1 text-[10px] text-rose-500">{{ form.errors.usecase }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Description</label>
                            <textarea v-model="form.description" rows="3"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="Input description..."></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-[10px] text-rose-500">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Document Status</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Group 2: Master Initiative Mapping -->
                <div class="border-t border-slate-100 dark:border-white/5 pt-8">
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
                                    {{ opt.code ? `[${String(opt.code).replace(/#/g, '')}] ` : '' }}{{ opt.name }}
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

                    <div v-if="form.errors.initiative_ids" class="mt-2 text-xs italic text-rose-500">* {{ form.errors.initiative_ids }}</div>
                </div>
            </section>

            <CompendiumCharterDocument
                :form="form"
                :initiative-options="initiativeOptions"
                :coe-options="coeOptions"
                :source-options="sourceOptions"
                :theme-options="themeOptions"
                :editable="isEditing"
            />

            <div class="pt-1">
                <div v-if="appendix" class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Appendix Detail</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                v-if="!isAppendixEditing"
                                type="button"
                                @click="startAppendixEdit"
                                class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-700 transition-all hover:bg-amber-100"
                            >
                                Edit Appendix
                            </button>
                            <button
                                v-if="isAppendixEditing"
                                type="button"
                                @click="cancelAppendixEdit"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-transparent dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Batal
                            </button>
                            <button
                                v-if="isAppendixEditing"
                                type="button"
                                @click="submitAppendix"
                                :disabled="appendixForm.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-[#0f63b5] px-6 py-2 text-xs font-bold text-white shadow-md transition-all active:scale-95 hover:bg-[#0c4e8f] disabled:opacity-50"
                            >
                                {{ appendixForm.processing ? 'Menyimpan...' : 'Simpan Appendix' }}
                            </button>
                        </div>
                    </div>

                    <section
                        v-if="isAppendixEditing"
                        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                    >
                        <div class="mb-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Master Initiative Mapping</h4>
                            <p class="mt-1 text-xs text-slate-500">Pilih atau sesuaikan inisiatif yang terhubung ke Appendix.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="max-w-xl">
                                <select
                                    @change="onSelectAppendixInitiative"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Initiative untuk dimapping...</option>
                                    <option
                                        v-for="opt in initiativeOptions"
                                        :key="`appendix-initiative-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="appendixForm.initiative_ids.includes(Number(opt.id))"
                                    >
                                        {{ opt.code ? `[${String(opt.code).replace(/#/g, '')}] ` : '' }}{{ opt.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        v-for="item in selectedAppendixInitiatives"
                                        :key="`appendix-tag-${item.id}`"
                                        class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                                    >
                                        <span class="max-w-[280px] truncate">{{ appendixInitiativeTagLabel(item).replace(/#/g, '') }}</span>
                                        <button
                                            type="button"
                                            @click="removeAppendixInitiative(item.id)"
                                            class="text-slate-400 transition-colors hover:text-rose-500"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <p v-if="selectedAppendixInitiatives.length === 0" class="flex items-center text-xs italic text-slate-400">
                                    Belum ada inisiatif yang dipilih.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section
                        v-if="isAppendixEditing"
                        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                    >
                        <div class="mb-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Compendium Mapping</h4>
                            <p class="mt-1 text-xs text-slate-500">Pilih compendium yang terhubung ke Appendix.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="max-w-xl">
                                <select
                                    @change="onSelectAppendixCompendium"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Compendium untuk dimapping...</option>
                                    <option
                                        v-for="opt in compendiumOptions"
                                        :key="`appendix-compendium-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="appendixForm.compendium_ids.includes(Number(opt.id))"
                                    >
                                        {{ formatCompendiumLabel(opt) }}
                                    </option>
                                </select>
                            </div>

                            <div class="min-h-[40px] rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/5">
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        v-for="item in selectedAppendixCompendiums"
                                        :key="`appendix-compendium-tag-${item.id}`"
                                        class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm dark:border-blue-500/30 dark:bg-[#1a1a1a] dark:text-blue-400"
                                    >
                                        <span class="max-w-[280px] truncate">{{ item.label }}</span>
                                        <button
                                            type="button"
                                            @click="removeAppendixCompendium(item.id)"
                                            class="text-slate-400 transition-colors hover:text-rose-500"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <p v-if="selectedAppendixCompendiums.length === 0" class="flex items-center text-xs italic text-slate-400">
                                    Belum ada compendium yang dipilih.
                                </p>
                            </div>
                        </div>

                        <div v-if="appendixForm.errors.compendium_ids" class="mt-2 text-xs italic text-rose-500">* {{ appendixForm.errors.compendium_ids }}</div>
                    </section>

                    <AppendixCharterDocument 
                        :initiative="appendixData" 
                        :form="appendixForm"
                        :editable="appendixEditable"
                        :coe-options="coeOptions"
                        :theme-options="themeOptions"
                    />
                </div>
                <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-12 text-center dark:border-white/10 dark:bg-white/5">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-slate-900 dark:text-white">Tidak Ada Data Appendix Terkait</h3>
                    <p class="mt-2 text-xs text-slate-500">Compendium ini belum dihubungkan dengan Charter Appendix manapun. Klik tombol <strong>"Manage Appendix"</strong> di atas untuk menghubungkannya.</p>
                </div>
            </div>
        </div>

        <!-- Use the new separate component -->
        <AppendixCharterModal
            :show="isAppendixModalOpen"
            :compendium="compendium"
            :appendix="appendix"
            :compendium-options="compendiumOptions"
            :initiative-options="initiativeOptions"
            :coe-options="coeOptions"
            :source-options="sourceOptions"
            :theme-options="themeOptions"
            :organization-options="organizationOptions"
            @close="closeAppendixModal"
            @success="closeAppendixModal"
        />
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CompendiumCharterDocument from '@/Components/Compendium/CompendiumCharterDocument.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';
import AppendixCharterModal from '@/Components/Appendix/AppendixCharterModal.vue';

const COMPENDIUM_CREATE_VALUE = '__create__';

const props = defineProps({
    compendium: { type: Object, default: null },
    appendix: { type: Object, default: null },
    compendiumOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
});

const toNumber = (value, fallback = null) => {
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
};

const compendiumId = computed(() => toNumber(props.compendium?.id, 0));
const appendixId = computed(() => toNumber(props.appendix?.id, 0));
const hasAppendix = computed(() => appendixId.value > 0);

const isAppendixModalOpen = ref(false);
const isAppendixEditing = ref(false);

const openAppendixModal = () => {
    isAppendixModalOpen.value = true;
};

const closeAppendixModal = () => {
    isAppendixModalOpen.value = false;
};

const selectedThemes = computed(() => {
    const ids = Array.isArray(props.compendium?.rjpp_tagging_ids) 
        ? props.compendium.rjpp_tagging_ids 
        : [];
        
    const map = new Map((props.themeOptions ?? []).map(opt => [toNumber(opt.id, 0), opt]));
    return ids.map(id => map.get(toNumber(id, 0))).filter(Boolean);
});

const appendixData = computed(() => {
    const a = props.appendix;
    const getLabel = (val) => {
        if (val === 1) return 'High';
        if (val === 2) return 'Medium';
        if (val === 3) return 'Low';
        return '-';
    };

    // Resolve sign_by as array
    let signBy = a?.sign_by ?? [];
    if (typeof signBy === 'string') {
        try { signBy = JSON.parse(signBy); } catch { signBy = signBy ? [signBy] : []; }
    }

    // Resolve rjpp themes from IDs
    const themeMap = new Map((props.themeOptions ?? []).map(t => [Number(t.id), t]));
    const rjppThemes = (a?.rjpp_tagging_ids ?? []).map(id => themeMap.get(Number(id))).filter(Boolean);

    return {
        // TrsScInitiative fields
        usecase:      a?.usecase ?? '-',
        description:  a?.description ?? '-',
        owner:        a?.owner ?? '-',
        coe:          a?.coe ?? '-',
        value_label:  getLabel(a?.value),
        urgency_label: getLabel(a?.urgency),
        // TrsScDetails fields
        organization:        a?.organization ?? '-',
        update_doc:          a?.update_doc ?? '-',
        situation:           a?.situation ?? '-',
        key_functionalities: a?.key_functionalities ?? '-',
        value_rationale:     a?.value_rationale ?? '-',
        value_matrics:       a?.value_matrics ?? '-',
        urgency_rationale:   a?.urgency_rationale ?? '-',
        urgency_expected:    a?.urgency_expected ?? '-',
        ease_label:          getLabel(a?.ease),
        ease_rationale:      a?.ease_rationale ?? '-',
        ease_detail:         a?.ease_detail ?? '-',
        resource_label:      getLabel(a?.resource),
        resource_rationale:  a?.resource_rationale ?? '-',
        resource_detail:     a?.resource_detail ?? '-',
        predecessor:         a?.predecessor ?? '-',
        successor:           a?.successor ?? '-',
        otherBU:             a?.otherBU ?? '-',
        // Sign
        sign_by:             signBy,
        // RJPP
        rjppThemes,
    };
});



const statusOptions = [
    { value: '1', label: 'Drafting' },
    { value: '2', label: 'Propose' },
    { value: '3', label: 'Review' },
    { value: '4', label: 'Baseline' },
    { value: '5', label: 'Approved' },
];

const normalizeIdList = (values) => {
    if (!Array.isArray(values)) return [];
    return values.map((value) => toNumber(value, 0)).filter((value) => value > 0);
};

const normalizeSourceId = (value) => {
    const num = toNumber(value, 0);
    return num > 0 ? num : '';
};

const buildFormPayload = (compendium = {}) => ({
    initiative_ids: normalizeIdList(compendium.initiative_ids),
    owner: String(compendium.owner ?? ''),
    coe: String(compendium.coe ?? ''),
    usecase: String(compendium.usecase ?? ''),
    description: String(compendium.description ?? ''),
    source_id: normalizeSourceId(compendium.source_id),
    value: compendium.value === null || compendium.value === undefined ? null : toNumber(compendium.value, null),
    urgency: compendium.urgency === null || compendium.urgency === undefined ? null : toNumber(compendium.urgency, null),
    rjpp_tagging_ids: normalizeIdList(compendium.rjpp_tagging_ids),
    status: String(compendium.status ?? '1'),
});

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

const isExisting = computed(() => compendiumId.value > 0);
const isEditing = ref(!isExisting.value);
const appendixEditable = computed(() => isAppendixEditing.value || isEditing.value);

const form = useForm(buildFormPayload(props.compendium ?? {}));
const appendixForm = useForm(buildAppendixFormPayload(props.appendix ?? {}));

const selectedCompendiumId = computed({
    get: () => (isExisting.value ? String(compendiumId.value) : ''),
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

const selectedAppendixInitiatives = computed(() => {
    const optionsById = new Map(
        (props.initiativeOptions ?? []).map((option) => [toNumber(option.id, 0), option])
    );
    return normalizeIdList(appendixForm.initiative_ids).map((id) => {
        const option = optionsById.get(id);
        return {
            id,
            code: option?.code ?? '',
            name: option?.name ?? `Initiative ${id}`,
        };
    });
});

const appendixInitiativeTagLabel = (item) => {
    if (!item) return '-';
    if (item.code) return `[${item.code}] ${item.name}`;
    return item.name;
};

const selectedAppendixCompendiums = computed(() => {
    const optionsById = new Map(
        (props.compendiumOptions ?? []).map((option) => [toNumber(option.id, 0), option])
    );
    return normalizeIdList(appendixForm.compendium_ids).map((id) => {
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

const addAppendixInitiative = (id) => {
    const numericId = toNumber(id, 0);
    if (!numericId) return;
    if (!appendixForm.initiative_ids.includes(numericId)) {
        appendixForm.initiative_ids.push(numericId);
    }
};

const removeAppendixInitiative = (id) => {
    const numericId = toNumber(id, 0);
    appendixForm.initiative_ids = normalizeIdList(appendixForm.initiative_ids).filter((item) => item !== numericId);
};

const onSelectAppendixInitiative = (event) => {
    const selectedId = toNumber(event.target.value, 0);
    addAppendixInitiative(selectedId);
    event.target.value = '';
};

const addAppendixCompendium = (id) => {
    const numericId = toNumber(id, 0);
    if (!numericId) return;
    if (!appendixForm.compendium_ids.includes(numericId)) {
        appendixForm.compendium_ids.push(numericId);
    }
};

const removeAppendixCompendium = (id) => {
    const numericId = toNumber(id, 0);
    appendixForm.compendium_ids = normalizeIdList(appendixForm.compendium_ids).filter((item) => item !== numericId);
};

const onSelectAppendixCompendium = (event) => {
    const selectedId = toNumber(event.target.value, 0);
    addAppendixCompendium(selectedId);
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

const syncAppendixForm = () => {
    appendixForm.defaults(buildAppendixFormPayload(props.appendix ?? {}));
    appendixForm.reset();
    appendixForm.clearErrors();
};

const startAppendixEdit = () => {
    syncAppendixForm();
    isAppendixEditing.value = true;
};

const cancelAppendixEdit = () => {
    syncAppendixForm();
    isAppendixEditing.value = false;
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
    const endpointBase = '/program-planning/program-definition/digital-initiatives/compendium';
    form.transform((data) => ({
        ...data,
        initiative_ids: normalizeIdList(data.initiative_ids),
        rjpp_tagging_ids: normalizeIdList(data.rjpp_tagging_ids),
        owner: String(data.owner ?? '').trim(),
        coe: String(data.coe ?? '').trim(),
        usecase: String(data.usecase ?? '').trim(),
        description: String(data.description ?? '').trim(),
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

const submitAppendix = () => {
    if (!hasAppendix.value) return;
    appendixForm.transform((data) => ({
        ...data,
        initiative_ids: normalizeIdList(data.initiative_ids),
        compendium_ids: normalizeIdList(data.compendium_ids),
        rjpp_tagging_ids: normalizeIdList(data.rjpp_tagging_ids),
        owner: String(data.owner ?? '').trim(),
        coe: String(data.coe ?? '').trim(),
        usecase: String(data.usecase ?? '').trim(),
        description: String(data.description ?? '').trim(),
        organization: String(data.organization ?? '').trim(),
        update_doc: String(data.update_doc ?? '').trim(),
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

    appendixForm.put(`/program-planning/program-definition/digital-initiatives/appendix/${appendixId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            isAppendixEditing.value = false;
        },
    });
};

watch(
    () => props.appendix,
    () => {
        if (!isAppendixEditing.value) {
            syncAppendixForm();
        }
    },
    { deep: true },
);
</script>
