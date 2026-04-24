<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: { type: Object, required: true },
});

const getLevelColorClass = (label) => {
    if (!label) return 'hidden';
    const l = String(label).toLowerCase();
    if (l === 'high') return 'bg-emerald-500 text-white';
    if (l === 'medium') return 'bg-orange-500 text-white';
    if (l === 'low') return 'bg-rose-500 text-white';
    return 'bg-slate-400 text-white';
};

const formatUpdatedDate = (value) => {
    if (!value) return '-';
    const str = String(value).trim();
    if (!str) return '-';
    const date = new Date(str);
    if (Number.isNaN(date.getTime())) {
        return str;
    }
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(date);
};

const signFirst = computed(() => {
    return props.initiative.sign_by?.[0] ?? '-';
});

const signOthers = computed(() => {
    const rest = (props.initiative.sign_by ?? []).slice(1);
    return rest.length ? rest.join(', ') : '-';
});
</script>

<template>
    <div
        class="charter-wrapper w-full border border-[#3b82f6] bg-white text-slate-800 shadow-sm print:shadow-none"
        style="font-family: 'Segoe UI', sans-serif;"
    >
        <!-- Header Section -->
        <div class="flex flex-wrap items-center justify-between border-b border-slate-200 px-5 py-2">
            <div class="min-w-0 flex-1">
                <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                    <span class="shrink-0 text-[#3b5e96]">Digital Initiative</span>
                    <span class="mx-2 shrink-0 text-slate-400">|</span>
                    <span>{{ initiative.usecase ?? '-' }}</span>
                </h1>
            </div>

            <!-- Score Panel -->
            <div class="score-panel">
                <div class="score-column border-r border-[#3b82f6]">
                    <div class="bar-sub-mini text-center">Value</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        {{ initiative.value_label ?? '-' }}
                    </div>
                </div>
                <div class="score-column border-r border-[#3b82f6]">
                    <div class="bar-sub-mini text-center">Urgency</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        {{ initiative.urgency_label ?? '-' }}
                    </div>
                </div>
                <div class="score-column border-r border-[#3b82f6]">
                    <div class="bar-sub-mini text-center">Easy</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        {{ initiative.ease_label ?? '-' }}
                    </div>
                </div>
                <div class="score-column">
                    <div class="bar-sub-mini text-center">Resource</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        {{ initiative.resource_label ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadata Bar -->
        <div class="grid grid-cols-1 overflow-hidden border-x border-b border-[#3b82f6] lg:grid-cols-4">
            <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="flex w-28 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">Project Owner</div>
                <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">{{ initiative.owner ?? '-' }}</div>
            </div>
            <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">PIC</div>
                <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">{{ initiative.organization ?? '-' }}</div>
            </div>
            <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">CoE</div>
                <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">{{ initiative.coe ?? '-' }}</div>
            </div>
            <div class="flex">
                <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">Updated</div>
                <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">{{ formatUpdatedDate(initiative.update_doc) }}</div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.score-panel {
    display: flex;
    border: 1px solid #3b82f6;
    min-width: 320px;
    background: #fff;
}

.score-column {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.bar-sub-mini {
    background: #2e6ea2;
    color: #fff;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
}

.panel-body-mini {
    padding: 6px;
    background: #fff;
    min-height: 32px;
}

@media print {
    :deep(*) {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .charter-wrapper {
        max-width: none !important;
        width: 100% !important;
        padding: 0 !important;
    }

    .score-panel {
        border-color: #3b82f6 !important;
    }
}
</style>
