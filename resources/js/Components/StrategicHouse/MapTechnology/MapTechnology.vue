<template>
    <div class="space-y-4">
        <!-- Pending Removals Warning -->
        <div v-if="pendingRemovals.length > 0"
            class="flex flex-col gap-3 border border-amber-200 bg-amber-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between rounded-xl">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-amber-900">
                    {{ pendingRemovals.length }} mapping ditandai untuk dihapus.
                </p>
                <p class="mt-1 text-xs text-amber-800">
                    Klik `Simpan Perubahan` untuk menerapkan penghapusan, atau `Batal` untuk mengembalikan.
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                <button type="button"
                    class="inline-flex items-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="bulkDeleteProcessing" @click="savePendingRemovals">
                    {{ bulkDeleteProcessing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
                <button type="button"
                    class="inline-flex items-center rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-medium text-amber-800 transition hover:bg-amber-100"
                    @click="pendingRemovals = []">
                    Batal
                </button>
            </div>
        </div>

        <div class="flex justify-between items-center px-1">
            <button v-if="editable" type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-[#1C75BC] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#155a96] focus:outline-none focus:ring-2 focus:ring-[#1C75BC] focus:ring-offset-2"
                @click="showAddModal = true">
                + Tambah Mapping
            </button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <h1 class="text-center text-l font-bold mt-4 mb-4">Pertamina's ITSP initiatives to
                    technology type mapping</h1>
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/5">
                            <th
                                class="whitespace-nowrap px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                IT Architecture Building Block
                            </th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                List of IT Initiatives
                            </th>
                            <th v-for="tech in techColumns" :key="tech.id"
                                class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 min-w-[100px]">
                                {{ tech.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                        <tr v-for="ini in filteredInitiatives" :key="ini.id"
                            class="group transition-colors hover:bg-slate-50/50 dark:hover:bg-white/5">
                            <!-- IT Architecture (Strategy Pillar / Primary CoE) -->
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                        {{ ini.primary_coe?.name || '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- List of Initiative -->
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-start gap-2">
                                    <span
                                        class="inline-flex shrink-0 items-center justify-center rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        {{ ini.code }}
                                    </span>
                                    <span class="text-xs text-slate-700 dark:text-slate-200 font-medium">
                                        {{ ini.name }}
                                    </span>
                                </div>
                            </td>

                            <!-- Tech CoE Columns -->
                            <td v-for="tech in techColumns" :key="`${ini.id}-${tech.id}`"
                                class="px-4 py-4 text-center align-top">
                                <div v-if="ini.tech_mappings.has(tech.id)"
                                    class="flex justify-center group/cell relative">
                                    <div
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/20">
                                        <CheckIcon class="h-4 w-4 text-[#1C75BC] dark:text-blue-400" />
                                    </div>

                                    <!-- Remove button in edit mode -->
                                    <button v-if="editable && !isPrimaryMapping(ini, tech.id)" type="button"
                                        class="absolute -top-1 -right-1 hidden group-hover/cell:flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600 transition-colors"
                                        @click="markForRemoval(ini.id, tech.id)" title="Hapus Mapping">
                                        <XMarkIcon class="h-3 w-3" />
                                    </button>

                                    <div v-if="editable && isPrimaryMapping(ini, tech.id)"
                                        class="absolute -bottom-4 left-1/2 -translate-x-1/2 opacity-0 group-hover/cell:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase">Primary</span>
                                    </div>
                                </div>
                                <div v-else class="text-slate-300 dark:text-slate-700">-</div>
                            </td>
                        </tr>
                        <tr v-if="!filteredInitiatives.length">
                            <td :colspan="2 + techColumns.length" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <p class="text-sm italic text-slate-500 dark:text-slate-400">Map Technology not
                                        available</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Mapping Modal -->
        <MapTechnologyModal :show="showAddModal" :initiative-options="initiativeOptions" :tech-columns="techColumns"
            :processing="storeProcessing" :error="modalError" @close="showAddModal = false"
            @confirm="handleStoreMapping" />
    </div>
</template>

<script setup>
import { computed, ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import MapTechnologyModal from './MapTechnologyModal.vue';

const props = defineProps({
    mapTechnologies: {
        type: [Array, Object],
        default: () => ({}),
    },
    editable: {
        type: Boolean,
        default: false,
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
});

const techColumns = [
    { id: 2, label: 'AI/Analytics' },
    { id: 4, label: 'Cloud & Advanced Computing' },
    { id: 3, label: 'IoT' },
    { id: 6, label: 'Robotics' },
    { id: 5, label: 'RPA' },
];

const showAddModal = ref(false);
const storeProcessing = ref(false);
const bulkDeleteProcessing = ref(false);
const modalError = ref('');
const pendingRemovals = ref([]);

const processedInitiatives = computed(() => {
    const initiativeMap = new Map();
    const techIds = techColumns.map(t => t.id);

    // mapTechnologies is grouped by coed_id
    Object.values(props.mapTechnologies).forEach(mappings => {
        mappings.forEach(mapping => {
            const ini = mapping.initiative;
            if (!ini) return;

            if (!initiativeMap.has(ini.id)) {
                initiativeMap.set(ini.id, {
                    id: ini.id,
                    code: String(ini.code ?? ''),
                    name: ini.name,
                    primary_coe: ini.coe,
                    primary_coe_id: ini.coe_id,
                    organization: ini.organization,
                    tech_mappings: new Set()
                });
            }

            // Add technology mapping from trs_map_technology
            if (techIds.includes(mapping.coed_id)) {
                initiativeMap.get(ini.id).tech_mappings.add(mapping.coed_id);
            }
        });
    });

    // Also include primary coe mapping if it matches a tech column
    Array.from(initiativeMap.values()).forEach(ini => {
        if (techIds.includes(ini.primary_coe_id)) {
            ini.tech_mappings.add(ini.primary_coe_id);
        }
    });

    return Array.from(initiativeMap.values()).sort((a, b) => {
        const coeCompare = String(a.primary_coe?.name ?? '').localeCompare(String(b.primary_coe?.name ?? ''));
        if (coeCompare !== 0) return coeCompare;
        return String(a.code ?? '').localeCompare(String(b.code ?? ''));
    });
});

const filteredInitiatives = computed(() => {
    return processedInitiatives.value.map(ini => {
        const newTechMappings = new Set(ini.tech_mappings);

        // Remove pending deletions
        pendingRemovals.value.forEach(rem => {
            if (rem.initiative_id === ini.id) {
                newTechMappings.delete(rem.coed_id);
            }
        });

        return {
            ...ini,
            tech_mappings: newTechMappings
        };
    }).filter(ini => ini.tech_mappings.size > 0);
});

const isPrimaryMapping = (ini, techId) => {
    return ini.primary_coe_id === techId;
};

const markForRemoval = (initiativeId, coedId) => {
    pendingRemovals.value.push({ initiative_id: initiativeId, coed_id: coedId });
};

const handleStoreMapping = (data) => {
    storeProcessing.value = true;
    modalError.value = '';

    router.post(route('strategic-house.map-technology.store'), data, {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
        },
        onError: (errors) => {
            modalError.value = Object.values(errors)[0];
        },
        onFinish: () => {
            storeProcessing.value = false;
        },
    });
};

const savePendingRemovals = () => {
    bulkDeleteProcessing.value = true;

    router.post(route('strategic-house.map-technology.bulk-destroy'), {
        removals: pendingRemovals.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            pendingRemovals.value = [];
        },
        onFinish: () => {
            bulkDeleteProcessing.value = false;
        },
    });
};
</script>
