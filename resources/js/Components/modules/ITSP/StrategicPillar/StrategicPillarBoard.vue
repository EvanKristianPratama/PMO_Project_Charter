<script setup>
import { computed, ref } from 'vue';

const initiativeColumnOptions = [2, 3, 4, 5, 6];
const initiativeColumnCount = ref(4);
const showBusinessUnit = ref(false);
const showStatusColors = ref(true);
const showInitiativeCode = ref(true);

const props = defineProps({
    strategicPillars: {
        type: Array,
        default: () => [],
    },
    taggings: {
        type: Array,
        default: () => [],
    },
    allOrganizations: {
        type: Array,
        default: () => [],
    },
    selectedGoalId: {
        type: [String, Number, null],
        default: null,
    },
    selectedOrgId: {
        type: [String, Number, null],
        default: null,
    },
    editMode: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'update:selectedGoalId',
    'update:selectedOrgId',
    'navigate',
    'delete-tag',
    'add-tag',
]);

const neutralStatusToneClass = 'bg-slate-50 border-slate-300 text-slate-700 dark:bg-slate-800/60 dark:border-slate-600 dark:text-slate-200';
const neutralStatusChipClass = 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';

const selectedGoalProxy = computed({
    get: () => (props.selectedGoalId ?? ''),
    set: (value) => {
        emit('update:selectedGoalId', value === '' ? null : value);
    },
});

const selectedOrgProxy = computed({
    get: () => (props.selectedOrgId ?? ''),
    set: (value) => {
        emit('update:selectedOrgId', value === '' ? null : value);
    },
});

const hasSelectedGoalFilter = computed(() => selectedGoalProxy.value !== '' && selectedGoalProxy.value !== null && selectedGoalProxy.value !== undefined);
const hasSelectedOrgFilter = computed(() => selectedOrgProxy.value !== '' && selectedOrgProxy.value !== null && selectedOrgProxy.value !== undefined);

const normalizeStatusLabel = (rawStatus) => {
    const value = String(rawStatus ?? '').toLowerCase().trim();

    if (!value || value === '0' || value === '1' || value.includes('draft') || value.includes('not start')) {
        return 'Drafting';
    }

    if (value === '2' || value.includes('propose')) {
        return 'Propose';
    }

    if (value === '3' || value.includes('review')) {
        return 'Review';
    }

    if (value === '4' || value.includes('approved')) {
        return 'Approve';
    }

    if (value === '5' || value.includes('baseline')) {
        return 'Baseline';
    }

    if (value.includes('progress') || value.includes('active') || value.includes('implement')) {
        return 'In Progress';
    }

    if (value.includes('cancel') || value.includes('reject') || value.includes('drop') || value.includes('hold')) {
        return 'On Hold';
    }

    return null;
};

const getStatusToneClass = (status) => {
    const label = normalizeStatusLabel(status);

    if (label === 'Drafting') return 'bg-slate-50 border-slate-300 text-slate-700 dark:bg-slate-800/60 dark:border-slate-600 dark:text-slate-200';
    if (label === 'Propose') return 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-100';
    if (label === 'Review') return 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-100';
    if (label === 'Approve') return 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-100';
    if (label === 'Baseline') return 'bg-violet-50 border-violet-200 text-violet-800 dark:bg-violet-500/10 dark:border-violet-500/20 dark:text-violet-100';
    if (label === 'In Progress') return 'bg-cyan-50 border-cyan-200 text-cyan-800 dark:bg-cyan-500/10 dark:border-cyan-500/20 dark:text-cyan-100';
    if (label === 'On Hold') return 'bg-rose-50 border-rose-200 text-rose-800 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-100';

    return neutralStatusToneClass;
};

const getStatusChipClass = (status) => {
    const label = normalizeStatusLabel(status);

    if (label === 'Drafting') return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
    if (label === 'Propose') return 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
    if (label === 'Review') return 'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
    if (label === 'Approve') return 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
    if (label === 'Baseline') return 'bg-violet-500/10 text-violet-700 border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-400 dark:border-violet-500/20';
    if (label === 'In Progress') return 'bg-cyan-500/10 text-cyan-700 border-cyan-500/20 dark:bg-cyan-500/10 dark:text-cyan-400 dark:border-cyan-500/20';
    if (label === 'On Hold') return 'bg-rose-500/10 text-rose-700 border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';

    return neutralStatusChipClass;
};

const sortByLabel = (items = []) => [...items].sort((left, right) => {
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

    return String(left?.title ?? '').localeCompare(String(right?.title ?? ''), undefined, {
        numeric: true,
        sensitivity: 'base',
    });
});

const initiativeDisplayName = (initiative) => {
    const name = String(initiative?.name ?? '').trim();

    if (name !== '') {
        return name;
    }

    return String(initiative?.label ?? '-').trim() || '-';
};

const normalizeInitiative = (tag, fallbackKey) => {
    const initiative = tag?.initiative ?? {};
    const code = String(initiative?.code ?? '').trim();
    const name = String(initiative?.name ?? '').trim();
    const label = String(initiative?.label ?? [code, name].filter(Boolean).join(' - ')).trim();

    return {
        id: initiative?.id ?? fallbackKey,
        code,
        name,
        label: label !== '' ? label : '-',
        business_unit: initiative?.business_unit ?? initiative?.organization?.id ?? null,
        organization_name: initiative?.organization?.name ?? '',
        implementation_status: initiative?.latest_status?.status ?? initiative?.status,
        rawTag: tag,
    };
};

const matchesOrganizationFilter = (initiative) => {
    if (!hasSelectedOrgFilter.value) {
        return true;
    }

    return String(initiative?.business_unit ?? '') === String(selectedOrgProxy.value);
};

const goalOptions = computed(() => sortByLabel(Array.isArray(props.strategicPillars) ? props.strategicPillars : []));

const organizationOptions = computed(() => {
    const organizations = Array.isArray(props.allOrganizations) && props.allOrganizations.length > 0
        ? [...props.allOrganizations]
        : [];

    if (organizations.length === 0) {
        const fallbackOrganizations = new Map();

        (Array.isArray(props.taggings) ? props.taggings : []).forEach((tag) => {
            const initiative = tag?.initiative ?? {};
            const orgId = initiative?.business_unit;
            const orgName = String(initiative?.organization?.name ?? '').trim();

            if (orgId !== null && orgId !== undefined && String(orgId).trim() !== '') {
                fallbackOrganizations.set(String(orgId), {
                    id: orgId,
                    name: orgName !== '' ? orgName : String(orgId),
                });
            }
        });

        return Array.from(fallbackOrganizations.values()).sort((left, right) => String(left.name ?? '').localeCompare(String(right.name ?? ''), undefined, {
            numeric: true,
            sensitivity: 'base',
        }));
    }

    return organizations.sort((left, right) => String(left?.name ?? '').localeCompare(String(right?.name ?? ''), undefined, {
        numeric: true,
        sensitivity: 'base',
    }));
});

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

const getGoalInitiatives = (goalCode) => {
    const code = String(goalCode ?? '').trim();

    if (!code) {
        return [];
    }

    return sortByLabel(
        (Array.isArray(props.taggings) ? props.taggings : [])
            .filter((tag) => String(tag?.goal ?? '').trim() === code && !tag?.themes_id)
            .map((tag, index) => normalizeInitiative(tag, `${code}-direct-${index + 1}`))
            .filter(matchesOrganizationFilter),
    );
};

const getThemeInitiatives = (goalCode, themeId) => {
    const code = String(goalCode ?? '').trim();
    const themeKey = String(themeId ?? '').trim();

    if (!code || !themeKey) {
        return [];
    }

    return sortByLabel(
        (Array.isArray(props.taggings) ? props.taggings : [])
            .filter((tag) => String(tag?.goal ?? '').trim() === code && String(tag?.themes_id ?? '').trim() === themeKey)
            .map((tag, index) => normalizeInitiative(tag, `${code}-theme-${themeKey}-${index + 1}`))
            .filter(matchesOrganizationFilter),
    );
};

const tbcInitiatives = computed(() => {
    if (hasSelectedGoalFilter.value) {
        return [];
    }

    return sortByLabel(
        (Array.isArray(props.taggings) ? props.taggings : [])
            .filter((tag) => (tag?.goal === null || tag?.goal === undefined || String(tag?.goal).trim() === '') && !tag?.themes_id)
            .map((tag, index) => normalizeInitiative(tag, `tbc-${index + 1}`))
            .filter(matchesOrganizationFilter),
    );
});

const displayGoals = computed(() => {
    const goals = goalOptions.value
        .filter((goal) => !hasSelectedGoalFilter.value || String(goal?.id ?? '') === String(selectedGoalProxy.value))
        .map((rawGoal) => {
            const goalCode = String(rawGoal?.code ?? '').trim();
            const themes = (Array.isArray(rawGoal?.themes) ? rawGoal.themes : [])
                .map((theme, index) => {
                    const initiatives = getThemeInitiatives(goalCode, theme?.id);

                    return {
                        id: theme?.id ?? `${goalCode}-theme-${index + 1}`,
                        theme_number: Number(theme?.theme_number ?? index + 1),
                        name: String(theme?.name ?? theme?.label ?? `Theme ${index + 1}`),
                        initiatives_count: initiatives.length,
                        initiatives,
                    };
                })
                .sort((left, right) => left.theme_number - right.theme_number);

            const directInitiatives = getGoalInitiatives(goalCode);
            const rows = themes.map((theme) => ({
                key: `theme-${theme.id}`,
                type: 'theme',
                label: `${theme.theme_number}. ${theme.name}`,
                initiatives: theme.initiatives,
                initiatives_count: theme.initiatives.length,
            }));

            if (directInitiatives.length > 0) {
                rows.push({
                    key: `${goalCode}-direct`,
                    type: 'direct',
                    label: '',
                    initiatives: directInitiatives,
                    initiatives_count: directInitiatives.length,
                });
            }

            if (rows.length === 0) {
                rows.push({
                    key: `${goalCode}-empty`,
                    type: 'empty',
                    label: 'No initiatives',
                    initiatives: [],
                    initiatives_count: 0,
                });
            }

            const totalInitiatives = rows.reduce(
                (sum, row) => sum + Number(row.initiatives_count ?? row.initiatives?.length ?? 0),
                0,
            );

            return {
                id: rawGoal?.id ?? goalCode,
                code: goalCode !== '' ? goalCode : '-',
                title: String(rawGoal?.title ?? rawGoal?.name ?? goalCode),
                total_initiatives: totalInitiatives,
                rows,
            };
        });

    if (!hasSelectedGoalFilter.value) {
        const tbcItems = tbcInitiatives.value;

        if (tbcItems.length > 0) {
            goals.push({
                id: 'tbc-goal',
                code: 'TBC',
                title: 'not clasified',
                total_initiatives: tbcItems.length,
                rows: [
                    {
                        key: 'tbc-direct',
                        type: 'direct',
                        label: '',
                        initiatives: tbcItems,
                        initiatives_count: tbcItems.length,
                    },
                ],
            });
        }
    }

    return goals;
});

</script>

<template>
    <div class="mb-4 space-y-4">
        <div class="mockup-header flex items-center justify-between">
            <div class="flex items-center justify-start">
                <div class="initiative-view-switch">
                    <select
                        v-model="selectedGoalProxy"
                        class="initiative-view-select mr-2"
                    >
                        <option value="">Semua Blok</option>
                        <option
                            v-for="goal in goalOptions"
                            :key="`goal-opt-${goal.id}`"
                            :value="goal.id"
                        >
                            {{ goal.code }} - {{ goal.title }}
                        </option>
                    </select>

                    <select
                        v-model="selectedOrgProxy"
                        class="initiative-view-select mr-2"
                    >
                        <option value="">Semua Owner</option>
                        <option
                            v-for="org in organizationOptions"
                            :key="`org-opt-${org.id}`"
                            :value="org.id"
                        >
                            {{ org.name }}
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

                    <span class="initiative-view-switch__label ml-2">Tampilan kolom:</span>
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

                    <button
                        v-if="editMode"
                        type="button"
                        class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-semibold bg-emerald-600 text-white shadow-sm transition-colors hover:bg-emerald-700"
                        @click="emit('add-tag')"
                    >
                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Tagging
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="dual-growth-mockup">
        <div class="mockup-board-scroll">
            <h1 class="text-center text-l font-bold mt-4 mb-4">Digital Initiative Support to Strategic Pillar Mapping</h1>
            <table class="dg-table">
                <thead>
                    <tr>
                        <th colspan="2" class="top-head top-head-left">
                            Strategic Pillar
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
                            <td
                                v-if="rowIndex === 0"
                                class="goal-cell"
                                :class="{ 'goal-cell--tbc': goal.code === 'TBC' }"
                                :rowspan="goal.rows.length"
                                :colspan="!goal.rows.some((item) => item.type === 'theme') ? 2 : 1"
                            >
                                <div class="goal-cell__inner">
                                    <div class="goal-cell__code">
                                        {{ goal.code }}
                                    </div>
                                    <div class="goal-label-wrapper">
                                        <span class="goal-cell__text">{{ goal.title }}</span>
                                        <span class="count-capsule" :class="{ 'count-capsule--muted': goal.code === 'TBC' }">{{ goal.total_initiatives }}</span>
                                    </div>
                                </div>
                            </td>

                            <td
                                v-if="goal.rows.some((item) => item.type === 'theme')"
                                class="theme-cell"
                                :class="{
                                    'theme-cell--empty': row.type !== 'theme' && row.type !== 'direct',
                                    'theme-cell--direct': row.type === 'direct',
                                }"
                            >
                                <template v-if="row.type === 'theme'">
                                    <div class="theme-cell__inner">
                                        <div class="theme-label-wrapper">
                                            <span class="theme-cell__text">{{ row.label }}</span>
                                            <span class="count-capsule">{{ row.initiatives_count }}</span>
                                        </div>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="theme-cell__placeholder" :class="{ 'theme-cell__placeholder--direct': row.type === 'direct' }">
                                        <span v-if="row.type !== 'direct'">{{ row.label }}</span>
                                        <span
                                            v-if="row.initiatives_count > 0"
                                            class="count-capsule"
                                            :class="{ 'count-capsule--muted': row.type !== 'direct' }"
                                        >
                                            {{ row.initiatives_count }}
                                        </span>
                                    </div>
                                </template>
                            </td>

                            <td class="initiatives-cell">
                                <div
                                    v-if="row.initiatives.length"
                                    class="initiatives-grid"
                                    :style="{
                                        '--initiative-column-count': initiativeColumnCount,
                                        '--row-count': buildInitiativeColumns(row.initiatives).rowCount,
                                    }"
                                >
                                    <div
                                        v-for="initiative in buildInitiativeColumns(row.initiatives).items"
                                        :key="`${row.key}-${initiative.id}`"
                                        class="initiative-box"
                                        :class="[
                                            getStatusToneClass(initiative.implementation_status),
                                            { 'initiative-box--no-code': !showInitiativeCode || !initiative.code },
                                        ]"
                                        :title="initiative.label"
                                        @click="emit('navigate', initiative.rawTag)"
                                    >
                                        <span
                                            v-if="showInitiativeCode && initiative.code"
                                            class="initiative-box__code"
                                            :class="showStatusColors ? getStatusChipClass(initiative.implementation_status) : neutralStatusChipClass"
                                        >
                                            {{ initiative.code }}
                                        </span>

                                        <span
                                            class="initiative-box__name"
                                            :class="{ 'initiative-box__name--solo': !showInitiativeCode || !initiative.code }"
                                        >
                                            <span class="initiative-box__label-text">
                                                {{ initiativeDisplayName(initiative) }}
                                            </span>
                                            <span v-if="showBusinessUnit" class="initiative-box__bu">
                                                {{ initiative.organization_name || initiative.business_unit }}
                                            </span>
                                        </span>

                                        <button
                                            v-if="editMode"
                                            type="button"
                                            class="initiative-box__remove"
                                            title="Remove"
                                            @click.stop="emit('delete-tag', initiative.rawTag)"
                                        >
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
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

.mockup-header {
    padding: 18px 24px 12px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
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

.goal-cell--tbc {
    background: #f8fafc;
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

.goal-cell__code {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.14);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    line-height: 1;
    color: #ffffff;
    text-transform: uppercase;
}

.goal-cell__text {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
}

.goal-cell--tbc .goal-cell__code {
    border-color: #cbd5e1;
    background: #e2e8f0;
    color: #475569;
}

.goal-cell--tbc .goal-cell__text {
    color: #475569;
}

.goal-cell--tbc .count-capsule {
    border-color: #cbd5e1;
    background: #e2e8f0;
    box-shadow: none;
    color: #475569;
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

.theme-cell--direct {
    background: #0f6fb7;
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

.theme-cell__placeholder--direct {
    font-style: normal;
    color: #ffffff;
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
    grid-template-columns: auto minmax(0, 1fr) auto;
    min-height: 24px;
    width: 100%;
    align-items: stretch;
    border: 1px solid #374151;
    background: #ffffff;
    font-size: 9px;
    font-weight: 500;
    line-height: 1.1;
    color: #1f2937;
    overflow: hidden;
    cursor: pointer;
    transition: filter 0.15s ease, transform 0.15s ease;
}

.initiative-box:hover {
    filter: brightness(0.97);
}

.initiative-box--no-code {
    grid-template-columns: minmax(0, 1fr) auto !important;
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

.initiative-box__name--solo {
    padding-left: 5px;
}

.initiative-box__bu {
    font-size: 7.5px;
    font-weight: 700;
    font-style: italic;
    opacity: 0.7;
}

.initiative-box__label-text {
    line-height: 1.1;
}

.initiative-box__remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    padding: 0 5px;
    border: 0;
    border-left: 1px solid rgba(148, 163, 184, 0.22);
    background: transparent;
    color: inherit;
    opacity: 0.55;
    transition: opacity 0.15s ease, color 0.15s ease, background-color 0.15s ease;
    cursor: pointer;
}

.initiative-box__remove:hover {
    opacity: 1;
    color: #dc2626;
    background: rgba(255, 255, 255, 0.35);
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
    .mockup-header {
        padding: 16px 18px 10px;
    }

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

:deep(.dark) .goal-cell--tbc {
    background: #0f172a;
}

:deep(.dark) .goal-cell--tbc .goal-cell__code,
:deep(.dark) .goal-cell--tbc .goal-cell__text {
    color: #cbd5e1;
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

:deep(.dark) .initiative-box__remove:hover {
    background: rgba(15, 23, 42, 0.4);
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
