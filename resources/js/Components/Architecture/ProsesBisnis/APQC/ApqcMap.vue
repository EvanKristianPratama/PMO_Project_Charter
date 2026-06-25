<template>
    <section class="space-y-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="flex flex-col gap-3 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">APQC Process Map</h2>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex shrink-0 gap-1">
                        <button
                            type="button"
                            @click="$emit('switch-to-table')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20"
                        >
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18" />
                            </svg>
                            Table
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-600"
                        >
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h6" />
                            </svg>
                            Map
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="totalVisibleProcesses === 0" class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center dark:border-white/10 dark:bg-[#171717]">
            <svg class="mx-auto mb-3 h-10 w-10 text-slate-300 dark:text-white/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
            </svg>
            <p class="text-sm text-slate-400 dark:text-slate-500">Data proses APQC tidak ditemukan.</p>
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200 bg-[#eeeeee] p-2 shadow-sm dark:border-white/10 dark:bg-[#111111]">
            <div class="relative space-y-4">
                <div class="pointer-events-none absolute right-0 top-0 h-8 w-44 rounded-bl-[28px] bg-[#4b2f82] dark:bg-[#6d4db4]"></div>

                <section class="relative rounded-xl border border-slate-200 bg-[#f7f7f7] px-3 pb-3 pt-3 shadow-sm dark:border-white/10 dark:bg-[#1b1b1b]">
                    <h3 class="mb-2 text-center text-[18px] font-extrabold uppercase tracking-wide text-[#1f5a82] dark:text-sky-300">
                        Operating Processes
                    </h3>

                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
                        <article
                            v-for="process in operatingProcesses"
                            :key="'operating-' + process.id"
                            class="flex min-h-[142px] flex-col overflow-hidden rounded-md border-2 border-[#5c8bad] bg-white shadow-[0_1px_0_rgba(15,23,42,0.12)] dark:border-sky-700/80 dark:bg-[#222222]"
                        >
                            <div class="flex h-10 items-center justify-center bg-[#005483] text-base font-extrabold text-white">
                                {{ process.mapNumber }}
                            </div>
                            <div class="flex flex-1 items-center justify-center px-2.5 py-3 text-center text-[13px] font-extrabold leading-tight text-[#4f4f4f] dark:text-slate-100">
                                {{ process.displayName }}
                            </div>
                        </article>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-[#f7f7f7] px-4 py-3 shadow-sm dark:border-white/10 dark:bg-[#1b1b1b]">
                    <h3 class="mb-3 text-center text-[18px] font-extrabold uppercase tracking-wide text-[#1f5a82] dark:text-sky-300">
                        Management and Support Services
                    </h3>

                    <div class="space-y-3">
                        <article
                            v-for="process in managementProcesses"
                            :key="'management-' + process.id"
                            class="flex min-h-8 overflow-hidden rounded-md border-2 border-[#5c9cc1] bg-white dark:border-sky-700/80 dark:bg-[#222222]"
                        >
                            <div class="support-number flex w-[62px] shrink-0 items-center justify-center bg-[#0a78a8] pl-1 text-sm font-extrabold text-white">
                                {{ process.mapNumber }}
                            </div>
                            <div class="flex flex-1 items-center px-3 py-1.5 text-[14px] font-extrabold leading-tight text-[#4f4f4f] dark:text-slate-100">
                                {{ process.displayName }}
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    apqcList: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['switch-to-table']);

const searchQuery = ref('');

const SECTION = {
    OPERATING: 'operating',
    MANAGEMENT: 'management',
};

const apqcMap = computed(() => new Map(props.apqcList.map(item => [Number(item.id), item])));

const childrenMap = computed(() => {
    const map = new Map();

    props.apqcList.forEach(item => {
        const id = Number(item.id);
        if (!map.has(id)) map.set(id, []);

        if (item.parent_id) {
            const parentId = Number(item.parent_id);
            if (!map.has(parentId)) map.set(parentId, []);
            map.get(parentId).push(item);
        }
    });

    return map;
});

const normalizeText = value => (value || '').toString().trim().toLowerCase();

const getSectionKey = (grup) => {
    const normalized = normalizeText(grup);

    if (normalized.includes('operating')) return SECTION.OPERATING;
    if (normalized.includes('management') || normalized.includes('support')) return SECTION.MANAGEMENT;

    return 'other';
};

const getRoot = (item) => {
    let current = item;
    const visited = new Set();

    while (current?.parent_id && !visited.has(Number(current.id))) {
        visited.add(Number(current.id));
        const parent = apqcMap.value.get(Number(current.parent_id));
        if (!parent) break;
        current = parent;
    }

    return current;
};

const rootProcesses = computed(() => {
    const roots = new Map();

    props.apqcList.forEach(item => {
        const root = getRoot(item);
        if (root?.id) {
            roots.set(Number(root.id), root);
        }
    });

    return [...roots.values()].sort(sortByProcessOrder);
});

const getLeadingNumber = (value) => {
    const match = (value || '').toString().trim().match(/^(\d+(?:\.\d+)?)/);
    return match ? Number(match[1]) : null;
};

function sortByProcessOrder(a, b) {
    const numberA = getLeadingNumber(a.name);
    const numberB = getLeadingNumber(b.name);

    if (numberA !== null && numberB !== null && numberA !== numberB) {
        return numberA - numberB;
    }

    if (numberA !== null && numberB === null) return -1;
    if (numberA === null && numberB !== null) return 1;

    return (a.name || '').localeCompare(b.name || '', undefined, {
        numeric: true,
        sensitivity: 'base',
    });
}

const cleanProcessName = (name) => {
    return (name || '-')
        .toString()
        .replace(/^\s*\d+(?:\.\d+)?\s*[-.)]?\s*/u, '')
        .trim() || '-';
};

const matchesSearch = (item) => {
    const query = normalizeText(searchQuery.value);
    if (!query) return true;

    const ownMatch = [item.name, item.deskripsi, item.grup].some(value => normalizeText(value).includes(query));
    if (ownMatch) return true;

    const children = childrenMap.value.get(Number(item.id)) || [];
    return children.some(child => matchesSearch(child));
};

const makeProcessView = (items, startNumber) => {
    return items
        .filter(matchesSearch)
        .map((item, index) => {
            const leadingNumber = getLeadingNumber(item.name);
            const mapNumber = leadingNumber !== null ? leadingNumber.toFixed(1) : `${startNumber + index}.0`;

            return {
                ...item,
                displayName: cleanProcessName(item.name),
                mapNumber,
            };
        });
};

const operatingRoots = computed(() =>
    rootProcesses.value.filter(item => getSectionKey(item.grup) === SECTION.OPERATING)
);

const managementRoots = computed(() =>
    rootProcesses.value.filter(item => getSectionKey(item.grup) === SECTION.MANAGEMENT)
);

const operatingProcesses = computed(() => makeProcessView(operatingRoots.value, 1));

const managementProcesses = computed(() => makeProcessView(
    managementRoots.value,
    operatingRoots.value.length > 0 ? operatingRoots.value.length + 1 : 7
));

const totalVisibleProcesses = computed(() => operatingProcesses.value.length + managementProcesses.value.length);
</script>

<style scoped>
.support-number {
    clip-path: polygon(0 0, calc(100% - 13px) 0, 100% 50%, calc(100% - 13px) 100%, 0 100%);
}
</style>
