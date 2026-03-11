<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: { type: Object, required: true },
});

const formatDate = (dateString) => {
    if (!dateString) return 'May 2024';
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const urgencyClass = computed(() => {
    const u = String(props.initiative.urgency || '').toLowerCase();
    if (u === 'high' || u === 'tinggi') return 'bg-[#00b050] text-white'; // Green
    if (u === 'medium' || u === 'sedang') return 'bg-[#ffc000] text-white'; // Yellow
    if (u === 'low' || u === 'rendah') return 'bg-[#92d050] text-white'; // Light Green
    return 'bg-slate-200 text-slate-600';
});

const valueClass = computed(() => {
    const v = String(props.initiative.value || '').toLowerCase();
    if (v === 'high' || v === 'tinggi') return 'bg-[#00b050] text-white';
    if (v === 'medium' || v === 'sedang') return 'bg-[#ffc000] text-white';
    if (v === 'low' || v === 'rendah') return 'bg-[#92d050] text-white';
    return 'bg-slate-200 text-slate-600';
});
</script>

<template>
    <div class="charter-wrapper w-full border border-slate-200 bg-white text-slate-800 shadow-sm print:shadow-none" style="font-family: 'Segoe UI', sans-serif;">
        <!-- Header Section -->
        <div>
            <!-- Title Row -->
            <div class="relative mb-4 pt-5 pl-5">
                <h1 class="flex text-2xl font-bold leading-tight text-slate-900">
                    <span class="shrink-0 text-[#3b5e96]">Scope Charter Detail</span>
                    <span class="mx-2 shrink-0 text-slate-400">|</span> 
                    <span class="block">-</span>
                </h1>
            </div>
            
            <!-- Metadata Bar (Contiguous) -->
            <div class="grid grid-cols-1 overflow-hidden border border-[#3b82f6] lg:grid-cols-4">
                 <!-- Project Owner -->
                 <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                    <div class="flex w-28 shrink-0 items-center justify-center bg-[#1e4f8f] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                        Project Owner
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] font-semibold text-slate-900">
                        -
                    </div>
                 </div>

                 <!-- H/SH -->
                 <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                    <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                        H/SH
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                        -
                    </div>
                 </div>

                 <!-- CoE -->
                 <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                    <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                        CoE
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                        -
                    </div>
                 </div>

                 <!-- RJJP -->
                 <div class="flex">
                    <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                        RJJP
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                        -
                    </div>
                 </div>
            </div>
        </div>

        <!-- Main Content 3-Col (Contiguous with Metadata Bar) -->
        <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-3">
            <!-- Col 1: Use Case Description -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                    Use Case Description
                </div>
                <div class="flex-1 bg-white p-4 text-[12px] text-slate-700 whitespace-pre-line">
                    -
                </div>
            </div>

            <!-- Col 2: Current Situation -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                    Current situation/ frictions addressed
                </div>
                <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                    <ul class="list-disc pl-4 space-y-2">
                        <li>-</li>
                    </ul>
                </div>
            </div>

            <!-- Col 3: Key Functionalities -->
            <div class="flex flex-col">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                    Key functionalities/scope
                </div>
                <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                    <ul class="list-disc pl-4 space-y-2">
                        <li>-</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Grid (Contiguous with Main Content) -->
        <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-3">
            <!-- Value Indication -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="flex items-center bg-[#1e4f8f] px-3 py-1.5 text-white">
                    <div class="text-[12px] font-bold">
                        Value Indication
                    </div>
                    <div class="ml-2 px-1.5 py-0 text-[10px] font-bold rounded-full border border-white/20 shadow-sm" :class="valueClass">
                        High
                    </div>
                </div>
                <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Metrics Impacted</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                </div>
            </div>

            <!-- Urgency -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="flex items-center bg-[#1e4f8f] px-3 py-1.5 text-white">
                    <div class="text-[12px] font-bold">
                        Urgency
                    </div>
                    <div class="ml-2 px-1.5 py-0 text-[10px] font-bold rounded-full border border-white/20 shadow-sm" :class="urgencyClass">
                        High
                    </div>
                </div>
                <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Expected Go-Live</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                </div>
            </div>

            <!-- Ease of Implementation -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0">
                <div class="flex items-center bg-[#1e4f8f] px-3 py-1.5 text-white">
                    <div class="text-[12px] font-bold">
                        Ease of Implementation
                    </div>
                    <div class="ml-2 px-1.5 py-0 text-[10px] font-bold rounded-full border border-white/20 shadow-sm bg-[#ffc000] text-white">
                        Medium
                    </div>
                </div>
                <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Notes</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-2">
            <!-- Resource Requirement -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="flex items-center bg-[#1e4f8f] px-3 py-1.5 text-white">
                    <div class="text-[12px] font-bold">
                        Resource Requirement
                    </div>
                    <div class="ml-2 px-1.5 py-0 text-[10px] font-bold rounded-full border border-white/20 shadow-sm bg-[#ffc000] text-white">
                        Medium
                    </div>
                </div>
                <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Notes</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                </div>
            </div>

            <!-- Interdependency -->
            <div class="flex flex-col">
                <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                    Interdependency
                </div>
                <div class="grid grid-cols-3 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Predecessor</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Successor</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                    <div class="flex flex-col">
                        <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Other BUs</div>
                        <p class="px-2 py-1.5 flex-1">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Print overrides to ensure background colors print */
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