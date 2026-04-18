<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-if="!isEditMode"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                        @click="openAddSupport"
                    >
                        Tambah Support
                    </button>
                    <button
                        v-if="!isEditMode"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        @click="isEditMode = true"
                    >
                        Edit Mapping
                    </button>
                    <button
                        v-else
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        @click="isEditMode = false"
                    >
                        Selesai Edit
                    </button>
                </div>

                <div v-if="!isEditMode && props.coeOptions.length > 0" class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Filter CoE:</label>
                    <select
                        v-model="filterCoe"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm focus:border-slate-500 focus:outline-none dark:border-white/10 dark:bg-slate-900 dark:text-slate-200"
                    >
                        <option value="">Semua CoE</option>
                        <option v-for="coe in props.coeOptions" :key="coe.id" :value="coe.name">
                            {{ coe.name }}
                        </option>
                    </select>
                </div>
            </div>

            <InitiativeSupport
                ref="initiativeSupportRef"
                :groups="processedGroups"
                :editable="isEditMode"
                :digital-options="digitalInitiativeOptions"
                :it-options="itInitiativeOptions"
                :coe-options="coeOptions"
                @cancel-add-support="isEditMode = false"
            />
        </div>
    </component>
</template>

<script setup>
import { computed, nextTick, ref } from 'vue';
import InitiativeSupport from '@/Components/InitiativeSupport/InitiativeSupport.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    groups: {
        type: Array,
        default: () => [],
    },
    digitalInitiativeOptions: {
        type: Array,
        default: () => [],
    },
    itInitiativeOptions: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
});

const isEditMode = ref(false);
const initiativeSupportRef = ref(null);
const filterCoe = ref('');

/**
 * Mengelompokkan dan mengurutkan data berdasarkan CoE secara otomatis.
 * Grup dengan CoE, Note, dan set IT Initiatives yang sama akan digabungkan.
 */
const processedGroups = computed(() => {
    if (!props.groups || props.groups.length === 0) return [];

    let filteredGroups = props.groups;
    
    if (filterCoe.value) {
        filteredGroups = props.groups.filter(group => {
            const coeName = group.digital_initiatives?.[0]?.coe_name || 'No CoE';
            return coeName === filterCoe.value;
        });
    }

    const mergedMap = new Map();

    filteredGroups.forEach(group => {
        // Ambil CoE dari initiative digital pertama sebagai representasi grup
        const coeName = group.digital_initiatives?.[0]?.coe_name || 'No CoE';
        const note = (group.note || '').trim().toLowerCase();
        const itIds = [...(group.it_initiatives || [])]
            .map(it => it.id)
            .sort((a, b) => a - b)
            .join(',');
        
        // Key unik berdasarkan kombinasi CoE + Note + Set IT Initiatives
        const key = `${coeName}|${note}|${itIds}`;

        if (mergedMap.has(key)) {
            const existing = mergedMap.get(key);
            
            // Tambahkan digital initiatives yang belum ada di grup ini
            group.digital_initiatives.forEach(di => {
                if (!existing.digital_initiatives.some(edi => edi.id === di.id)) {
                    existing.digital_initiatives.push({ ...di });
                }
            });
            
            // Gabungkan data mapping
            if (Array.isArray(group.mappings)) {
                existing.mappings.push(...group.mappings);
            }
            if (Array.isArray(group.mapping_ids)) {
                existing.mapping_ids.push(...group.mapping_ids);
            }
            existing.total_mappings = existing.mappings.length;
        } else {
            // Clone group untuk menghindari mutasi props langsung
            mergedMap.set(key, {
                ...JSON.parse(JSON.stringify(group)),
                group_key: key
            });
        }
    });

    // Urutkan berdasarkan nama CoE secara alfabetis
    return Array.from(mergedMap.values()).sort((a, b) => {
        // Ambil inisiatif digital pertama setelah diurutkan berdasarkan ID untuk perbandingan grup
        const getSortInfo = (group) => {
            const digitals = [...(group.digital_initiatives || [])].sort((left, right) => {
                return (left.id || 0) - (right.id || 0);
            });
            
            return {
                coe: (digitals[0]?.coe_name || 'No CoE').toLowerCase(),
                id: digitals[0]?.id || 0
            };
        };

        const infoA = getSortInfo(a);
        const infoB = getSortInfo(b);
        
        // 1. Urutkan berdasarkan CoE (tetap mengelompokkan CoE yang sama)
        if (infoA.coe !== infoB.coe) return infoA.coe.localeCompare(infoB.coe);

        // 2. Jika CoE sama, urutkan berdasarkan ID (urutan database)
        return infoA.id - infoB.id;
    });
});

const openAddSupport = async () => {
    if (!isEditMode.value) {
        isEditMode.value = true;
        await nextTick();
    }

    initiativeSupportRef.value?.openAddSupportModal?.({
        exitEditOnCancel: true,
    });
};

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (props.embedded ? {} : { title: 'Initiative Support' }));
</script>
