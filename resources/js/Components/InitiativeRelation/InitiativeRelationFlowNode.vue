<template>
    <div
        class="initiative-status-card"
        :class="[data.statusClass, { 'initiative-status-card--focus': isFocused }]"
        :title="cardTitle"
    >
        <Handle type="target" :position="Position.Left" class="initiative-status-card__handle initiative-status-card__handle--left" />
        <Handle type="source" :position="Position.Right" class="initiative-status-card__handle initiative-status-card__handle--right" />

        <div class="initiative-status-card__code">
            {{ data.code }}
        </div>

        <div class="initiative-status-card__body">
            <div class="initiative-status-card__name">
                {{ data.name }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Handle, Position } from '@vue-flow/core';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    selected: {
        type: Boolean,
        default: false,
    },
});

const isFocused = computed(() => Boolean(props.selected || props.data?.isCurrent));

const cardTitle = computed(() => {
    const parts = [];

    if (props.data?.code) {
        parts.push(`[${props.data.code}]`);
    }

    if (props.data?.name) {
        parts.push(props.data.name);
    }

    if (props.data?.statusLabel) {
        parts.push(props.data.statusLabel);
    }

    return parts.join(' · ');
});
</script>

<style scoped>
.initiative-status-card {
    position: relative;
    display: inline-flex;
    min-width: 230px;
    max-width: 280px;
    overflow: hidden;
    border: 1px solid var(--initiative-status-border, #2563eb);
    border-radius: 0.25rem;
    background: var(--initiative-status-bg, #eff6ff);
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    user-select: none;
    transition: box-shadow 0.15s ease;
}

.initiative-status-card:hover {
    box-shadow: 0 3px 8px rgba(15, 23, 42, 0.12);
    outline: 2px solid var(--initiative-status-border, #2563eb);
    outline-offset: 2px;
}

.initiative-status-card:active {
    cursor: pointer;
}

.initiative-status-card--focus {
    outline: 3px solid var(--initiative-status-border, #2563eb);
    outline-offset: 3px;
    box-shadow: 0 0 0 5px color-mix(in srgb, var(--initiative-status-border, #2563eb) 20%, transparent),
                0 8px 20px rgba(15, 23, 42, 0.18);
    cursor: grab;
}

.initiative-status-card--focus:active {
    cursor: grabbing;
}

.initiative-status-card__code {
    display: flex;
    min-width: 44px;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    border-right: 1px solid var(--initiative-status-border, #2563eb);
    background: var(--initiative-status-accent, #2563eb);
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.1;
}

.initiative-status-card__body {
    min-width: 0;
    flex: 1;
    padding: 5px 9px 6px;
}

.initiative-status-card__name {
    color: var(--initiative-status-text, #1e3a8a);
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
    white-space: normal;
    word-break: break-word;
}

.initiative-status-card__handle {
    position: absolute;
    top: 50%;
    width: 8px;
    height: 8px;
    opacity: 0;
    pointer-events: none;
    border: 0;
    background: transparent;
    transform: translateY(-50%);
}

.initiative-status-card__handle--left {
    left: -4px;
}

.initiative-status-card__handle--right {
    right: -4px;
}

.status-on-track {
    --initiative-status-border: #059669;
    --initiative-status-accent: #10b981;
    --initiative-status-bg: #ecfdf5;
    --initiative-status-text: #064e3b;
}

.status-done {
    --initiative-status-border: #2563eb;
    --initiative-status-accent: #3b82f6;
    --initiative-status-bg: #eff6ff;
    --initiative-status-text: #1e3a8a;
}

.status-at-risk {
    --initiative-status-border: #d97706;
    --initiative-status-accent: #f59e0b;
    --initiative-status-bg: #fffbeb;
    --initiative-status-text: #78350f;
}

.status-delayed {
    --initiative-status-border: #e11d48;
    --initiative-status-accent: #f43f5e;
    --initiative-status-bg: #fff1f2;
    --initiative-status-text: #881337;
}

.status-not-signed {
    --initiative-status-border: #e11d48;
    --initiative-status-accent: #f43f5e;
    --initiative-status-bg: #fff1f2;
    --initiative-status-text: #881337;
}

.status-not-started {
    --initiative-status-border: #475569;
    --initiative-status-accent: #64748b;
    --initiative-status-bg: #f8fafc;
    --initiative-status-text: #0f172a;
}

:deep(.dark) .initiative-status-card {
    background: #1f1f1f;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.22);
}

:deep(.dark) .initiative-status-card__name {
    color: #f8fafc;
}

:deep(.dark) .status-on-track {
    --initiative-status-border: #10b981;
    --initiative-status-accent: #059669;
    --initiative-status-bg: rgba(16, 185, 129, 0.14);
    --initiative-status-text: #d1fae5;
}

:deep(.dark) .status-done {
    --initiative-status-border: #3b82f6;
    --initiative-status-accent: #2563eb;
    --initiative-status-bg: rgba(59, 130, 246, 0.14);
    --initiative-status-text: #dbeafe;
}

:deep(.dark) .status-at-risk {
    --initiative-status-border: #f59e0b;
    --initiative-status-accent: #d97706;
    --initiative-status-bg: rgba(245, 158, 11, 0.14);
    --initiative-status-text: #fef3c7;
}

:deep(.dark) .status-delayed {
    --initiative-status-border: #f43f5e;
    --initiative-status-accent: #e11d48;
    --initiative-status-bg: rgba(244, 63, 94, 0.14);
    --initiative-status-text: #ffe4e6;
}

:deep(.dark) .status-not-signed {
    --initiative-status-border: #f43f5e;
    --initiative-status-accent: #e11d48;
    --initiative-status-bg: rgba(244, 63, 94, 0.14);
    --initiative-status-text: #ffe4e6;
}

:deep(.dark) .status-not-started {
    --initiative-status-border: #64748b;
    --initiative-status-accent: #475569;
    --initiative-status-bg: rgba(100, 116, 139, 0.14);
    --initiative-status-text: #f1f5f9;
}
</style>