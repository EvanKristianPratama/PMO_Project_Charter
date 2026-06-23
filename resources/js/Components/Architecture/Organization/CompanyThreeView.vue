<template>
    <!-- ROOT: render semua root companies (parent_id null) -->
    <template v-if="isRoot">
        <div v-if="!companies || companies.length === 0"
            class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
            Data company tidak ditemukan.
        </div>

        <div v-else class="px-4 py-6 overflow-x-auto">
            <div class="flex flex-row justify-center items-start gap-8 flex-wrap min-w-max">
                <CompanyThreeView
                    v-for="rootCompany in rootCompanies"
                    :key="rootCompany.id"
                    :node="rootCompany"
                    :all-companies="companies"
                    :is-root="false"
                    :depth="0"
                />
            </div>
        </div>
    </template>

    <!-- NODE: render satu kotak company + children-nya -->
    <div v-else class="relative flex flex-col items-center min-w-0 shrink-0">
        <!-- Sibling connectors -->
        <div v-if="depth >= 1 && (!isFirstChild || !isLastChild)"
            class="absolute top-0 h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-3' : '',
                isLastChild ? '-left-3 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-3 -right-3' : '',
            ]" aria-hidden="true" />

        <!-- Garis vertikal dari atas ke kotak (untuk depth >= 1) -->
        <div
            v-if="depth >= 1"
            class="w-px bg-slate-300 dark:bg-white/20"
            style="height: 16px;"
            aria-hidden="true"
        />

        <!-- Kotak node -->
        <div
            class="relative flex flex-col items-center justify-center rounded border px-3 py-2 text-center leading-tight shadow-sm transition duration-200 cursor-default"
            :class="nodeBoxClass"
            :title="nodeTitle"
        >
            <!-- Ikon company -->
            <span class="flex h-5 w-5 items-center justify-center rounded mb-1" :class="iconBgClass">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </span>

            <!-- Nama company -->
            <span class="block max-w-full break-words whitespace-normal text-[9px] font-bold">
                {{ node.name }}
            </span>

            <!-- Singkatan -->
            <span v-if="node.singkatan" class="block max-w-full break-words whitespace-normal text-[8px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                ({{ node.singkatan }})
            </span>

            <!-- Organization -->
            <span v-if="node.organization" class="block max-w-full break-words whitespace-normal text-[8px] font-normal mt-0.5 opacity-70">
                {{ node.organization }}
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
                <div class="relative flex flex-row justify-center items-start gap-6 flex-wrap">

                    <CompanyThreeView
                        v-for="(child, idx) in children"
                        :key="child.id"
                        :node="child"
                        :all-companies="allCompanies"
                        :is-root="false"
                        :depth="depth + 1"
                        :is-first-child="idx === 0"
                        :is-last-child="idx === children.length - 1"
                    />
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

defineOptions({ name: 'CompanyThreeView' });

const props = defineProps({
    /** Data semua companies (array of { id, parent_id, name, organization }) - hanya di root */
    companies: {
        type: Array,
        default: () => [],
    },
    /** Node company saat ini (saat bukan root) */
    node: {
        type: Object,
        default: null,
    },
    /** Semua companies (untuk resolve children) */
    allCompanies: {
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
});

// Expand by default kecuali depth >= 1
const isExpanded = ref(props.depth < 1);

// =====================
// Root-level computed
// =====================

/** Root companies = yang tidak punya parent_id */
const rootCompanies = computed(() => {
    return (props.companies || []).filter(c => !c.parent_id);
});

// =====================
// Node-level computed
// =====================

/** Children langsung dari node ini */
const children = computed(() => {
    if (!props.node) return [];
    const allList = props.allCompanies.length ? props.allCompanies : props.companies;
    return allList.filter(c => Number(c.parent_id) === Number(props.node.id));
});

const hasChildren = computed(() => children.value.length > 0);

const nodeTitle = computed(() => {
    const parts = [props.node?.name];
    if (props.node?.organization) parts.push(props.node.organization);
    if (hasChildren.value) {
        parts.push(`(Klik ± untuk ${isExpanded.value ? 'sembunyikan' : 'tampilkan'} anak)`);
    }
    return parts.filter(Boolean).join(' · ');
});

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

/** Style box — root company lebih menonjol, child company lebih simpel */
const nodeBoxClass = computed(() => {
    if (props.depth === 0) {
        return 'min-w-[100px] max-w-[140px] bg-white text-slate-900 border-blue-300 dark:bg-[#1a1a1a] dark:text-slate-100 dark:border-blue-500/40 ring-1 ring-blue-200 dark:ring-blue-500/20';
    }
    return 'min-w-[90px] max-w-[130px] bg-white text-slate-900 border-slate-300 dark:bg-[#1a1a1a] dark:text-slate-100 dark:border-white/10';
});

/** Icon background: biru untuk root, slate untuk child */
const iconBgClass = computed(() => {
    if (props.depth === 0) {
        return 'bg-blue-500 text-white';
    }
    return 'bg-slate-200 text-slate-600 dark:bg-white/10 dark:text-slate-300';
});
</script>
