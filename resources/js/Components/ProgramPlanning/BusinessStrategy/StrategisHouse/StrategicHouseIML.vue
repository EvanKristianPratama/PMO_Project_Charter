<template>
    <div class="flex flex-col items-center w-full max-w-7xl mx-auto space-y-1 p-4">
        <div class="relative w-[90%] flex justify-center mt-4" v-if="strategicHouseIML">
            <div class="w-full bg-[#1c2a5e] text-white pt-12 pb-4 px-12 text-center"
                style="clip-path: polygon(50% 0%, 100% 100%, 0% 100%); min-height: 140px; display: flex; flex-direction: column; justify-content: flex-end;">
                <div class="font-bold text-xs italic mb-1">Vision:</div>
                <div class="font-semibold text-sm max-w-xl mx-auto leading-tight">
                    {{ strategicHouseIML.vision || '-' }}
                </div>
            </div>
        </div>

        <div class="w-[90%] bg-[#121f40] text-white text-center py-4 px-8 shadow-md" v-if="strategicHouseIML">
            <div class="font-bold text-xs italic mb-1">Mission:</div>
            <div class="font-semibold text-sm">{{ strategicHouseIML.mission || '-' }}</div>
        </div>

        <template v-if="mainGoals.length">
            <div class="w-[90%]">
                <div class="grid gap-1 md:grid-cols-4">
                    <article v-for="goal in mainGoals" :key="goal.id" class="goal-card">
                        <div class="goal-card__header">
                            <h2 class="goal-card__title">{{ goal.title }}</h2>
                        </div>

                        <div class="goal-card__roof">

                        </div>

                        <div class="goal-card__body">
                            <template v-if="goal.themes && goal.themes.length">
                                <div class="pillar-row">
                                    <section v-for="theme in goal.themes" :key="theme.id" class="pillar-card">
                                        <div class="pillar-card__title">
                                            <span class="pillar-card__bullet"></span>
                                            <span>{{ theme.name }}</span>
                                        </div>
                                    </section>
                                </div>

                            </template>
                        </div>
                    </article>
                </div>
            </div>
        </template>

        <template v-if="enablerGoal">
            <div class="w-[90%] ">
                <div v-if="enablerGoal.themes && enablerGoal.themes.length" class="enabler-row">
                    <section v-for="theme in enablerGoal.themes" :key="theme.id" class="enabler-theme">
                        <div class="enabler-theme__name">{{ theme.name }}</div>

                        <ul v-if="theme.pillar_themes && theme.pillar_themes.length" class="enabler-theme__list">
                            <li v-for="pillar in theme.pillar_themes" :key="pillar.id">
                                <span class="pillar-card__bullet"></span>
                                <span class="pillar-card__text">{{ pillar.strategy }}</span>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </template>
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

const mainGoals = computed(() => {
    if (!props.imlGoals) return [];
    return props.imlGoals.filter(goal => Number(goal.id) !== 36);
});

const enablerGoal = computed(() => {
    if (!props.imlGoals) return null;
    return props.imlGoals.find(goal => Number(goal.id) === 36) || null;
});

const goalBadge = (goal) => {
    const code = String(goal?.code ?? '').trim();
    if (code) return code.slice(0, 3).toUpperCase();

    const title = String(goal?.title ?? '').trim();
    if (!title) return 'GOAL';

    return title
        .split(/\s+/)
        .slice(0, 2)
        .map(word => word.charAt(0).toUpperCase())
        .join('');
};
</script>

<style scoped>
.goal-card {
    background: linear-gradient(to bottom, #d9e7f6 0 37%, #172754 20%);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
}

.goal-card__header {
    padding: 18px 16px 10px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    text-align: center;
}

.goal-card__title {
    color: #1d274d;
    font-size: 1.05rem;
    font-weight: 700;
    font-style: italic;
    line-height: 1.25;
}

.goal-card__roof {
    position: relative;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #172754;
    clip-path: polygon(50% 0%, 100% 100%, 0% 100%);
}

.goal-card__body {
    color: #f7fbff;
}

.pillar-row {
    display: flex;
    flex-direction: column;
    align-items: stretch;
}

.pillar-card {
    width: 100%;
    padding: 5px;
}

.pillar-card__title {
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 300;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.pillar-card__bullet {
    width: 5px;
    height: 5px;
    margin-top: 6px;
    border-radius: 9999px;
    background: #ffdd8a;
    flex: 0 0 auto;
}

.pillar-card__text {
    flex: 1;
}

.enabler-row {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.enabler-theme {
    padding: 5px;
    background: #0d2450;
}

.enabler-theme__name {
    margin-bottom: 5px;
    color: #f7f7f7;
    font-size: 0.82rem;
    font-weight: 700;
    line-height: 1.25;
}

.enabler-theme__list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.enabler-theme__list li {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    color: #f0f6ff;
    font-size: 0.72rem;
    line-height: 1.35;
}
</style>
