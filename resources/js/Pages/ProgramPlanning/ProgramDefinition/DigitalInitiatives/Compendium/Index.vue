<template>
    <UserLayout title="Digital Initiatives - Compendium List">
        <div class="animate-fade-in space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            href="/program-planning/program-definition/digital-initiatives"
                            class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
                        >
                            <span aria-hidden="true">&larr;</span>
                            Kembali ke Digital Initiatives
                        </Link>
                        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Compendium List</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Data gabungan dari Master Initiative dan Scope Charter Initiative.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                        >
                            Total: {{ totalCompendiumItems }}
                        </span>
                        <button
                            type="button"
                            @click="openCreateModal"
                            class="inline-flex items-center rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f]"
                        >
                            New Compendium
                        </button>
                    </div>
                </div>
            </section>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
                    <span>Compendium Data Table</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[40px]">
                            <col class="w-[120px]">
                            <col class="w-[150px]">
                            <col class="w-[200px]">
                            <col class="w-[70px]">
                            <col class="w-[70px]">
                            <col class="w-[80px]">
                            <col class="w-[80px]">
                            <col class="w-[100px]">
                            <col class="w-[80px]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">RJPP</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">CoE</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Source</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(item, index) in compendiumItems" :key="`compendium-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-3 text-center text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.project_owner ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200 font-medium">{{ item.use_case ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200 line-clamp-2 hover:line-clamp-none transition-all duration-300">
                                    {{ item.desc ?? '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.value)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.value ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.urgency)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.urgency ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.rjpp ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.coe ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-medium text-slate-700 dark:bg-white/10 dark:text-slate-300">
                                            {{ item.data_source ?? '-' }}
                                        </span>
                                        <span v-if="item.data_source_created !== '-'" class="px-2 text-[9px] text-slate-500 dark:text-slate-400">
                                            {{ item.data_source_created }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="`/program-planning/program-definition/digital-initiatives/compendium/${item.id}/edit`"
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                            title="Edit Compendium"
                                        >
                                            Show
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="compendiumItems.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-xs text-slate-500 dark:text-slate-400 italic">
                                    Belum ada data compendium. Silakan tambahkan data baru.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            v-if="isCreateModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-4"
            @click.self="closeCreateModal"
        >
            <div class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#171717]">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-white/10">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">
                        New Compendium
                    </h2>
                    <button
                        type="button"
                        class="rounded-md px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-slate-200"
                        @click="closeCreateModal"
                    >
                        Close
                    </button>
                </div>

                <form class="flex-1 space-y-4 overflow-y-auto px-5 py-4" @submit.prevent="submitCreate">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Initiative</label>
                            <div class="space-y-2">
                                <select
                                    @change="(e) => { addInitiative(Number(e.target.value)); e.target.value = ''; }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Initiative...</option>
                                    <option
                                        v-for="opt in initiativeOptions"
                                        :key="`initiative-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="form.initiative_ids.includes(opt.id)"
                                    >
                                        {{ opt.code ? `[${opt.code}] ` : '' }}{{ themeDisplayLabel(opt) }}
                                    </option>
                                </select>
                                <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                    <template v-if="form.initiative_ids.length">
                                        <span
                                            v-for="id in form.initiative_ids"
                                            :key="`initiative-tag-${id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                        >
                                            {{ initiativeLabel(id) }}
                                            <button type="button" class="text-blue-700/70 hover:text-rose-500 dark:text-blue-300/80" @click="removeInitiative(id)">x</button>
                                        </span>
                                    </template>
                                    <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada initiative dipilih.</span>
                                </div>
                            </div>
                            <p v-if="form.errors.initiative_ids" class="mt-1 text-[10px] text-rose-500">{{ form.errors.initiative_ids }}</p>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Project Owner</label>
                                <input
                                    v-model="form.owner"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Input project owner..."
                                >
                                <p v-if="form.errors.owner" class="mt-1 text-[10px] text-rose-500">{{ form.errors.owner }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Usecase</label>
                                <input
                                    v-model="form.usecase"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Input usecase..."
                                >
                                <p v-if="form.errors.usecase" class="mt-1 text-[10px] text-rose-500">{{ form.errors.usecase }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            placeholder="Input description..."></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-[10px] text-rose-500">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Source</label>
                        <select
                            v-model="form.source_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                        >
                            <option value="">Pilih Source...</option>
                            <option v-for="source in sourceOptions" :key="`source-${source.id}`" :value="source.id">
                                {{ sourceDisplayLabel(source) }}
                            </option>
                        </select>
                        <p v-if="form.errors.source_id" class="mt-1 text-[10px] text-rose-500">{{ form.errors.source_id }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Value</label>
                            <select
                                v-model="form.value"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="opt in scoreOptions" :key="`value-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.value" class="mt-1 text-[10px] text-rose-500">{{ form.errors.value }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Urgency</label>
                            <select
                                v-model="form.urgency"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="opt in scoreOptions" :key="`urgency-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.urgency" class="mt-1 text-[10px] text-rose-500">{{ form.errors.urgency }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Status</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="opt in statusOptions" :key="`status-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-[10px] text-rose-500">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">RJPP Tagging</label>
                        <div class="space-y-2">
                            <select
                                @change="(e) => { addRjppTagging(Number(e.target.value)); e.target.value = ''; }"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">+ Pilih RJPP Tagging...</option>
                                <option
                                    v-for="opt in themeOptions"
                                    :key="`theme-opt-${opt.id}`"
                                    :value="opt.id"
                                    :disabled="form.rjpp_tagging_ids.includes(opt.id)"
                                >
                                    {{ themeDisplayLabel(opt) }}
                                </option>
                            </select>
                            <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                <template v-if="form.rjpp_tagging_ids.length">
                                    <span
                                        v-for="id in form.rjpp_tagging_ids"
                                        :key="`rjpp-tag-${id}`"
                                        class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-300"
                                    >
                                        {{ themeLabel(id) }}
                                        <button type="button" class="text-amber-700/70 hover:text-rose-500 dark:text-amber-300/80" @click="removeRjppTagging(id)">x</button>
                                    </span>
                                </template>
                                <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada RJPP dipilih.</span>
                            </div>
                        </div>
                        <p v-if="form.errors.rjpp_tagging_ids" class="mt-1 text-[10px] text-rose-500">{{ form.errors.rjpp_tagging_ids }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-3 dark:border-white/10">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/10"
                            @click="closeCreateModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f] disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Compendium' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    compendiumItems: {
        type: Array,
        default: () => [],
    },
    totalCompendiumItems: {
        type: Number,
        default: 0,
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    sourceOptions: {
        type: Array,
        default: () => [],
    },
    themeOptions: {
        type: Array,
        default: () => [],
    },
});

const isCreateModalOpen = ref(false);

const openCreateModal = () => {
    isCreateModalOpen.value = true;
};

const scoreOptions = [
    { value: 1, label: 'High' },
    { value: 2, label: 'Medium' },
    { value: 3, label: 'Low' },
    { value: null, label: 'TBC' },
];

const statusOptions = [
    { value: 1, label: 'Drafting' },
    { value: 2, label: 'Propose' },
    { value: 3, label: 'Review' },
    { value: 4, label: 'Baseline' },
    { value: 5, label: 'Approved' },
];

const form = useForm({
    initiative_ids: [],
    owner: '',
    usecase: '',
    description: '',
    source_id: '',
    value: null,
    urgency: null,
    rjpp_tagging_ids: [],
    status: 1,
});

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
    resetForm();
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
    form.initiative_ids = [];
    form.owner = '';
    form.source_id = '';
    form.rjpp_tagging_ids = [];
    form.value = null;
    form.urgency = null;
    form.status = 1;
};

const addInitiative = (id) => {
    if (id && !form.initiative_ids.includes(id)) {
        form.initiative_ids.push(id);
    }
};

const removeInitiative = (id) => {
    form.initiative_ids = form.initiative_ids.filter((item) => item !== id);
};

const initiativeLabel = (id) => {
    const selected = props.initiativeOptions.find((item) => item.id === id);
    if (!selected) return String(id);
    return selected.code ? `[${selected.code}] ${selected.name}` : selected.name;
};

const addRjppTagging = (id) => {
    if (id && !form.rjpp_tagging_ids.includes(id)) {
        form.rjpp_tagging_ids.push(id);
    }
};

const removeRjppTagging = (id) => {
    form.rjpp_tagging_ids = form.rjpp_tagging_ids.filter((item) => item !== id);
};

const themeDisplayLabel = (theme) => {
    if (!theme) return '-';

    const strategicPillar = String(theme?.strategic_pillar_title ?? theme?.strategic_pillar ?? '').trim();
    const themeNumber = String(theme?.theme_number ?? theme?.code ?? '').trim();
    const themeName = String(theme?.theme_name ?? theme?.name ?? '').trim();

    if (strategicPillar === '' && themeNumber === '') {
        return themeName || '-';
    }

    const prefix = strategicPillar !== '' ? `[${strategicPillar}]` : '';
    const number = themeNumber !== '' ? ` #${themeNumber}` : '';
    const suffix = themeName !== '' ? ` - ${themeName}` : '';

    return `${prefix}${number}${suffix}`.trim() || '-';
};

const themeLabel = (id) => {
    const theme = props.themeOptions.find((item) => item.id === id);
    return themeDisplayLabel(theme) || String(id);
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

const submitCreate = () => {
    form.transform((data) => ({
        ...data,
        value: data.value === '' ? null : data.value,
        urgency: data.urgency === '' ? null : data.urgency,
    })).post('/program-planning/program-definition/digital-initiatives/compendium', {
        preserveScroll: true,
        onSuccess: () => {
            isCreateModalOpen.value = false;
            resetForm();
        },
    });
};

const scoreClass = (label) => {
    const l = String(label ?? '').toLowerCase();
    if (l === 'high') return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    if (l === 'medium') return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    if (l === 'low') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    return 'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300';
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hover\:line-clamp-none:hover {
    -webkit-line-clamp: unset;
    overflow: visible;
}
</style>








