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
            coe: displayValue(mi.coe?.name ?? mi.coe_name ?? mi.coe),
            projectOwner: displayValue(
                organization?.name ?? mi.project_owner ?? mi.owner_name ?? mi.projectOwner ?? mi.business_unit
            ),
            group: displayValue(organization?.groub?.name ?? mi.group ?? mi.business_unit),
            description: displayValue(mi.description),
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

const getScoreLabel = (type) => {
    const source = props.initiative;
    if (!source) return '-';

    // 1. Try to find direct label
    const label = source[`${type}_label`] ?? source.appendix_data?.[`${type}_label`] ?? source.project_charter?.[`${type}_label`];
    if (label && String(label).trim() !== '') return label;

    // 2. Try to map numeric value
    const val = source[type] ?? source.appendix_data?.[type] ?? source.project_charter?.[type];
    const numeric = normalizeNumericValue(val);
    if (numeric === 1) return 'High';
    if (numeric === 2) return 'Medium';
    if (numeric === 3) return 'Low';

    return '-';
};

const headerScores = computed(() => ({
    value: getScoreLabel('value'),
    urgency: getScoreLabel('urgency'),
    ease: getScoreLabel('ease'),
    resource: getScoreLabel('resource'),
}));
</script>

<template>
    <div class="charter-header-wrap bg-white text-slate-900 border border-slate-200">
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
    font-weight: 700;
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
