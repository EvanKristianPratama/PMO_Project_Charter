<template>
    <UserLayout :title="page.title">
        <div class="strategic-house animate-fade-in">
            <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717] sm:p-6">

                <!-- ═══ ROOF: Focus Bands (Maximize Legacy Business + Build Low Carbon) ═══ -->
                <div class="roof-section">
                    <!-- Main roof bar -->
                    <div class="roof-top">
                        <div class="roof-main">
                            <div class="roof-main-label">{{ page.headline }}</div>
                            <div class="roof-sub-items">
                                <div
                                    v-for="(band, idx) in focusBands.slice(0, 2)"
                                    :key="band.id"
                                    class="roof-sub-item"
                                >
                                    {{ band.label }}
                                </div>
                            </div>
                        </div>
                        <div v-if="focusBands.length > 2" class="roof-side">
                            <span>{{ focusBands[2].label }}</span>
                        </div>
                    </div>
                </div>

                <!-- ═══ CONNECTOR: small decorative chain ═══ -->
                <div class="connector-chain">
                    <svg width="20" height="28" viewBox="0 0 20 28">
                        <circle cx="10" cy="6" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5"/>
                        <line x1="10" y1="11" x2="10" y2="17" stroke="#9cb8d8" stroke-width="1.5"/>
                        <circle cx="10" cy="22" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5"/>
                    </svg>
                </div>

                <!-- ═══ VISION TRAPEZOID ═══ -->
                <div class="vision-trapezoid">
                    <p class="vision-title">{{ page.visionTitle }}:</p>
                    <p class="vision-text">{{ page.visionText }}</p>
                </div>

                <!-- ═══ CONNECTOR ═══ -->
                <div class="connector-chain">
                    <svg width="20" height="28" viewBox="0 0 20 28">
                        <circle cx="10" cy="6" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5"/>
                        <line x1="10" y1="11" x2="10" y2="17" stroke="#9cb8d8" stroke-width="1.5"/>
                        <circle cx="10" cy="22" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5"/>
                    </svg>
                </div>

                <!-- ═══ DIGITAL TRANSFORMATION INITIATIVES SECTION ═══ -->
                <div class="dti-section">
                    <div class="dti-header">
                        <p class="dti-count">{{ summary.total_initiatives }}</p>
                        <p class="dti-label">{{ page.initiativeLabel }}</p>
                    </div>

                    <div class="dti-cards">
                        <div
                            v-for="card in technologyCards"
                            :key="card.name"
                            class="dti-card"
                        >
                            {{ card.display_name }}
                        </div>
                    </div>
                </div>

                <!-- ═══ CONNECTOR ═══ -->
                <div class="connector-chain">
                    <svg width="20" height="28" viewBox="0 0 20 28">
                        <circle cx="10" cy="6" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5"/>
                        <line x1="10" y1="11" x2="10" y2="17" stroke="#9cb8d8" stroke-width="1.5"/>
                        <circle cx="10" cy="22" r="5" fill="none" stroke="#9cb8d8" stroke-width="1.5"/>
                    </svg>
                </div>

                <!-- ═══ GRAND IT STRATEGY SECTION ═══ -->
                <div class="gits-section">
                    <div class="gits-header">
                        <h2 class="gits-title">{{ page.grandStrategyTitle }}</h2>
                        <p class="gits-subtitle">{{ page.grandStrategyText }}</p>
                    </div>

                    <div class="gits-pillars">
                        <article
                            v-for="card in strategyCards"
                            :key="card.name"
                            class="gits-pillar"
                        >
                            <h3 class="gits-pillar-title">{{ card.display_name }}</h3>
                            <div class="gits-pillar-desc">
                                <p
                                    v-for="initiative in card.initiatives_preview"
                                    :key="initiative.id"
                                >
                                    {{ initiative.label }}
                                </p>
                                <p v-if="card.is_empty" class="gits-pillar-empty">
                                    Belum ada initiative yang terhubung ke area ini.
                                </p>
                            </div>
                        </article>
                    </div>

                    <!-- ═══ FOUNDATION BAR ═══ -->
                    <div v-if="foundationCard" class="foundation-bar">
                        <span class="foundation-title">{{ foundationCard.display_name }}:</span>
                        <span class="foundation-desc">Memungkinkan pelaksanaan efektif dari semua digital dan IT initiative</span>
                    </div>
                </div>

            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    focusBands: {
        type: Array,
        default: () => [],
    },
    technologyCards: {
        type: Array,
        default: () => [],
    },
    strategyCards: {
        type: Array,
        default: () => [],
    },
    foundationCard: {
        type: Object,
        default: null,
    },
    architectureCard: {
        type: Object,
        default: null,
    },
    tbcCard: {
        type: Object,
        default: null,
    },
    unassignedInitiatives: {
        type: Array,
        default: () => [],
    },
});
</script>

<style scoped>
/* ─── ROOF SECTION ─── */
.roof-section {
    margin-bottom: 0;
}

.roof-top {
    display: flex;
    gap: 12px;
    align-items: stretch;
}

.roof-main {
    flex: 1;
    text-align: center;
}

.roof-main-label {
    background: #e8eff8;
    border: 1px solid #c5d6e8;
    padding: 12px 24px;
    font-size: 15px;
    font-weight: 600;
    color: #1a2a3a;
    border-radius: 4px;
}

.roof-sub-items {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.roof-sub-item {
    flex: 1;
    background: #e8eff8;
    border: 1px solid #c5d6e8;
    padding: 10px 16px;
    font-size: 12px;
    font-weight: 500;
    color: #2a4a6a;
    text-align: center;
    border-radius: 4px;
}

.roof-side {
    width: 160px;
    background: #184f96;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    line-height: 1.4;
}

/* ─── CONNECTORS ─── */
.connector-chain {
    display: flex;
    justify-content: center;
    padding: 4px 0;
}

/* ─── VISION TRAPEZOID ─── */
.vision-trapezoid {
    background: linear-gradient(180deg, #1e6dc0 0%, #184f96 100%);
    color: #fff;
    text-align: center;
    padding: 24px 40px;
    clip-path: polygon(10% 0%, 90% 0%, 100% 100%, 0% 100%);
    border-radius: 6px;
}

.vision-title {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.02em;
}

.vision-text {
    margin-top: 8px;
    font-size: 12px;
    line-height: 1.7;
    color: rgba(255,255,255,0.92);
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

/* ─── DIGITAL TRANSFORMATION INITIATIVES ─── */
.dti-section {
    background: #b8d4f0;
    border-radius: 6px;
    padding: 20px 20px 24px;
}

.dti-header {
    text-align: center;
    margin-bottom: 16px;
}

.dti-count {
    font-size: 16px;
    font-weight: 700;
    color: #0d2a4a;
    display: inline;
}

.dti-label {
    font-size: 16px;
    font-weight: 700;
    color: #0d2a4a;
    display: inline;
}

.dti-header p {
    display: inline;
}

.dti-cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}

.dti-card {
    background: #fff;
    border: 1.5px solid #3b82c8;
    border-radius: 4px;
    padding: 14px 12px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    color: #184f96;
}

/* ─── GRAND IT STRATEGY SECTION ─── */
.gits-section {
    background: #dde8f4;
    border-radius: 6px;
    padding: 24px 20px;
}

.gits-header {
    text-align: center;
    margin-bottom: 20px;
}

.gits-title {
    font-size: 20px;
    font-weight: 700;
    color: #0d2a4a;
}

.gits-subtitle {
    font-size: 12px;
    color: #3a5a7a;
    margin-top: 4px;
}

.gits-pillars {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
    margin-bottom: 16px;
}

.gits-pillar {
    background: linear-gradient(180deg, #2567a8 0%, #184f96 100%);
    border-radius: 10px;
    padding: 16px 14px;
    color: #fff;
    display: flex;
    flex-direction: column;
}

.gits-pillar-title {
    font-size: 14px;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 8px;
}

.gits-pillar-desc {
    font-size: 10px;
    line-height: 1.5;
    color: rgba(255,255,255,0.85);
    flex: 1;
}

.gits-pillar-desc p {
    margin-bottom: 4px;
}

.gits-pillar-empty {
    color: rgba(255,255,255,0.6);
    font-style: italic;
}

/* ─── FOUNDATION BAR ─── */
.foundation-bar {
    background: linear-gradient(90deg, #1b4f93 0%, #215da8 50%, #1b4f93 100%);
    border-radius: 8px;
    padding: 14px 24px;
    color: #fff;
    text-align: center;
    font-size: 13px;
    line-height: 1.5;
}

.foundation-title {
    font-weight: 700;
    margin-right: 4px;
}

.foundation-desc {
    color: rgba(255,255,255,0.85);
}

/* ─── RESPONSIVE ─── */
@media (max-width: 1024px) {
    .gits-pillars {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .roof-top {
        flex-direction: column;
    }

    .roof-side {
        width: 100%;
    }

    .dti-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .gits-pillars {
        grid-template-columns: repeat(2, 1fr);
    }

    .vision-trapezoid {
        clip-path: polygon(5% 0%, 95% 0%, 100% 100%, 0% 100%);
        padding: 20px 24px;
    }
}

@media (max-width: 480px) {
    .dti-cards {
        grid-template-columns: 1fr;
    }

    .gits-pillars {
        grid-template-columns: 1fr;
    }

    .roof-sub-items {
        flex-direction: column;
    }
}

/* ─── DARK MODE ─── */
:deep(.dark) .roof-main-label,
:deep(.dark) .roof-sub-item {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

:deep(.dark) .dti-section {
    background: #12253f;
}

:deep(.dark) .dti-count,
:deep(.dark) .dti-label {
    color: #e2e8f0;
}

:deep(.dark) .dti-card {
    background: #1e293b;
    border-color: #3b82c8;
    color: #93c5fd;
}

:deep(.dark) .gits-section {
    background: #0f1d2f;
}

:deep(.dark) .gits-title {
    color: #e2e8f0;
}

:deep(.dark) .gits-subtitle {
    color: #94a3b8;
}
</style>
