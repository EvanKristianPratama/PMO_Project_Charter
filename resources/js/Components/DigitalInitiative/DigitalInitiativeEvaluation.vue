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
        class="charter-wrapper w-full border border-slate-200 bg-white text-slate-800 shadow-sm print:shadow-none"
        style="font-family: 'Segoe UI', sans-serif;"
    >
        <!-- Bottom Grid Row 1: Value / Urgency / Ease -->
        <div class="grid grid-cols-1 border-b border-[#3b82f6] lg:grid-cols-3">
            <!-- Value Indication -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white flex items-center justify-start gap-2">
                    <span>Value Indication</span>
                    <span v-if="initiative.value_label" :class="['px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider', getLevelColorClass(initiative.value_label)]">
                        {{ initiative.value_label }}
                    </span>
                </div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Rationale</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.value_rationale ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Metrics Impacted</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.value_matrics ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Urgency -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white flex items-center justify-start gap-2">
                    <span>Urgency</span>
                    <span v-if="initiative.urgency_label" :class="['px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider', getLevelColorClass(initiative.urgency_label)]">
                        {{ initiative.urgency_label }}
                    </span>
                </div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Rationale</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.urgency_rationale ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center text-[10px] leading-tight">
                            Expected Go-Live</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.urgency_expected ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ease of Implementation -->
            <div class="flex flex-col">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white flex items-center justify-start gap-2">
                    <span>Easy of Implementation</span>
                    <span v-if="initiative.ease_label" :class="['px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider', getLevelColorClass(initiative.ease_label)]">
                        {{ initiative.ease_label }}
                    </span>
                </div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Rationale</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.ease_rationale ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Detail</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.ease_detail ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid Row 2: Resource / Interdependency / Sign By -->
        <div class="grid grid-cols-1 lg:grid-cols-3">
            <!-- Resource Requirement -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white flex items-center justify-start gap-2">
                    <span>Resource Requirement</span>
                    <span v-if="initiative.resource_label" :class="['px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider', getLevelColorClass(initiative.resource_label)]">
                        {{ initiative.resource_label }}
                    </span>
                </div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Rationale</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.resource_rationale ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#1e4f8f] flex items-center">
                            Detail</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.resource_detail ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interdependency -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Interdependency</div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Predecessor</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.predecessor ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Successor</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.successor ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center text-[10px] leading-tight">
                            Other BUs Implement</div>
                        <div class="px-2 py-1.5 flex-1">
                            <p class="whitespace-pre-line break-words">{{ initiative.otherBU ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sign By -->
            <div class="flex flex-col">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">Sign By</div>
                <div class="flex-1 flex flex-col divide-y divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Primary</div>
                        <div class="flex-1 flex items-center justify-center p-2">
                            <p class="text-center font-semibold text-slate-900">{{ signFirst }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1">
                        <div
                            class="bg-[#2e6ea2] w-28 shrink-0 px-2 py-1.5 font-bold text-white border-r border-[#3b82f6] flex items-center">
                            Others</div>
                        <div class="flex-1 flex items-center justify-center p-2">
                            <p class="text-center font-semibold text-slate-900">{{ signOthers }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
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
}
</style>
