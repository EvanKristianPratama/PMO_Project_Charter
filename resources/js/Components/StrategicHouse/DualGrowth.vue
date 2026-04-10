<template>
    <section class="dual-growth-mockup">
        <div class="mockup-header">
            <div>
                <p class="mockup-eyebrow">Mockup Preview</p>
            </div>

            <div class="mockup-legend">
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--core"></span>
                    Core / operational
                </span>
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--highlight"></span>
                    Priority highlight
                </span>
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--business"></span>
                    Business / analytics
                </span>
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--green"></span>
                    Emerging / low carbon
                </span>
            </div>
        </div>

        <div class="mockup-board-scroll">
            <div class="mockup-board">
                <div class="board-header">
                    <div class="board-header__group board-header__group--strategy">
                        Dual Growth Strategy
                    </div>
                    <div class="board-header__group board-header__group--initiative">
                        Digital Initiative
                    </div>
                </div>

                <div
                    v-for="section in displaySections"
                    :key="section.code"
                    class="board-row"
                >
                    <aside class="board-row__label">
                        <span class="board-row__code">{{ section.code }}</span>
                        <div class="board-row__pill">
                            <span>{{ section.title }}</span>
                        </div>
                    </aside>

                    <div
                        class="board-row__lane"
                        :class="`board-row__lane--${section.tone}`"
                        :style="laneStyle(section)"
                    >
                        <article
                            v-for="card in section.cards"
                            :key="card.id"
                            class="roadmap-card"
                            :class="`roadmap-card--${card.variant}`"
                            :style="cardStyle(card)"
                        >
                            {{ card.label }}
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    goals: {
        type: Array,
        default: () => [],
    },
});

const sectionTemplates = [
    {
        code: 'A',
        title: 'Maximizing Value',
        tone: 'value',
        cards: [
            { id: 'a1', label: 'Predictive Maintenance', column: 1, row: 1, variant: 'highlight' },
            { id: 'a2', label: 'AI Drilling in Upstream', column: 1, row: 2, variant: 'highlight' },
            { id: 'a3', label: 'Profitability Analytic (PROFITOR)', column: 1, row: 4, variant: 'business' },
            { id: 'a4', label: 'Smart Ship 2.0', column: 2, row: 1, variant: 'core' },
            { id: 'a5', label: 'Smart Port & Terminal', column: 2, row: 2, variant: 'core' },
            { id: 'a6', label: 'Fleet Predictive Maintenance', column: 2, row: 3, variant: 'core' },
            { id: 'a7', label: 'Buspro X', column: 2, row: 4, variant: 'highlight' },
            { id: 'a8', label: 'SiPGas & Cost of Gas Purchases Optimization', column: 2, row: 5, variant: 'highlight' },
            { id: 'a9', label: 'Optigas', column: 2, row: 6, variant: 'highlight' },
            { id: 'a10', label: 'AI for Jargas Customer Mapping', column: 2, row: 7, variant: 'highlight' },
            { id: 'a11', label: 'Dynamic Scheduling Automation System (DSAS)', column: 3, row: 1, variant: 'highlight' },
            { id: 'a12', label: 'ILO App (IPMAN Enhancement)', column: 3, row: 2, variant: 'highlight' },
            { id: 'a13', label: 'Integrated Control Tower', column: 3, row: 3, variant: 'highlight' },
            { id: 'a14', label: 'Digitalisasi Asset Management', column: 3, row: 4, variant: 'green' },
            { id: 'a15', label: 'I-WIMS', column: 4, row: 1, variant: 'highlight' },
            { id: 'a16', label: 'IMS Claims 2.0', column: 4, row: 2, variant: 'core' },
            { id: 'a17', label: 'AIMS (DIGIO Integration) incl. GIS', column: 4, row: 3, variant: 'core' },
            { id: 'a18', label: 'Integrated Plant Maintenance System', column: 4, row: 4, variant: 'core' },
            { id: 'a19', label: 'Asset Lifecycle Management', column: 4, row: 5, variant: 'highlight' },
            { id: 'a20', label: 'Smart Contract Internal Transaction', column: 4, row: 6, variant: 'core' },
            { id: 'a21', label: 'VCITS', column: 5, row: 1, variant: 'business' },
            { id: 'a22', label: 'Smart Meter to Control Gas Usage', column: 5, row: 2, variant: 'business' },
            { id: 'a23', label: 'Realtime Stock in Transit & FSO', column: 5, row: 3, variant: 'business' },
            { id: 'a24', label: 'Tonnage Planning & Fulfillment', column: 5, row: 4, variant: 'business' },
            { id: 'a25', label: 'PMS', column: 5, row: 5, variant: 'business' },
            { id: 'a26', label: 'B2B Portal & Customer Management', column: 5, row: 6, variant: 'business' },
            { id: 'a27', label: 'SH IML Super Apps', column: 6, row: 1, variant: 'business' },
            { id: 'a28', label: 'Bunker Optimization - Data Analytic', column: 6, row: 2, variant: 'business' },
            { id: 'a29', label: 'LML Control Tower 24/7', column: 6, row: 3, variant: 'business' },
            { id: 'a30', label: 'Data Democratization', column: 6, row: 4, variant: 'green' },
            { id: 'a31', label: 'Tanker Pool Platform', column: 6, row: 5, variant: 'business' },
            { id: 'a32', label: 'Digital Twin - Real Time Opt. (RTO)', column: 1, row: 7, variant: 'core', span: 2 },
        ],
    },
    {
        code: 'B',
        title: 'Expand to new markets & adjacencies',
        tone: 'market',
        cards: [
            { id: 'b1', label: 'Market Intelligence - Forecasting & Competition Assess.', column: 1, row: 1, variant: 'business' },
            { id: 'b2', label: 'Digitalisasi Berlangganan Gas', column: 1, row: 2, variant: 'core' },
            { id: 'b3', label: 'Voice of Customer Analytics', column: 1, row: 3, variant: 'highlight' },
            { id: 'b4', label: 'Big Data / Analytics for Customer Behavior', column: 5, row: 1, variant: 'business' },
            { id: 'b5', label: 'Supply Chain Optimization & Facility Modelling', column: 6, row: 1, variant: 'business' },
        ],
    },
    {
        code: 'C',
        title: 'Building low carbon business',
        tone: 'carbon',
        cards: [
            { id: 'c1', label: 'Market Intelligence - Forecasting & Competition Assess.', column: 1, row: 1, variant: 'business' },
            { id: 'c2', label: 'IMS Chartering', column: 1, row: 2, variant: 'green' },
            { id: 'c3', label: 'Demand Forecasting', column: 2, row: 1, variant: 'highlight' },
            { id: 'c4', label: 'Dynamic Fuel & Non-Fuel Price Forecast', column: 2, row: 2, variant: 'highlight' },
            { id: 'c5', label: 'Integrated Weather System & Forecasting', column: 2, row: 3, variant: 'highlight' },
            { id: 'c6', label: 'Digital Leadership Program', column: 5, row: 1, variant: 'business' },
            { id: 'c7', label: 'Drone-based Utilization for Maintenance and Inspection', column: 5, row: 2, variant: 'green' },
            { id: 'c8', label: 'Geohazard Mitigation Using Int. Real-time Monitoring System', column: 6, row: 1, variant: 'business' },
            { id: 'c9', label: 'Inc. Accuracy of Production Forecasting Using Analytics', column: 6, row: 2, variant: 'business' },
        ],
    },
];

const displaySections = computed(() => {
    const sourceGoals = props.goals.length > 0
        ? props.goals
        : sectionTemplates.map((section) => ({
            code: section.code,
            title: section.title,
        }));

    return sourceGoals.map((goal, index) => {
        const template = sectionTemplates[index] ?? {};

        return {
            code: String(goal?.code ?? template.code ?? String.fromCharCode(65 + index)),
            title: String(goal?.title ?? goal?.label ?? template.title ?? `Goal ${index + 1}`),
            tone: template.tone ?? 'market',
            cards: Array.isArray(template.cards) ? template.cards : [],
        };
    });
});

const cardStyle = (card) => ({
    gridColumn: `${card.column} / span ${card.span ?? 1}`,
    gridRow: `${card.row} / span ${card.rowSpan ?? 1}`,
});

const laneStyle = (section) => {
    const rowCount = Math.max(
        1,
        ...section.cards.map((card) => card.row + (card.rowSpan ?? 1) - 1),
    );

    return {
        '--lane-rows': rowCount,
    };
};
</script>

<style scoped>
.dual-growth-mockup {
    margin-top: 24px;
    border: 1px solid #d9e2ec;
    border-radius: 28px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.mockup-header {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 24px 16px;
    border-bottom: 1px solid #e2e8f0;
    background:
        radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 30%),
        linear-gradient(180deg, #fcfdff 0%, #f7fafc 100%);
}

.mockup-eyebrow {
    margin: 0 0 8px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #2563eb;
}

.mockup-title {
    margin: 0;
    font-size: 24px;
    line-height: 1.15;
    font-weight: 800;
    color: #0f172a;
}

.mockup-subtitle {
    margin: 10px 0 0;
    max-width: 760px;
    font-size: 13px;
    line-height: 1.7;
    color: #475569;
}

.mockup-legend {
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
    justify-content: flex-end;
    gap: 10px 14px;
    min-width: 280px;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
}

.legend-swatch {
    width: 14px;
    height: 14px;
    border-radius: 4px;
    border: 2px solid transparent;
}

.legend-swatch--core {
    background: #86c7f3;
    border-color: #5096cb;
}

.legend-swatch--highlight {
    background: #86c7f3;
    border-color: #ef4444;
}

.legend-swatch--business {
    background: #facc15;
    border-color: #f97316;
}

.legend-swatch--green {
    background: #86efac;
    border-color: #22c55e;
}

.mockup-board-scroll {
    overflow-x: auto;
    padding: 18px;
}

.mockup-board {
    --goal-column-width: 148px;
    min-width: 980px;
    border: 1px solid #d4dde8;
    border-radius: 18px;
    background: #ffffff;
    overflow: hidden;
}

.board-header {
    display: grid;
    grid-template-columns: var(--goal-column-width) minmax(0, 1fr);
    gap: 0;
    margin-bottom: 0;
}

.board-header__group {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 10px 12px;
    border-bottom: 1px solid #d4dde8;
    background: #0f6fb7;
    color: #ffffff;
    font-size: 13px;
    font-weight: 800;
    line-height: 1.15;
    text-align: center;
}

.board-header__group--initiative {
    border-left: 1px solid rgba(255, 255, 255, 0.2);
}

.board-row {
    display: grid;
    grid-template-columns: var(--goal-column-width) minmax(0, 1fr);
    gap: 0;
    align-items: stretch;
    margin-bottom: 0;
}

.board-row__label {
    display: grid;
    grid-template-columns: 36px minmax(0, 1fr);
    align-items: stretch;
    gap: 0;
    border-right: 1px solid #d4dde8;
    border-bottom: 1px solid #d4dde8;
    background: #0f6fb7;
}

.board-row__code {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100%;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    background: linear-gradient(180deg, #8fd3ff 0%, #65baf5 100%);
    color: #0f3f69;
    font-size: 15px;
    font-weight: 800;
}

.board-row__pill {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    min-height: 100%;
    padding: 12px 16px 12px 12px;
    background: transparent;
    color: #fff;
    text-align: left;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.2;
}

.board-row__lane {
    position: relative;
    display: grid;
    grid-template-columns: repeat(6, minmax(118px, 1fr));
    grid-template-rows: repeat(var(--lane-rows, 1), minmax(22px, auto));
    gap: 6px 8px;
    padding: 10px;
    border-bottom: 1px solid #d4dde8;
    overflow: hidden;
}

.board-row:last-child .board-row__label,
.board-row:last-child .board-row__lane {
    border-bottom: none;
}

.board-row__lane::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        linear-gradient(to right, rgba(37, 99, 235, 0.12) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(37, 99, 235, 0.08) 1px, transparent 1px),
        radial-gradient(circle at 1px 1px, rgba(71, 85, 105, 0.16) 1px, transparent 1.2px);
    background-size: calc(100% / 6) 100%, 100% calc(100% / var(--lane-rows, 1)), 14px 14px;
    pointer-events: none;
}

.board-row__lane > * {
    position: relative;
    z-index: 1;
}

.board-row__lane--value {
    background: linear-gradient(180deg, #f6f3cc 0%, #f1edc0 100%);
}

.board-row__lane--market {
    background: linear-gradient(180deg, #edf1fb 0%, #e7edf9 100%);
}

.board-row__lane--carbon {
    background: linear-gradient(180deg, #e8eefb 0%, #e2ebfa 100%);
}

.roadmap-card {
    display: flex;
    align-items: center;
    min-height: 22px;
    padding: 3px 6px;
    border-radius: 0;
    font-size: 10px;
    font-weight: 600;
    line-height: 1.15;
    color: #163047;
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.05);
}

.roadmap-card--core {
    background: #86c7f3;
    border: 2px solid #4b98ca;
}

.roadmap-card--highlight {
    background: #86c7f3;
    border: 2px solid #ef4444;
}

.roadmap-card--business {
    background: #facc15;
    border: 2px solid #f97316;
}

.roadmap-card--green {
    background: #86efac;
    border: 2px solid #22c55e;
}

@media (max-width: 1024px) {
    .mockup-header {
        flex-direction: column;
    }

    .mockup-legend {
        justify-content: flex-start;
        min-width: 0;
    }
}

@media (max-width: 768px) {
    .dual-growth-mockup {
        border-radius: 22px;
    }

    .mockup-header {
        padding: 18px 18px 14px;
    }

    .mockup-board-scroll {
        padding: 14px;
    }
}

:deep(.dark) .dual-growth-mockup {
    border-color: rgba(148, 163, 184, 0.16);
    background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
}

:deep(.dark) .mockup-board {
    border-color: rgba(148, 163, 184, 0.18);
    background: #0f172a;
}

:deep(.dark) .mockup-header {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    background:
        radial-gradient(circle at top right, rgba(59, 130, 246, 0.16), transparent 30%),
        linear-gradient(180deg, #111827 0%, #0f172a 100%);
}

:deep(.dark) .mockup-title,
:deep(.dark) .board-row__code,
:deep(.dark) .board-row__pill {
    color: #f8fafc;
}

:deep(.dark) .mockup-subtitle,
:deep(.dark) .legend-item {
    color: #cbd5e1;
}

:deep(.dark) .board-header__group,
:deep(.dark) .board-row__label,
:deep(.dark) .board-row__lane {
    border-color: rgba(148, 163, 184, 0.18);
}

:deep(.dark) .board-row__lane {
    border-bottom-color: rgba(148, 163, 184, 0.18);
}

:deep(.dark) .board-row__lane--value {
    background: linear-gradient(180deg, rgba(104, 95, 27, 0.45) 0%, rgba(86, 79, 23, 0.45) 100%);
}

:deep(.dark) .board-row__lane--market,
:deep(.dark) .board-row__lane--carbon {
    background: linear-gradient(180deg, rgba(30, 41, 59, 0.88) 0%, rgba(15, 23, 42, 0.88) 100%);
}

:deep(.dark) .roadmap-card {
    color: #0f172a;
}
</style>
