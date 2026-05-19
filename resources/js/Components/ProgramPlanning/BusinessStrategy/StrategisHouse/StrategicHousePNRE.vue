<template>
    <div class="mx-auto flex w-[90%] flex-col items-center px-4 py-4">
        <section class="house-shell">
            <!-- Roof: Vision -->
            <div class="roof-wrap">
                <div class="roof-panel">
                    <div class="roof-label">Vision:</div>
                    <div class="roof-vision">
                        {{ house.vision }}
                    </div>
                </div>
            </div>

            <!-- Base of roof: Mission -->
            <div class="mission-panel">
                <div class="mission-label">Mission:</div>
                <div class="mission-copy">
                    <span v-for="(line, index) in missionLines" :key="`${line}-${index}`" class="block">
                        {{ line }}
                    </span>
                </div>
            </div>

            <div class="columns-grid">
                <article v-for="goal in displayMainGoals" :key="goal.id" class="pillar-card">
                    <header class="pillar-card__header" :class="headerClass(goal)">
                        <div class="pillar-card__title">
                            {{ goal.title }}
                        </div>
                    </header>

                    <div class="pillar-card__body">
                        <div v-if="goal.themes && goal.themes.length" class="theme-list">
                            <section v-for="theme in goal.themes" :key="theme.id" class="theme-card">
                                <div class="theme-card__label">
                                    <span class="theme-card__dot" :class="themeDotClass(goal)"></span>
                                    <span class="theme-card__text">{{ theme.name }}</span>
                                </div>
                            </section>
                        </div>
                    </div>
                </article>
            </div>

            <section v-if="displayEnablerGoal">
                <div v-if="enablerThemeRows.length" class="enabler-grid">
                    <div v-for="(row, rowIndex) in enablerThemeRows" :key="`enabler-row-${rowIndex}`"
                        class="enabler-grid__row">
                        <section v-for="theme in row" :key="theme.id" class="enabler-grid__cell">
                            <div class="enabler-grid__label">
                                <span class="enabler-grid__text">{{ theme.name }}</span>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

            <div class="core-values-bar">
                PNRE Core Values - {{ house.coreValues }}
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    strategicHousePNRE: {
        type: Object,
        default: () => ({})
    },
    pnreGoals: {
        type: Array,
        default: () => []
    }
});

const house = computed(() => ({
    vision: String(props.strategicHousePNRE?.strategy || props.strategicHousePNRE?.vision || '').trim(),
    mission: String(props.strategicHousePNRE?.mission || '').trim(),
    coreValues: String(props.strategicHousePNRE?.additional_info || '').trim(),
}));

const missionLines = computed(() => {
    return house.value.mission
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);
});

const orderedGoals = computed(() => {
    const goals = Array.isArray(props.pnreGoals) ? props.pnreGoals.filter(Boolean) : [];

    return goals.slice().sort((a, b) => {
        const left = Number(a?.code ?? a?.id ?? 0);
        const right = Number(b?.code ?? b?.id ?? 0);
        return left - right;
    });
});

const mainGoals = computed(() => orderedGoals.value.filter((goal) => Number(goal.id) !== 40));
const enablerGoal = computed(() => orderedGoals.value.find((goal) => Number(goal.id) === 40) || null);

const displayMainGoals = computed(() => {
    return mainGoals.value.map((goal) => ({
        ...goal,
        themes: Array.isArray(goal.themes) ? goal.themes : [],
    }));
});

const displayEnablerGoal = computed(() => {
    if (!enablerGoal.value) return null;

    return {
        ...enablerGoal.value,
        themes: Array.isArray(enablerGoal.value.themes) ? enablerGoal.value.themes : [],
    };
});

const enablerThemeRows = computed(() => {
    const themes = displayEnablerGoal.value?.themes || [];
    const rows = [];

    for (let index = 0; index < themes.length; index += 2) {
        rows.push(themes.slice(index, index + 2));
    }

    return rows;
});

const headerClass = (goal) => {
    const colorMap = {
        A: 'pillar-card__header--blue',
        B: 'pillar-card__header--green',
        C: 'pillar-card__header--orange',
    };

    return colorMap[String(goal?.code || '').trim().toUpperCase()] || 'pillar-card__header--blue';
};

const themeDotClass = (goal) => {
    const colorMap = {
        A: 'theme-card__dot--blue',
        B: 'theme-card__dot--green',
        C: 'theme-card__dot--orange',
    };

    return colorMap[String(goal?.code || '').trim().toUpperCase()] || 'theme-card__dot--blue';
};
</script>

<style scoped>
.house-shell {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.roof-wrap {
    display: flex;
    justify-content: center;
}

.roof-panel {
    width: 100%;
    min-height: 140px;
    padding: 3rem 3rem 1rem;
    color: #ffffff;
    text-align: center;
    background: linear-gradient(180deg, #2f7d62 0%, #1d6a51 100%);
    clip-path: polygon(50% 0%, 100% 100%, 0% 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.roof-label,
.mission-label {
    margin-bottom: 0.25rem;
    font-size: 0.75rem;
    font-weight: 800;
    font-style: italic;
    line-height: 1.2;
}

.roof-vision {
    max-width: 40rem;
    margin: 0 auto;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.25;
}

.mission-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 2rem 0.5rem;
    border: 1px solid #93c8b4;
    background: linear-gradient(180deg, #b6d4c7 0%, #a8cbbb 100%);
    color: #ffffff;
    text-align: center;
    box-shadow: 0 10px 20px rgba(12, 52, 39, 0.08);
}

.mission-copy {
    max-width: 60rem;
    font-size: 0.875rem;
    font-weight: 600;
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

.pillar-card__header,
.enabler-shell__header {
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

.pillar-card__title,
.enabler-shell__title {
    font-size: 0.92rem;
    font-weight: 800;
    line-height: 1.2;
}

.pillar-card__body {
    padding: 0.75rem 0.7rem 0.9rem;
}

.theme-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.theme-card__label {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.8rem;
    font-weight: 800;
}

.theme-card__dot {
    flex: 0 0 auto;
    width: 0.42rem;
    height: 0.42rem;
    border-radius: 9999px;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.75);
}

.theme-card__dot--blue {
    background: #2f6ab9;
}

.theme-card__dot--green {
    background: #14955b;
}

.theme-card__dot--orange {
    background: #e96d0e;
}

.theme-card__dot--enabler {
    background: #0f5f88;
}

.theme-card__text {
    min-width: 0;
    flex: 1 1 auto;
}

.pillar-empty {
    padding: 0.45rem;
    color: #64748b;
    font-size: 0.72rem;
    font-style: italic;
    line-height: 1.35;
    text-align: center;
}

.enabler-shell__header {
    background: linear-gradient(180deg, #0f5f88 0%, #0c4e73 100%);
}

.enabler-grid {
    display: flex;
    flex-direction: column;
    gap: 0.28rem;
}

.enabler-grid__row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.28rem;
}

.enabler-grid__cell {
    display: flex;
    align-items: center;
    min-height: 34px;
    padding: 0.35rem 0.65rem;
    border-radius: 0.55rem;
    border: 1px solid #dfe7bb;
    background: linear-gradient(180deg, #f7f9d3 0%, #eef1ad 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55);
}

.enabler-grid__label {
    width: 100%;
    color: #3e4634;
    text-align: center;
    font-size: 0.73rem;
    font-weight: 700;
    font-style: italic;
    line-height: 1.25;
}

.enabler-grid__text {
    display: inline-block;
    max-width: 100%;
}

.core-values-bar {
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
