<template>
    <!-- ROOT: render semua company sebagai section terpisah -->
    <template v-if="isRoot">
        <div v-if="!companies || companies.length === 0"
            class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
            Data {{ typeLabel }} tidak ditemukan.
        </div>

        <div v-else class="space-y-4">
            <section
                v-for="company in companiesWithBods"
                :key="company.id"
                class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <!-- Company Header -->
                <div class="border-b border-slate-200 px-3 py-2 dark:border-white/10 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white text-[8px] font-bold shrink-0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                    <h2 class="text-xs font-semibold uppercase text-slate-800 dark:text-white">
                        {{ company.name }}
                    </h2>
                    <span class="ml-auto text-[9px] text-slate-400 dark:text-slate-500">
                        {{ company.bods.length }} anggota
                    </span>
                </div>

                <div class="px-4 py-4 overflow-x-auto">
                    <div v-if="company.bods.length === 0"
                        class="rounded-md border border-dashed border-slate-200 py-6 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
                        Tidak ada data {{ typeLabel }} untuk company ini.
                    </div>

                    <!--
                        Kasus cross-company parent:
                        Kelompokkan BOD company ini berdasarkan cross-company parent-nya.
                        Jika ada BOD yang parent_id-nya mengarah ke BOD company lain,
                        tampilkan ghost node parent tersebut di atas sebagai referensi.
                    -->
                    <div v-else class="space-y-6">
                        <!--
                            Group 1: BOD yang parent-nya dari company lain
                            Tampilkan ghost node sebagai referensi di atas tiap grup.
                        -->
                        <div
                            v-for="ghostGroup in getCrossCompanyGroups(company)"
                            :key="'ghost-' + ghostGroup.ghostNode.id"
                            class="flex flex-col items-center"
                        >
                            <!-- Ghost node (parent dari company lain) -->
                            <div class="flex flex-col items-center">
                                <div
                                    class="relative flex flex-col items-center justify-center rounded border-2 border-dashed px-2 py-1.5 text-center leading-tight cursor-default opacity-70"
                                    :class="ghostBoxClass"
                                    :title="`Referensi eksternal dari ${ghostGroup.ghostCompanyName}`"
                                >
                                    <!-- Badge company asal -->
                                    <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-amber-400 text-[7px] font-bold text-slate-900 px-1.5 py-0.5 leading-none shadow">
                                        {{ ghostGroup.ghostCompanyName }}
                                    </span>
                                    <!-- Jabatan -->
                                    <span class="block max-w-full break-words whitespace-normal text-[9px] font-bold mt-1">
                                        {{ ghostGroup.ghostNode.name }}
                                    </span>
                                    <!-- Pejabat -->
                                    <span v-if="ghostGroup.ghostNode.pejabat" class="block max-w-full break-words whitespace-normal text-[8px] font-normal mt-0.5 opacity-75">
                                        {{ ghostGroup.ghostNode.pejabat }}
                                    </span>
                                    <!-- Label referensi -->
                                    <span class="block text-[7px] font-semibold mt-1 text-amber-600 dark:text-amber-400 uppercase tracking-wide">
                                        ↑ Ref. Eksternal
                                    </span>
                                </div>

                                <!-- Garis vertikal turun ke anak-anak -->
                                <div class="w-px bg-slate-300 dark:bg-white/20" style="height: 16px;" aria-hidden="true" />
                            </div>

                            <!-- Children lokal yang mengarah ke ghost node ini -->
                            <div class="relative flex flex-row justify-center items-start gap-4 flex-wrap min-w-max">
                                <BodThreeView
                                    v-for="(child, idx) in ghostGroup.children"
                                    :key="child.id"
                                    :node="child"
                                    :all-bods="company.bods"
                                    :all-bods-global="bods"
                                    :companies="companies"
                                    :is-root="false"
                                    :depth="1"
                                    :is-first-child="idx === 0"
                                    :is-last-child="idx === ghostGroup.children.length - 1"
                                    :type-label="typeLabel"
                                />
                            </div>
                        </div>

                        <!--
                            Group 2: BOD murni tanpa cross-company parent
                            (parent_id null, atau parent ada di company yang sama)
                        -->
                        <div
                            v-if="getLocalRootBods(company).length > 0"
                            class="flex flex-row justify-center items-start gap-4 flex-wrap min-w-max"
                        >
                            <BodThreeView
                                v-for="rootBod in getLocalRootBods(company)"
                                :key="rootBod.id"
                                :node="rootBod"
                                :all-bods="company.bods"
                                :all-bods-global="bods"
                                :companies="companies"
                                :is-root="false"
                                :depth="0"
                                :type-label="typeLabel"
                            />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </template>

    <!-- NODE: render satu kotak jabatan BOD + children-nya -->
    <div v-else class="relative flex flex-col items-center min-w-0 shrink-0">
        <!-- Sibling connectors -->
        <div v-if="depth >= 1 && (!isFirstChild || !isLastChild)"
            class="absolute top-0 h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-2' : '',
                isLastChild ? '-left-2 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-2 -right-2' : '',
            ]" aria-hidden="true" />

        <!-- Garis vertikal dari atas ke kotak (untuk non-root depth >= 1) -->
        <div
            v-if="depth >= 1"
            class="w-px bg-slate-300 dark:bg-white/20"
            style="height: 16px;"
            aria-hidden="true"
        />

        <!-- Kotak node -->
        <div
            class="relative flex flex-col items-center justify-center rounded border px-2 py-1.5 text-center leading-tight shadow-sm transition duration-200 cursor-default"
            :class="nodeBoxClass"
            :title="nodeTitle"
        >
            <!-- Jabatan -->
            <span class="block max-w-full break-words whitespace-normal text-[9px] font-bold">
                {{ node.name }}
            </span>
            <!-- Pejabat -->
            <span v-if="node.pejabat" class="block max-w-full break-words whitespace-normal text-[8px] font-normal mt-0.5 opacity-75">
                {{ node.pejabat }}
            </span>

            <!-- Expand/collapse indicator -->
            <span
                v-if="hasChildren"
                @click.stop="toggleExpand"
                class="absolute -bottom-2 left-1/2 -translate-x-1/2 flex items-center justify-center w-4 h-4 rounded-full text-[8px] font-bold leading-none transition-colors duration-200 cursor-pointer z-10 shadow"
                :class="isExpanded
                    ? 'bg-blue-500 text-white'
                    : 'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-300'"
            >
                {{ isExpanded ? '−' : '+' }}
            </span>
        </div>

        <!-- Children -->
        <template v-if="hasChildren && isExpanded">
            <div class="relative mt-4 w-full">
                <!-- Garis vertikal turun -->
                <div
                    class="absolute left-1/2 -translate-x-1/2 w-px bg-slate-300 dark:bg-white/20"
                    style="top: -8px; height: 8px;"
                    aria-hidden="true"
                />

                <!-- Children nodes -->
                <div class="relative flex flex-row justify-center items-start gap-4 flex-wrap">

                    <BodThreeView
                        v-for="(child, idx) in children"
                        :key="child.id"
                        :node="child"
                        :all-bods="allBods"
                        :all-bods-global="allBodsGlobal"
                        :companies="companies"
                        :is-root="false"
                        :depth="depth + 1"
                        :is-first-child="idx === 0"
                        :is-last-child="idx === children.length - 1"
                        :type-label="typeLabel"
                    />
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

defineOptions({ name: 'BodThreeView' });

const props = defineProps({
    /** Data companies (array of { id, name }) */
    companies: {
        type: Array,
        default: () => [],
    },
    /** Data semua bods (array) - hanya di root */
    bods: {
        type: Array,
        default: () => [],
    },
    /** Node BOD saat ini (saat bukan root) */
    node: {
        type: Object,
        default: null,
    },
    /** Semua BOD dalam satu company (untuk resolve children lokal) */
    allBods: {
        type: Array,
        default: () => [],
    },
    /** Semua BOD lintas company (untuk resolve cross-company parent) */
    allBodsGlobal: {
        type: Array,
        default: () => [],
    },
    isRoot: {
        type: Boolean,
        default: true,
    },
    depth: {
        type: Number,
        default: 0,
    },
    isFirstChild: {
        type: Boolean,
        default: false,
    },
    isLastChild: {
        type: Boolean,
        default: false,
    },
    typeLabel: {
        type: String,
        default: 'BOD',
    },
});

// Expand by default kecuali depth >= 3
const isExpanded = ref(props.depth < 3);

// =====================
// Root-level computed
// =====================

/** Gabungkan companies dengan bods masing-masing */
const companiesWithBods = computed(() => {
    return (props.companies || [])
        .map(company => ({
            ...company,
            bods: (props.bods || []).filter(b => Number(b.company_id) === Number(company.id)),
        }))
        .filter(c => c.bods.length > 0);
});

/**
 * Dapatkan nama company berdasarkan company_id.
 */
const getCompanyName = (companyId) => {
    const company = (props.companies || []).find(c => Number(c.id) === Number(companyId));
    return company ? company.name : 'Unknown';
};

/**
 * Untuk satu company:
 * Kembalikan array grup cross-company parent.
 * Setiap grup: { ghostNode, ghostCompanyName, children[] }
 *
 * BOD "cross-company child" = BOD yang parent_id-nya mengarah ke BOD dari company lain.
 * BOD tersebut adalah root hierarchy lokal (tidak punya parent lokal),
 * tetapi punya parent di company berbeda.
 */
const getCrossCompanyGroups = (company) => {
    const allBodsGlobal = props.bods || [];
    const localBodIds = new Set(company.bods.map(b => b.id));

    // Temukan BOD lokal yang parent_id-nya ada di company lain
    const crossChildren = company.bods.filter(b => {
        if (!b.parent_id) return false;
        // Cek apakah parent_id ada di luar company ini (tidak ada di localBodIds)
        return !localBodIds.has(Number(b.parent_id));
    });

    if (crossChildren.length === 0) return [];

    // Kelompokkan berdasarkan parent_id
    const groupMap = new Map();
    for (const child of crossChildren) {
        const parentId = Number(child.parent_id);
        if (!groupMap.has(parentId)) {
            const ghostNode = allBodsGlobal.find(b => b.id === parentId);
            if (!ghostNode) continue; // parent tidak ditemukan, skip

            groupMap.set(parentId, {
                ghostNode,
                ghostCompanyName: getCompanyName(ghostNode.company_id),
                children: [],
            });
        }
        groupMap.get(parentId)?.children.push(child);
    }

    return Array.from(groupMap.values());
};

/**
 * BOD lokal yang benar-benar root (parent_id null ATAU parent_id ke company sendiri
 * tapi bukan sebagai cross-company child di atas).
 * Ini adalah BOD yang TIDAK muncul dalam getCrossCompanyGroups().
 */
const getLocalRootBods = (company) => {
    const allBodsGlobal = props.bods || [];
    const localBodIds = new Set(company.bods.map(b => b.id));

    return company.bods.filter(b => {
        if (!b.parent_id) return true; // tidak punya parent sama sekali
        // parent ada di company yang sama → bukan root
        if (localBodIds.has(Number(b.parent_id))) return false;
        // parent ada di company lain → sudah ditangani getCrossCompanyGroups, skip di sini
        const parentNode = allBodsGlobal.find(x => x.id === Number(b.parent_id));
        return !parentNode; // hanya tampilkan jika parent benar-benar tidak ditemukan
    });
};

// =====================
// Node-level computed
// =====================

/** Children langsung dari node ini (hanya dalam company yang sama / allBods) */
const children = computed(() => {
    if (!props.node) return [];
    return props.allBods.filter(b => Number(b.parent_id) === Number(props.node.id));
});

const hasChildren = computed(() => children.value.length > 0);

const nodeTitle = computed(() => {
    const parts = [props.node?.name];
    if (props.node?.pejabat) parts.push(props.node.pejabat);
    if (props.node?.sumber) parts.push(`Sumber: ${props.node.sumber}`);
    if (hasChildren.value) {
        parts.push(`(Klik ± untuk ${isExpanded.value ? 'sembunyikan' : 'tampilkan'} bawahan)`);
    }
    return parts.filter(Boolean).join(' · ');
});

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

/** Style box — node normal */
const nodeBoxClass = computed(() => {
    return 'min-w-[80px] max-w-[120px] bg-white text-slate-900 border-slate-300 dark:bg-[#1a1a1a] dark:text-slate-100 dark:border-white/10';
});

/** Style box — ghost node (cross-company parent referensi) */
const ghostBoxClass = computed(() => {
    return 'min-w-[80px] max-w-[130px] bg-amber-50 text-amber-900 border-amber-300 dark:bg-amber-900/20 dark:text-amber-200 dark:border-amber-500/40';
});
</script>
