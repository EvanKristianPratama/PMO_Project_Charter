<script setup>
import { ClockIcon, DocumentTextIcon, Squares2X2Icon } from '@heroicons/vue/24/outline';

const props = defineProps({
    project: { type: Object, required: true },
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
    <article class="charter-sheet mx-auto w-full max-w-[1400px] border border-slate-300 bg-[#e9e9e9] p-8 text-slate-900 shadow-sm dark:border-white/20 dark:bg-[#d7d7d7]">
        <header class="charter-block border-b-2 border-[#2e6ea2] pb-3">
            <h1 class="text-5xl font-extrabold leading-tight tracking-tight">
                Project Charter: {{ project.name || '-' }}
            </h1>
        </header>

        <section class="charter-block mt-5 grid gap-5 xl:grid-cols-3">
            <div class="meta-item">
                <span class="meta-icon">
                    <Squares2X2Icon class="h-7 w-7" />
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
                    <ClockIcon class="h-7 w-7" />
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
                    <DocumentTextIcon class="h-7 w-7" />
                </span>
                <div class="meta-label">Project Owner</div>
                <div class="meta-value font-semibold">
                    {{ project.owner_name || project.owner?.name || 'Unassigned' }}
                </div>
            </div>
        </section>

        <section class="charter-block mt-5">
            <div class="bar-main">Project Information</div>

            <div class="mt-2 grid gap-4 lg:grid-cols-2">
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

            <article v-if="showScope()" class="panel mt-4">
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

        <section class="charter-block mt-5 grid gap-4 xl:grid-cols-[46%_54%]">
            <article class="panel">
                <div class="bar-sub h-12 !text-4xl !font-bold leading-[1.2]">Impact and value</div>
                <div class="panel-body min-h-[180px]">
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

            <article class="panel">
                <div class="bar-main">Required resources</div>
                <div class="mt-2 grid gap-3 md:grid-cols-3">
                    <div class="panel">
                        <div class="bar-sub">Key Personnel</div>
                        <div class="panel-body min-h-[156px]">
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
                        <div class="bar-sub">Key Items</div>
                        <div class="panel-body min-h-[156px]">
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
                        <div class="bar-sub">Indicative budget requirement</div>
                        <div class="panel-body min-h-[156px]">
                            <input
                                v-if="editable"
                                v-model="form.budget"
                                type="text"
                                class="field-input"
                                placeholder="~ 3 - 8 mn USD"
                            />
                            <p v-else class="text-3xl font-semibold text-slate-900">{{ form.budget || '-' }}</p>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="charter-block mt-5">
            <div class="bar-main">Potential risks</div>

            <div class="mt-2 grid gap-4 lg:grid-cols-2">
                <article class="panel">
                    <div class="bar-sub">Risk identified</div>
                    <div class="panel-body min-h-[172px]">
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
                    <div class="panel-body min-h-[172px]">
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
    display: grid;
    grid-template-columns: 76px 1fr;
    align-items: center;
    column-gap: 12px;
}

.meta-icon {
    display: inline-flex;
    height: 72px;
    width: 72px;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: #0f63b5;
    color: #fff;
}

.meta-label {
    height: 40px;
    display: flex;
    align-items: center;
    padding: 0 14px;
    background: #0f63b5;
    color: #fff;
    font-size: 31px;
    font-weight: 700;
}

.meta-value {
    grid-column: 2;
    margin-top: 8px;
    font-size: 36px;
    line-height: 1.25;
}

.meta-input {
    width: 100%;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px 10px;
    font-size: 24px;
    font-weight: 600;
    background: #fff;
    outline: none;
}

.bar-main {
    background: #0f63b5;
    color: #fff;
    padding: 10px 14px;
    font-size: 39px;
    font-weight: 700;
    line-height: 1.2;
}

.bar-sub {
    background: #3f6f9f;
    color: #fff;
    padding: 9px 14px;
    font-size: 35px;
    font-weight: 700;
    line-height: 1.2;
}

.panel {
    border: 2px solid #2e6ea2;
    border-radius: 0;
    background: transparent;
}

.panel-body {
    padding: 13px 14px;
    background: #e9e9e9;
}

.bullet-list {
    margin: 0;
    padding-left: 24px;
    list-style: disc;
    font-size: 38px;
    line-height: 1.35;
}

.bullet-list li + li {
    margin-top: 5px;
}

.field-area {
    width: 100%;
    min-height: 160px;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 10px 12px;
    resize: vertical;
    background: #fff;
    font-size: 24px;
    line-height: 1.45;
    outline: none;
}

.field-area-sm {
    min-height: 130px;
}

.field-input {
    width: 100%;
    border: 1px solid #2e6ea2;
    border-radius: 0;
    padding: 8px 10px;
    font-size: 24px;
    font-weight: 600;
    background: #fff;
    outline: none;
}

.empty {
    color: #6b7280;
    font-size: 24px;
}

@media (max-width: 1536px) {
    .meta-label {
        font-size: 18px;
    }

    .meta-value {
        font-size: 20px;
    }

    .bar-main {
        font-size: 23px;
    }

    .bar-sub {
        font-size: 20px;
    }

    .bullet-list {
        font-size: 16px;
    }

    .field-area,
    .field-input,
    .meta-input {
        font-size: 15px;
    }

    .empty {
        font-size: 15px;
    }
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
