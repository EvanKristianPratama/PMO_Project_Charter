<script setup>
</script>

<template>
    <div class="sh-spinner-wrapper">
        <div class="sh-spinner">
            <svg class="sh-spinner-svg" viewBox="0 0 50 50">
                <circle
                    cx="25" cy="25" r="20"
                    fill="none"
                    stroke-width="3"
                    stroke-linecap="round"
                    class="sh-spinner-circle"
                />
            </svg>
        </div>
        <span class="sh-spinner-text">Loading…</span>
    </div>
</template>

<style scoped>
.sh-spinner-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding-top: 64px;
    padding-bottom: 120px;
}

.sh-spinner {
    width: 36px;
    height: 36px;
}

.sh-spinner-svg {
    width: 100%;
    height: 100%;
    animation: sh-rotate 0.8s linear infinite;
}

.sh-spinner-circle {
    stroke: #1C75BC;
    stroke-dasharray: 90, 150;
    stroke-dashoffset: 0;
    animation: sh-dash 1.2s ease-in-out infinite;
}

:where(.dark, .dark *) .sh-spinner-circle {
    stroke: #53BDE6;
}

.sh-spinner-text {
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    color: #94a3b8;
}

:where(.dark, .dark *) .sh-spinner-text {
    color: #64748b;
}

@keyframes sh-rotate {
    100% { transform: rotate(360deg); }
}

@keyframes sh-dash {
    0% {
        stroke-dasharray: 1, 150;
        stroke-dashoffset: 0;
    }
    50% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -35;
    }
    100% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -124;
    }
}
</style>
