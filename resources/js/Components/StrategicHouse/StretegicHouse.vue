<script setup>
import { ref, computed } from 'vue';
import { EyeIcon, EyeSlashIcon, CalendarIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const props = defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    mappingBusinessStrategyGroups: {
        type: Array,
        default: () => [],
    },
    mappingBusinessStrategyColumns: {
        type: Array,
        default: () => [],
    },
    mappingBusinessStrategyOrganizationOptions: {
        type: Array,
        default: () => [],
    },
    roofSection: {
        type: Object,
        default: () => ({
            main_goal: null,
            main_goal_themes: [],
            side_goal: null,
        }),
    },
    technologyCards: {
        type: Array,
        default: () => [],
    },
    strategyCards: {
        type: Array,
        default: () => [],
    },
    foundationCard: {
        type: Object,
        default: null,
    },
    architectureCard: {
        type: Object,
        default: null,
    },
    tbcCard: {
        type: Object,
        default: null,
    },
    unassignedInitiatives: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();
const selectedSource = ref('all');
const showDetails = ref(false);
const showDtiStatusImplementation = ref(false);
const showGitsStatusImplementation = ref(false);
const showStrategyDetails = ref(true);
const showBusinessStrategy = ref(false);
const selectedBusinessUnit = ref('');
const selectedPeriod = ref(null);
const selectedGitsPeriod = ref('latest');

const dtiSelectedStatus = ref('');
const gitsSelectedStatus = ref('');

const monthsOrder = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const availableGitsPeriods = computed(() => {
    const periodSet = new Set();
    
    // Gather all unique month-year periods from all IT initiatives
    const gitsInitiatives = [
        ...props.strategyCards.flatMap(c => (c.initiatives || [])),
        ...(props.foundationCard?.initiatives || []),
        ...(props.architectureCard?.initiatives || []),
    ];

    gitsInitiatives.forEach(initiative => {
        if (initiative.statuses && Array.isArray(initiative.statuses)) {
            initiative.statuses.forEach(s => {
                const month = s.month || s.end || s.start;
                if (month && s.year) {
                    periodSet.add(`${month}-${s.year}`);
                }
            });
        }
    });
    
    const list = Array.from(periodSet).map(period => {
        const [month, year] = period.split('-');
        return { 
            label: `${month} ${year}`, 
            value: period, 
            month, 
            year: parseInt(year) 
        };
    }).sort((a, b) => {
        if (a.year !== b.year) return b.year - a.year;
        return monthsOrder.indexOf(b.month) - monthsOrder.indexOf(a.month);
    });

    return [{ label: 'Latest Status Update', value: 'latest' }, ...list];
});

const statusDesiredOrder = ['DF', 'Done', 'DT 2026', 'ITSBP', 'On Progress', 'On Review', 'SH'];
const gitsStatusDesiredOrder = ['On Track', 'At Risk', 'Done', 'Not Started', 'Not Signed',];

const normalizeStatusLabel = (rawStatus) => {
    const s = String(rawStatus ?? '').trim();
    if (!s) return null;
    const lower = s.toLowerCase();
    if (lower === 'df') return 'DF';
    if (lower === 'done') return 'Done';
    if (lower === 'dt 2026') return 'DT 2026';
    if (lower === 'itsbp') return 'ITSBP';
    if (lower === 'on progress' || lower === 'on progres') return 'On Progress';
    if (lower === 'on review') return 'On Review';
    if (lower === 'sh') return 'SH';
    return s;
};

const getGitsStatusColorClass = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase();
    if (normalized === 'on track') return 'status-color-itontrack';
    if (normalized === 'done') return 'status-color-itdone';
    if (normalized === 'at risk') return 'status-color-itatrisk';
    if (normalized === 'not started') return 'status-color-itnotstarted';
    if (normalized === 'not signed') return 'status-color-itnotsigned';
    return '';
};

const getStatusColorClass = (status, section = 'dti') => {
    const showStatus = section === 'gits' ? showGitsStatusImplementation.value : showDtiStatusImplementation.value;
    if (!showStatus) return 'status-color-neutral';
    
    if (section === 'gits') {
        return getGitsStatusColorClass(status) || 'status-color-neutral';
    }

    const s = normalizeStatusLabel(status);
    if (s === 'DF') return 'status-color-df';
    if (s === 'Done') return 'status-color-done';
    if (s === 'DT 2026') return 'status-color-dt2026';
    if (s === 'ITSBP') return 'status-color-itsbp';
    if (s === 'On Review') return 'status-color-onreview';
    if (s === 'On Progress') return 'status-color-onprogress';
    if (s === 'SH') return 'status-color-sh';
    return 'status-color-neutral';
};

const getInitiativeStatus = (initiative) => {
    // If it's an IT Initiative (tipe_initiative === 2), use its own period filter
    if (initiative.tipe_initiative === 2) {
        if (!initiative.statuses || !Array.isArray(initiative.statuses)) {
            return initiative.implementation_status;
        }

        if (selectedGitsPeriod.value === 'latest') {
            const sorted = [...initiative.statuses].sort((a, b) => {
                if (a.year !== b.year) return b.year - a.year;
                const monthA = a.month || a.end || a.start;
                const monthB = b.month || b.end || b.start;
                return monthsOrder.indexOf(monthB) - monthsOrder.indexOf(monthA);
            });
            const latest = sorted[0];
            return latest ? latest.status : initiative.implementation_status;
        }

        const found = initiative.statuses.find(s => {
            const month = s.month || s.end || s.start;
            return `${month}-${s.year}` === selectedGitsPeriod.value;
        });
        return found ? found.status : null;
    }

    // Default DTI logic using shared PMO period
    if (!selectedPeriod.value) {
        return initiative.implementation_status;
    }

    const found = (initiative.statuses || []).find(s => 
        s.start === selectedPeriod.value.start && 
        s.end === selectedPeriod.value.end && 
        s.year === selectedPeriod.value.year
    );

    return found ? found.status : null;
};

const statusLegend = computed(() => {
    const stats = {};
    statusDesiredOrder.forEach((label) => {
        stats[label] = 0;
    });

    const dtiFilter = (ini) => {
        const matchesSource = selectedSource.value === 'all' || ini.source == selectedSource.value;
        return matchesSource && matchesSelectedBusinessUnit(ini);
    };

    const allInitiatives = [
        ...props.technologyCards.flatMap(c => (c.initiatives || []).filter(dtiFilter)),
        ...(props.unassignedInitiatives || []).filter(dtiFilter),
    ];

    allInitiatives.forEach((initiative) => {
        const label = normalizeStatusLabel(getInitiativeStatus(initiative));
        if (label && stats.hasOwnProperty(label)) {
            stats[label]++;
        }
    });

    return statusDesiredOrder.map((label) => ({
        label,
        class: getStatusColorClass(label, 'dti'),
        count: stats[label],
    })).filter(item => item.count > 0);
});

const gitsStatusLegend = computed(() => {
    const stats = {};
    gitsStatusDesiredOrder.forEach((label) => {
        stats[label] = 0;
    });
    stats['Other'] = 0;

    const gitsInitiatives = [
        ...props.strategyCards.flatMap(c => (c.initiatives || [])),
        ...(props.foundationCard?.initiatives || []),
        ...(props.architectureCard?.initiatives || []),
    ];

    gitsInitiatives.forEach((initiative) => {
        const periodStatus = getInitiativeStatus(initiative);
        if (!periodStatus) return;

        const label = gitsStatusDesiredOrder.find(
            s => s.toLowerCase() === String(periodStatus).trim().toLowerCase()
        ) || 'Other';
        
        stats[label]++;
    });

    const legend = gitsStatusDesiredOrder.map((label) => ({
        label,
        class: getGitsStatusColorClass(label),
        count: stats[label],
    })).filter(item => item.count > 0);

    if (stats['Other'] > 0) {
        legend.push({
            label: 'Other',
            class: '',
            count: stats['Other']
        });
    }

    return legend;
});

const normalizeBusinessStrategyGroupKey = (value) => {
    const normalizedValue = String(value ?? '').trim().toLowerCase();

    if (!normalizedValue) return '';
    if (normalizedValue === 'subholding' || normalizedValue === 'sub holding') return 'subholding';
    if (normalizedValue === 'holding') return 'holding';
    if (normalizedValue === 'other' || normalizedValue === 'other organization') return 'other';

    return normalizedValue;
};

const businessStrategyOrganizationEntries = computed(() => {
    return (props.mappingBusinessStrategyOrganizationOptions || []).map((option) => {
        const label = String(option?.label ?? '');
        const [groupLabel = ''] = label.split(' - ');

        return {
            value: String(option?.value ?? ''),
            group_key: normalizeBusinessStrategyGroupKey(groupLabel),
        };
    });
});

const selectedBusinessUnitIds = computed(() => {
    if (!selectedBusinessUnit.value) return null;

    if (String(selectedBusinessUnit.value).startsWith('group:')) {
        const groupKey = normalizeBusinessStrategyGroupKey(String(selectedBusinessUnit.value).replace('group:', ''));

        return new Set(
            businessStrategyOrganizationEntries.value
                .filter((entry) => entry.group_key === groupKey)
                .map((entry) => entry.value)
                .filter(Boolean),
        );
    }

    return new Set([String(selectedBusinessUnit.value)]);
});

const matchesSelectedBusinessUnit = (initiative) => {
    if (!selectedBusinessUnitIds.value) return true;

    if (String(selectedBusinessUnit.value).startsWith('group:')) {
        const groupKey = normalizeBusinessStrategyGroupKey(String(selectedBusinessUnit.value).replace('group:', ''));
        const initiativeGroupKey = Number(initiative?.groub_id ?? 0) === 2
            ? 'subholding'
            : Number(initiative?.groub_id ?? 0) > 0
                ? 'holding'
                : '';

        if (initiativeGroupKey) {
            return initiativeGroupKey === groupKey;
        }
    }

    return selectedBusinessUnitIds.value.has(String(initiative?.business_unit_id ?? ''));
};

const filterDtiInitiatives = (initiatives) => {
    if (!initiatives) return [];

    return initiatives.filter((ini) => {
        const matchesSource = selectedSource.value === 'all' || ini.source == selectedSource.value;
        const periodStatus = getInitiativeStatus(ini);
        const implStatus = normalizeStatusLabel(periodStatus);
        const matchesStatus = !dtiSelectedStatus.value || implStatus === dtiSelectedStatus.value;
        
        // Fix: In DTI section, we only filter by period if a specific status filter from the legend is active.
        // This ensures the total initiative count remains correct even if some initiatives 
        // don't have a status record for the selected period.
        const matchesPeriod = !dtiSelectedStatus.value || periodStatus !== null;

        return matchesSource && matchesSelectedBusinessUnit(ini) && matchesStatus && matchesPeriod;
    });
};

const filterGitsInitiatives = (initiatives) => {
    if (!initiatives) return [];

    return initiatives.filter((ini) => {
        // Source and Business Unit filters are specifically ignored for GITS section
        const periodStatus = getInitiativeStatus(ini);
        
        const rawLabel = periodStatus ? String(periodStatus).trim() : null;
        let implStatus = null;
        
        if (rawLabel) {
            implStatus = gitsStatusDesiredOrder.find(
                s => s.toLowerCase() === rawLabel.toLowerCase()
            ) || 'Other';
        }

        const matchesStatus = !gitsSelectedStatus.value || implStatus === gitsSelectedStatus.value;
        
        // Fix: In GITS section, we only filter by period if a specific status filter from the legend is active.
        // This ensures the strategic structure (pillars) remains visible even if some initiatives 
        // don't have a status record for the selected period.
        const matchesPeriod = !gitsSelectedStatus.value || periodStatus !== null;

        return matchesStatus && matchesPeriod;
    });
};

const processedCards = (cards, initiativeFilter = (initiatives) => initiatives || []) => {
    return cards.map(card => {
        const filteredInis = initiativeFilter(card.initiatives || []);
        const previewInis = filteredInis.slice(0, 3);
        return {
            ...card,
            initiatives: filteredInis,
            initiatives_count: filteredInis.length,
            initiatives_preview: previewInis,
            remaining_initiatives_count: Math.max(0, filteredInis.length - previewInis.length),
            is_empty: filteredInis.length === 0
        };
    });
};

const filteredTechnologyCards = computed(() => processedCards(props.technologyCards, filterDtiInitiatives));
const filteredStrategyCards = computed(() => processedCards(props.strategyCards, filterGitsInitiatives));
const filteredUnassignedInitiatives = computed(() => filterDtiInitiatives(props.unassignedInitiatives || []));

const filteredFoundationCard = computed(() => {
    if (!props.foundationCard) return null;
    const card = props.foundationCard;
    const filteredInis = filterGitsInitiatives(card.initiatives || []);
    return {
        ...card,
        initiatives: filteredInis,
        initiatives_count: filteredInis.length,
    };
});

const filteredArchitectureCard = computed(() => {
    if (!props.architectureCard) return null;
    const card = props.architectureCard;
    const filteredInis = filterGitsInitiatives(card.initiatives || []);
    return {
        ...card,
        initiatives: filteredInis,
        initiatives_count: filteredInis.length,
    };
});

const unassignedCard = computed(() => {
    const filteredInis = filteredUnassignedInitiatives.value;
    if (filteredInis.length === 0) return null;

    const previewInis = filteredInis.slice(0, 3);
    return {
        name: 'unassigned',
        display_name: 'Not Classified',
        initiatives: filteredInis,
        initiatives_count: filteredInis.length,
        initiatives_preview: previewInis,
        remaining_initiatives_count: Math.max(0, filteredInis.length - previewInis.length),
        is_empty: false
    };
});

const filteredDtiInitiativesCount = computed(() => {
    const initiativeKeys = new Set();
    const initiativeGroups = [
        ...filteredTechnologyCards.value.map(card => card.initiatives || []),
        filteredUnassignedInitiatives.value,
    ];

    initiativeGroups.forEach((initiatives) => {
        initiatives.forEach((initiative) => {
            const key = initiative?.id ?? [initiative?.code, initiative?.name].filter(Boolean).join('::');
            if (key) {
                initiativeKeys.add(String(key));
            }
        });
    });

    return initiativeKeys.size;
});

const coeTooltip = (card) => {
    if (!card?.initiatives?.length) {
        return `${card?.display_name ?? 'CoE'}: -`;
    }

    const lines = card.initiatives.map((initiative) => initiative.label);

    return `${card.display_name} (${card.initiatives_count})\n${lines.join('\n')}`;
};

const initiativeSummaryHref = (initiative) => {
    const initiativeId = Number(initiative?.id ?? 0);
    return initiativeId > 0
        ? route('program-planning.program-definition.digital-initiatives.summary.index', initiativeId)
        : null;
};

const initiativeProjectCharterHref = (initiative) => {
    const projectId = Number(initiative?.mapped_project_id ?? initiative?.id ?? 0);
    return projectId > 0
        ? route('it-initiatives.show', { project: projectId, tab: 'charter' })
        : null;
};

const initiativeHoverTitle = (initiative) => {
    const description = String(initiative?.description ?? '').trim();
    const businessUnitName = String(initiative?.business_unit_name ?? '').trim();

    if (description) {
        return businessUnitName ? `(${businessUnitName}) ${description}` : description;
    }

    const code = String(initiative?.code ?? '').trim();
    const name = String(initiative?.name ?? '').trim();

    const fallbackLabel = [code, name].filter(Boolean).join(' - ') || String(initiative?.label ?? 'initiative').trim();

    return businessUnitName ? `(${businessUnitName}) ${fallbackLabel}` : fallbackLabel;
};

const orderedBusinessStrategyColumns = computed(() => {
    const preferredOrder = ['maximazing_value', 'expand', 'low_carbon'];
    const columnMap = new Map((props.mappingBusinessStrategyColumns || []).map((column) => [column.key, column]));

    return preferredOrder
        .map((key) => columnMap.get(key))
        .filter(Boolean);
});

const businessStrategyRows = computed(() => {
    return (props.mappingBusinessStrategyGroups || []).flatMap((group) => {
        return (group.rows || []).map((row) => ({
            ...row,
            group_key: normalizeBusinessStrategyGroupKey(row.group_key || group.key || row.group_label || group.label),
            group_label: row.group_label || group.label || '',
        }));
    });
});

const businessStrategyOptions = computed(() => {
    const seen = new Set();

    const rowOptions = businessStrategyRows.value
        .filter((row) => {
            const value = String(row.business_unit_id ?? '');

            if (!value || seen.has(value)) {
                return false;
            }

            seen.add(value);
            return true;
        })
        .map((row) => ({
            value: String(row.business_unit_id),
            label: row.group_label ? `${row.group_label} - ${row.business_unit}` : row.business_unit,
        }));

    return [
        { value: 'group:holding', label: 'All Holding' },
        { value: 'group:subholding', label: 'All Sub Holding' },
        ...rowOptions,
    ];
});

const filteredBusinessStrategyRows = computed(() => {
    if (!selectedBusinessUnitIds.value) {
        return businessStrategyRows.value;
    }

    return businessStrategyRows.value.filter(
        (row) => selectedBusinessUnitIds.value.has(String(row.business_unit_id ?? '')),
    );
});

const bulletIndentedStrategyColumns = new Set(['maximazing_value', 'expand', 'low_carbon']);
const shouldIndentStrategyColumn = (columnKey) => bulletIndentedStrategyColumns.has(String(columnKey ?? ''));
const isBulletLine = (line) => String(line ?? '').trimStart().startsWith('\u2022');
const splitStrategyValueLines = (value) => String(value ?? '').split(/\r?\n/);

const getBusinessUnitLogo = (row) => {
    if (row.business_unit_logo) return row.business_unit_logo;
    
    // Mapping manual berdasarkan nama BU jika logo tidak dikirim dari backend
    const buName = String(row.business_unit || '').trim();
    if (buName.includes('Upstream')) return '/icon/Upstream.png';
    if (buName.includes('Gas')) return '/icon/Gas.png';
    if (buName.includes('C&T') || buName.includes('Commercial')) return '/icon/C&T.png';
    if (buName.includes('R&P') || buName.includes('Refining')) return '/icon/R&P.png';
    if (buName.includes('PNRE')) return '/icon/PNRE.png';
    if (buName.includes('IML') || buName.includes('Logistics')) return '/icon/IML.png';
    if (buName.includes('APFS') || buName.includes('Financial')) return '/icon/APFS.png';
    
    return null;
};

const dtiInitiativeCountByBu = computed(() => {
    const counts = {};
    const seenInitiativesByBu = {};

    // Collect all DTI initiatives
    const allDti = [
        ...props.technologyCards.flatMap(c => c.initiatives || []),
        ...(props.unassignedInitiatives || [])
    ];

    allDti.forEach((ini) => {
        // Use same filtering logic as in filterDtiInitiatives (Source, Status, Period)
        // But exclude the global BU filter since we want counts for each BU row
        const matchesSource = selectedSource.value === 'all' || ini.source == selectedSource.value;
        const periodStatus = getInitiativeStatus(ini);
        const implStatus = normalizeStatusLabel(periodStatus);
        const matchesStatus = !dtiSelectedStatus.value || implStatus === dtiSelectedStatus.value;
        const matchesPeriod = !dtiSelectedStatus.value || periodStatus !== null;

        if (matchesSource && matchesStatus && matchesPeriod) {
            const buId = String(ini.business_unit_id ?? '');
            const iniKey = String(ini.id ?? [ini.code, ini.name].filter(Boolean).join('::'));
            
            if (buId && iniKey) {
                if (!seenInitiativesByBu[buId]) {
                    seenInitiativesByBu[buId] = new Set();
                }
                
                if (!seenInitiativesByBu[buId].has(iniKey)) {
                    seenInitiativesByBu[buId].add(iniKey);
                    counts[buId] = (counts[buId] || 0) + 1;
                }
            }
        }
    });

    return counts;
});

const getBusinessUnitInitiativeCount = (row) => {
    const buId = String(row.business_unit_id ?? '');
    return dtiInitiativeCountByBu.value[buId] || 0;
};
</script>

<template>
    <section class="sh-mockup">
        <div class="mockup-content">
            <div class="top-actions">
                <select
                    v-model="selectedBusinessUnit"
                    class="business-strategy-filter"
                >
                    <option value="">All Business Unit</option>
                    <option
                        v-for="option in businessStrategyOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
                <button
                    @click="showBusinessStrategy = !showBusinessStrategy"
                    class="dti-toggle"
                    :title="showBusinessStrategy ? 'Hide Business Strategy' : 'Show Business Strategy'"
                >
                    <EyeIcon v-if="showBusinessStrategy" class="dti-toggle-icon" />
                    <EyeSlashIcon v-else class="dti-toggle-icon" />
                </button>
            </div>

            <!-- ═══ ROOF: Focus Bands (Maximize Legacy Business + Build Low Carbon) ═══ -->
            <div class="roof-section">
                <div class="roof-headline">{{ page.headline }}</div>
            </div>

            <!-- ═══ CONNECTOR: small decorative chain ═══ -->
            <div class="business-strategy-panel">
                <div class="business-strategy-table-wrap">
                    <div class="business-strategy-table-scroll">
                        <table class="strategy-table">
                            <colgroup>
                                <col class="business-strategy-col business-strategy-col--business-unit" />
                                <col v-if="orderedBusinessStrategyColumns.length > 1" :span="orderedBusinessStrategyColumns.length - 1" class="business-strategy-col business-strategy-col--legacy" />
                                <col class="business-strategy-col business-strategy-col--carbon" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th rowspan="2" class="head-cell head-cell--business-unit"></th>
                                    <th v-if="orderedBusinessStrategyColumns.length > 1" :colspan="orderedBusinessStrategyColumns.length - 1" class="head-cell">
                                        <div class="strategy-head-card strategy-head-card--legacy">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="strategy-head-card__title">{{ roofSection.main_goal?.title ?? page.headline }}</span>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="roofSection.side_goal" rowspan="2" class="head-cell head-cell--carbon">
                                        <div class="strategy-head-card strategy-head-card--carbon">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="strategy-head-card__title">{{ roofSection.side_goal.title }}</span>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                                <tr v-if="orderedBusinessStrategyColumns.length > 1">
                                    <th v-for="column in orderedBusinessStrategyColumns.filter(c => c.key !== 'low_carbon')" :key="column.key" class="head-cell">
                                        <div class="strategy-head-card strategy-head-card--legacy-child">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>{{ column.label }}</span>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="showBusinessStrategy">
                                <tr v-for="row in filteredBusinessStrategyRows" :key="row.id">
                                    <td class="strategy-table__cell primary-cell">
                                        <div class="primary-cell__content">
                                            <div class="primary-logo-wrapper" v-if="getBusinessUnitLogo(row)">
                                                <img :src="getBusinessUnitLogo(row)" :alt="`${row.business_unit} logo`" class="primary-business-unit-logo">
                                            </div>
                                            <div class="primary-label-wrapper">
                                                <span class="primary-business-unit-name">{{ row.business_unit }}</span>
                                                <span v-if="getBusinessUnitInitiativeCount(row) > 0" class="bu-count-capsule">
                                                    {{ getBusinessUnitInitiativeCount(row) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        v-for="column in orderedBusinessStrategyColumns"
                                        :key="`${row.id}-${column.key}`"
                                        class="strategy-cell"
                                    >
                                        <p v-if="row.values?.[column.key]" class="strategy-cell__value">
                                            <span
                                                v-for="(line, lineIndex) in splitStrategyValueLines(row.values[column.key])"
                                                :key="`${row.id}-${column.key}-line-${lineIndex}`"
                                                class="strategy-cell__line"
                                                :class="{
                                                    'strategy-cell__line--bullet': shouldIndentStrategyColumn(column.key) && isBulletLine(line),
                                                }"
                                            >
                                                {{ line }}
                                            </span>
                                        </p>
                                        <p v-else class="strategy-cell__empty">
                                            -
                                        </p>
                                    </td>
                                </tr>
                                <tr v-if="!filteredBusinessStrategyRows.length">
                                    <td
                                        :colspan="orderedBusinessStrategyColumns.length + 1"
                                        class="business-strategy-table__blank"
                                    >
                                        Belum ada data business strategy.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="connector-chain">
                <img src="/chain-strategic-house.png" alt="Chain" class="w-7 h-auto" />
            </div>

            <!-- ═══ VISION TRAPEZOID ═══ -->
            <div class="vision-trapezoid">
                <p class="vision-title">{{ page.visionTitle }}:</p>
                <p class="vision-text">{{ page.visionText }}</p>
            </div>

            <!-- ═══ CONNECTOR ═══ -->
            <div class="connector-chain">
                <img src="/chain-strategic-house.png" alt="Chain" class="w-7 h-auto" />
            </div>

            
            
            <!-- ═══ DIGITAL TRANSFORMATION INITIATIVES SECTION ═══ -->
            <div class="dti-section" :class="{ 'dti-section--compact': !showDetails }">
                <!-- Status Implementation Legend -->
                <div
                    v-if="showDtiStatusImplementation"
                    class="animate-fade-in-up"
                >
                    <div class="status-legend-container">
                        <div class="status-legend-header">
                            <span class="status-legend-title">Implementation Status (Digital):</span>
                            <select 
                                v-if="statusPeriods && statusPeriods.length > 0" 
                                v-model="selectedPeriod" 
                                class="status-period-select"
                            >
                                <option :value="null">All (Latest)</option>
                                <option v-for="period in statusPeriods" :key="period.label" :value="period">
                                    {{ period.label }}
                                </option>
                            </select>
                            <span v-else class="status-period-fallback">(November - Desember 2025):</span>
                        </div>
                        <div class="status-legend-items">
                            <div
                                v-for="status in statusLegend"
                                :key="`status-legend-${status.label}`"
                                class="status-legend-item"
                                :class="{ 'status-legend-item--inactive': dtiSelectedStatus && dtiSelectedStatus !== status.label }"
                                @click="dtiSelectedStatus = dtiSelectedStatus === status.label ? '' : status.label"
                                :title="`Filter: ${status.label}`"
                            >
                                <span
                                    class="status-swatch"
                                    :class="status.class"
                                ></span>
                                <span class="status-label">
                                    {{ status.label }} <span class="status-count">({{ status.count }})</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dti-header">
                    <div class="dti-header-copy">
                        <p class="dti-count">{{ filteredDtiInitiativesCount }} Digital transformation
                            initiatives</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <select v-if="showDetails" v-model="selectedSource" class="appearance-none rounded-lg border border-slate-300 bg-white/90 px-2 py-1.5 text-[10px] font-bold text-slate-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800/90 dark:text-slate-200">
                            <option value="all">All Initiatives</option>
                            <option value="3">Baseline RSTI 2025-2029</option>
                            <option value="4">New Initiative 2026</option>
                        </select>
                        <button @click="showDtiStatusImplementation = !showDtiStatusImplementation" class="dti-toggle" :class="{ 'dti-toggle--active': !showDtiStatusImplementation }" :title="showDtiStatusImplementation ? 'Hide Status Implementation' : 'Show Status Implementation'">
                            <CalendarIcon class="dti-toggle-icon" />
                        </button>
                        <button @click="showDetails = !showDetails" class="dti-toggle" :title="showDetails ? 'Hide Filters & Counts' : 'Show Filters & Counts'">
                            <EyeIcon v-if="showDetails" class="dti-toggle-icon" />
                            <EyeSlashIcon v-else class="dti-toggle-icon" />
                        </button>
                    </div>
                </div>

                <div class="dti-cards" :class="{ 'dti-cards--hidden': !showDetails }">
                    <div v-for="card in filteredTechnologyCards" :key="card.name" class="dti-card dti-card--compact">
                        <div v-if="showDetails" class="dti-card-badge" :title="coeTooltip(card)">
                            {{ card.initiatives_count }}
                        </div>
                        <div class="dti-card-content">
                            <div class="dti-card-title">{{ card.display_name }}</div>
                            <div v-if="showDetails" class="dti-card-list-wrapper">
                                <ul v-if="card.initiatives?.length" class="dti-card-list">
                                    <li v-for="(ini, idx) in card.initiatives" :key="`${card.name}-ini-${idx}`" class="dti-card-list-item">
                                        <Link
                                            v-if="initiativeSummaryHref(ini)"
                                            :href="initiativeSummaryHref(ini)"
                                            class="initiative-link initiative-link--dti"
                                            :title="initiativeHoverTitle(ini)"
                                        >
                                            <span v-if="showDetails" class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini))">{{ ini.code }}</span>
                                            <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                        </Link>
                                        <template v-else>
                                            <span v-if="showDetails" class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini))">{{ ini.code }}</span>
                                            <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                        </template>
                                    </li>
                                </ul>
                                <div v-else class="dti-card-list-empty">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Not Classified Card -->
                    <div v-if="unassignedCard" class="dti-card dti-card--compact">
                        <div v-if="showDetails" class="dti-card-badge" :title="coeTooltip(unassignedCard)">
                            {{ unassignedCard.initiatives_count }}
                        </div>
                        <div class="dti-card-content">
                            <div class="dti-card-title">{{ unassignedCard.display_name }}</div>
                            <div v-if="showDetails" class="dti-card-list-wrapper">
                                <ul v-if="unassignedCard.initiatives?.length" class="dti-card-list">
                                    <li v-for="(ini, idx) in unassignedCard.initiatives" :key="`${unassignedCard.name}-ini-${idx}`" class="dti-card-list-item">
                                        <Link
                                            v-if="initiativeSummaryHref(ini)"
                                            :href="initiativeSummaryHref(ini)"
                                            class="initiative-link initiative-link--dti"
                                            :title="initiativeHoverTitle(ini)"
                                        >
                                            <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini))">{{ ini.code }}</span>
                                            <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                        </Link>
                                        <template v-else>
                                            <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini))">{{ ini.code }}</span>
                                            <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                        </template>
                                    </li>
                                </ul>
                                <div v-else class="dti-card-list-empty">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ CONNECTOR ═══ -->
            <div class="connector-chain">
                <img src="/chain-strategic-house.png" alt="Chain" class="w-7 h-auto" />
            </div>

            <!-- ═══ GRAND IT STRATEGY SECTION ═══ -->
            <div class="gits-section">
                <!-- Status Implementation Legend for GITS -->
                <div v-if="showGitsStatusImplementation" class="mb-4 animate-fade-in-up">
                    <div class="status-legend-container">
                        <div class="status-legend-header">
                            <span class="status-legend-title">Review Implementation Status (IT):</span>
                            <select 
                                v-model="selectedGitsPeriod" 
                                class="status-period-select"
                            >
                                <option v-for="period in availableGitsPeriods" :key="period.value" :value="period.value">
                                    {{ period.label }}
                                </option>
                            </select>
                        </div>
                        <div class="status-legend-items">
                            <div
                                v-for="status in gitsStatusLegend"
                                :key="`gits-status-legend-${status.label}`"
                                class="status-legend-item"
                                :class="{ 'status-legend-item--inactive': gitsSelectedStatus && gitsSelectedStatus !== status.label }"
                                @click="gitsSelectedStatus = gitsSelectedStatus === status.label ? '' : status.label"
                                :title="`Filter: ${status.label}`"
                            >
                                <span
                                    class="status-swatch"
                                    :class="status.class"
                                ></span>
                                <span class="status-label">
                                    {{ status.label }} <span class="status-count">({{ status.count }})</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gits-header">
                    <div class="gits-header-content">
                        <h2 class="gits-title">{{ page.grandStrategyTitle }}</h2>
                        <p class="gits-subtitle">{{ page.grandStrategyText }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="showGitsStatusImplementation = !showGitsStatusImplementation" class="dti-toggle" :class="{ 'dti-toggle--active': !showGitsStatusImplementation }" :title="showGitsStatusImplementation ? 'Hide Status Implementation' : 'Show Status Implementation'">
                            <CalendarIcon class="dti-toggle-icon" />
                        </button>
                        <button @click="showStrategyDetails = !showStrategyDetails" class="dti-toggle" :title="showStrategyDetails ? 'Show Initiatives' : 'Show Descriptions'">
                            <EyeIcon v-if="!showStrategyDetails" class="dti-toggle-icon" />
                            <EyeSlashIcon v-else class="dti-toggle-icon" />
                        </button>
                    </div>
                </div>

                <div class="gits-pillars" :class="{ 'gits-pillars--hidden': !showStrategyDetails }">
                    <article v-for="card in filteredStrategyCards" :key="card.name" class="gits-pillar">
                        <div v-if="!showStrategyDetails && card.initiatives_count > 0" class="gits-capsule-badge" :title="coeTooltip(card)">
                            {{ card.initiatives_count }}
                        </div>
                        <h3 class="gits-pillar-title">{{ card.display_name }}</h3>
                        
                        <!-- Description View -->
                        <div v-if="showStrategyDetails" class="gits-pillar-desc">
                            <template v-if="card.description_lines?.length">
                                <p v-for="(line, lineIndex) in card.description_lines" :key="`${card.name}-desc-${lineIndex}`">
                                    {{ line }}
                                </p>
                            </template>
                            <template v-else-if="card.initiatives_preview?.length">
                                <div v-for="(ini, idx) in card.initiatives_preview" :key="`${card.name}-prev-${idx}`">
                                    <Link
                                        v-if="initiativeProjectCharterHref(ini)"
                                        :href="initiativeProjectCharterHref(ini)"
                                        class="initiative-link initiative-link--gits mb-1"
                                        :title="initiativeHoverTitle(ini)"
                                    >
                                        {{ ini.label || ini.name }}
                                    </Link>
                                    <p v-else class="mb-1">{{ ini.label || ini.name }}</p>
                                </div>
                            </template>
                            <p v-if="card.is_empty" class="gits-pillar-empty">
                                - yang terhubung ke area ini.
                            </p>
                        </div>

                        <!-- Initiatives View (Visible when showStrategyDetails is false) -->
                        <div v-else class="gits-pillar-list-wrapper">
                            <ul v-if="card.initiatives?.length" class="gits-pillar-list">
                                <li v-for="(ini, idx) in card.initiatives" :key="`${card.name}-ini-${idx}`" class="gits-pillar-list-item">
                                    <Link
                                        v-if="initiativeProjectCharterHref(ini)"
                                        :href="initiativeProjectCharterHref(ini)"
                                        class="initiative-link initiative-link--gits"
                                        :title="initiativeHoverTitle(ini)"
                                    >
                                        <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini), 'gits')">{{ ini.code }}</span>
                                        <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                    </Link>
                                    <template v-else>
                                        <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini), 'gits')">{{ ini.code }}</span>
                                        <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                    </template>
                                </li>
                            </ul>
                            <div v-else class="gits-pillar-list-empty">
                                -
                            </div>
                        </div>
                    </article>
                </div>

                <!-- ═══ FOUNDATION BAR ═══ -->
                <div v-if="filteredFoundationCard" class="foundation-bar" :class="{ 'foundation-bar--expanded': !showStrategyDetails }">
                    <div class="foundation-header">
                        <span class="foundation-title">{{ filteredFoundationCard.display_name }}<template v-if="showStrategyDetails">:</template></span>
                        <div v-if="!showStrategyDetails && filteredFoundationCard.initiatives_count > 0" class="gits-capsule-badge gits-capsule-badge--dark">
                            {{ filteredFoundationCard.initiatives_count }}
                        </div>
                    </div>
                    
                    <span v-if="showStrategyDetails" class="foundation-desc">Memungkinkan pelaksanaan efektif dari semua digital
                        dan IT initiative</span>
                    
                    <div v-else class="gits-pillar-list-wrapper">
                        <ul v-if="filteredFoundationCard.initiatives?.length" class="gits-pillar-list gits-pillar-list--horizontal">
                            <li v-for="(ini, idx) in filteredFoundationCard.initiatives" :key="`foundation-ini-${idx}`" class="gits-pillar-list-item">
                                <Link
                                    v-if="initiativeProjectCharterHref(ini)"
                                    :href="initiativeProjectCharterHref(ini)"
                                    class="initiative-link initiative-link--gits"
                                    :title="initiativeHoverTitle(ini)"
                                >
                                    <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini), 'gits')">{{ ini.code }}</span>
                                    <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                </Link>
                                <template v-else>
                                    <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini), 'gits')">{{ ini.code }}</span>
                                    <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                </template>
                            </li>
                        </ul>
                        <div v-else class="gits-pillar-list-empty">
                            -
                        </div>
                    </div>
                </div>

                <!-- ═══ ARCHITECTURE BAR ═══ -->
                <div v-if="!showStrategyDetails && filteredArchitectureCard" class="architecture-bar">
                    <div class="architecture-header">
                        <span class="architecture-title">{{ filteredArchitectureCard.display_name }}</span>
                        <div v-if="filteredArchitectureCard.initiatives_count > 0" class="gits-capsule-badge gits-capsule-badge--dark">
                            {{ filteredArchitectureCard.initiatives_count }}
                        </div>
                    </div>
                    
                    <div class="gits-pillar-list-wrapper">
                        <ul v-if="filteredArchitectureCard.initiatives?.length" class="gits-pillar-list gits-pillar-list--horizontal">
                            <li v-for="(ini, idx) in filteredArchitectureCard.initiatives" :key="`architecture-ini-${idx}`" class="gits-pillar-list-item">
                                <Link
                                    v-if="initiativeProjectCharterHref(ini)"
                                    :href="initiativeProjectCharterHref(ini)"
                                    class="initiative-link initiative-link--gits"
                                    :title="initiativeHoverTitle(ini)"
                                >
                                    <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini), 'gits')">{{ ini.code }}</span>
                                    <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                </Link>
                                <template v-else>
                                    <span class="initiative-code-tag" :class="getStatusColorClass(getInitiativeStatus(ini), 'gits')">{{ ini.code }}</span>
                                    <span :title="initiativeHoverTitle(ini)">{{ ini.name || ini.label }}</span>
                                </template>
                            </li>
                        </ul>
                        <div v-else class="gits-pillar-list-empty">
                            -
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ─── MOCKUP WRAPPER ─── */
.sh-mockup {
    border: 1px solid #d9e2ec;
    border-radius: 28px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.mockup-header {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 24px 16px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.mockup-eyebrow {
    margin: 0 0 8px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #2563eb;
}

.mockup-content {
    padding: 24px;
}

.top-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.business-strategy-filter {
    min-width: 220px;
    max-width: 320px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 28px 6px 10px;
    appearance: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 12px;
}

.business-strategy-filter:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.business-strategy-panel {
    width: 90%;
    margin-left: 0;
    margin-right: auto;
    border-radius: 12px;
}


.business-strategy-table-wrap {
    margin-top: 14px;
}

.business-strategy-table-scroll {
    overflow-x: auto;
    background: #fff;
}

.strategy-table {
    width: 100%;
    min-width: 920px;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    background: #ffffff;
}

.strategy-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.strategy-table thead th {
    border: 0;
    background: transparent;
    padding: 0 4px 8px;
    vertical-align: stretch;
}

.business-strategy-col--business-unit {
    width: 11.11%;
}

.business-strategy-col--legacy {
    width: calc((88.89% - 260px) / 2);
}

.business-strategy-col--carbon {
    width: 260px;
}

.head-cell--business-unit {
    width: 11.11%;
}

.head-cell--carbon {
    width: 260px;
}

.strategy-head-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 100%;
    height: 100%;
    border: 1px solid #c5d6e8;
    border-radius: 10px;
    padding: 12px 16px;
    text-align: center;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.strategy-head-card__title {
    display: block;
    line-height: 1.2;
}

.strategy-head-card--legacy {
    min-height: 40px;
    background: #e8eff8;
    color: #1a2a3a;
    font-size: 15px;
    font-weight: 700;
}

.strategy-head-card--legacy-child {
    min-height: 34px;
    background: #e8eff8;
    color: #2a4a6a;
    font-size: 13px;
    font-weight: 700;
}

.strategy-head-card--carbon {
    min-height: 84px;
    border-color: #2f5596;
    background: linear-gradient(180deg, #3b64a8 0%, #2f5596 100%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
}

.strategy-table tbody th,
.strategy-table td {
    vertical-align: top;
}

.strategy-table__cell {
    padding: 10px 12px;
    font-size: 11px;
    line-height: 1.45;
}

.business-strategy-col--legacy {
    width: calc((100% - 260px) / 2);
}

.business-strategy-col--carbon {
    width: 260px;
}

.strategy-cell {
    width: auto;
    min-width: 0;
    padding: 8px 10px;
    background: #ffffff;
    border: 1px solid #c7d2de;
}

.strategy-cell__value {
    font-size: 11px;
    font-weight: 400;
    line-height: 1.45;
    color: #1f2937;
    margin: 0;
    word-break: break-word;
    text-align: left;
}

.strategy-cell__line {
    display: block;
    white-space: break-spaces;
    tab-size: 4;
}

.strategy-cell__line--bullet {
    padding-left: 14px;
    text-indent: -14px;
}

.strategy-cell__empty {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    font-style: italic;
    margin: 0;
    text-align: left;
}

.business-strategy-table__empty,
.business-strategy-table__blank {
    color: #7a8da3;
    font-style: italic;
}

.business-strategy-table__blank {
    text-align: center;
    padding: 18px 12px;
}

.primary-cell {
    width: auto;
    min-width: 0;
    background: #f8fbff;
    vertical-align: middle !important;
}

.primary-cell__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 10px;
    height: 100%;
    justify-content: center;
    color: #1e293b;
    text-align: center;
}

.primary-logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 40px;
}

.primary-business-unit-logo {
    display: block;
    max-width: 120px;
    max-height: 42px;
    width: auto;
    height: auto;
    object-fit: contain;
}

.primary-label-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    flex-wrap: wrap;
}

.primary-business-unit-name {
    font-size: 11px;
    font-weight: 800;
    line-height: 1.2;
}

.bu-count-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background: rgba(15, 111, 183, 0.1);
    border: 1px solid rgba(15, 111, 183, 0.2);
    font-size: 9px;
    font-weight: 800;
    color: #0f6fb7;
    flex-shrink: 0;
}

/* ─── ROOF SECTION ─── */
.roof-section {
    margin-bottom: 0;
}

.roof-headline {
    margin-bottom: 8px;
    text-align: center;
    font-size: 15px;
    font-weight: 800;
    color: #1a2a3a;
}

.roof-top {
    display: flex;
    gap: 12px;
    align-items: stretch;
    width: 80%;
    margin: 0 auto;
}

.roof-main {
    flex: 1;
    text-align: center;
}

.roof-side {
    width: 260px;
    display: flex;
}

.roof-main-label {
    background: #e8eff8;
    border: 1px solid #c5d6e8;
    padding: 2px 24px;
    font-size: 14px;
    font-weight: 600;
    color: #1a2a3a;
    border-radius: 4px;
}

.roof-sub-items {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.roof-sub-item {
    flex: 1;
    background: #e8eff8;
    border: 1px solid #c5d6e8;
    padding: 2px 16px;
    font-size: 12px;
    font-weight: 500;
    color: #2a4a6a;
    text-align: center;
    border-radius: 4px;
}

.roof-side-label {
    width: 100%;
    background: linear-gradient(180deg, #3b64a8 0%, #2f5596 100%);
    color: #fff;
    border-radius: 10px;
    padding: 2px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
}

/* ─── CONNECTORS ─── */
.connector-chain {
    display: flex;
    justify-content: center;
}

/* ─── VISION TRAPEZOID ─── */
.vision-trapezoid {
    background: linear-gradient(180deg, #1e6dc0 0%, #184f96 100%);
    color: #fff;
    text-align: center;
    padding: 17px 40px;
    clip-path: polygon(10% 0%, 90% 0%, 100% 100%, 0% 100%);
    border-radius: 6px;
}

.vision-title {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.02em;
}

.vision-text {
    font-size: 12px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.92);
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

/* ─── DIGITAL TRANSFORMATION INITIATIVES ─── */
.dti-section {
    background: #b8d4f0;
    border-radius: 6px;
    padding: 20px 20px 24px;
    transition: all 0.3s ease-in-out;
}

.dti-section--compact {
    padding: 12px 20px 16px;
}

.dti-header {
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    transition: all 0.3s ease;
}

.dti-section--compact .dti-header {
    margin-bottom: 10px;
}

.dti-header-copy {
    flex: 1;
    text-align: center;
}

.dti-count {
    font-size: 16px;
    font-weight: 700;
    color: #0d2a4a;
    display: inline;
}

.dti-label {
    font-size: 16px;
    font-weight: 700;
    color: #0d2a4a;
    display: inline;
}

.dti-header p {
    display: inline;
}

.dti-toggle {
    border: 1px solid rgba(13, 42, 74, 0.16);
    background: rgba(255, 255, 255, 0.9);
    color: #184f96;
    border-radius: 999px;
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.dti-toggle:hover {
    background: #fff;
    border-color: rgba(24, 79, 150, 0.32);
}

.dti-toggle-icon {
    width: 18px;
    height: 18px;
    stroke-width: 1.8;
}

.dti-cards {
    display: grid;
    grid-auto-columns: 1fr;
    grid-auto-flow: column;
    gap: 10px;
    transition: all 0.3s ease-in-out;
}

.dti-cards--hidden {
    align-items: start;
    grid-auto-rows: min-content;
    gap: 8px;
}

.dti-card {
    background: #fff;
    border: 1.5px solid #3b82c8;
    border-radius: 4px;
    padding: 12px 12px 10px;
    text-align: left;
    color: #184f96;
    position: relative;
    min-height: auto;
    height: auto;
    transition: all 0.3s ease-in-out;
}

.dti-cards--hidden .dti-card {
    min-height: 0;
    height: auto;
    align-self: start;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

:not(.dti-cards--hidden) .dti-card {
    height: 100%;
}

.dti-card--compact {
    min-height: auto;
    display: flex;
    align-items: center;
}

/* When showing details, increase height to fit the list */
:not(.dti-cards--hidden) .dti-card--compact {
    align-items: flex-start;
    padding-top: 10px;
}

.dti-cards--hidden .dti-card--compact {
    display: block;
    height: min-content;
}

.dti-card-content {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.dti-card--compact .dti-card-title {
    margin-top: 0;
}

/* Title adjustments when list is visible */
:not(.dti-cards--hidden) .dti-card-title {
    margin-top: 4px;
    padding-right: 36px;
}

.dti-card-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    min-width: 26px;
    height: 26px;
    border-radius: 999px;
    background: #2f5596;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 8px;
    box-shadow: 0 4px 12px rgba(47, 85, 150, 0.16);
    z-index: 10;
}

.dti-card-title {
    padding-right: 42px;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
    margin-top: 12px;
    width: 100%;
    transition: all 0.2s ease;
}

.dti-cards--hidden .dti-card-title {
    padding-right: 0;
    margin-top: 0;
    margin-bottom: 0;
    line-height: 1.2;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Removed redundant selector */

.dti-card-list-wrapper {
    margin-top: 8px;
    border-top: 1px dashed rgba(59, 130, 200, 0.4);
    padding-top: 8px;
    width: 100%;
    max-height: 120px;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
}

.dti-card-list {
    overflow-y: auto;
    max-height: 110px;
    padding-right: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.dti-card-list-item {
    font-size: 10px;
    line-height: 1.3;
    color: #365780;
    margin-bottom: 4px;
    padding: 2px 4px;
    background: rgba(59, 130, 200, 0.05);
    border-radius: 2px;
}

.initiative-link {
    display: block;
    color: inherit;
    text-decoration: none;
    transition: opacity 0.2s ease, text-decoration-color 0.2s ease;
}

.initiative-link:hover {
    text-decoration: underline;
}

.initiative-link:focus-visible {
    outline: 2px solid currentColor;
    outline-offset: 2px;
    border-radius: 3px;
}

.initiative-link--dti:hover {
    opacity: 0.8;
}

.dti-card-list-empty {
    font-size: 10px;
    font-style: italic;
    color: #6b85a8;
    text-align: center;
    padding-top: 10px;
}

/* Custom scrollbar for initiative list */
.dti-card-list::-webkit-scrollbar {
    width: 3px;
}
.dti-card-list::-webkit-scrollbar-track {
    background: rgba(59, 130, 200, 0.05);
}
.dti-card-list::-webkit-scrollbar-thumb {
    background: rgba(47, 85, 150, 0.3);
    border-radius: 3px;
}
.dti-card-list::-webkit-scrollbar-thumb:hover {
    background: rgba(47, 85, 150, 0.5);
}

.dti-card-preview {
    margin-top: 10px;
    border-top: 1px dashed rgba(59, 130, 200, 0.35);
    padding-top: 8px;
}

.dti-card-preview-item,
.dti-card-preview-more,
.dti-card-preview-empty {
    font-size: 10px;
    line-height: 1.35;
}

.dti-card-preview-item {
    color: #365780;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dti-card-preview-more {
    color: #184f96;
    font-weight: 600;
}

.dti-card-preview-empty {
    color: #6b85a8;
    font-style: italic;
}

/* ─── GRAND IT STRATEGY SECTION ─── */
.gits-section {
    background: #dde8f4;
    border-radius: 6px;
    padding: 24px 20px;
    transition: all 0.3s ease-in-out;
}

.gits-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
}

.gits-header-content {
    flex: 1;
    text-align: center;
}

.gits-title {
    font-size: 17px;
    font-weight: 700;
    color: #0d2a4a;
}

.gits-subtitle {
    font-size: 15px;
    color: #3a5a7a;
}

.gits-pillars {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
    margin-bottom: 16px;
    transition: all 0.3s ease-in-out;
}

.gits-pillar {
    background: linear-gradient(180deg, #2567a8 0%, #184f96 100%);
    border-radius: 10px;
    padding: 16px 14px;
    color: #fff;
    display: flex;
    flex-direction: column;
    min-height: auto;
    height: 100%;
    transition: all 0.3s ease-in-out;
    position: relative;
}

:not(.gits-pillars--hidden) .gits-pillar {
    min-height: 120px;
}

.gits-pillars--hidden .gits-pillar {
    min-height: auto;
    justify-content: center;
    padding: 10px 14px;
}

.gits-pillar-title {
    font-size: 14px;
    text-align: center;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}

.gits-pillars--hidden .gits-pillar-title {
    margin-bottom: 0;
}

.gits-pillar-desc {
    font-size: 10px;
    text-align: center;
    line-height: 1;
    color: rgba(255, 255, 255, 0.85);
    flex: 1;
    transition: all 0.3s ease-in-out;
}

.gits-pillar-desc p {
    margin-bottom: 4px;
}

.gits-pillar-list-wrapper {
    margin-top: 8px;
    border-top: 1px dashed rgba(255, 255, 255, 0.3);
    padding-top: 8px;
    width: 100%;
    max-height: 120px;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
}

.gits-pillar-list {
    overflow-y: auto;
    max-height: 110px;
    padding-right: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.gits-pillar-list-item {
    font-size: 10px;
    line-height: 1.3;
    color: #fff;
    margin-bottom: 4px;
    padding: 2px 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    text-align: left;
}

.initiative-link--gits:hover {
    opacity: 0.85;
}

.gits-pillar-list-empty {
    font-size: 10px;
    font-style: italic;
    color: rgba(255, 255, 255, 0.6);
    text-align: center;
    padding-top: 10px;
}

/* Custom scrollbar for gits initiative list */
.gits-pillar-list::-webkit-scrollbar {
    width: 3px;
}
.gits-pillar-list::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.gits-pillar-list::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}
.gits-pillar-list::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

.gits-pillar-empty {
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
}

/* Capsule Badge for GITS */
.gits-capsule-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #ffffff;
    color: #184f96;
    font-size: 10px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 999px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 10;
}

.gits-capsule-badge--dark {
    background: #ffffff;
    color: #184f96;
    position: static;
    display: inline-block;
    margin-left: 8px;
    vertical-align: middle;
}

.gits-pillar-list--horizontal {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
    max-height: none;
    overflow: visible;
}

.gits-pillar-list--horizontal .gits-pillar-list-item {
    margin-bottom: 0;
    white-space: nowrap;
}

/* ─── FOUNDATION BAR ─── */
.foundation-bar {
    background: linear-gradient(90deg, #1b4f93 0%, #215da8 50%, #1b4f93 100%);
    border-radius: 8px;
    padding: 14px 24px;
    color: #fff;
    text-align: center;
    line-height: 1.5;
    transition: all 0.3s ease-in-out;
}

.foundation-header {
    display: flex;
    align-items: center;
    justify-content: center;
}

.foundation-bar--expanded {
    padding: 16px 24px;
}

.foundation-title {
    font-weight: 700;
    font-size: 13px;
    margin-right: 4px;
}

.foundation-desc {
    color: rgba(255, 255, 255, 0.85);
    font-size: 10px;
}

/* ─── ARCHITECTURE BAR ─── */
.architecture-bar {
    margin-top: 10px;
    background: linear-gradient(90deg, #1b4f93 0%, #215da8 50%, #1b4f93 100%);
    border-radius: 8px;
    padding: 12px 24px;
    color: #fff;
    text-align: center;
    line-height: 1.5;
    transition: all 0.3s ease-in-out;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.architecture-header {
    display: flex;
    align-items: center;
    justify-content: center;
}

.architecture-title {
    font-weight: 700;
    font-size: 13px;
    margin-right: 4px;
}

/* ─── RESPONSIVE ─── */
@media (max-width: 1024px) {
    .gits-pillars {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .roof-top {
        flex-direction: column;
        width: 90%;
    }

    .roof-side {
        width: 100%;
    }

    .dti-header,
    .gits-header,
    .business-strategy-panel__header {
        flex-direction: column;
        align-items: stretch;
    }

    .dti-header-copy,
    .gits-header-content {
        text-align: center;
    }

    .dti-toggle {
        align-self: center;
    }

    .top-actions {
        justify-content: center;
        flex-wrap: wrap;
    }

    .business-strategy-filter {
        width: 100%;
        max-width: none;
    }

    .business-strategy-table {
        min-width: 760px;
    }

    .business-strategy-col--business-unit {
        width: 200px;
    }

    .business-strategy-col--legacy {
        width: calc((100% - 420px) / 2);
    }

    .business-strategy-col--carbon {
        width: 220px;
    }

    .business-strategy-panel {
        width: 90%;
    }

    .dti-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .gits-pillars {
        grid-template-columns: repeat(2, 1fr);
    }

    .mockup-header {
        padding: 18px 18px 14px;
    }

    .mockup-content {
        padding: 16px;
    }

    .vision-trapezoid {
        clip-path: polygon(5% 0%, 95% 0%, 100% 100%, 0% 100%);
        padding: 20px 24px;
    }
}

@media (max-width: 480px) {
    .dti-cards {
        grid-template-columns: 1fr;
    }

    .gits-pillars {
        grid-template-columns: 1fr;
    }

    .roof-sub-items {
        flex-direction: column;
    }
}

/* ─── DARK MODE ─── */
:deep(.dark) .sh-mockup {
    border-color: rgba(148, 163, 184, 0.16);
    background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
}

:deep(.dark) .mockup-header {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    background: #111827;
}

:deep(.dark) .business-strategy-panel {
    border-color: rgba(148, 163, 184, 0.16);
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.96) 0%, rgba(17, 24, 39, 0.98) 100%);
}

:deep(.dark) .primary-cell {
    background: rgba(15, 23, 42, 0.55);
}

:deep(.dark) .business-strategy-filter {
    border-color: rgba(148, 163, 184, 0.24);
    background-color: rgba(15, 23, 42, 0.8);
    color: #cbd5e1;
}


:deep(.dark) .business-strategy-panel__subtitle {
    color: #94a3b8;
}

:deep(.dark) .business-strategy-table-scroll {
    background: #0f172a;
}

:deep(.dark) .strategy-table thead th {
    background: transparent;
}

:deep(.dark) .strategy-head-card--legacy,
:deep(.dark) .strategy-head-card--legacy-child {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

:deep(.dark) .strategy-head-card--carbon {
    border-color: #3b82f6;
    background: linear-gradient(180deg, #274a87 0%, #1f3e74 100%);
}

:deep(.dark) .strategy-table th,
:deep(.dark) .strategy-table td {
    border-color: rgba(148, 163, 184, 0.12);
}

:deep(.dark) .strategy-table__cell {
    color: #cbd5e1;
}

:deep(.dark) .strategy-cell {
    background: rgba(15, 23, 42, 0.35);
    border-color: rgba(148, 163, 184, 0.12);
}

:deep(.dark) .strategy-cell__value {
    color: #e2e8f0;
}

:deep(.dark) .strategy-cell__empty {
    color: #94a3b8;
}

:deep(.dark) .business-strategy-table__empty,
:deep(.dark) .business-strategy-table__blank {
    color: #94a3b8;
}

:deep(.dark) .roof-main-label,
:deep(.dark) .roof-sub-item {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

:deep(.dark) .roof-headline {
    color: #f8fafc;
}

:deep(.dark) .roof-side-label {
    background: linear-gradient(180deg, #274a87 0%, #1f3e74 100%);
}

:deep(.dark) .dti-section {
    background: #12253f;
}

:deep(.dark) .dti-count,
:deep(.dark) .dti-label {
    color: #e2e8f0;
}

:deep(.dark) .dti-toggle {
    background: rgba(15, 23, 42, 0.8);
    border-color: rgba(148, 163, 184, 0.25);
    color: #bfdbfe;
}

:deep(.dark) .dti-toggle:hover {
    background: rgba(15, 23, 42, 0.95);
    border-color: rgba(96, 165, 250, 0.35);
}

:deep(.dark) .dti-card {
    background: #1e293b;
    border-color: #3b82c8;
    color: #93c5fd;
}

:deep(.dark) .dti-card-badge {
    background: #60a5fa;
    color: #0f172a;
}

:deep(.dark) .dti-card-preview {
    border-top-color: rgba(96, 165, 250, 0.25);
}

:deep(.dark) .dti-card-preview-item {
    color: #cbd5e1;
}

:deep(.dark) .dti-card-preview-more {
    color: #93c5fd;
}

:deep(.dark) .dti-card-preview-empty {
    color: #94a3b8;
}

:deep(.dark) .gits-section {
    background: #0f1d2f;
}

:deep(.dark) .gits-title {
    color: #e2e8f0;
}

:deep(.dark) .gits-subtitle {
    color: #94a3b8;
}

.status-legend-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
}

.status-legend-header {
    display: flex;
    align-items: center;
    gap: 8px;
    border-right: 1px solid #154584;
    padding-right: 16px;
}

.status-legend-title {
    font-size: 11px;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.05em;
}

.status-period-select {
    font-size: 11px;
    font-weight: 700;
    color: #2563eb;
    background: #eff6ff;
    border: none;
    border-radius: 6px;
    padding: 2px 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.status-period-select:hover {
    background: #dbeafe;
}

.status-period-fallback {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
}

.status-legend-items {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.status-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s;
}

.status-legend-item:hover {
    background: rgba(0, 0, 0, 0.03);
}

.status-legend-item--inactive {
    opacity: 0.35;
}

.status-swatch {
    width: 10px;
    height: 10px;
    border-radius: 2px;
}

.status-label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
}

.status-count {
    font-weight: 500;
    color: #94a3b8;
}

/* Status Code Tag */
.initiative-code-tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 16px;
    padding: 0 4px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 800;
    color: #ffffff;
    margin-right: 6px;
    flex-shrink: 0;
}

/* Implementation Status Colors */
.status-color-df { background-color: #0d9488 !important; border: 1px solid #0f766e; }
.status-color-done { background-color: #65a30d !important; border: 1px solid #4d7c0f; }
.status-color-dt2026 { background-color: #ea580c !important; border: 1px solid #c2410c; }
.status-color-itsbp { background-color: #06b6d4 !important; border: 1px solid #0891b2; }
.status-color-onreview { background-color: #ca8a04 !important; border: 1px solid #a16207; }
.status-color-onprogress { background-color: #2563eb !important; border: 1px solid #1d4ed8; }
.status-color-sh { background-color: #ef4444 !important; border: 1px solid #dc2626; }
.status-color-neutral { background-color: #94a3b8 !important; border: 1px solid #64748b; }

/* IT Initiative (GITS) Status Colors - Consistent with IT Building Blocks */
.status-color-itontrack { background-color: #3b82f6 !important; color: #ffffff !important; border: 1px solid #2563eb !important; }
.status-color-itdone { background-color: #10b981 !important; color: #ffffff !important; border: 1px solid #059669 !important; }
.status-color-itatrisk { background-color: #ddf907 !important; color: #ffffff !important; border: 1px solid #ca8a04 !important; }
.status-color-itdelayed { background-color: #f43f5e !important; color: #ffffff !important; border: 1px solid #e11d48 !important; }
.status-color-itnotstarted { background-color: #3b82f6 !important; color: #ffffff !important; border: 1px solid #2563eb !important; }
.status-color-itnotsigned { background-color: #ef4444 !important; color: #ffffff !important; border: 1px solid #dc2626 !important; }

.dti-toggle--active {
    background: #184f96 !important;
    color: #ffffff !important;
    border-color: #0d2a4a !important;
}

:deep(.dark) .status-legend-panel {
    background: #0f172a;
    border-color: rgba(148, 163, 184, 0.1);
}

:deep(.dark) .status-legend-header {
    border-color: rgba(148, 163, 184, 0.1);
}

:deep(.dark) .status-period-select {
    background: rgba(37, 99, 235, 0.1);
    color: #60a5fa;
}

:deep(.dark) .status-label {
    color: #e2e8f0;
}

:deep(.dark) .status-legend-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

@media (max-width: 768px) {
    .status-legend-header {
        border-right: none;
        padding-right: 0;
        width: 100%;
        margin-bottom: 8px;
    }
}
</style>
