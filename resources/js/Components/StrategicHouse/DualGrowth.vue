<script setup>
import { computed, ref } from 'vue';

const initiativeColumnOptions = [2, 3, 4, 5, 6];
const initiativeColumnCount = ref(6);

const props = defineProps({
    goals: {
        type: Array,
        default: () => [],
    },
});

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
    'Coe Not Identified',
];

const normalizeCoeName = (rawName) => {
    let name = String(rawName ?? '').trim();

    if (!name || name === '-' || name.toUpperCase() === 'NO COE') return 'Coe Not Identified';

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
    if (name === 'Coe Not Identified') return 'coe-color-none';

    return 'coe-color-none';
};

const normalizeInitiative = (initiative, fallbackKey) => {
    const code = String(initiative?.code ?? '').trim();
    const name = String(initiative?.name ?? '').trim();
    const label = String(initiative?.label ?? [code, name].filter(Boolean).join(' - ')).trim();
    const coeName = normalizeCoeName(initiative?.coe_name);

    return {
        id: initiative?.id ?? fallbackKey,
        code,
        name,
        label: label !== '' ? label : '-',
        coe_name: coeName,
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
    const sourceByCode = new Map(
        (Array.isArray(props.goals) ? props.goals : [])
            .filter((goal) => goal?.code)
            .map((goal) => [String(goal.code).toUpperCase(), goal]),
    );

    return fallbackGoals.map((fallbackGoal) => {
        const rawGoal = sourceByCode.get(fallbackGoal.code) ?? fallbackGoal;

        const themes = (Array.isArray(rawGoal?.themes) ? rawGoal.themes : fallbackGoal.themes)
            .map((theme, index) => {
                const initiatives = Array.isArray(theme?.initiatives)
                    ? theme.initiatives.map((initiative, initiativeIndex) => normalizeInitiative(
                        initiative,
                        `${fallbackGoal.code}-theme-${index + 1}-initiative-${initiativeIndex + 1}`,
                    ))
                    : [];

                return {
                    id: theme?.id ?? `${fallbackGoal.code}-theme-${index + 1}`,
                    theme_number: Number(theme?.theme_number ?? index + 1),
                    name: String(theme?.name ?? theme?.label ?? `Theme ${index + 1}`),
                    initiatives_count: Number(theme?.initiatives_count ?? initiatives.length),
                    initiatives,
                };
            })
            .sort((left, right) => left.theme_number - right.theme_number);

        const directInitiatives = Array.isArray(rawGoal?.direct_initiatives)
            ? rawGoal.direct_initiatives.map((initiative, initiativeIndex) => normalizeInitiative(
                initiative,
                `${fallbackGoal.code}-direct-${initiativeIndex + 1}`,
            ))
            : [];

        const rows = themes.map((theme) => ({
            key: `theme-${theme.id}`,
            type: 'theme',
            label: `${theme.theme_number}. ${theme.name}`,
            initiatives: theme.initiatives,
        }));

        if (directInitiatives.length > 0) {
            rows.push({
                key: `${fallbackGoal.code}-direct`,
                type: 'direct',
                label: 'No themes',
                initiatives: directInitiatives,
            });
        }

        if (rows.length === 0) {
            rows.push({
                key: `${fallbackGoal.code}-empty`,
                type: 'empty',
                label: 'No themes',
                initiatives: [],
            });
        }

        return {
            id: rawGoal?.id ?? fallbackGoal.id,
            code: fallbackGoal.code,
            title: String(rawGoal?.title ?? fallbackGoal.title),
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
                    stats['Coe Not Identified'] += 1;
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
</script>

<template>
    <section class="dual-growth-mockup">
        <div class="mockup-header flex items-center justify-between">
            <p class="mockup-eyebrow">Mockup Preview</p>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Tampilan kolom:</span>
                <select v-model="initiativeColumnCount" class="initiative-view-select">
                    <option v-for="opt in initiativeColumnOptions" :key="opt" :value="opt">
                        {{ opt }} Kolom
                    </option>
                </select>
            </div>
        </div>

        <div class="dual-growth-legend">
            <div
                v-for="coe in coeLegend"
                :key="`legend-${coe.id}`"
                class="legend-item"
            >
                <span
                    class="legend-swatch"
                    :class="getCoeColorClass(coe.name)"
                ></span>
                <span class="legend-label">
                    {{ coe.name }} <span class="legend-count">({{ coe.count }})</span>
                </span>
            </div>
        </div>

        <div class="mockup-board-scroll">
            <table class="dg-table">
                <thead>
                    <tr>
                        <th class="head-empty" colspan="2"></th>
                        <th class="head-initiative">Digital Initiatives</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="goal in displayGoals" :key="goal.id ?? goal.code">
                        <tr
                            v-for="(row, rowIndex) in goal.rows"
                            :key="row.key"
                        >
                            <td
                                v-if="rowIndex === 0"
                                class="goal-cell"
                                :rowspan="goal.rows.length"
                                :colspan="!goal.rows.some(r => r.type === 'theme') ? 2 : 1"
                            >
                                <div class="goal-cell__inner">
                                    <span class="goal-cell__text">{{ goal.title }}</span>
                                </div>
                            </td>

                            <td
                                v-if="goal.rows.some(r => r.type === 'theme')"
                                class="theme-cell"
                                :class="{ 'theme-cell--empty': row.type !== 'theme' }"
                            >
                                <template v-if="row.type === 'theme'">
                                    <div class="theme-cell__inner">
                                        <span class="theme-cell__text">{{ row.label }}</span>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="theme-cell__placeholder">
                                        {{ row.label }}
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
                                        :class="getCoeColorClass(initiative.coe_name)"
                                        :title="initiative.label"
                                    >
                                        <span
                                            v-if="initiative.code"
                                            class="initiative-box__code"
                                        >
                                            {{ initiative.code }}
                                        </span>

                                        <span
                                            class="initiative-box__name"
                                            :class="{ 'initiative-box__name--full': !initiative.code }"
                                        >
                                            <span class="initiative-box__label-text">
                                                {{ initiativeDisplayName(initiative) }}
                                            </span>
                                        </span>
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
    margin-top: 24px;
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
}

.dg-table thead th {
    border: 1px solid #ffffff;
    padding: 0;
    background: #0f6fb7;
}

.head-empty {
    width: 160px;
}

.head-initiative {
    padding: 12px 16px !important;
    text-align: center;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.02em;
    color: #ffffff;
}

.initiatives-cell {
    border: 1px solid #d7e1ea;
    vertical-align: top;
}

.goal-cell,
.theme-cell {
    border: 1px solid #d7e1ea;
    vertical-align: middle;
}

.goal-cell {
    width: 88px;
    background: #0f6fb7;
}

.goal-cell__inner,
.theme-cell__inner {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100%;
    padding: 16px 10px;
}

.goal-cell__text,
.theme-cell__text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    text-align: center;
}

.goal-cell__text {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.1;
    color: #ffffff;
}

.theme-cell {
    width: 72px;
    background: linear-gradient(180deg, #78b8ea 0%, #63a9df 100%);
}

.theme-cell__text {
    font-size: 11px;
    font-weight: 700;
    line-height: 1.25;
    color: #ffffff;
}

.theme-cell--empty {
    background: #ffffff;
}

.theme-cell__placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100%;
    padding: 14px 8px;
    font-size: 10px;
    font-style: italic;
    text-align: center;
    color: #94a3b8;
}

.initiatives-cell {
    padding: 8px;
    background: #ffffff;
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
    overflow: hidden;
}

.coe-color-blue { background-color: #eff6ff; border-color: #1d4ed8 !important; }
.coe-color-emerald { background-color: #ecfdf5; border-color: #047857 !important; }
.coe-color-amber { background-color: #fffbeb; border-color: #b45309 !important; }
.coe-color-purple { background-color: #faf5ff; border-color: #6d28d9 !important; }
.coe-color-rose { background-color: #fff1f2; border-color: #be123c !important; }
.coe-color-none { background-color: #ffffff; border-color: #374151 !important; }

.legend-swatch.coe-color-blue { background-color: #1d4ed8 !important; }
.legend-swatch.coe-color-emerald { background-color: #047857 !important; }
.legend-swatch.coe-color-amber { background-color: #b45309 !important; }
.legend-swatch.coe-color-purple { background-color: #6d28d9 !important; }
.legend-swatch.coe-color-rose { background-color: #be123c !important; }
.legend-swatch.coe-color-none { background-color: #374151 !important; }

.coe-color-blue .initiative-box__code { border-right-color: #1d4ed8; background-color: rgba(29, 78, 216, 0.1); }
.coe-color-emerald .initiative-box__code { border-right-color: #047857; background-color: rgba(4, 120, 87, 0.1); }
.coe-color-amber .initiative-box__code { border-right-color: #b45309; background-color: rgba(180, 83, 9, 0.1); }
.coe-color-purple .initiative-box__code { border-right-color: #6d28d9; background-color: rgba(109, 40, 217, 0.1); }
.coe-color-rose .initiative-box__code { border-right-color: #be123c; background-color: rgba(190, 18, 60, 0.1); }

.initiative-box__code {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #374151;
    padding: 2px 4px;
    font-weight: 700;
    letter-spacing: 0.01em;
    white-space: nowrap;
}

.initiative-box__name {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    padding: 2px 8px 2px 5px;
    word-break: break-word;
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

@media (max-width: 1024px) {
    .dg-table {
        min-width: 980px;
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
:deep(.dark) .head-initiative,
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

:deep(.dark) .head-empty,
:deep(.dark) .head-initiative,
:deep(.dark) .theme-cell--empty,
:deep(.dark) .initiatives-cell {
    background: #0f172a;
}

:deep(.dark) .goal-cell,
:deep(.dark) .theme-cell {
    background: #36588f;
}

:deep(.dark) .theme-cell__text {
    color: rgba(226, 232, 240, 0.82);
}

:deep(.dark) .initiative-box {
    color: #f8fafc;
}

:deep(.dark) .initiative-box__code {
    color: #f8fafc;
}

:deep(.dark) .coe-color-blue { background-color: rgba(29, 78, 216, 0.2); }
:deep(.dark) .coe-color-emerald { background-color: rgba(4, 120, 87, 0.2); }
:deep(.dark) .coe-color-amber { background-color: rgba(180, 83, 9, 0.2); }
:deep(.dark) .coe-color-purple { background-color: rgba(109, 40, 217, 0.2); }
:deep(.dark) .coe-color-rose { background-color: rgba(190, 18, 60, 0.2); }

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
