<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: { type: Object, required: true },
});
</script>

<template>
    <div class="charter-wrapper w-full border border-slate-200 bg-white text-slate-800 shadow-sm print:shadow-none"
        style="font-family: 'Segoe UI', sans-serif;">
        <!-- Header Section -->
        <div class="flex flex-wrap items-center justify-between border-b border-slate-200 px-5 py-2">
            <!-- Title Row -->
            <div class="min-w-0 flex-1">
                <h1 class="text-[18px] font-extrabold leading-tight text-slate-900">
                    <span class="shrink-0 text-[#3b5e96]">Scope Charter Detail</span>
                    <span class="mx-2 shrink-0 text-slate-400">|</span>
                    <span class="">-</span>
                </h1>
            </div>

            <!-- Score Panel -->
            <div class="score-panel">
                <div class="score-column border-r border-[#1e4f8f]">
                    <div class="bar-sub-mini text-center">Value</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        -
                    </div>
                </div>
                <div class="score-column border-r border-[#1e4f8f]">
                    <div class="bar-sub-mini text-center">Urgency</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        -
                    </div>
                </div>
                <div class="score-column border-r border-[#1e4f8f]">
                    <div class="bar-sub-mini text-center">Ease</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        -
                    </div>
                </div>
                <div class="score-column">
                    <div class="bar-sub-mini text-center">Resource</div>
                    <div class="panel-body-mini flex items-center justify-center text-[13px] text-slate-900">
                        -
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metadata Bar -->
    <div class="grid grid-cols-1 overflow-hidden border-x border-b border-[#3b82f6] lg:grid-cols-4">
        <!-- Project Owner -->
        <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div
                class="flex w-28 shrink-0 items-center justify-center bg-[#1e4f8f] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                Project Owner
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] font-semibold text-slate-900">
                -
            </div>
        </div>

        <!-- H/SH -->
        <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div
                class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                H/SH
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                -
            </div>
        </div>

        <!-- CoE -->
        <div class="flex border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div
                class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                CoE
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                -
            </div>
        </div>

        <!-- updated -->
        <div class="flex">
            <div
                class="flex w-16 shrink-0 items-center justify-center bg-[#2e6ea2] px-2 py-1.5 text-center text-[12px] font-bold text-white">
                Updated
            </div>
            <div class="flex flex-1 items-center bg-white px-3 py-1.5 text-[12px] text-slate-900">
                {{ initiative.updated_at }}
            </div>
        </div>
    </div>

    <!-- Main Content 3-Col -->
    <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-3">
        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Use Case Description
            </div>
            <div class="flex-1 bg-white p-4 text-[12px] text-slate-700 whitespace-pre-line">
                -
            </div>
        </div>

        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Current situation/ frictions addressed
            </div>
            <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                -
            </div>
        </div>

        <div class="flex flex-col">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Key functionalities/scope
            </div>
            <div class="flex-1 bg-white p-4 text-[12px] text-slate-700">
                -
            </div>
        </div>
    </div>

    <!-- RJPP Tagging Section -->
    <div class="border-x border-b border-[#3b82f6]">
        <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
            RJPP Tagging
        </div>
        <div class="bg-white p-2.5">
            <div class="table-wrap">
                <table class="initiative-table">
                    <thead>
                        <tr>
                            <th class="w-[45px] text-center">Code</th>
                            <th class="text-center">Strategic Pillar Title</th>
                            <th colspan="2" class="text-center">Themes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!initiative.themes || !initiative.themes.length">
                            <td class="cell-center h-8"></td>
                            <td></td>
                            <td class="cell-center"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="cell-center">-</td>
                            <td>-</td>
                            <td class="cell-center">-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Grid -->
    <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-3">
        <!-- Value Indication -->
        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Value Indication
            </div>
            <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Metrics Impacted
                    </div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
            </div>
        </div>

        <!-- Urgency -->
        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Urgency
            </div>
            <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Expected Go-Live
                        Date</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
            </div>
        </div>

        <!-- Ease of Implementation -->
        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Ease of Implementation
            </div>
            <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Detail</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 border-x border-b border-[#3b82f6] lg:grid-cols-3">
        <!-- Resource Requirement -->
        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Resource Requirement
            </div>
            <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600">
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Rationale</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
                <div class="flex flex-col">
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Detail</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
            </div>
        </div>

        <!-- Interdependency -->
        <div class="flex flex-col border-b border-[#3b82f6] lg:border-b-0 lg:border-r lg:border-r-[#3b82f6]">
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
                    <div class="bg-[#2e6ea2] px-2 py-1 font-bold text-white border-b border-[#3b82f6]">Other BUs Implement</div>
                    <p class="px-2 py-1.5 flex-1">-</p>
                </div>
            </div>
        </div>

        <!-- Sign By -->
        <div class="flex flex-col">
            <div class="bg-[#1e4f8f] px-3 py-1.5 text-[12px] font-bold text-white">
                Sign By
            </div>
            <div class="grid grid-cols-2 divide-x divide-[#3b82f6] bg-white text-[11px] text-slate-600 h-full">
                <div class="flex items-center justify-center p-2">
                    <p class="">-</p>
                </div>
                <div class="flex items-center justify-center p-2">
                    <p class="">-</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.score-panel {
    display: flex;
    border: 1px solid #1e4f8f;
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

.table-wrap {
    overflow-x: auto;
    border: 1px solid #cbd5e1;
}

.initiative-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.initiative-table th,
.initiative-table td {
    border: 1px solid #cbd5e1;
    padding: 8px 10px;
    vertical-align: middle;
}

.initiative-table th {
    background: #eff6ff;
    color: #1e3a8a;
    font-size: 11px;
    font-weight: 700;
    text-align: left;
}

.cell-center {
    text-align: center;
    white-space: nowrap;
}

.empty-row {
    text-align: center;
    color: #94a3b8;
    font-style: italic;
}

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

    .score-panel {
        border-color: #1e4f8f !important;
    }
}
</style>
