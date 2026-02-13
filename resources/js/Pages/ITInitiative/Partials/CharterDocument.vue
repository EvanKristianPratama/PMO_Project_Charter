<script setup>
import { ClockIcon, DocumentTextIcon, Squares2X2Icon } from '@heroicons/vue/24/outline';

const props = defineProps({
    itInitiative: { type: Object, required: true },
    form: { type: Object, required: true },
    editable: { type: Boolean, default: false },
});

const lineItems = (value) => String(value || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

const showScope = () => props.editable || Boolean(String(props.form.scope || '').trim());
</script>

<template>
    <article class="charter-sheet mx-auto w-full max-w-[1200px] border border-slate-300 bg-[#e9e9e9] p-5 text-slate-900 shadow-sm dark:border-white/20 dark:bg-[#d7d7d7]">
        <header class="charter-block border-b-2 border-[#2e6ea2] pb-3">
            <h1 class="text-2xl font-extrabold leading-tight tracking-tight text-[#0f63b5]">
                IT Initiative Charter: {{ itInitiative.name || '-' }}
            </h1>
            <p class="mt-1 text-sm font-semibold text-slate-700">
                Version: {{ form.version_label || 'Draft' }}
            </p>
        </header>

        <section class="charter-block mt-4 grid gap-5 lg:grid-cols-3">
            <div class="meta-item">
                <span class="meta-icon">
                    <Squares2X2Icon class="h-5 w-5" />
                </span>
                <div class="meta-label">Category</div>
                <div class="meta-value">
                    <input
                        v-if="editable"
                        v-model="form.category"
                        type="text"
                        class="meta-input"
                        placeholder="User Interface and Experience"
                    />
                    <span v-else class="font-semibold">{{ form.category || '-' }}</span>
                </div>
            </div>

            <div class="meta-item">
                <span class="meta-icon">
                    <ClockIcon class="h-5 w-5" />
                </span>
                <div class="meta-label">Duration</div>
                <div class="meta-value">
                    <input
                        v-if="editable"
                        v-model="form.duration"
                        type="text"
                        class="meta-input"
                        placeholder="2 Years (2025-2026)"
                    />
                    <span v-else class="font-semibold">{{ form.duration || '-' }}</span>
                </div>
            </div>

            <div class="meta-item">
                <span class="meta-icon">
                    <DocumentTextIcon class="h-5 w-5" />
                </span>
                <div class="meta-label">Company Owner</div>
                <div class="meta-value font-semibold">
                    {{ itInitiative.owner_name || itInitiative.owner?.name || '-' }}
                </div>
            </div>
        </section>

        <section class="charter-block mt-4">
            <div class="bar-main">IT Initiative Information</div>

            <div class="mt-2 grid gap-3 lg:grid-cols-2">
                <article class="panel">
                    <div class="bar-sub">Background - Current gap / opportunities</div>
                    <div class="panel-body">
                        <textarea
                            v-if="editable"
                            v-model="form.background"
                            class="field-area"
                            placeholder="One point per line..."
                        ></textarea>
                        <ul v-else-if="lineItems(form.background).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.background)" :key="`bg-${idx}`">{{ line }}</li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>

                <article class="panel">
                    <div class="bar-sub">Objectives</div>
                    <div class="panel-body">
                        <textarea
                            v-if="editable"
                            v-model="form.objectives"
                            class="field-area"
                            placeholder="One point per line..."
                        ></textarea>
                        <ul v-else-if="lineItems(form.objectives).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.objectives)" :key="`obj-${idx}`">{{ line }}</li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>
            </div>

            <article v-if="showScope()" class="panel mt-3">
                <div class="bar-sub">Scope</div>
                <div class="panel-body">
                    <textarea
                        v-if="editable"
                        v-model="form.scope"
                        class="field-area"
                        placeholder="One point per line..."
                    ></textarea>
                    <ul v-else-if="lineItems(form.scope).length" class="bullet-list">
                        <li v-for="(line, idx) in lineItems(form.scope)" :key="`scope-${idx}`">{{ line }}</li>
                    </ul>
                    <p v-else class="empty">-</p>
                </div>
            </article>
        </section>

        <section class="charter-block mt-4 grid gap-3 lg:grid-cols-[49%_50%] items-start">
            <article class="panel">
                <div class="bar-sub h-10 !text-xl !font-bold leading-[1.2]">Impact and value</div>
                <div class="panel-body min-h-[140px]">
                    <textarea
                        v-if="editable"
                        v-model="form.impact_value"
                        class="field-area"
                        placeholder="One point per line..."
                    ></textarea>
                    <ul v-else-if="lineItems(form.impact_value).length" class="bullet-list">
                        <li v-for="(line, idx) in lineItems(form.impact_value)" :key="`impact-${idx}`">{{ line }}</li>
                    </ul>
                    <p v-else class="empty">-</p>
                </div>
            </article>

            <article>
                <div class="bar-main">Required resources</div>
                <div class="mt-2 grid gap-2 md:grid-cols-3">
                    <div class="panel">
                        <div class="bar-sub !py-1 !px-2 !text-[13px]">Key Personnel</div>
                        <div class="panel-body min-h-[40px] !p-1.5">
                            <textarea
                                v-if="editable"
                                v-model="form.key_personnel"
                                class="field-area field-area-sm"
                                placeholder="One point per line..."
                            ></textarea>
                            <ul v-else-if="lineItems(form.key_personnel).length" class="bullet-list">
                                <li v-for="(line, idx) in lineItems(form.key_personnel)" :key="`kp-${idx}`">{{ line }}</li>
                            </ul>
                            <p v-else class="empty">-</p>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="bar-sub !py-1 !px-2 !text-[13px]">Key Items</div>
                        <div class="panel-body min-h-[40px] !p-1.5">
                            <textarea
                                v-if="editable"
                                v-model="form.key_items"
                                class="field-area field-area-sm"
                                placeholder="One point per line..."
                            ></textarea>
                            <ul v-else-if="lineItems(form.key_items).length" class="bullet-list">
                                <li v-for="(line, idx) in lineItems(form.key_items)" :key="`ki-${idx}`">{{ line }}</li>
                            </ul>
                            <p v-else class="empty">-</p>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="bar-sub !py-1 !px-2 !text-[13px]">Indicative budget requirement</div>
                        <div class="panel-body min-h-[40px] !p-1.5">
                            <input
                                v-if="editable"
                                v-model="form.budget"
                                type="text"
                                class="field-input"
                                placeholder="~ 3 - 8 mn USD"
                            />
                            <p v-else class="text-xl font-semibold text-slate-900">{{ form.budget || '-' }}</p>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="charter-block mt-4">
            <div class="bar-main">Potential risks</div>

            <div class="mt-2 grid gap-3 lg:grid-cols-2">
                <article class="panel">
                    <div class="bar-sub">Risk identified</div>
                    <div class="panel-body min-h-[130px]">
                        <textarea
                            v-if="editable"
                            v-model="form.risks_identified"
                            class="field-area"
                            placeholder="One point per line..."
                        ></textarea>
                        <ul v-else-if="lineItems(form.risks_identified).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.risks_identified)" :key="`risk-${idx}`">{{ line }}</li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>

                <article class="panel">
                    <div class="bar-sub">Risk mitigation</div>
                    <div class="panel-body min-h-[130px]">
                        <textarea
                            v-if="editable"
                            v-model="form.risk_mitigation"
                            class="field-area"
                            placeholder="One point per line..."
                        ></textarea>
                        <ul v-else-if="lineItems(form.risk_mitigation).length" class="bullet-list">
                            <li v-for="(line, idx) in lineItems(form.risk_mitigation)" :key="`mit-${idx}`">{{ line }}</li>
                        </ul>
                        <p v-else class="empty">-</p>
                    </div>
                </article>
            </div>
        </section>
    </article>
</template>

<style scoped>
.charter-sheet {
    font-family: "Segoe UI", Inter, Arial, sans-serif;
    border-radius: 0;
}

.charter-block {
    break-inside: avoid;
    page-break-inside: avoid;
}

.meta-item {
    display: flex;
    align-items: center;
}

.meta-icon {
    display: inline-flex;
    height: 48px;
    width: 48px;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: #0f63b5;
    color: #fff;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #e2e8f0;
    z-index: 10;
    flex-shrink: 0;
}

.meta-label {
    height: 32px;
    display: flex;
    align-items: center;
    padding: 0 12px 0 24px;
    margin-left: -18px;
    background: #0f63b5;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    border-radius: 0 4px 4px 0;
    z-index: 1;
}

.meta-value {
    flex: 1;
    margin-left: 12px;
    font-size: 15px;
    line-height: 1.25;
    font-weight: 600;
}

.meta-input {
    width: 100%;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 4px 8px;
    font-size: 14px;
    font-weight: 600;
    background: #fff;
    outline: none;
}

.bar-main {
    background: #0f63b5;
    color: #fff;
    padding: 8px 12px;
    font-size: 18px;
    font-weight: 700;
    line-height: 1.2;
}

.bar-sub {
    background: #3f6f9f;
    color: #fff;
    padding: 6px 12px;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.2;
}

.panel {
    border: 2px solid #2e6ea2;
    border-radius: 0;
    background: transparent;
}

.panel-body {
    padding: 10px;
    background: #e9e9e9;
}

.bullet-list {
    margin: 0;
    padding-left: 0;
    list-style: none;
    font-size: 14px;
    line-height: 1.4;
}

.bullet-list li + li {
    margin-top: 2px;
}

.field-area {
    width: 100%;
    min-height: 100px;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px;
    resize: vertical;
    background: #fff;
    font-size: 14px;
    line-height: 1.4;
    outline: none;
}

.field-area-sm {
    min-height: 40px;
    font-size: 12px;
    line-height: 1.25;
}

.field-input {
    width: 100%;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 6px 8px;
    font-size: 14px;
    font-weight: 600;
    background: #fff;
    outline: none;
}

.empty {
    color: #6b7280;
    font-size: 14px;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    .charter-sheet {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        border: 0;
        background: #ffffff !important;
        box-shadow: none;
    }

    .panel-body {
        background: #ffffff !important;
    }

    .field-area,
    .field-input,
    .meta-input {
        border: 1px solid #2e6ea2;
    }
}
</style>
