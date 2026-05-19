<template>
    <div class="mx-auto flex w-full max-w-7xl flex-col items-center px-4 py-4">
        <section class="house-shell w-full max-w-[1120px]">
            <div class="roof-wrap">
                <div class="roof-panel">
                    <div class="roof-label">Vision</div>
                    <div class="roof-vision">
                        {{ house.vision }}
                    </div>
                </div>
            </div>

            <div class="mission-panel">
                <div class="mission-label">Mission</div>
                <div class="mission-copy">
                    <span v-for="(line, index) in missionLines" :key="`${line}-${index}`" class="block">
                        {{ line }}
                    </span>
                </div>
            </div>

            <div class="columns-grid">
                <article
                    v-for="column in columns"
                    :key="column.title"
                    class="pillar-card"
                >
                    <header class="pillar-card__header" :class="column.headerClass">
                        <div class="pillar-card__title">
                            {{ column.title }}
                        </div>
                    </header>

                    <div class="pillar-card__body">
                        <div v-for="(item, index) in column.items" :key="`${column.title}-${index}`" class="pillar-item">
                            <div class="pillar-icon" :class="column.iconClass">
                                {{ column.icon }}
                            </div>

                            <div class="pillar-copy">
                                <div class="pillar-copy__label">
                                    {{ item.label }}
                                    <span v-if="item.text">:</span>
                                </div>
                                <div class="pillar-copy__text">
                                    {{ item.text || '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div class="footer-grid">
                <div v-for="(row, rowIndex) in footerRows" :key="`footer-row-${rowIndex}`" class="footer-row">
                    <div v-for="(cell, cellIndex) in row" :key="`${rowIndex}-${cellIndex}`" class="footer-cell">
                        {{ cell }}
                    </div>
                </div>
            </div>

            <div class="values-bar">
                {{ house.coreValues }}
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    strategicHouseIML: {
        type: Object,
        default: () => ({})
    },
    imlGoals: {
        type: Array,
        default: () => []
    }
});

const fallbackHouse = {
    vision: 'Energizing people and planet with green energy',
    mission: 'To lead energy transition and become a leader of Low Carbon Solutions, Renewable Energy & Future Green Business in the region through innovation and breakthrough initiatives to create values for stakeholders',
    coreValues: 'PNRE Core Values - Amanah Kompeten Harmonis Loyal Adaptif Kolaboratif',
};

const fallbackColumns = [
    {
        title: 'Grow Core Business',
        headerClass: 'pillar-card__header--blue',
        iconClass: 'pillar-icon--blue',
        icon: 'G',
        items: [
            { label: 'Gas-to-Power', text: 'Expand Jawa Satu & optimize opportunity in Pertamina Group & expand to C&I (IPP, CPP)' },
            { label: 'O&M Service & Energy Efficiency', text: 'Optimize opportunities in Pertamina Group' },
            { label: 'Solar', text: 'Scale up via SOE synergy & expand into C&I' },
            { label: 'Geothermal', text: 'Accelerate expansion of ~0.6 GW installed capacities' },
        ],
    },
    {
        title: 'Shape Future Green Business',
        headerClass: 'pillar-card__header--green',
        iconClass: 'pillar-icon--green',
        icon: 'H2',
        items: [
            { label: 'Hydrogen', text: 'Grow capability by serving domestic captive demand and expand to serve export market (HRS, H2 for refinery)' },
            { label: 'Battery & EV', text: 'Increase packing capacity stepwise (battery cell & pack, BSS)' },
            { label: 'Carbon business', text: 'Optimize opportunity for tech & nature-based credit (NBS, carbon trading)' },
            { label: 'Biofuel', text: 'Build capability to grab the opportunity (bioethanol, biomethane)' },
            { label: 'Other RE', text: 'Ramp up capability for wider opportunity (wind, biomass)' },
            { label: 'Decarbonization', text: 'CCS, energy efficiency, co-firing, CO2 liquefaction, binary plant' },
        ],
    },
    {
        title: 'Global Expansion',
        headerClass: 'pillar-card__header--orange',
        iconClass: 'pillar-icon--orange',
        icon: 'OE',
        items: [
            { label: 'Seek Opportunity to Enter Overseas', text: 'Green hydrogen' },
            { label: 'Overseas solar power plant', text: 'Export electricity' },
            { label: 'IPP Overseas', text: 'M&A' },
        ],
    },
];

const footerRows = [
    [
        'Strategic partnerships for technology leadership and maximum value creation',
        'Stakeholder management for supportive government policies & regulations',
    ],
    [
        'Acquire & build talent for new capabilities',
        'Equity support & competitive financing',
    ],
];

const house = computed(() => ({
    vision: String(props.strategicHouseIML?.vision || fallbackHouse.vision).trim(),
    mission: String(props.strategicHouseIML?.mission || fallbackHouse.mission).trim(),
    coreValues: fallbackHouse.coreValues,
}));

const missionLines = computed(() => {
    return house.value.mission
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);
});

const normalizeItems = (goal, fallbackItems) => {
    const themes = Array.isArray(goal?.themes) ? goal.themes : [];

    const derivedItems = themes.flatMap((theme) => {
        const themeLabel = String(theme?.name || '').trim();
        const pillarThemes = Array.isArray(theme?.pillar_themes) ? theme.pillar_themes : [];

        if (pillarThemes.length) {
            return pillarThemes.map((pillar) => ({
                label: themeLabel || String(goal?.title || '').trim(),
                text: String(pillar?.strategy || pillar?.title || '').trim(),
            }));
        }

        const themeText = String(theme?.strategy || theme?.description || '').trim();
        return [{
            label: themeLabel || String(goal?.title || '').trim(),
            text: themeText,
        }];
    }).filter((item) => item.label || item.text);

    return derivedItems.length ? derivedItems : fallbackItems;
};

const columns = computed(() => {
    const goals = Array.isArray(props.imlGoals) ? props.imlGoals.filter(Boolean) : [];
    const mainGoals = goals.slice(0, 3);

    if (!mainGoals.length) {
        return fallbackColumns;
    }

    return mainGoals.map((goal, index) => {
        const fallback = fallbackColumns[index] || fallbackColumns[fallbackColumns.length - 1];

        return {
            ...fallback,
            title: String(goal?.title || fallback.title).trim(),
            items: normalizeItems(goal, fallback.items),
        };
    });
});
</script>

<style scoped>
.house-shell {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding-bottom: 2.25rem;
}

.roof-wrap {
    display: flex;
    justify-content: center;
}

.roof-panel {
    width: 100%;
    min-height: 145px;
    padding: 1rem 2rem 0.85rem;
    color: #ffffff;
    text-align: center;
    background: linear-gradient(180deg, #2f7d62 0%, #1d6a51 100%);
    clip-path: polygon(2% 22%, 50% 0%, 98% 22%, 100% 100%, 0% 100%);
    box-shadow: 0 14px 28px rgba(8, 35, 28, 0.18);
}

.roof-label,
.mission-label {
    margin-bottom: 0.15rem;
    font-size: 0.82rem;
    font-weight: 800;
    font-style: italic;
    line-height: 1.2;
}

.roof-vision {
    max-width: 44rem;
    margin: 0 auto;
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.3;
}

.mission-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1rem 0.8rem;
    border: 1px solid #93c8b4;
    background: linear-gradient(180deg, #b6d4c7 0%, #a8cbbb 100%);
    color: #ffffff;
    text-align: center;
    box-shadow: 0 10px 20px rgba(12, 52, 39, 0.08);
}

.mission-copy {
    max-width: 60rem;
    font-size: 0.92rem;
    font-weight: 700;
    line-height: 1.35;
}

.columns-grid {
    display: grid;
    gap: 0.35rem;
    align-items: stretch;
}

@media (min-width: 1024px) {
    .columns-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.pillar-card {
    overflow: hidden;
    border: 1px solid #d9e1ec;
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.pillar-card__header {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0.55rem 0.8rem;
    color: #ffffff;
    text-align: center;
}

.pillar-card__header--blue {
    background: linear-gradient(180deg, #3f6fb5 0%, #2c5aa1 100%);
}

.pillar-card__header--green {
    background: linear-gradient(180deg, #2fb36f 0%, #14955b 100%);
}

.pillar-card__header--orange {
    background: linear-gradient(180deg, #f69128 0%, #e96d0e 100%);
}

.pillar-card__title {
    font-size: 0.92rem;
    font-weight: 800;
    line-height: 1.2;
}

.pillar-card__body {
    padding: 0.75rem 0.7rem 0.9rem;
}

.pillar-item {
    display: flex;
    gap: 0.6rem;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.pillar-item:last-child {
    margin-bottom: 0;
}

.pillar-icon {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    font-size: 0.66rem;
    font-weight: 900;
    letter-spacing: 0.04em;
}

.pillar-icon--blue {
    background: #e5efff;
    color: #2f6ab9;
}

.pillar-icon--green {
    background: #e2f7eb;
    color: #14955b;
}

.pillar-icon--orange {
    background: #fff0df;
    color: #e96d0e;
}

.pillar-copy {
    flex: 1 1 auto;
    min-width: 0;
    color: #223048;
}

.pillar-copy__label {
    font-size: 0.78rem;
    font-weight: 800;
    font-style: italic;
    line-height: 1.3;
}

.pillar-copy__text {
    margin-top: 0.05rem;
    font-size: 0.74rem;
    font-weight: 600;
    line-height: 1.35;
    color: #4b5566;
}

.footer-grid {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.footer-row {
    display: grid;
    gap: 0.25rem;
}

@media (min-width: 768px) {
    .footer-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.footer-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 0.55rem 0.8rem;
    border: 1px solid #d7e3d4;
    background: linear-gradient(180deg, #eef2c4 0%, #e7ebae 100%);
    color: #3b4632;
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    line-height: 1.3;
}

.values-bar {
    padding: 0.55rem 1rem;
    border: 1px solid #245f4c;
    background: linear-gradient(180deg, #2f7d62 0%, #1d6a51 100%);
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 800;
    text-align: center;
    box-shadow: 0 8px 18px rgba(8, 35, 28, 0.14);
}
</style>
