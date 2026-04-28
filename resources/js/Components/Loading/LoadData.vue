<template>
    <div class="load-data-container">
        <div class="load-data-inner">
            <!-- Circular Progress -->
            <div class="load-data-circle">
                <svg viewBox="0 0 128 128" class="circle-svg">
                    <!-- Track -->
                    <circle
                        cx="64"
                        cy="64"
                        r="56"
                        fill="none"
                        stroke-width="8"
                        class="circle-track"
                    />
                    <!-- Progress arc -->
                    <circle
                        cx="64"
                        cy="64"
                        r="56"
                        fill="none"
                        stroke-width="8"
                        stroke-linecap="round"
                        class="circle-progress"
                        :stroke-dasharray="circumference"
                        :stroke-dashoffset="circumference - (circumference * displayProgress) / 100"
                    />
                </svg>
                <!-- Percentage text -->
                <div class="circle-label">
                    <span class="circle-percent">{{ displayProgress }}<small>%</small></span>
                </div>
            </div>

            <!-- Text -->
            <div class="load-data-text">
                <p class="load-data-title">{{ title }}</p>
                <p class="load-data-message">{{ displayProgress < 100 ? message : 'Data siap ditampilkan!' }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Memuat Data',
    },
    message: {
        type: String,
        default: 'Mohon tunggu, data sedang diproses...',
    },
    /** Set to true when the actual data has arrived */
    finished: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['complete']);

const circumference = 2 * Math.PI * 56; // ≈ 351.86
const displayProgress = ref(0);

let rafId = null;
let startTime = null;

// Easing: fast at first, then slowly creeps upward while waiting for real data
const simulateTarget = 85;
const simulateCeiling = 95;
const simulateDuration = 4000; // ms to reach ~85 %
const simulateCreepDuration = 9000; // ms to creep from 85 -> ~95 if request is still pending

function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
}

function tickSimulate(now) {
    if (!startTime) startTime = now;
    const elapsed = now - startTime;
    const initialT = Math.min(elapsed / simulateDuration, 1);

    if (initialT < 1) {
        displayProgress.value = Math.round(easeOutCubic(initialT) * simulateTarget);
        rafId = requestAnimationFrame(tickSimulate);
        return;
    }

    const creepElapsed = elapsed - simulateDuration;
    const creepT = Math.min(creepElapsed / simulateCreepDuration, 1);
    displayProgress.value = Math.round(
        simulateTarget + (simulateCeiling - simulateTarget) * easeOutCubic(creepT)
    );

    if (displayProgress.value < simulateCeiling) {
        rafId = requestAnimationFrame(tickSimulate);
    }
}

// When finished becomes true → quickly ramp to 100 %
let finishStart = null;
let fromProgress = 0;
const finishDuration = 400; // ms

function tickFinish(now) {
    if (!finishStart) finishStart = now;
    const elapsed = now - finishStart;
    const t = Math.min(elapsed / finishDuration, 1);
    displayProgress.value = Math.round(fromProgress + (100 - fromProgress) * easeOutCubic(t));

    if (t < 1) {
        rafId = requestAnimationFrame(tickFinish);
    } else {
        displayProgress.value = 100;
        // Brief pause at 100 % so user can see it, then emit complete
        setTimeout(() => emit('complete'), 350);
    }
}

watch(() => props.finished, (val) => {
    if (val) {
        // Cancel simulated progress & capture current value
        if (rafId) cancelAnimationFrame(rafId);
        fromProgress = displayProgress.value;
        finishStart = null;
        rafId = requestAnimationFrame(tickFinish);
    }
});

onMounted(() => {
    if (props.finished) {
        // Already finished on mount — go straight to 100
        displayProgress.value = 100;
        setTimeout(() => emit('complete'), 100);
        return;
    }
    startTime = null;
    rafId = requestAnimationFrame(tickSimulate);
});

onBeforeUnmount(() => {
    if (rafId) cancelAnimationFrame(rafId);
});
</script>

<style scoped>
.load-data-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 420px;
    padding: 3rem 1rem;
    animation: fadeInUp 0.45s ease-out both;
}

.load-data-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
}

/* ── Circular Progress ──────────────────── */
.load-data-circle {
    position: relative;
    width: 140px;
    height: 140px;
}

.circle-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.circle-track {
    stroke: #e2e8f0;
}
:where(.dark, .dark *) .circle-track {
    stroke: rgba(255, 255, 255, 0.08);
}

.circle-progress {
    stroke: #1C75BC;
    transition: stroke-dashoffset 0.15s ease-out;
}
:where(.dark, .dark *) .circle-progress {
    stroke: #53BDE6;
}

.circle-label {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.circle-percent {
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #1C75BC;
    font-variant-numeric: tabular-nums;
}
.circle-percent small {
    font-size: 0.875rem;
    font-weight: 700;
    margin-left: 1px;
}
:where(.dark, .dark *) .circle-percent {
    color: #53BDE6;
}

/* ── Text ────────────────────────────────── */
.load-data-text {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.load-data-title {
    font-size: 0.8125rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #1e293b;
}
:where(.dark, .dark *) .load-data-title {
    color: #f1f5f9;
}

.load-data-message {
    font-size: 0.6875rem;
    font-weight: 500;
    font-style: italic;
    color: #64748b;
}
:where(.dark, .dark *) .load-data-message {
    color: #94a3b8;
}

/* ── Keyframes ───────────────────────────── */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0);    }
}
</style>
