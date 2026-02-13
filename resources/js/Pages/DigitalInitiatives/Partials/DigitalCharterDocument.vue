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
    if (u === 'medium' || u === 'sedang') return 'bg-[#ffc000] text-black'; // Yellow
    if (u === 'low' || u === 'rendah') return 'bg-[#92d050] text-black'; // Light Green
    return 'bg-slate-200 text-slate-600';
});

const valueClass = computed(() => {
    const v = String(props.initiative.value || '').toLowerCase();
    if (v === 'high' || v === 'tinggi') return 'bg-[#00b050] text-white';
    if (v === 'medium' || v === 'sedang') return 'bg-[#ffc000] text-black';
    if (v === 'low' || v === 'rendah') return 'bg-[#92d050] text-black';
    return 'bg-slate-200 text-slate-600';
});
</script>

<template>
    <div class="charter-wrapper mx-auto w-full max-w-[1400px] bg-white p-6 text-slate-800" style="font-family: 'Segoe UI', sans-serif;">
        <!-- Header Section -->
        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Row 1: Title (2 Cols) + Project Owner (1 Col) -->
            <div class="flex flex-col justify-center lg:col-span-2">
                 <div class="relative border-b-2 border-slate-300 pb-2">
                    <h1 class="flex text-3xl font-bold leading-tight text-slate-900">
                        <span class="shrink-0 text-[#3b5e96]">Scope Charter</span>
                        <span class="mx-2 shrink-0 text-slate-400">|</span> 
                        <span class="block">{{ initiative.useCase || 'Digital Initiative' }}</span>
                    </h1>
                 </div>
            </div>
            
            <div class="flex flex-col justify-end">
                 <!-- Project Owner -->
                 <div class="flex border border-slate-300">
                    <div class="flex w-32 items-center justify-center bg-[#1f4e79] p-2 text-center text-sm font-bold text-white">
                        Project Owner
                    </div>
                    <div class="flex flex-1 items-center bg-white p-2 text-sm font-semibold text-slate-900">
                        {{ initiative.projectOwner || '-' }}
                    </div>
                 </div>
            </div>

            <!-- Row 2: H/SH + CoE + RJJP (3 Cols) -->
            <!-- H/SH -->
            <div class="flex border border-slate-300">
                <div class="flex w-20 items-center justify-center bg-[#1f4e79] p-2 text-center text-sm font-bold text-white">
                    H/SH
                </div>
                <div class="flex flex-1 items-center bg-white p-2 text-sm text-slate-900">
                    {{ initiative.type || '-' }}
                </div>
            </div>

            <!-- CoE -->
            <div class="flex border border-slate-300">
                <div class="flex w-20 items-center justify-center bg-[#1f4e79] p-2 text-center text-sm font-bold text-white">
                    CoE
                </div>
                <div class="flex flex-1 items-center bg-white p-2 text-sm text-slate-900">
                    {{ initiative.coe || '-' }}
                </div>
            </div>

            <!-- RJJP -->
            <div class="flex border border-slate-300">
                <div class="flex w-20 items-center justify-center bg-[#1f4e79] p-2 text-center text-sm font-bold text-white">
                    RJJP
                </div>
                <div class="flex flex-1 items-center bg-white p-2 text-sm text-slate-900">
                    {{ initiative.rjjp || '-' }}
                </div>
            </div>
        </div>

        <!-- Main Content 3-Col -->
        <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Col 1: Use Case Description -->
            <div class="border border-slate-300">
                <div class="bg-[#1f4e79] p-2 text-sm font-bold text-white">
                    Use Case Description
                </div>
                <div class="min-h-[200px] p-4 text-sm text-slate-700 bg-white whitespace-pre-line">
                    {{ initiative.desc || '-' }}
                </div>
            </div>

            <!-- Col 2: Current Situation -->
            <div class="border border-slate-300">
                <div class="bg-[#1f4e79] p-2 text-sm font-bold text-white">
                    Current situation/ frictions addressed
                </div>
                <div class="min-h-[200px] p-4 text-sm text-slate-700 bg-white">
                    <ul class="list-disc pl-4 space-y-2">
                        <li>-</li>
                    </ul>
                </div>
            </div>

            <!-- Col 3: Key Functionalities -->
            <div class="border border-slate-300">
                <div class="bg-[#1f4e79] p-2 text-sm font-bold text-white">
                    Key functionalities/scope
                </div>
                <div class="min-h-[200px] p-4 text-sm text-slate-700 bg-white">
                    <ul class="list-disc pl-4 space-y-2">
                        <li>-</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Grid 3-Col -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Value Indication -->
            <div>
                <div class="mb-1 text-lg font-semibold text-slate-800">Value Indication</div>
                <div class="flex h-32 border border-slate-300">
                    <div class="flex w-24 items-center justify-center text-lg font-bold" :class="valueClass">
                        {{ initiative.value ? initiative.value : '-' }}
                    </div>
                    <div class="flex-1 bg-white p-2 text-xs text-slate-600">
                        <div class="font-bold text-slate-800">Rationale:</div>
                        <p>-</p>
                        <div class="mt-2 font-bold text-slate-800">Metrics Impacted:</div>
                        <p>-</p>
                    </div>
                </div>
            </div>

            <!-- Urgency -->
            <div>
                <div class="mb-1 text-lg font-semibold text-slate-800">Urgency</div>
                <div class="flex h-32 border border-slate-300">
                    <div class="flex w-24 items-center justify-center text-lg font-bold" :class="urgencyClass">
                        {{ initiative.urgency ? initiative.urgency : '-' }}
                    </div>
                    <div class="flex-1 bg-white p-2 text-xs text-slate-600">
                        <div class="font-bold text-slate-800">Rationale:</div>
                        <p>-</p>
                        <div class="mt-2 font-bold text-slate-800">Expected Go-Live Date:</div>
                        <p>-</p>
                    </div>
                </div>
            </div>

            <!-- Ease of Implementation -->
            <div>
                <div class="mb-1 text-lg font-semibold text-slate-800">Ease of Implementation</div>
                <div class="flex h-32 border border-slate-300">
                    <div class="flex w-24 items-center justify-center bg-[#ffc000] text-lg font-bold text-black">
                        Medium
                    </div>
                    <div class="flex-1 bg-white p-2 text-xs text-slate-600">
                        <p>Complexity in integrating with multiple backend systems.</p>
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
