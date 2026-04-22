<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <BusinessStrategy
                :page="page"
                :summary="summary"
                :groups="groups"
                :strategy-columns="strategyColumns"
                :organization-options="organizationOptions"
            />

            <section class="key-enablers-panel">
                <div class="key-enablers-panel__title">Key enablers</div>
                <div class="key-enablers-grid">
                    <article class="key-enabler-card">
                        <h3 class="key-enabler-card__heading">HSSE &amp; Sustainability</h3>
                        <p class="key-enabler-card__body">
                            HSSE Excellence with assurance of biz sustainability including Net Zero Target
                        </p>
                    </article>

                    <article class="key-enabler-card">
                        <h3 class="key-enabler-card__heading">Technology &amp; Innovation</h3>
                        <p class="key-enabler-card__body">
                            Technology excellence for Pertamina&apos;s competitiveness
                        </p>
                    </article>

                    <article class="key-enabler-card">
                        <h3 class="key-enabler-card__heading">Digital Transformation</h3>
                        <p class="key-enabler-card__body">
                            Digital transformation program to support Pertamina Group Technology, Internet of Things
                            (IoT), Automation, Artificial Intelligence (AI)
                        </p>
                    </article>

                    <article class="key-enabler-card">
                        <h3 class="key-enabler-card__heading">Policy Advocacy</h3>
                        <p class="key-enabler-card__body">
                            Advocating policy and stakeholder management
                        </p>
                    </article>

                    <article class="key-enabler-card">
                        <h3 class="key-enabler-card__heading">Human Resources &amp; Execution</h3>
                        <p class="key-enabler-card__body">
                            Development of human resources and simplification of business process
                        </p>
                    </article>
                </div>
            </section>
        </div>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import BusinessStrategy from '@/Components/StrategicHouse/BusinessStrategy/BusinessStrategy.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    groups: {
        type: Array,
        default: () => [],
    },
    strategyColumns: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (
    props.embedded
        ? {}
        : { title: props.page?.title ?? 'Business Strategy' }
));
</script>

<style scoped>
.key-enablers-panel {
    border: 1px solid #9ab3d3;
    background:
        linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.65);
}

.key-enablers-panel__title {
    border-bottom: 1px solid #9ab3d3;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 800;
    color: #2d5f9e;
    letter-spacing: 0.01em;
}

.key-enablers-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
}

.key-enabler-card {
    min-height: 90px;
    padding: 8px 10px 10px;
    border-right: 1px solid #b6c7dd;
    position: relative;
    background-size: 12px 12px;
}

.key-enabler-card:last-child {
    border-right: none;
}

.key-enabler-card__heading {
    margin: 0 0 4px;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.25;
    color: #3d68a2;
}

.key-enabler-card__body {
    margin: 0;
    font-size: 11px;
    line-height: 1.35;
    color: #1f2f45;
}

.key-enabler-card--highlight {
    border-radius: 14px;
    box-shadow: inset 0 0 0 4px #ff2b17;
    background-color: rgba(255, 255, 255, 0.92);
}

@media (max-width: 1024px) {
    .key-enablers-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .key-enablers-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 560px) {
    .key-enablers-grid {
        grid-template-columns: 1fr;
    }

    .key-enabler-card {
        border-right: none;
        border-bottom: 1px solid #b6c7dd;
    }

    .key-enabler-card:last-child {
        border-bottom: none;
    }
}

:deep(.dark) .key-enablers-panel {
    border-color: rgba(148, 163, 184, 0.28);
    background: linear-gradient(180deg, #122033 0%, #0f1a2a 100%);
    box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.08);
}

:deep(.dark) .key-enablers-panel__title {
    border-bottom-color: rgba(148, 163, 184, 0.24);
    color: #bfdbfe;
}

:deep(.dark) .key-enabler-card {
    border-right-color: rgba(148, 163, 184, 0.16);
    background-image:
        radial-gradient(circle at 1px 1px, rgba(148, 163, 184, 0.12) 1px, transparent 0);
}

:deep(.dark) .key-enabler-card__heading {
    color: #93c5fd;
}

:deep(.dark) .key-enabler-card__body {
    color: #e2e8f0;
}

:deep(.dark) .key-enabler-card--highlight {
    background-color: rgba(15, 23, 42, 0.78);
}
</style>
