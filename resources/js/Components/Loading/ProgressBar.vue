<script setup>
/**
 * ProgressBar — A minimal circular progress indicator.
 *
 * Usage:
 *   <ProgressBar :progress="loadingProgress" :visible="isLoading" label="Loading..." />
 */

defineProps({
    progress: {
        type: Number,
        default: 0,
    },
    visible: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
});

const CIRCUMFERENCE = 2 * Math.PI * 20; // ~125.6
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-500"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible" class="pb-simple-overlay">
            <div class="pb-card">
                <div class="pb-simple-circle">
                    <svg viewBox="0 0 48 48" class="pb-svg">
                        <circle cx="24" cy="24" r="20" fill="none" stroke-width="3" class="pb-track" />
                        <circle
                            cx="24" cy="24" r="20"
                            fill="none"
                            stroke-width="3"
                            stroke-linecap="round"
                            class="pb-fill"
                            :stroke-dasharray="CIRCUMFERENCE"
                            :stroke-dashoffset="CIRCUMFERENCE - (CIRCUMFERENCE * Math.min(100, Math.max(0, progress))) / 100"
                        />
                    </svg>
                    <span class="pb-text">{{ Math.round(progress) }}%</span>
                </div>
                <span v-if="label" class="pb-label">{{ label }}</span>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.pb-simple-overlay {
    position: fixed;
    inset: 0;
    z-index: 60;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 24px;
    pointer-events: none;
}

.pb-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 24px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

:where(.dark, .dark *) .pb-card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.pb-simple-circle {
    position: relative;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pb-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.pb-track {
    stroke: #e2e8f0;
}

:where(.dark, .dark *) .pb-track {
    stroke: rgba(255, 255, 255, 0.1);
}

.pb-fill {
    stroke: #1C75BC;
    transition: stroke-dashoffset 0.3s ease-out;
}

:where(.dark, .dark *) .pb-fill {
    stroke: #53BDE6;
}

.pb-text {
    position: absolute;
    font-size: 0.65rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    color: #1C75BC;
}

:where(.dark, .dark *) .pb-text {
    color: #53BDE6;
}

.pb-label {
    font-size: 0.65rem;
    font-weight: 600;
    color: #64748b;
    animation: pulse 2s ease-in-out infinite;
}

:where(.dark, .dark *) .pb-label {
    color: #94a3b8;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
</style>
