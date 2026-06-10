<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const showCode = ref(false);
const showOrganization = ref(false);
const selectedCoe = ref('');

const availableCoeOptions = computed(() => {
    const names = [...new Set(props.projects.map(p => normalizeCoeName(p.coe_name)))];
    return names.sort((a, b) => {
        const indexA = coeOrder.indexOf(a);
        const indexB = coeOrder.indexOf(b);

        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;

        if (a === 'CoE Not Identified') return 1;
        if (b === 'CoE Not Identified') return -1;
        return a.localeCompare(b);
    });
});

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
    // Maintained for backward compatibility
    showRoadmap: {
        type: Boolean,
        default: true,
    },
    showMaster: {
        type: Boolean,
        default: true,
    },
    showAppendix: {
        type: Boolean,
        default: true,
    },
    showCompendium: {
        type: Boolean,
        default: true,
    },
});

const coeOrder = [
    "AI/Analytics",
    "Cloud & Advanced Computing",
    "IoTs",
    "RPA",
    "Robotics",
    "CoE Not Identified",
];

const getProjectNumber = (project) => {
    if (project.code === undefined || project.code === null) return '?';
    const codeStr = String(project.code).trim();
    if (!codeStr) return '?';
    const parts = codeStr.split('-');
    const lastPart = parts[parts.length - 1];
    const num = parseInt(lastPart, 10);
    return isNaN(num) ? lastPart : num;
};

const getInitiativeTooltip = (project) => {
    const code = String(project.code || '').trim();
    const name = String(project.name || '').trim();
    const status = String(project.status_name || '').trim();
    return [code, name, status].filter(Boolean).join(' - ');
};

const navigateToProject = (project) => {
    try {
        if (typeof route === 'function') {
            router.visit(route('digital-initiatives.show', { digital_initiative: project.id, tab: 'detail' }));
        } else {
            router.visit(`/digital-initiatives/${project.id}?tab=detail`);
        }
    } catch (e) {
        router.visit(`/digital-initiatives/${project.id}?tab=detail`);
    }
};

const normalizeCoeName = (rawName) => {
    let name = String(rawName ?? '').trim();
    if (!name || name === '-' || name.toUpperCase() === 'NO COE') return 'CoE Not Identified';

    const upper = name.toUpperCase();
    if (upper.includes('IOT')) return 'IoTs';
    if (upper.includes('CLOUD') || upper.includes('COMPUTING') || name === 'Advance Cloud' || name === 'Cloud & Advanced Computing') return 'Cloud & Advanced Computing';
    if (upper === 'RPA') return 'RPA';
    if (upper.includes('ROBOT') || name === 'Robotics') return 'Robotics';
    if (upper.includes('ANALYTICS') || name === 'AI / Adv. Analytics' || name === 'AI/Analytics') return 'AI/Analytics';

    return name;
};

const getCoeColorClass = (coeName) => {
    const name = normalizeCoeName(coeName);

    if (name === 'IoTs') return 'coe-color-blue';
    if (name === 'Cloud & Advanced Computing') return 'coe-color-emerald';
    if (name === 'RPA') return 'coe-color-amber';
    if (name === 'Robotics') return 'coe-color-purple';
    if (name === 'AI/Analytics') return 'coe-color-rose';
    if (name === 'CoE Not Identified') return 'coe-color-none';

    return 'coe-color-none';
};

const getStatusColorClass = (project) => {
    const s = String(project.status_name || '').trim().toLowerCase();
    if (s.includes('done') || s.includes('complete') || s.includes('approve') || s === 'approved') return 'status-color-done';
    if (s.includes('progress') || s.includes('on progress') || s.includes('propose')) return 'status-color-onprogress';
    if (s.includes('review') || s.includes('on review')) return 'status-color-onreview';
    if (s.includes('draft') || s.includes('df')) return 'status-color-df';
    if (s.includes('baseline')) return 'status-color-itsbp';
    return '';
};

const getDocStatusClass = (score) => {
    const s = String(score ?? '').trim();
    if (s === '100%') return 'doc-status-ok';
    if (s === 'X') return 'doc-status-na';
    return 'doc-status-err';
};

const groupedData = computed(() => {
    let allCoEs = [...new Set(props.projects.map(p => normalizeCoeName(p.coe_name)))].sort((a, b) => {
        const indexA = coeOrder.indexOf(a);
        const indexB = coeOrder.indexOf(b);

        if (indexA !== -1 && indexB !== -1) return indexA - indexB;
        if (indexA !== -1) return -1;
        if (indexB !== -1) return 1;

        if (a === 'CoE Not Identified') return 1;
        if (b === 'CoE Not Identified') return -1;
        return a.localeCompare(b);
    });

    if (selectedCoe.value) {
        allCoEs = allCoEs.filter(coe => coe === selectedCoe.value);
    }

    return allCoEs.map(coeName => {
        const projectsInCoe = props.projects.filter(p => normalizeCoeName(p.coe_name) === coeName);

        const holding = projectsInCoe.filter(p => Number(p.groub_id) !== 2);
        const subHolding = projectsInCoe.filter(p => Number(p.groub_id) === 2);

        return {
            category: coeName,
            total: projectsInCoe.length,
            holding,
            subHolding,
        };
    });
});

</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Title box -->
        <div class="px-4 py-3 border-b border-[#c7d2de] bg-slate-50/30 dark:bg-white/5 flex items-center justify-between">
            <h1 class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">
                Review Digital Initiative Document Summary
            </h1>
            <div class="flex items-center gap-2">
                <select
                    v-model="selectedCoe"
                    class="rounded border border-slate-300 bg-white px-2 py-0.5 text-[8px] font-bold uppercase tracking-wider text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 focus:outline-none focus:ring-0 cursor-pointer"
                >
                    <option value="">All CoE</option>
                    <option v-for="coe in availableCoeOptions" :key="coe" :value="coe">
                        {{ coe }}
                    </option>
                </select>
                <button
                    type="button"
                    class="rounded border border-slate-300 bg-white px-2 py-0.5 text-[8px] font-bold uppercase tracking-wider text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                    @click="showCode = !showCode"
                >
                    {{ showCode ? 'Hide Code' : 'Show Code' }}
                </button>
                <button
                    type="button"
                    class="rounded border border-slate-300 bg-white px-2 py-0.5 text-[8px] font-bold uppercase tracking-wider text-slate-600 transition-all hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                    @click="showOrganization = !showOrganization"
                >
                    {{ showOrganization ? 'Hide Organization' : 'Show Organization' }}
                </button>
            </div>
        </div>

        <!-- Legend bar -->
        <div class="px-4 py-2 border-b border-[#c7d2de] bg-slate-50/50 dark:bg-white/5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[10px]">
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-slate-500 uppercase tracking-wider">Document Badges:</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="doc-badge text-[7px] font-bold">R</span>
                    <span class="text-slate-600 dark:text-slate-400">Roadmap</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="doc-badge text-[7px] font-bold">M</span>
                    <span class="text-slate-600 dark:text-slate-400">Master</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="doc-badge text-[7px] font-bold">A</span>
                    <span class="text-slate-600 dark:text-slate-400">Appendix</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="doc-badge text-[7px] font-bold">C</span>
                    <span class="text-slate-600 dark:text-slate-400">Compendium</span>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px]">
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-[#22c55e] border border-[#16a34a] dark:bg-[#22c55e] dark:border-[#16a34a]"></span>
                    <span class="text-slate-600 dark:text-slate-400">Complete / Available (100%)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-[#ef4444] border border-[#dc2626] dark:bg-[#ef4444] dark:border-[#dc2626]"></span>
                    <span class="text-slate-600 dark:text-slate-400">Incomplete (<100%)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-[#f1f5f9] border border-[#cbd5e1] dark:bg-white/5 dark:border-white/15"></span>
                    <span class="text-slate-600 dark:text-slate-400">Not Available (N/A / X)</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="itb-table min-w-full border-collapse">
                <thead>
                    <tr>
                        <th rowspan="2" class="top-head border-b border-r border-[#c7d2de] align-middle" style="width: 20%;">
                            CoE
                        </th>
                        <th colspan="2" class="top-head border-b border-[#c7d2de]" style="width: 80%;">
                            Digital Initiatives
                        </th>
                    </tr>
                    <tr>
                        <th class="top-head border-b border-r border-[#c7d2de] text-center" style="width: 40%; background-color: #0d5ea1;">
                            Holding
                        </th>
                        <th class="top-head border-b border-r border-[#c7d2de] text-center" style="width: 40%; background-color: #0d5ea1;">
                            Sub Holding
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(coe, index) in groupedData" :key="coe.category" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <!-- CoE Name -->
                        <td class="primary-cell border-r border-[#c7d2de]" :class="getCoeColorClass(coe.category)">
                            <div class="primary-cell__content">
                                <div class="primary-label-wrapper">
                                    <span class="text-[11px] font-bold text-slate-900">{{ coe.category }}</span>
                                    <span class="count-capsule">{{ coe.total }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Holding Initiatives Grid -->
                        <td class="initiatives-cell border-r border-[#c7d2de] p-3">
                            <div class="initiatives-grid">
                                <div v-for="p in coe.holding" :key="p.id"
                                    class="initiative-box initiative-box--clickable group"
                                    :class="[getCoeColorClass(coe.category), { 'initiative-box--no-code': !showCode }]"
                                    :title="getInitiativeTooltip(p)"
                                    @click="navigateToProject(p)">
                                    <span v-if="showCode" class="initiative-box__code" :class="getStatusColorClass(p)">
                                        {{ getProjectNumber(p) }}
                                    </span>
                                    <div class="initiative-box__name py-1 flex flex-col justify-between">
                                        <span class="initiative-box__label-text font-bold text-[9px] text-slate-800" :title="p.name">
                                            {{ p.name }}
                                        </span>
                                        <span v-if="showOrganization" class="text-[7.5px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mt-0.5" :title="p.organization_name">
                                            {{ p.organization_name }}
                                        </span>
                                        <div class="flex gap-0.5 mt-1">
                                            <span class="doc-badge" :class="getDocStatusClass(p.roadmap_score)">R</span>
                                            <span class="doc-badge" :class="getDocStatusClass(p.master_score)">M</span>
                                            <span class="doc-badge" :class="getDocStatusClass(p.appendix_score)">A</span>
                                            <span class="doc-badge" :class="getDocStatusClass(p.compendium_score)">C</span>
                                        </div>
                                    </div>
                                </div>
                                <span v-if="!coe.holding.length" class="text-slate-400 text-center block text-[10px] w-full py-1">-</span>
                            </div>
                        </td>

                        <!-- Sub Holding Initiatives Grid -->
                        <td class="initiatives-cell border-r border-[#c7d2de] p-3">
                            <div class="initiatives-grid">
                                <div v-for="p in coe.subHolding" :key="p.id"
                                    class="initiative-box initiative-box--clickable group"
                                    :class="[getCoeColorClass(coe.category), { 'initiative-box--no-code': !showCode }]"
                                    :title="getInitiativeTooltip(p)"
                                    @click="navigateToProject(p)">
                                    <span v-if="showCode" class="initiative-box__code" :class="getStatusColorClass(p)">
                                        {{ getProjectNumber(p) }}
                                    </span>
                                    <div class="initiative-box__name py-1 flex flex-col justify-between">
                                        <span class="initiative-box__label-text font-bold text-[9px] text-slate-800" :title="p.name">
                                            {{ p.name }}
                                        </span>
                                        <span v-if="showOrganization" class="text-[7.5px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mt-0.5" :title="p.organization_name">
                                            {{ p.organization_name }}
                                        </span>
                                        <div class="flex gap-0.5 mt-1">
                                            <span class="doc-badge" :class="getDocStatusClass(p.roadmap_score)">R</span>
                                            <span class="doc-badge" :class="getDocStatusClass(p.master_score)">M</span>
                                            <span class="doc-badge" :class="getDocStatusClass(p.appendix_score)">A</span>
                                            <span class="doc-badge" :class="getDocStatusClass(p.compendium_score)">C</span>
                                        </div>
                                    </div>
                                </div>
                                <span v-if="!coe.subHolding.length" class="text-slate-400 text-center block text-[10px] w-full py-1">-</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="groupedData.length === 0">
                        <td colspan="3" class="px-6 py-8 text-center text-[10px] font-bold uppercase tracking-widest text-slate-500">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
.itb-table {
    background: #ffffff;
    width: 100%;
    border-collapse: collapse;
}

.itb-table th,
.itb-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.top-head {
    padding: 8px 10px;
    background: #0f6fb7;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    text-align: center;
    letter-spacing: 0.05em;
}

.primary-cell {
    vertical-align: middle !important;
    min-width: 120px;
    transition: all 0.2s;
}

.primary-cell__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    text-align: center;
    font-weight: 700;
    color: #1e293b;
}

.primary-label-wrapper {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}

.count-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.1);
    font-size: 9px;
    font-weight: 800;
    color: inherit;
    flex-shrink: 0;
}

.initiatives-cell {
    padding: 6px;
    background: #f8fafc;
}

.initiative-box {
    position: relative;
    display: grid;
    grid-template-columns: 24px minmax(0, 1fr);
    min-height: 20px;
    width: 100%;
    align-items: stretch;
    border: 1px solid #374151;
    background: #ffffff;
    font-size: 9px;
    font-weight: 500;
    line-height: 1.1;
    color: #1f2937;
    border-radius: 0px;
    overflow: hidden;
}

.initiative-box--no-code {
    grid-template-columns: 1fr !important;
}

.initiative-box--clickable {
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.initiative-box--clickable:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 10;
}

.initiative-box__code {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1px 2px;
    font-weight: 700;
    letter-spacing: 0.01em;
    white-space: nowrap;
    min-width: 24px;
    width: 24px;
    flex-shrink: 0;
}

.initiative-box__name {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    max-width: none;
    padding: 1px 4px;
    word-break: break-word;
}

.initiative-box__label-text {
    line-height: 1.1;
}

/* Status colors */
.status-color-df {
    background-color: #0d9488 !important;
    color: #ffffff !important;
    border-color: #0f766e !important;
}

.status-color-done {
    background-color: #65a30d !important;
    color: #ffffff !important;
    border-color: #4d7c0f !important;
}

.status-color-dt2026 {
    background-color: #ea580c !important;
    color: #ffffff !important;
    border-color: #c2410c !important;
}

.status-color-itsbp {
    background-color: #06b6d4 !important;
    color: #ffffff !important;
    border-color: #0891b2 !important;
}

.status-color-onreview {
    background-color: #ca8a04 !important;
    color: #ffffff !important;
    border-color: #a16207 !important;
}

.status-color-onprogress {
    background-color: #2563eb !important;
    color: #ffffff !important;
    border-color: #1d4ed8 !important;
}

.status-color-sh {
    background-color: #ef4444 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

/* COE Color Classes */
.coe-color-blue { background-color: #dbeafe; border-color: #1d4ed8 !important; color: #1e3a8a; }
.coe-color-emerald { background-color: #d1fae5; border-color: #047857 !important; color: #065f46; }
.coe-color-amber { background-color: #fef3c7; border-color: #b45309 !important; color: #92400e; }
.coe-color-purple { background-color: #ede9fe; border-color: #6d28d9 !important; color: #5b21b6; }
.coe-color-rose { background-color: #ffe4e6; border-color: #be123c !important; color: #9f1239; }
.coe-color-indigo { background-color: #e0e7ff; border-color: #4338ca !important; color: #3730a3; }
.coe-color-none { background-color: #f8fafc; border-color: #475569 !important; color: #334155; }

.coe-color-blue .initiative-box__code { background-color: rgba(29, 78, 216, 0.1); }
.coe-color-emerald .initiative-box__code { background-color: rgba(4, 120, 87, 0.1); }
.coe-color-amber .initiative-box__code { background-color: rgba(180, 83, 9, 0.1); }
.coe-color-purple .initiative-box__code { background-color: rgba(109, 40, 217, 0.1); }
.coe-color-rose .initiative-box__code { background-color: rgba(190, 18, 60, 0.1); }
.coe-color-indigo .initiative-box__code { background-color: rgba(67, 56, 202, 0.1); }

/* Document checklist mini badge styles */
.doc-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 12px;
    height: 12px;
    font-size: 7.5px;
    font-weight: 900;
    border-radius: 2px;
    border: 1px solid transparent;
    flex-shrink: 0;
}

.doc-status-ok {
    background-color: #22c55e;
    border-color: #16a34a;
    color: #ffffff;
}

.doc-status-err {
    background-color: #ef4444;
    border-color: #dc2626;
    color: #ffffff;
}

.doc-status-na {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
    color: #64748b;
}

/* Initiatives layout grid */
.initiatives-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    grid-auto-rows: 1fr;
    gap: 6px;
    align-items: stretch;
}

/* Dark mode overrides */
:deep(.dark) .coe-color-blue { background-color: #dbeafe; color: #1e3a8a; }
:deep(.dark) .coe-color-emerald { background-color: #d1fae5; color: #065f46; }
:deep(.dark) .coe-color-amber { background-color: #fef3c7; color: #92400e; }
:deep(.dark) .coe-color-purple { background-color: #ede9fe; color: #5b21b6; }
:deep(.dark) .coe-color-rose { background-color: #ffe4e6; color: #9f1239; }
:deep(.dark) .coe-color-indigo { background-color: #e0e7ff; color: #3730a3; }
:deep(.dark) .coe-color-none { background-color: #f8fafc; color: #334155; }

:deep(.dark) .doc-status-ok {
    background-color: #22c55e;
    border-color: #16a34a;
    color: #ffffff;
}

:deep(.dark) .doc-status-err {
    background-color: #ef4444;
    border-color: #dc2626;
    color: #ffffff;
}

:deep(.dark) .doc-status-na {
    background-color: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: #94a3b8;
}
</style>
