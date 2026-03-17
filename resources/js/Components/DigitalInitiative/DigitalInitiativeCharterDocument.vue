<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: { type: Object, required: true },
});

const displayValue = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};

const mapSourceCreated = (source) => {
    if (!source) return '-';

    const month = displayValue(source.month);
    const year = displayValue(source.year);

    if (month !== '-' && year !== '-') {
        return `${month} ${year}`.trim();
    }

    if (month !== '-') return month;
    if (year !== '-') return year;

    return '-';
};

const mappedInitiatives = computed(() => {
    const source = props.initiative?.mapped_initiatives ?? props.initiative?.mappedInitiatives ?? [];
    const list = Array.isArray(source) ? source : [];

    return list.map((mi) => {
        const organization = mi.organization ?? null;
        const sourceData = mi.source_data ?? mi.sourceData ?? null;

        let sourceCreated = mapSourceCreated(sourceData);
        if (sourceCreated === '-') {
            sourceCreated = displayValue(mi.data_source_created ?? mi.source_data_created);
        }

        return {
            id: mi.id,
            code: String(mi.code ?? '').trim().replace(/#/g, ''),
            name: String(mi.name ?? `Initiative ${mi.id}`).trim(),
            coe: displayValue(mi.coe?.name ?? mi.coe_name ?? mi.coe ?? mi.coe_id),
            projectOwner: displayValue(
                organization?.name ?? mi.project_owner ?? mi.owner_name ?? mi.projectOwner ?? mi.business_unit
            ),
            group: displayValue(organization?.groub?.name ?? mi.group ?? mi.business_unit),
            description: displayValue(mi.description),
            dataSource: displayValue(
                sourceData?.name ?? mi.data_source ?? mi.source ?? mi.data_source_name
            ),
            dataSourceCreated: sourceCreated,
        };
    });
});

const uniqueValues = (values) => {
    const cleaned = values
        .map((value) => String(value ?? '').trim())
        .filter((value) => value !== '' && value !== '-');
    return [...new Set(cleaned)];
};

const joinValues = (values) => (values.length ? values.join(', ') : '-');

const headerMeta = computed(() => {
    const owners = uniqueValues(mappedInitiatives.value.map((item) => item.projectOwner));
    const coes = uniqueValues(mappedInitiatives.value.map((item) => item.coe));
    const sources = uniqueValues(
        mappedInitiatives.value.map((item) => {
            const name = String(item.dataSource ?? '').trim();
            const created = String(item.dataSourceCreated ?? '').trim();

            if (name && name !== '-' && created && created !== '-') {
                return `${name} (${created})`;
            }
            if (name && name !== '-') return name;
            if (created && created !== '-') return created;
            return '';
        })
    );

    return {
        owners: joinValues(owners),
        coes: joinValues(coes),
        sources: joinValues(sources),
    };
});

const mappedInitiativeGoals = computed(() => {
    const source = props.initiative?.mapped_initiatives ?? props.initiative?.mappedInitiatives ?? [];
    const list = Array.isArray(source) ? source : [];

    return list.flatMap((mi) => {
        const taggings = Array.isArray(mi.taggings)
            ? mi.taggings
            : Array.isArray(mi.initiative_taggings)
                ? mi.initiative_taggings
                : [];

        return taggings.map((tag) => {
            const theme = tag.theme ?? tag.themes ?? null;
            return {
                initiativeCode: String(mi.code ?? '').trim().replace(/#/g, ''),
                goal: tag.goal ?? '-',
                strategicPillar: theme?.strategic_pillar ?? '-',
                themeCode: String(theme?.theme_code ?? theme?.theme_number ?? theme?.code ?? '-').replace(/#/g, ''),
                themeName: theme?.theme_name ?? theme?.name ?? '-',
            };
        });
    });
});
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] bg-white text-slate-900 shadow-sm print:shadow-none">
        <!-- Header -->
        <div class="border-b border-slate-200 px-5 pb-3 pt-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                            <span class="shrink-0 text-[#3b5e96]">Digital Initiative</span>
                            <span class="mx-2 shrink-0 text-slate-400">|</span>
                            <span class="">{{ displayValue(initiative.no || initiative.code) }}</span>
                        </h1>
                    </div>
                    <p class="mt-1 text-[13px] text-slate-600">
                        {{ displayValue(initiative.useCase || initiative.name) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Bar -->
        <div class="info-bar">
            <div class="info-cell info-cell-compact">
                <span class="info-label info-label">Project Owner</span>
                <span class="info-sep"></span>
                <span class="info-value">{{ headerMeta.owners }}</span>
            </div>
            <div class="info-cell info-cell-coe">
                <span class="info-label">CoE</span>
                <span class="info-sep"></span>
                <span class="info-value">{{ headerMeta.coes }}</span>
            </div>
            <div class="info-cell info-cell-last">
                <span class="info-label">Data Source</span>
                <span class="info-sep"></span>
                <span class="info-value">{{ headerMeta.sources }}</span>
            </div>
        </div>

        <!-- Master Initiative Goal -->
        <div class="charter-section">
            <article class="panel border-t-0">
                <div class="bar-sub">Master Initiative Goal</div>
                <div class="panel-body space-y-3">
                    <div class="table-wrap">
                        <table class="initiative-table">
                            <thead>
                                <tr>
                                    <th class="w-[45px] text-center">Goal</th>
                                    <th class="text-center">Strategic Pillar Title</th>
                                    <th colspan="2" class="text-center">Themes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!mappedInitiativeGoals.length">
                                    <td colspan="4" class="empty-row text-center">Belum ada goal yang tersedia untuk initiative ini.</td>
                                </tr>
                                <tr v-for="(goal, index) in mappedInitiativeGoals" :key="`goal-${index}`">
                                    <td class="cell-center">{{ goal.goal }}</td>
                                    <td>{{ goal.strategicPillar }}</td>
                                    <td class="cell-center">{{ goal.themeCode }}</td>
                                    <td>{{ goal.themeName }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
        </div>
    </article>
</template>

<style scoped>
.charter-sheet {
    font-family: "Segoe UI", Arial, sans-serif;
    font-size: 13px;
    color: #1a1a1a;
    border: 1px solid #ccc;
}

.info-bar {
    display: flex;
    border-bottom: 1px solid #ccc;
    background: #f8f8f8;
}

.info-cell {
    display: flex;
    align-items: stretch;
    border-right: 1px solid #ccc;
    flex: 1;
}

.info-cell-compact {
    flex: 0.45;
}

.info-cell-coe {
    flex: 0.34;
}

.info-cell-last {
    border-right: none;
    flex: 0.8;
}

.info-label {
    background: #2563a8;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    flex-shrink: 0;
}

.info-label-dark {
    background: #2e6ea2;
}

.info-sep {
    width: 0;
    border-right: 1px solid #aac4e0;
}

.info-value {
    padding: 6px 10px;
    font-size: 13px;
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.charter-section {
    padding: 0;
    border-top: 1px solid #ddd;
}

.bar-main {
    background: #1e4f8f;
    color: #fff;
    padding: 7px 12px;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
}

.bar-sub {
    background: #2e6ea2;
    color: #fff;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
}

.panel {
    border: 1px solid #1e4f8f;
    border-radius: 0;
    background: transparent;
}

.panel-body {
    padding: 10px;
    background: #fff;
    font-size: 12px;
}

.table-wrap {
    overflow-x: auto;
    border: 1px solid #cbd5e1;
}

.initiative-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.initiative-table th,
.initiative-table td {
    border: 1px solid #cbd5e1;
    padding: 8px 10px;
    vertical-align: middle;
}

.initiative-table th {
    background: #eff6ff;
    color: #1e3a8a;
    font-size: 11px;
    font-weight: 700;
    text-align: left;
}

.cell-center {
    text-align: center;
    white-space: nowrap;
}

.empty-row {
    text-align: center;
    color: #94a3b8;
    font-style: italic;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    .charter-sheet {
        width: 100%;
        max-width: none;
        border: none;
        box-shadow: none;
    }

    .panel-body {
        background: #fff !important;
    }

    .table-wrap {
        overflow: visible;
    }
}

@media (max-width: 768px) {
    .info-bar {
        flex-direction: column;
    }

    .info-cell {
        border-right: none;
        border-bottom: 1px solid #ccc;
    }

    .info-cell-last {
        border-bottom: none;
    }
}
</style>
