<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const initiativeColumnOptions = [2, 3, 4, 5, 6];
const initiativeColumnCount = ref(6);
const showBusinessUnit = ref(false);
const showStatusColors = ref(true);
const showInitiativeCode = ref(true);
const selectedOrganization = ref('');
const selectedCoe = ref('');
const selectedStatus = ref('');
const selectedSource = ref('');

const props = defineProps({
    goals: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();

const initiativeSummaryHref = (initiative) => {
    const initiativeId = Number(initiative?.id ?? 0);
    return initiativeId > 0
        ? route('program-planning.program-definition.digital-initiatives.summary.index', initiativeId)
        : null;
};

const initiativeProjectCharterHref = (initiative) => {
    const mappedProjectId = Number(initiative?.mapped_project_id ?? 0);
    return mappedProjectId > 0
        ? route('it-initiatives.show', { project: mappedProjectId, tab: 'charter' })
        : null;
};

const initiativeLinkHref = (initiative) => {
    return initiativeProjectCharterHref(initiative) || initiativeSummaryHref(initiative);
};

const initiativeLinkTitle = (initiative) => {
    const label = String(initiative?.label ?? initiative?.name ?? initiative?.code ?? 'initiative').trim();
    if (initiativeProjectCharterHref(initiative)) {
        return `Lihat project charter IT untuk ${label}`;
    }
    return `Lihat capsule summary untuk ${label}`;
};

const statusDesiredOrder = ['DF', 'Done', 'DT 2026', 'ITSBP', 'On Review', 'SH'];

const normalizeStatusLabel = (rawStatus) => {
    const s = String(rawStatus ?? '').trim();
    if (!s) return null;
    if (s === 'DF') return 'DF';
    if (s === 'Done') return 'Done';
    if (s === 'DT 2026') return 'DT 2026';
    if (s === 'ITSBP') return 'ITSBP';
    if (s === 'On Review') return 'On Review';
    if (s === 'SH') return 'SH';
    return s;
};

const getStatusColorClass = (status) => {
    const s = normalizeStatusLabel(status);
    if (s === 'DF') return 'status-color-df';
    if (s === 'Done') return 'status-color-done';
    if (s === 'DT 2026') return 'status-color-dt2026';
    if (s === 'ITSBP') return 'status-color-itsbp';
    if (s === 'On Review') return 'status-color-onreview';
    if (s === 'SH') return 'status-color-sh';
    return '';
};

const organizationOptions = computed(() => {
    const orgMap = new Map();
    const allInitiatives = [];

    (props.goals || []).forEach((goal) => {
        (goal.themes || []).forEach((theme) => {
            (theme.initiatives || []).forEach((ini) => allInitiatives.push(ini));
        });
        (goal.direct_initiatives || []).forEach((ini) => allInitiatives.push(ini));
    });

    allInitiatives.forEach((ini) => {
        const org = ini.business_unit;
        if (org && org !== '-') {
            const groupLabel = ini.groub_id === 2 ? 'Sub Holding' : 'Holding';
            if (!orgMap.has(org)) {
                orgMap.set(org, groupLabel);
            }
        }
    });

    const individualOptions = Array.from(orgMap.entries())
        .map(([name, group]) => ({
            value: name,
            label: `${group} - ${name}`
        }))
        .sort((a, b) => a.label.localeCompare(b.label));

    return [
        { value: 'all_holding', label: 'All Holding' },
        { value: 'all_subholding', label: 'All Sub Holding' },
        ...individualOptions
    ];
});

const sourceOptions = computed(() => {
    const sourceMap = new Map();
    const allInitiatives = [];

    (props.goals || []).forEach((goal) => {
        (goal.themes || []).forEach((theme) => {
            (theme.initiatives || []).forEach((ini) => allInitiatives.push(ini));
        });
        (goal.direct_initiatives || []).forEach((ini) => allInitiatives.push(ini));
    });

    allInitiatives.forEach(ini => {
        const id = ini.source;
        let name = ini.source_name;

        if (!name) {
            if (id == 3) name = 'Baseline RSTI 2025-2029';
            else if (id == 4) name = 'New Initiatives 2026';
        }

        if (id !== undefined && id !== null && name) {
            if (!sourceMap.has(id)) {
                sourceMap.set(id, name);
            }
        }
    });

    const desiredOrder = ['Baseline RSTI 2025-2029', 'New Initiatives 2026'];
    
    return Array.from(sourceMap.entries())
        .map(([id, name]) => ({ value: id, label: name }))
        .sort((a, b) => {
            const indexA = desiredOrder.indexOf(a.label);
            const indexB = desiredOrder.indexOf(b.label);
            if (indexA !== -1 && indexB !== -1) return indexA - indexB;
            if (indexA !== -1) return -1;
            if (indexB !== -1) return 1;
            return a.label.localeCompare(b.label);
        });
});

const statusLegend = computed(() => {
    const stats = {};
    statusDesiredOrder.forEach((label) => {
        stats[label] = 0;
    });

    displayGoals.value.forEach((goal) => {
        goal.rows.forEach((row) => {
            (row.initiatives ?? []).forEach((initiative) => {
                const label = normalizeStatusLabel(initiative.implementation_status);
                if (label && stats.hasOwnProperty(label)) {
                    stats[label]++;
                }
            });
        });
    });

    return statusDesiredOrder.map((label) => ({
        label,
        class: getStatusColorClass(label),
        count: stats[label],
    }));
});

const totalOverallInitiatives = computed(() => displayGoals.value.reduce((sum, goal) => sum + goal.total_initiatives, 0));

const fallbackGoals = [
    {
        id: 'goal-a',
        code: 'A',
        title: 'Maximize Legacy Business',
        themes: [
            {
                id: 'theme-a1',
                theme_number: 1,
                name: 'Maximizing Value',
                initiatives: [],
            },
            {
                id: 'theme-a2',
                theme_number: 2,
                name: 'Expand to new markets & adjacencies',
                initiatives: [],
            },
        ],
        direct_initiatives: [],
    },
    {
        id: 'goal-b',
        code: 'B',
        title: 'Building low carbon business',
        themes: [],
        direct_initiatives: [],
    },
];

const desiredLegendOrder = [
    'IoT',
    'Advance Cloud',
    'RPA',
    'Robotics',
    'AI / Adv. Analytics',
    'CoE Not Identified',
];

const normalizeCoeName = (rawName) => {
    let name = String(rawName ?? '').trim();

    if (!name || name === '-' || name.toUpperCase() === 'NO COE') return 'CoE Not Identified';

    const upper = name.toUpperCase();

    if (upper === 'IOT') return 'IoT';
    if (upper.includes('CLOUD') || upper.includes('COMPUTING') || name === 'Advance Cloud') return 'Advance Cloud';
    if (upper === 'RPA') return 'RPA';
    if (upper.includes('ROBOT') || name === 'Robotics') return 'Robotics';
    if (upper.includes('ANALYTICS') || name === 'AI / Adv. Analytics') return 'AI / Adv. Analytics';

    return name;
};

const getCoeColorClass = (coeName) => {
    const name = normalizeCoeName(coeName);

    if (name === 'IoT') return 'coe-color-blue';
    if (name === 'Advance Cloud') return 'coe-color-emerald';
    if (name === 'RPA') return 'coe-color-amber';
    if (name === 'Robotics') return 'coe-color-purple';
    if (name === 'AI / Adv. Analytics') return 'coe-color-rose';
    if (name === 'CoE Not Identified') return 'coe-color-none';

    return 'coe-color-none';
};

const normalizeInitiative = (initiative, fallbackKey) => {
    const code = String(initiative?.code ?? '').trim();
    const name = String(initiative?.name ?? '').trim();
    const label = String(initiative?.label ?? [code, name].filter(Boolean).join(' - ')).trim();
    const coeName = normalizeCoeName(initiative?.coe_name || initiative?.coe?.name);

    // Ambil status dari atribut langsung atau dari relasi latest_status_implementation
    const implementationStatus = initiative?.implementation_status 
        ?? initiative?.latest_status_implementation?.review_status
        ?? initiative?.latest_status_implementation?.status 
        ?? initiative?.latest_status_implementation?.implementation_status;

    return {
        id: initiative?.id ?? fallbackKey,
        code,
        name,
        label: label !== '' ? label : '-',
        coe_name: coeName,
        business_unit: initiative?.business_unit,
        groub_id: initiative?.groub_id,
        implementation_status: implementationStatus,
        source: initiative?.source,
        source_name: initiative?.source_name,
        description: initiative?.description,
    };
};

const buildInitiativeColumns = (initiatives = [], columnCount = initiativeColumnCount.value) => {
    const items = Array.isArray(initiatives)
        ? [...initiatives].sort((left, right) => {
            const leftCode = String(left?.code ?? '').trim();
            const rightCode = String(right?.code ?? '').trim();

            if (leftCode !== '' || rightCode !== '') {
                const codeCompare = leftCode.localeCompare(rightCode, undefined, {
                    numeric: true,
                    sensitivity: 'base',
                });

                if (codeCompare !== 0) {
                    return codeCompare;
                }
            }

            return String(left?.name ?? left?.label ?? '').localeCompare(
                String(right?.name ?? right?.label ?? ''),
                undefined,
                { numeric: true, sensitivity: 'base' },
            );
        })
        : [];

    if (items.length === 0) {
        return { items: [], rowCount: 0 };
    }

    return {
        items,
        rowCount: Math.ceil(items.length / Number(columnCount)),
    };
};

const displayGoals = computed(() => {
    const goalsFromProps = Array.isArray(props.goals) ? props.goals : [];
    
    if (goalsFromProps.length === 0) {
        return fallbackGoals.map((fallbackGoal) => {
            return {
                ...fallbackGoal,
                total_initiatives: 0,
                rows: [
                    {
                        key: `${fallbackGoal.code}-empty`,
                        type: 'empty',
                        label: 'No themes',
                        initiatives: [],
                        initiatives_count: 0,
                    }
                ],
            };
        });
    }

    return goalsFromProps.map((rawGoal) => {
        const themes = (Array.isArray(rawGoal?.themes) ? rawGoal.themes : [])
            .map((theme, index) => {
                const initiatives = Array.isArray(theme?.initiatives)
                    ? theme.initiatives
                        .map((initiative, initiativeIndex) => normalizeInitiative(
                            initiative,
                            `${rawGoal.code}-theme-${index + 1}-initiative-${initiativeIndex + 1}`,
                        ))
                        .filter((ini) => {
                            let matchesOrg = true;
                            if (selectedOrganization.value === 'all_holding') {
                                matchesOrg = ini.groub_id !== 2;
                            } else if (selectedOrganization.value === 'all_subholding') {
                                matchesOrg = ini.groub_id === 2;
                            } else if (selectedOrganization.value !== '') {
                                matchesOrg = ini.business_unit === selectedOrganization.value;
                            }
                            const matchesCoe = !selectedCoe.value || ini.coe_name === selectedCoe.value;
                            const implStatus = normalizeStatusLabel(ini.implementation_status);
                            const matchesStatus = !selectedStatus.value || implStatus === selectedStatus.value;
                            const matchesSource = !selectedSource.value || ini.source == selectedSource.value;
                            return matchesOrg && matchesCoe && matchesStatus && matchesSource;
                        })
                    : [];

                return {
                    id: theme?.id ?? `${rawGoal.code}-theme-${index + 1}`,
                    theme_number: Number(theme?.theme_number ?? index + 1),
                    name: String(theme?.name ?? theme?.label ?? `Theme ${index + 1}`),
                    initiatives_count: initiatives.length,
                    initiatives,
                };
            })
            .sort((left, right) => left.theme_number - right.theme_number);

        const directInitiatives = Array.isArray(rawGoal?.direct_initiatives)
            ? rawGoal.direct_initiatives
                .map((initiative, initiativeIndex) => normalizeInitiative(
                    initiative,
                    `${rawGoal.code}-direct-${initiativeIndex + 1}`,
                ))
                .filter((ini) => {
                    let matchesOrg = true;
                    if (selectedOrganization.value === 'all_holding') {
                        matchesOrg = ini.groub_id =bu== 1;
                    } else if (selectedOrganization.value === 'all_subholding') {
                        matchesOrg = ini.groub_id === 2;
                    } else if (selectedOrganization.value !== '') {
                        matchesOrg = ini.business_unit === selectedOrganization.value;
                    }
                    const matchesCoe = !selectedCoe.value || ini.coe_name === selectedCoe.value;
                    const implStatus = normalizeStatusLabel(ini.implementation_status);
                    const matchesStatus = !selectedStatus.value || implStatus === selectedStatus.value;
                    const matchesSource = !selectedSource.value || ini.source == selectedSource.value;
                    return matchesOrg && matchesCoe && matchesStatus && matchesSource;
                })
            : [];

        const rows = themes.map((theme) => ({
            key: `theme-${theme.id}`,
            type: 'theme',
            label: `${theme.theme_number}. ${theme.name}`,
            initiatives: theme.initiatives,
            initiatives_count: theme.initiatives.length,
        }));

        if (directInitiatives.length > 0) {
            rows.push({
                key: `${rawGoal.code}-direct`,
                type: 'direct',
                label: 'No themes',
                initiatives: directInitiatives,
                initiatives_count: directInitiatives.length,
            });
        }

        if (rows.length === 0) {
            rows.push({
                key: `${rawGoal.code}-empty`,
                type: 'empty',
                label: 'No themes',
                initiatives: [],
                initiatives_count: 0,
            });
        }

        const totalInitiatives = rows.reduce(
            (sum, row) => sum + Number(row.initiatives_count ?? row.initiatives?.length ?? 0),
            0,
        );

        return {
            id: rawGoal?.id ?? rawGoal.code,
            code: rawGoal.code,
            title: String(rawGoal?.title ?? rawGoal.code),
            total_initiatives: totalInitiatives,
            rows,
        };
    });
});

const coeLegend = computed(() => {
    const stats = {};
    desiredLegendOrder.forEach((name) => {
        stats[name] = 0;
    });

    displayGoals.value.forEach((goal) => {
        goal.rows.forEach((row) => {
            (row.initiatives ?? []).forEach((initiative) => {
                const name = normalizeCoeName(initiative.coe_name);

                if (stats[name] !== undefined) {
                    stats[name] += 1;
                } else {
                    stats['CoE Not Identified'] += 1;
                }
            });
        });
    });

    return desiredLegendOrder.map((name, index) => ({
        id: index + 1,
        name,
        count: stats[name] ?? 0,
    }));
});

const initiativeDisplayName = (initiative) => {
    const name = String(initiative?.name ?? '').trim();

    if (name !== '') {
        return name;
    }

    return String(initiative?.label ?? '-').trim() || '-';
};

const initiativeOptionLabel = (initiative) => {
    const code = String(initiative?.code ?? '').trim();
    const name = initiativeDisplayName(initiative);

    if (code !== '' && name !== '-') {
        return `[${code}] ${name}`;
    }

    return name !== '-' ? name : code || '-';
};
</script>

<template>
    <div class="mb-4 space-y-4">
        <div class="space-y-2.5">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <div v-for="coe in coeLegend" :key="`coe-legend-${coe.id}`" class="flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded-sm shadow-sm legend-swatch" :class="getCoeColorClass(coe.name)"></span>
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
                        {{ coe.name }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ coe.count
                        }})</span>
                    </span>
                </div>

                <!-- Total Overall -->
                <div class="flex items-center gap-1.5 border-l border-slate-300 pl-4 ml-1 dark:border-white/10">
                    <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">
                        Total Digital Initiatives <span class="text-slate-500 dark:text-slate-400 font-medium">({{
                            totalOverallInitiatives }})</span>
                    </span>
                </div>
            </div>

            <!-- Status Implementation Legend -->
            <div
                v-if="showStatusColors"
                class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 border-t border-slate-100 dark:border-white/5"
            >
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Implementation Status (November - Desember 2025):</span>
                <div
                    v-for="status in statusLegend"
                    :key="`status-legend-${status.label}`"
                    class="flex items-center gap-1.5 cursor-pointer select-none transition-opacity"
                    :class="{ 'opacity-40': selectedStatus && selectedStatus !== status.label }"
                    @click="selectedStatus = selectedStatus === status.label ? '' : status.label"
                    :title="`Filter: ${status.label}`"
                >
                    <span
                        class="h-3 w-3 rounded-sm shadow-sm"
                        :class="status.class"
                    ></span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        {{ status.label }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ status.count }})</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <div class="initiative-view-switch">
                    <select
                        v-model="selectedOrganization"
                        class="initiative-view-select mr-2"
                    >
                        <option value="">All Organizations</option>
                        <option
                            v-for="org in organizationOptions"
                            :key="org.value"
                            :value="org.value"
                        >
                            {{ org.label }}
                        </option>
                    </select>

                    <select
                        v-model="selectedCoe"
                        class="initiative-view-select mr-2"
                    >
                        <option value="">All CoE</option>
                        <option
                            v-for="coe in coeLegend"
                            :key="`coe-opt-${coe.id}`"
                            :value="coe.name"
                        >
                            {{ coe.name }}
                        </option>
                    </select>

                    <select
                        v-model="selectedStatus"
                        class="initiative-view-select mr-2"
                    >
                        <option value="">All Status</option>
                        <option
                            v-for="status in statusLegend"
                            :key="`status-opt-${status.label}`"
                            :value="status.label"
                        >
                            {{ status.label }}
                        </option>
                    </select>

                    <select
                        v-model="selectedSource"
                        class="initiative-view-select mr-2"
                    >
                        <option value="">All Initiatives</option>
                        <option
                            v-for="source in sourceOptions"
                            :key="`source-opt-${source.value}`"
                            :value="source.value"
                        >
                            {{ source.label }}
                        </option>
                    </select>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showBusinessUnit }"
                        title="Tampilkan/Sembunyikan Business Unit"
                        @click="showBusinessUnit = !showBusinessUnit"
                    >
                        <svg v-if="showBusinessUnit" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                        <span>Business Unit</span>
                    </button>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showStatusColors }"
                        title="Tampilkan/Sembunyikan Warna Status Implementasi"
                        @click="showStatusColors = !showStatusColors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        <span>Status Impl.</span>
                    </button>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showInitiativeCode }"
                        title="Tampilkan/Sembunyikan Code Initiative"
                        @click="showInitiativeCode = !showInitiativeCode"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span>Code</span>
                    </button>

                    <select
                        v-model="initiativeColumnCount"
                        class="initiative-view-select"
                    >
                        <option
                            v-for="option in initiativeColumnOptions"
                            :key="`col-opt-${option}`"
                            :value="option"
                        >
                            {{ option }} Kolom
                        </option>
                    </select>
                </div>
            </div>
        </div>
        
    </div>

    <section class="dual-growth-mockup">
        <div class="mockup-board-scroll">
            <h1 class="text-center text-l font-bold mt-4 mb-4">Digital Initiative Support to Pertamina Group Dual Growth Strategy</h1>
            <table class="dg-table">
                <thead>
                    <tr>
                        <th colspan="2" class="top-head top-head-left">
                            Dual Growth Strategy
                        </th>
                        <th class="top-head top-head-right">
                            Digital Initiatives
                        </th>
                    </tr>
                    <tr>
                        <th class="sub-head sub-head-goal">
                            Goal
                        </th>
                        <th class="sub-head sub-head-theme">
                            Theme
                        </th>
                        <th class="sub-head sub-head-initiative"></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="goal in displayGoals" :key="goal.id ?? goal.code">
                        <tr v-for="(row, rowIndex) in goal.rows" :key="row.key">
                            <td v-if="rowIndex === 0" class="goal-cell" :rowspan="goal.rows.length"
                                :colspan="!goal.rows.some(r => r.type === 'theme') ? 2 : 1">
                                <div class="goal-cell__inner">
                                    <div class="goal-label-wrapper">
                                        <span class="goal-cell__text">{{ goal.title }}</span>
                                        <span class="count-capsule">{{ goal.total_initiatives }}</span>
                                    </div>
                                </div>
                            </td>

                            <td v-if="goal.rows.some(r => r.type === 'theme')" class="theme-cell"
                                :class="{ 'theme-cell--empty': row.type !== 'theme' }">
                                <template v-if="row.type === 'theme'">
                                    <div class="theme-cell__inner">
                                        <div class="theme-label-wrapper">
                                            <span class="theme-cell__text">{{ row.label }}</span>
                                            <span class="count-capsule">{{ row.initiatives_count }}</span>
                                        </div>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="theme-cell__placeholder">
                                        <span>{{ row.label }}</span>
                                        <span v-if="row.initiatives_count > 0"
                                            class="count-capsule count-capsule--muted">
                                            {{ row.initiatives_count }}
                                        </span>
                                    </div>
                                </template>
                            </td>

                            <td class="initiatives-cell">
                                <div v-if="row.initiatives.length" class="initiatives-grid" :style="{
                                    '--initiative-column-count': initiativeColumnCount,
                                    '--row-count': buildInitiativeColumns(row.initiatives).rowCount,
                                }">
                                    <component
                                        :is="initiativeLinkHref(initiative) ? Link : 'div'"
                                        v-for="initiative in buildInitiativeColumns(row.initiatives).items"
                                        :key="`${row.key}-${initiative.id}`"
                                        :href="initiativeLinkHref(initiative)"
                                        class="initiative-box group"
                                        :class="[
                                            getCoeColorClass(initiative.coe_name),
                                            { 'initiative-box--no-code': !showInitiativeCode || !initiative.code },
                                            { 'initiative-box--clickable': initiativeLinkHref(initiative) }
                                        ]"
                                    >
                                        <!-- Custom Smart Tooltip -->
                                        <div class="absolute top-full left-1/2 z-50 mt-1 hidden -translate-x-1/2 w-max max-w-[250px] sm:max-w-xs md:max-w-sm bg-white border border-slate-800 shadow-sm px-1.5 py-1 text-left text-[9px] italic text-slate-800 group-hover:block pointer-events-none whitespace-normal break-words dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                                            {{ initiative.description || initiativeOptionLabel(initiative) }}
                                        </div>

                                        <span v-if="showInitiativeCode && initiative.code"
                                            class="initiative-box__code"
                                            :class="showStatusColors ? getStatusColorClass(initiative.implementation_status) : ''">
                                            {{ initiative.code }}
                                        </span>

                                        <span class="initiative-box__name"
                                            :class="{ 'initiative-box__name--full': !showInitiativeCode || !initiative.code }">
                                            <span class="initiative-box__label-text">
                                                {{ initiativeDisplayName(initiative) }}
                                            </span>
                                            <span v-if="showBusinessUnit" class="initiative-box__bu">{{ initiative.business_unit }}</span>
                                        </span>
                                    </component>
                                </div>

                                <span v-else class="initiative-empty">-</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
.dual-growth-mockup {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
}

.mockup-eyebrow {
    margin: 0;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #2563eb;
}

.dual-growth-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 16px;
    padding: 12px 24px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.legend-swatch {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    border: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.legend-label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
}

.legend-count {
    font-weight: 500;
    color: #94a3b8;
}

.mockup-board-scroll {
    overflow-x: auto;
}

.dg-table {
    width: 100%;
    min-width: 1120px;
    border-collapse: collapse;
    table-layout: fixed;
    background: #ffffff;
}

.dg-table th,
.dg-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.top-head {
    padding: 10px 12px;
    background: #0f6fb7;
    color: #ffffff;
    font-size: 14px;
    font-weight: 800;
    line-height: 1.1;
    text-align: center;
}

.top-head-left {
    width: 26%;
}

.top-head-right {
    width: 74%;
}

.sub-head {
    padding: 6px 10px;
    background: #eef4f8;
    color: #4f6b85;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
}

.sub-head-goal {
    width: 12%;
}

.sub-head-theme {
    width: 14%;
}

.sub-head-initiative {
    width: 74%;
}

.goal-cell,
.theme-cell {
    padding: 0;
    vertical-align: middle !important;
    text-align: center;
}

.goal-cell {
    width: 140px;
    background: #0f6fb7;
}

.goal-cell__inner,
.theme-cell__inner {
    display: flex;
    min-height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 12px;
    text-align: center;
}

.goal-label-wrapper,
.theme-label-wrapper {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.goal-cell__text,
.theme-cell__text {
    display: block;
    text-align: center;
    word-break: break-word;
}

.goal-cell__text {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
}

.theme-cell {
    width: 180px;
    background: linear-gradient(180deg, #78b8ea 0%, #63a9df 100%);
}

.theme-cell__text {
    font-size: 12px;
    font-weight: 700;
    line-height: 1.25;
    color: #ffffff;
}

.theme-cell--empty {
    background: #ffffff;
}

.theme-cell__placeholder {
    display: flex;
    min-height: 100%;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 10px;
    font-size: 11px;
    font-weight: 600;
    font-style: italic;
    text-align: center;
    color: #94a3b8;
}

.count-capsule {
    box-sizing: border-box;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    min-width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.14);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    font-size: 13px;
    font-weight: 800;
    font-style: normal;
    font-variant-numeric: tabular-nums;
    line-height: 1;
    color: #ffffff;
    flex-shrink: 0;
}

.count-capsule--muted {
    border-color: #cbd5e1;
    background: #e2e8f0;
    box-shadow: none;
    color: #475569;
}

.initiatives-cell {
    padding: 8px;
    background: #f8fafc;
}

.initiatives-grid {
    display: grid;
    grid-template-columns: repeat(var(--initiative-column-count, 2), minmax(0, 1fr));
    grid-auto-flow: column;
    grid-template-rows: repeat(var(--row-count, 1), minmax(min-content, 1fr));
    align-items: stretch;
    gap: 8px;
}

.initiative-box {
    position: relative;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    min-height: 24px;
    width: 100%;
    align-items: stretch;
    border: 1px solid #374151;
    background: #ffffff;
    font-size: 9px;
    font-weight: 500;
    line-height: 1.1;
    color: #1f2937;
}

.initiative-box--clickable {
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.initiative-box--clickable:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 10;
}

/* Ensure Link doesn't have default <a> styles */
a.initiative-box {
    text-decoration: none;
    color: inherit;
}

.initiative-box--no-code {
    grid-template-columns: 1fr !important;
}

.coe-color-blue {
    background-color: #eff6ff;
    border-color: #1d4ed8 !important;
}

.coe-color-emerald {
    background-color: #ecfdf5;
    border-color: #047857 !important;
}

.coe-color-amber {
    background-color: #fffbeb;
    border-color: #b45309 !important;
}

.coe-color-purple {
    background-color: #faf5ff;
    border-color: #6d28d9 !important;
}

.coe-color-rose {
    background-color: #fff1f2;
    border-color: #be123c !important;
}

.coe-color-none {
    background-color: #ffffff;
    border-color: #374151 !important;
}

.legend-swatch.coe-color-blue {
    background-color: #1d4ed8 !important;
}

.legend-swatch.coe-color-emerald {
    background-color: #047857 !important;
}

.legend-swatch.coe-color-amber {
    background-color: #b45309 !important;
}

.legend-swatch.coe-color-purple {
    background-color: #6d28d9 !important;
}

.legend-swatch.coe-color-rose {
    background-color: #be123c !important;
}

.legend-swatch.coe-color-none {
    background-color: #374151 !important;
}

.coe-color-blue .initiative-box__code {
    border-right-color: #1d4ed8;
    background-color: rgba(29, 78, 216, 0.1);
}

.coe-color-emerald .initiative-box__code {
    border-right-color: #047857;
    background-color: rgba(4, 120, 87, 0.1);
}

.coe-color-amber .initiative-box__code {
    border-right-color: #b45309;
    background-color: rgba(180, 83, 9, 0.1);
}

.coe-color-purple .initiative-box__code {
    border-right-color: #6d28d9;
    background-color: rgba(109, 40, 217, 0.1);
}

.coe-color-rose .initiative-box__code {
    border-right-color: #be123c;
    background-color: rgba(190, 18, 60, 0.1);
}

.initiative-box__code {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #374151;
    padding: 2px 4px;
    font-weight: 700;
    letter-spacing: 0.01em;
    white-space: nowrap;
    min-width: 28px;
}

.initiative-box__name {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    padding: 2px 8px 2px 5px;
    word-break: break-word;
}

.initiative-box__bu {
    font-size: 7.5px;
    font-weight: 700;
    font-style: italic;
    opacity: 0.7;
}

.initiative-box__name--full {
    grid-column: 1 / -1;
    padding-left: 5px;
}

.initiative-box__label-text {
    line-height: 1.1;
}

.initiative-empty {
    font-size: 13px;
    font-style: italic;
    color: #94a3b8;
}

.initiative-view-switch {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    border-radius: 12px;
    background: transparent;
    padding: 2px;
}

.initiative-view-select {
    appearance: none;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 24px 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
    transition: all 0.15s ease;
}

.initiative-view-select:hover {
    border-color: #0f6fb7;
    color: #0f6fb7;
}

.initiative-view-select:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.bu-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    transition: all 0.15s ease;
    cursor: pointer;
}

.bu-toggle-btn:hover {
    border-color: #0f6fb7;
    background: #f8fafc;
}

.bu-toggle-btn--active {
    background: #0f6fb7;
    border-color: #0f6fb7;
    color: #ffffff;
}

.bu-toggle-btn--active:hover {
    background: #0d5ea1;
    border-color: #0d5ea1;
}

.status-color-df {
    background-color: #0d9488 !important;
    color: #ffffff !important;
    border-color: #0f766e !important;
}

.status-color-done {
    background-color: #65a30d !important;
    color: #ffffff !important;
    border-color: #4d7c0f !important;
}

.status-color-dt2026 {
    background-color: #ea580c !important;
    color: #ffffff !important;
    border-color: #c2410c !important;
}

.status-color-itsbp {
    background-color: #06b6d4 !important;
    color: #ffffff !important;
    border-color: #0891b2 !important;
}

.status-color-onreview {
    background-color: #ca8a04 !important;
    color: #ffffff !important;
    border-color: #a16207 !important;
}

.status-color-sh {
    background-color: #ef4444 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

@media (max-width: 1024px) {
    .dg-table {
        min-width: 980px;
    }

    .goal-cell {
        width: 120px;
    }

    .theme-cell {
        width: 160px;
    }
}

@media (max-width: 768px) {
    .dual-growth-legend {
        padding: 10px 18px;
    }

    .dg-table {
        min-width: 860px;
    }

    .top-head {
        font-size: 13px;
    }

    .sub-head {
        font-size: 11px;
    }

    .goal-cell__inner,
    .theme-cell__inner {
        padding: 12px 10px;
    }

    .goal-cell__text,
    .theme-cell__text {
        font-size: 11px;
    }

    .count-capsule {
        width: 30px;
        min-width: 30px;
        height: 30px;
        padding: 0;
        font-size: 12px;
    }

    .initiatives-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

:deep(.dark) .dual-growth-mockup {
    border-color: rgba(148, 163, 184, 0.16);
    background: #111827;
}

:deep(.dark) .mockup-header,
:deep(.dark) .dual-growth-legend {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    background: #111827;
}

:deep(.dark) .legend-label,
:deep(.dark) .mockup-eyebrow,
:deep(.dark) .top-head,
:deep(.dark) .sub-head,
:deep(.dark) .goal-cell__text {
    color: #e2e8f0;
}

:deep(.dark) .legend-count,
:deep(.dark) .theme-cell__placeholder,
:deep(.dark) .initiative-empty {
    color: #94a3b8;
}

:deep(.dark) .dg-table thead th,
:deep(.dark) .goal-cell,
:deep(.dark) .theme-cell,
:deep(.dark) .initiatives-cell {
    border-color: rgba(148, 163, 184, 0.22);
}

:deep(.dark) .top-head,
:deep(.dark) .sub-head,
:deep(.dark) .theme-cell--empty,
:deep(.dark) .initiatives-cell {
    background: #0f172a;
}

:deep(.dark) .goal-cell,
:deep(.dark) .theme-cell {
    background: #36588f;
}

:deep(.dark) .theme-cell--empty {
    background: #0f172a;
}

:deep(.dark) .sub-head {
    color: #cbd5e1;
}

:deep(.dark) .theme-cell__text {
    color: rgba(226, 232, 240, 0.82);
}

:deep(.dark) .count-capsule--muted {
    border-color: rgba(148, 163, 184, 0.26);
    background: rgba(51, 65, 85, 0.9);
    color: #cbd5e1;
}

:deep(.dark) .initiative-box {
    color: #f8fafc;
}

:deep(.dark) .initiative-box__code {
    color: #f8fafc;
}

:deep(.dark) .coe-color-blue {
    background-color: rgba(29, 78, 216, 0.2);
}

:deep(.dark) .coe-color-emerald {
    background-color: rgba(4, 120, 87, 0.2);
}

:deep(.dark) .coe-color-amber {
    background-color: rgba(180, 83, 9, 0.2);
}

:deep(.dark) .coe-color-purple {
    background-color: rgba(109, 40, 217, 0.2);
}

:deep(.dark) .coe-color-rose {
    background-color: rgba(190, 18, 60, 0.2);
}

:deep(.dark) .initiative-view-select {
    border-color: rgba(148, 163, 184, 0.22);
    background-color: rgba(15, 23, 42, 0.9);
    color: #cbd5e1;
}

:deep(.dark) .initiative-view-select:hover {
    border-color: rgba(148, 163, 184, 0.4);
    color: #bfdbfe;
}
</style>