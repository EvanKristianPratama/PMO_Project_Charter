<script setup>
import { computed } from 'vue';
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    project: { type: Object, required: true },
    sequence: { type: [Number, String], default: null },
    yearStart: { type: Number, default: 2025 },
    yearEnd: { type: Number, default: 2029 },
    expanded: { type: Boolean, default: false },
    displayVersionLabel: { type: [String, Number], default: null },
});

const emit = defineEmits(['toggle']);

const yStart = computed(() => Number(props.yearStart) || 2025);
const yEnd = computed(() => {
    const end = Number(props.yearEnd) || 2029;
    return end >= yStart.value ? end : yStart.value;
});

const years = computed(() =>
    Array.from({ length: yEnd.value - yStart.value + 1 }, (_, i) => yStart.value + i)
);

const totalQuarters = computed(() => years.value.length * 4);

const TYPE_BLOCK = 1;
const TYPE_DASHED = 2;

const parseDateMeta = (value) => {
    if (!value) return null;
    const matched = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (matched) {
        return { year: Number(matched[1]), monthIndex: Number(matched[2]) - 1 };
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) return null;

    return { year: parsed.getUTCFullYear(), monthIndex: parsed.getUTCMonth() };
};

const toQuarterIndex = (dateValue) => {
    const meta = parseDateMeta(dateValue);
    if (!meta || totalQuarters.value === 0) return null;

    const raw = (meta.year - yStart.value) * 4 + Math.floor(meta.monthIndex / 3);
    return Math.max(0, Math.min(raw, totalQuarters.value - 1));
};

const normalizeMilestoneType = (value) => {
    const raw = Number(value);
    return raw === TYPE_DASHED || raw === 4 ? TYPE_DASHED : TYPE_BLOCK;
};

const normalizeVersionKey = (value) => {
    const raw = String(value ?? '').trim();
    const lower = raw.toLowerCase();

    if (!raw || lower === 'v') {
        return 'v1';
    }

    if (/^v\d+$/.test(lower)) {
        return `v${Math.max(Number(lower.slice(1)) || 1, 1)}`;
    }

    if (/^\d+$/.test(raw)) {
        return `v${Math.max(Number(raw) || 1, 1)}`;
    }

    return lower;
};

const toDisplayVersionLabel = (value) => {
    const raw = String(value ?? '').trim();
    const lower = raw.toLowerCase();

    if (!raw || lower === 'v') {
        return 'v1';
    }

    if (/^\d+$/.test(raw)) {
        return `v${Math.max(Number(raw) || 1, 1)}`;
    }

    return raw;
};

const versionEntries = computed(() => {
    const versions = new Map();
    const registerVersion = (value) => {
        const key = normalizeVersionKey(value);
        if (!versions.has(key)) {
            versions.set(key, toDisplayVersionLabel(value));
        }
    };

    (props.project?.milestones ?? []).forEach((milestone) => {
        registerVersion(milestone?.version);
    });

    (Array.isArray(props.project?.charters) ? props.project.charters : []).forEach((charter) => {
        registerVersion(charter?.version_label);
    });

    if (props.project?.charter?.version_label) {
        registerVersion(props.project.charter.version_label);
    }

    if (versions.size === 0) {
        registerVersion('v1');
    }

    return Array.from(versions.entries())
        .map(([key, label]) => ({ key, label }))
        .sort((a, b) => {
            const numA = Number(a.key.replace(/\D/g, '')) || 0;
            const numB = Number(b.key.replace(/\D/g, '')) || 0;

            if (numA !== numB) {
                return numA - numB;
            }

            return a.key.localeCompare(b.key);
        });
});

const versionTimelineRanges = computed(() => {
    const ranges = versionEntries.value.map((version) => ({
        ...version,
        solid: null,
        dashed: null,
    }));

    const rangeByVersion = new Map(ranges.map((item) => [item.key, item]));

    (props.project?.milestones ?? []).forEach((milestone) => {
        const key = normalizeVersionKey(milestone?.version);
        const bucket = rangeByVersion.get(key);
        if (!bucket) return;

        const si = toQuarterIndex(milestone.start_date ?? milestone.end_date);
        const ei = toQuarterIndex(milestone.end_date ?? milestone.start_date);
        if (si === null && ei === null) return;

        const range = {
            start: Math.min(si ?? ei, ei ?? si),
            end: Math.max(si ?? ei, ei ?? si),
        };

        const target = normalizeMilestoneType(milestone.milestone_type ?? milestone.type) === TYPE_DASHED
            ? 'dashed'
            : 'solid';

        if (!bucket[target]) {
            bucket[target] = range;
            return;
        }

        bucket[target] = {
            start: Math.min(bucket[target].start, range.start),
            end: Math.max(bucket[target].end, range.end),
        };
    });

    return ranges;
});

const rangeStyle = (range) => {
    if (!range || totalQuarters.value === 0) return null;

    const left = (range.start / totalQuarters.value) * 100;
    const width = ((range.end - range.start + 1) / totalQuarters.value) * 100;

    return { left: `${left}%`, width: `${width}%` };
};

const projectVersionLabel = computed(() => {
    const explicit = String(props.displayVersionLabel ?? '').trim();
    if (explicit) {
        return explicit;
    }

    const fromCharter = String(props.project?.charter?.version_label ?? '').trim();
    if (fromCharter) {
        return fromCharter;
    }

    const firstVersion = versionEntries.value[0]?.label ?? '';
    return String(firstVersion).trim();
});

const hideInlineVersionPills = computed(() => {
    return String(props.displayVersionLabel ?? '').trim().length > 0;
});
</script>

<template>
    <div
        class="summary-row"
        :class="{ 'summary-row--expanded': expanded }"
        role="button"
        tabindex="0"
        @click="emit('toggle')"
        @keydown.enter.prevent="emit('toggle')"
    >
        <div class="col-no">
            <span class="seq-badge">{{ sequence ?? '' }}</span>
        </div>

        <div class="col-name">
            <div class="project-name-wrap">
                <span class="project-name">{{ project.name || '-' }}</span>
                <span v-if="projectVersionLabel" class="project-version-capsule">{{ projectVersionLabel }}</span>
            </div>
        </div>

        <div class="col-timeline">
            <div class="year-row">
                <div v-for="year in years" :key="`y-${year}`" class="year-cell">{{ year }}</div>
            </div>

            <div class="timeline-version-list">
                <div
                    v-for="version in versionTimelineRanges"
                    :key="`timeline-version-${version.key}`"
                    class="timeline-version-row"
                >
                    <span v-if="!hideInlineVersionPills" class="version-pill">{{ version.label }}</span>

                    <div class="bar-track">
                        <span
                            v-for="(year, yi) in years"
                            :key="`div-${version.key}-${year}`"
                            class="year-divider"
                            :style="{ left: `${((yi + 1) / years.length) * 100}%` }"
                        ></span>

                        <div v-if="rangeStyle(version.dashed)" class="bar-dashed" :style="rangeStyle(version.dashed)"></div>
                        <div v-if="rangeStyle(version.solid)" class="bar-fill" :style="rangeStyle(version.solid)"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-toggle">
            <span class="toggle-btn">
                <ChevronUpIcon v-if="expanded" class="toggle-icon" />
                <ChevronDownIcon v-else class="toggle-icon" />
            </span>
        </div>
    </div>
</template>

<style scoped>
.summary-row {
    display: flex;
    align-items: stretch;
    border: 1px solid #d0dce8;
    border-bottom: none;
    background: #ffffff;
    cursor: pointer;
    transition: background 0.12s ease;
    min-height: 38px;
    font-family: "Segoe UI", Arial, sans-serif;
}

.summary-row:last-child,
.summary-row--expanded {
    border-bottom: 1px solid #d0dce8;
}

.summary-row:first-child {
    border-radius: 6px 6px 0 0;
}

.summary-row:hover {
    background: #f0f7ff;
}

.summary-row--expanded {
    background: #eaf3fb;
    border-color: #1c75bc;
}

.col-no {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    flex-shrink: 0;
    border-right: 1px solid #d0dce8;
}

.seq-badge {
    font-size: 10px;
    font-weight: 700;
    color: #1c75bc;
}

.col-name {
    display: flex;
    align-items: center;
    width: 28%;
    flex-shrink: 0;
    padding: 0 12px;
    border-right: 1px solid #d0dce8;
    min-width: 0;
}

.project-name {
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}

.project-name-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.project-version-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 10px;
    border-radius: 999px;
    border: 1px solid #9ca3af;
    background: #e5e7eb;
    color: #374151;
    font-size: 9px;
    font-weight: 600;
    line-height: 1.2;
    letter-spacing: 0.01em;
    white-space: nowrap;
}

.col-timeline {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 4px 0;
    min-width: 0;
    overflow: hidden;
}

.year-row {
    display: flex;
    padding: 0 2px;
}

.year-cell {
    flex: 1;
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    line-height: 1;
    padding-bottom: 3px;
}

.timeline-version-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.timeline-version-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.version-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    padding: 1px 6px;
    border-radius: 999px;
    border: 1px solid #94a3b8;
    background: #f1f5f9;
    color: #334155;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
}

.bar-track {
    flex: 1;
    position: relative;
    height: 12px;
    margin: 0 2px 0 0;
    background: #f1f5f9;
    border-radius: 3px;
}

.year-divider {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 1px;
    background: #cbd5e1;
    pointer-events: none;
}

.year-divider:last-of-type {
    display: none;
}

.bar-fill {
    position: absolute;
    top: 1px;
    bottom: 1px;
    border-radius: 2px;
    background: linear-gradient(90deg, #1c75bc, #2b87cc);
    transition: width 0.25s ease, left 0.25s ease;
    z-index: 2;
}

.bar-dashed {
    position: absolute;
    top: 50%;
    height: 0;
    border-top: 2px dashed #1c75bc;
    opacity: 0.8;
    transform: translateY(-50%);
    transition: width 0.25s ease, left 0.25s ease;
    z-index: 1;
}

.col-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    flex-shrink: 0;
    border-left: 1px solid #d0dce8;
}

.toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 4px;
    color: #1c75bc;
    transition: background 0.12s ease;
}

.summary-row:hover .toggle-btn {
    background: #dbeafe;
}

.toggle-icon {
    width: 14px;
    height: 14px;
}
</style>
