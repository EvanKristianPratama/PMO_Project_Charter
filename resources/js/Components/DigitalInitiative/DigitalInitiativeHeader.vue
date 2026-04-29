<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: { type: Object, required: true },
});

const displayValue = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};

const normalizeNumericValue = (value) => {
    const digits = String(value ?? '').trim().replace(/[^\d-]/g, '');
    if (digits === '') return null;

    const parsed = Number(digits);
    return Number.isFinite(parsed) ? parsed : null;
};

const pickDisplayValue = (...values) => {
    for (const value of values) {
        const trimmed = String(value ?? '').trim();
        if (trimmed !== '') {
            return trimmed;
        }
    }

    return '-';
};

const headerTitle = computed(() => (
    pickDisplayValue(
        props.initiative?.name,
        props.initiative?.useCase,
        props.initiative?.usecase
    )
));

const headerDescription = computed(() => (
    pickDisplayValue(
        props.initiative?.description,
        props.initiative?.detail_useCase_description
    )
));

const statusLabel = computed(() => {
    const source = props.initiative;
    return displayValue(source?.statusRef?.name ?? source?.status_name ?? source?.status);
});

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
    let source = props.initiative?.mapped_initiatives ?? props.initiative?.mappedInitiatives;

    if (!source && props.initiative) {
        source = [props.initiative];
    }

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
            name: displayValue(mi.name),
            projectOwner: displayValue(
                mi.appendix_data?.owner ?? mi.appendixData?.owner ?? mi.owner ?? mi.project_owner ?? mi.owner_name ?? mi.projectOwner
            ),
            pic: displayValue(
                mi.appendix_data?.organization ?? mi.appendixData?.organization ?? mi.pic ?? mi.organization_name
            ),
            group: displayValue(organization?.groub?.name ?? mi.group ?? mi.business_unit),
            description: displayValue(mi.description),
            coe: displayValue(mi.appendix_data?.coe ?? mi.appendixData?.coe ?? mi.coe?.name ?? mi.coe_name ?? mi.coe),
            dataSource: displayValue(
                sourceData?.name ?? mi.data_source_name ?? mi.data_source ?? mi.source_name ?? (typeof mi.source === 'object' ? mi.source?.name : null)
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
    const pics = uniqueValues(mappedInitiatives.value.map((item) => item.pic));
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
        pics: joinValues(pics),
        coes: joinValues(coes),
        sources: joinValues(sources),
    };
});

const getScoreLabel = (type) => {
    const source = props.initiative;
    if (!source) return '-';

    // Define potential data paths based on DigitalCharterDocument and AppendixCharterDocument structures
    const paths = [
        source,
        source.appendix_data,
        source.appendixData,
        source.project_charter,
        source.projectCharter,
        source.charter,
    ];

    // 1. Try to find direct label (e.g., value_label, valueLabel)
    for (const p of paths) {
        if (!p) continue;
        const label = p[`${type}_label`] ?? p[`${type}Label`];
        if (label && String(label).trim() !== '' && label !== '-') {
            return String(label).trim();
        }
    }

    // 2. Try to map numeric value (1=High, 2=Medium, 3=Low)
    for (const p of paths) {
        if (!p) continue;
        const val = p[type];
        const numeric = normalizeNumericValue(val);
        if (numeric === 1) return 'High';
        if (numeric === 2) return 'Medium';
        if (numeric === 3) return 'Low';
    }

    return '-';
};

const headerScores = computed(() => ({
    value: getScoreLabel('value'),
    urgency: getScoreLabel('urgency'),
    ease: getScoreLabel('ease'),
    resource: getScoreLabel('resource'),
}));

const getLevelColorClass = (label) => {
    if (!label) return 'hidden';
    const l = String(label).toLowerCase();
    if (l === 'high') return 'bg-emerald-500 text-white';
    if (l === 'medium') return 'bg-orange-500 text-white';
    if (l === 'low') return 'bg-rose-500 text-white';
    return 'bg-slate-400 text-white';
};

const getLongText = (key) => {
    const source = props.initiative;
    if (!source) return '-';

    const paths = [
        source,
        source.appendix_data,
        source.appendixData,
        source.project_charter,
        source.projectCharter,
        source.charter,
    ];

    for (const p of paths) {
        if (!p) continue;
        const val = p[key];
        if (val && String(val).trim() !== '' && val !== '-') {
            return String(val).trim();
        }
    }

    return '-';
};
</script>

<template>
    <div class="charter-header-wrap bg-white text-slate-900 border border-[#3b82f6] shadow-sm">
        <!-- Header -->
        <div class="border-b border-slate-200 px-5 pb-3 pt-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                            <span class="shrink-0 text-[#3b5e96]">Digital Initiative</span>
                            <span class="mx-2 shrink-0 text-slate-400">|</span>
                            <span>{{ headerTitle }}</span>
                        </h1>
                        <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 border border-slate-200">
                            {{ statusLabel }}
                        </span>
                    </div>
                    <p class="mt-1 text-[13px] text-slate-600">
                        {{ headerDescription }}
                    </p>
                </div>

                <!-- Score Panel -->
                <div class="score-panel">
                    <div class="score-column border-r border-[#3b82f6]">
                        <div class="bar-sub-mini text-center">Value</div>
                        <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                            {{ headerScores.value }}
                        </div>
                    </div>
                    <div class="score-column border-r border-[#3b82f6]">
                        <div class="bar-sub-mini text-center">Urgency</div>
                        <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                            {{ headerScores.urgency }}
                        </div>
                    </div>
                    <div class="score-column border-r border-[#3b82f6]">
                        <div class="bar-sub-mini text-center">Easy</div>
                        <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                            {{ headerScores.ease }}
                        </div>
                    </div>
                    <div class="score-column">
                        <div class="bar-sub-mini text-center">Resource</div>
                        <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                            {{ headerScores.resource }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Bar -->
        <div class="info-bar text-[12px]">
            <div class="info-cell info-cell-compact">
                <span class="info-label">Project Owner</span>
                <span class="info-sep"></span>
                <span class="info-value">{{ headerMeta.owners }}</span>
            </div>
            <div class="info-cell info-cell-pic">
                <span class="info-label">PIC</span>
                <span class="info-sep"></span>
                <span class="info-value">{{ headerMeta.pics }}</span>
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

        <!-- Additional Info: Value & Urgency Rationale -->
        <div class="grid grid-cols-1 lg:grid-cols-2 border-t border-[#3b82f6]">
            <!-- Value Indication Detail -->
            <div class="flex flex-col lg:border-r lg:border-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white flex items-center justify-start gap-2">
                    <span>Value Indication</span>
                </div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">Rationale</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ getLongText('value_rationale') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">Metrics Impacted</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ getLongText('value_matrics') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Urgency Detail -->
            <div class="flex flex-col">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white flex items-center justify-start gap-2">
                    <span>Urgency</span>    
                </div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">Rationale</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ getLongText('urgency_rationale') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center text-[10px] leading-tight">Expected Go-Live</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ getLongText('urgency_expected') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.charter-header-wrap {
    font-family: "Segoe UI", Arial, sans-serif;
    font-size: 13px;
    color: #1a1a1a;
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
    flex: 0.35;
}

.info-cell-pic {
    flex: 0.25;
}

.info-cell-coe {
    flex: 0.3;
}

.info-cell-last {
    border-right: none;
    flex: 0.5;
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

.score-panel {
    display: flex;
    border: 1px solid #3b82f6;
    min-width: 320px;
    background: #fff;
    align-self: flex-start;
}

.score-column {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.bar-sub-mini {
    background: #2e6ea2;
    color: #fff;
    padding: 3px 8px;
    font-size: 11px;
    line-height: 1.2;
}

.panel-body-mini {
    padding: 6px;
    background: #fff;
    min-height: 32px;
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
