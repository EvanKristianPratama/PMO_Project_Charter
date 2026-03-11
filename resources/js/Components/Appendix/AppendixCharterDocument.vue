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
                    <span class="block">{{ initiative.useCase || 'Digital Initiative' }}</span>
                </h1>
            </div>
            
            <!-- Metadata Bar (Contiguous) -->
            <div class="grid grid-cols-1 overflow-hidden border border-[#3b82f6] lg:grid-cols-4">
                 <!-- Project Owner -->
                 <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                    <div class="flex w-28 shrink-0 items-center justify-center bg-[#1e4f8f] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wider text-white">
                        Project Owner
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] font-semibold text-slate-900">
                        {{ initiative.projectOwner || '-' }}
                    </div>
                 </div>

                 <!-- H/SH -->
                 <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                    <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wider text-white">
                        H/SH
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                        {{ initiative.type || '-' }}
                    </div>
                 </div>

                 <!-- CoE -->
                 <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                    <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wider text-white">
                        CoE
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                        {{ initiative.coe || '-' }}
                    </div>
                 </div>

                 <!-- RJJP -->
                 <div class="flex">
                    <div class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[10px] font-bold uppercase tracking-wider text-white">
                        RJJP
                    </div>
                    <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                        {{ initiative.rjjp || '-' }}
                    </div>
                 </div>
            </div>
        </div>

        <!-- Main Content 3-Col (Contiguous with Metadata Bar) -->
        <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-3">
            <!-- Col 1: Use Case Description -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    Use Case Description
                </div>
                <div class="flex-1 bg-white p-4 text-[12px] text-slate-700 whitespace-pre-line">
                    {{ initiative.desc || '-' }}
                </div>
            </div>

            <!-- Col 2: Current Situation -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
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
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
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
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    Value Indication
                </div>
                <div class="flex flex-1 items-stretch bg-white">
                    <div class="flex w-20 shrink-0 items-center justify-center text-sm font-bold" :class="valueClass">
                        {{ initiative.value ? initiative.value : '-' }}
                    </div>
                    <div class="flex-1 border-l border-[#3b82f6] p-3 text-[10px] text-slate-600">
                        <div class="font-bold uppercase tracking-tight text-slate-800">Rationale:</div>
                        <p class="mb-2">-</p>
                        <div class="font-bold uppercase tracking-tight text-slate-800">Metrics Impacted:</div>
                        <p>-</p>
                    </div>
                </div>
            </div>

            <!-- Urgency -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    Urgency
                </div>
                <div class="flex flex-1 items-stretch bg-white">
                    <div class="flex w-20 shrink-0 items-center justify-center text-sm font-bold" :class="urgencyClass">
                        {{ initiative.urgency ? initiative.urgency : '-' }}
                    </div>
                    <div class="flex-1 border-l border-[#3b82f6] p-3 text-[10px] text-slate-600">
                        <div class="font-bold uppercase tracking-tight text-slate-800">Rationale:</div>
                        <p class="mb-2">-</p>
                        <div class="font-bold uppercase tracking-tight text-slate-800">Expected Go-Live:</div>
                        <p>-</p>
                    </div>
                </div>
            </div>

            <!-- Ease of Implementation -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0">
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    Ease of Implementation
                </div>
                <div class="flex flex-1 items-stretch bg-white">
                    <div class="flex w-20 shrink-0 items-center justify-center bg-[#ffc000] text-sm font-bold text-white">
                        Medium
                    </div>
                    <div class="flex-1 border-l border-[#3b82f6] p-3 text-[10px] text-slate-600">
                        <div class="font-bold uppercase tracking-tight text-slate-800">Rationale:</div>
                        <p class="mb-2">-</p>
                        <div class="font-bold uppercase tracking-tight text-slate-800">Notes:</div>
                        <p>-</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-2">
            <!-- Resource Requirement -->
            <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    Resource Requirement
                </div>
                <div class="flex flex-1 items-stretch bg-white">
                    <div class="flex w-20 shrink-0 items-center justify-center bg-[#ffc000] text-sm font-bold text-white">
                        Medium
                    </div>
                    <div class="flex-1 border-l border-[#3b82f6] p-3 text-[10px] text-slate-600">
                        <div class="font-bold uppercase tracking-tight text-slate-800">Rationale:</div>
                        <p class="mb-2">-</p>
                        <div class="font-bold uppercase tracking-tight text-slate-800">Notes:</div>
                        <p>-</p>
                    </div>
                </div>
            </div>

            <!-- Interdependency -->
            <div class="flex flex-col">
                <div class="bg-[#2e6ea2] px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    Interdependency
                </div>
                <div class="flex-1 bg-white p-3 text-[10px] text-slate-600">
                    <div class="font-bold uppercase tracking-tight text-slate-800">Predecessor:</div>
                    <p class="mb-1">-</p>
                    <div class="font-bold uppercase tracking-tight text-slate-800">Successor:</div>
                    <p class="mb-1">-</p>
                    <div class="font-bold uppercase tracking-tight text-slate-800">Other BUs implementing the same use cases:</div>
                    <p>-</p>
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