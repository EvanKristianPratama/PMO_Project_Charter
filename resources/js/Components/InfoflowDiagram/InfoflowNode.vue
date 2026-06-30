<template>
    <div class="infoflow-node" :class="[typeClass, { 'infoflow-node--hover': isHovered }]"
         @mouseenter="isHovered = true" @mouseleave="isHovered = false">
        <!-- Target handle (left side) -->
        <Handle v-if="data.nodeType !== 'input'" type="target" :position="Position.Left" class="infoflow-node__handle" />
        <!-- Source handle (right side) -->
        <Handle v-if="data.nodeType !== 'output'" type="source" :position="Position.Right" class="infoflow-node__handle" />

        <!-- Badge -->
        <div class="infoflow-node__badge" :class="badgeClass">
            <component :is="iconComponent" class="w-3.5 h-3.5" />
            <span>{{ badgeLabel }}</span>
        </div>

        <!-- Content -->
        <div class="infoflow-node__content">
            <div v-if="data.subtitle" class="infoflow-node__subtitle">{{ data.subtitle }}</div>
            <div class="infoflow-node__label">{{ data.label }}</div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { Handle, Position } from '@vue-flow/core';

const props = defineProps({
    data: { type: Object, default: () => ({}) },
});

const isHovered = ref(false);

const typeClass = computed(() => {
    const t = props.data?.nodeType || 'practice';
    return `infoflow-node--${t}`;
});

const badgeClass = computed(() => {
    const t = props.data?.nodeType || 'practice';
    if (t === 'input') return 'badge--input';
    if (t === 'output') return 'badge--output';
    return 'badge--practice';
});

const badgeLabel = computed(() => {
    const t = props.data?.nodeType || 'practice';
    if (t === 'input') return 'INPUT';
    if (t === 'output') return 'OUTPUT';
    return 'KEBIJAKAN';
});

// Simple inline SVG icons as render functions
const InputIcon = {
    render() {
        return h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', 'stroke-width': '2', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5' })
        ]);
    }
};

const OutputIcon = {
    render() {
        return h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', 'stroke-width': '2', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12M12 16.5V3' })
        ]);
    }
};

const PracticeIcon = {
    render() {
        return h('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', 'stroke-width': '2', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z' }),
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z' })
        ]);
    }
};

const iconComponent = computed(() => {
    const t = props.data?.nodeType || 'practice';
    if (t === 'input') return InputIcon;
    if (t === 'output') return OutputIcon;
    return PracticeIcon;
});
</script>

<style scoped>
.infoflow-node {
    position: relative;
    min-width: 220px;
    max-width: 280px;
    border-radius: 12px;
    padding: 0;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: default;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.infoflow-node--hover {
    transform: translateY(-2px);
}

/* ── Input Node ── */
.infoflow-node--input {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1.5px solid #93c5fd;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.12);
}
.infoflow-node--input.infoflow-node--hover {
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2);
    border-color: #60a5fa;
}

/* ── Practice Node ── */
.infoflow-node--practice {
    background: linear-gradient(135deg, #faf5ff, #f3e8ff);
    border: 1.5px solid #c084fc;
    box-shadow: 0 2px 8px rgba(168, 85, 247, 0.12);
}
.infoflow-node--practice.infoflow-node--hover {
    box-shadow: 0 6px 20px rgba(168, 85, 247, 0.2);
    border-color: #a855f7;
}

/* ── Output Node ── */
.infoflow-node--output {
    background: linear-gradient(135deg, #fdf2f8, #fce7f3);
    border: 1.5px solid #f472b6;
    box-shadow: 0 2px 8px rgba(236, 72, 153, 0.12);
}
.infoflow-node--output.infoflow-node--hover {
    box-shadow: 0 6px 20px rgba(236, 72, 153, 0.2);
    border-color: #ec4899;
}

/* ── Badge ── */
.infoflow-node__badge {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.badge--input {
    background: rgba(59, 130, 246, 0.15);
    color: #2563eb;
}
.badge--practice {
    background: rgba(168, 85, 247, 0.15);
    color: #7c3aed;
}
.badge--output {
    background: rgba(236, 72, 153, 0.15);
    color: #db2777;
}

/* ── Content ── */
.infoflow-node__content {
    padding: 8px 12px 10px;
}

.infoflow-node__subtitle {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 3px;
}

.infoflow-node__label {
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.35;
    word-break: break-word;
}

/* ── Handle ── */
.infoflow-node__handle {
    width: 8px !important;
    height: 8px !important;
    border: 2px solid #94a3b8 !important;
    background: white !important;
    opacity: 0;
    pointer-events: none;
}

/* ── Dark mode ── */
:deep(.dark) .infoflow-node--input {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(59, 130, 246, 0.06));
    border-color: rgba(96, 165, 250, 0.4);
}
:deep(.dark) .infoflow-node--practice {
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.12), rgba(168, 85, 247, 0.06));
    border-color: rgba(192, 132, 252, 0.4);
}
:deep(.dark) .infoflow-node--output {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.12), rgba(236, 72, 153, 0.06));
    border-color: rgba(244, 114, 182, 0.4);
}
:deep(.dark) .infoflow-node__label {
    color: #e2e8f0;
}
:deep(.dark) .infoflow-node__subtitle {
    color: #64748b;
}
:deep(.dark) .badge--input { color: #93c5fd; }
:deep(.dark) .badge--practice { color: #c4b5fd; }
:deep(.dark) .badge--output { color: #f9a8d4; }
</style>
