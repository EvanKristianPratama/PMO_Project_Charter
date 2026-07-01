<template>
    <section class="space-y-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="flex flex-col gap-3 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Business Capability Map</h2>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <select
                        v-model="selectedGroupBusiness"
                        class="w-52 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    >
                        <option value="">All Group Business</option>
                        <option v-for="option in groupBusinessOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari capability..."
                        class="w-44 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <div class="flex shrink-0 gap-1">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20"
                            @click="$emit('switch-to-table')"
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

        <div v-if="totalVisibleCapabilities === 0" class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center dark:border-white/10 dark:bg-[#171717]">
            <svg class="mx-auto mb-3 h-10 w-10 text-slate-300 dark:text-white/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
            </svg>
            <p class="text-sm text-slate-400 dark:text-slate-500">Data business capability tidak ditemukan.</p>
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200 bg-[#eeeeee] p-2 shadow-sm dark:border-white/10 dark:bg-[#111111]">
            <div class="relative space-y-4">
                <div class="pointer-events-none absolute right-0 top-0 h-8 w-44 rounded-bl-[28px] bg-[#4b2f82] dark:bg-[#6d4db4]"></div>

                <CapabilitySection
                    title="Core Capability"
                    :groups="coreCapabilityGroups"
                    accent-class="bg-[#005483]"
                    border-class="border-[#5c8bad] dark:border-sky-700/80"
                    layout="grid"
                />

                <CapabilitySection
                    title="Enterprise Capability"
                    :groups="enterpriseCapabilityGroups"
                    accent-class="bg-[#0a78a8]"
                    border-class="border-[#5c9cc1] dark:border-sky-700/80"
                    layout="stack"
                />
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, defineComponent, h, ref } from 'vue';

const CapabilitySection = defineComponent({
    props: {
        title: { type: String, required: true },
        groups: { type: Array, default: () => [] },
        accentClass: { type: String, default: 'bg-[#005483]' },
        borderClass: { type: String, default: 'border-[#5c8bad] dark:border-sky-700/80' },
        layout: { type: String, default: 'grid' },
    },
    setup(props) {
        const makeLeaf = capability => h('li', {
            class: 'rounded border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold leading-snug text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300',
        }, capability.name);

        const makeSubGroup = subgroup => h('div', {
            class: 'space-y-1.5 rounded-md bg-white/80 p-2 dark:bg-[#252525]',
        }, [
            h('div', { class: 'text-[12px] font-extrabold leading-tight text-[#4f4f4f] dark:text-slate-100' }, subgroup.name),
            h('ul', { class: 'space-y-1' }, subgroup.capabilities.map(makeLeaf)),
        ]);

        const makeBusinessGroup = group => h('article', {
            class: [
                'flex min-h-[160px] flex-col overflow-hidden rounded-md border-2 bg-white shadow-[0_1px_0_rgba(15,23,42,0.12)] dark:bg-[#222222]',
                props.borderClass,
            ],
        }, [
            h('div', {
                class: ['flex min-h-10 items-center justify-center px-2 text-center text-[12px] font-extrabold uppercase leading-tight text-white', props.accentClass],
            }, group.name),
            h('div', { class: 'flex flex-1 flex-col gap-2 p-2' }, group.subgroups.map(makeSubGroup)),
        ]);

        return () => h('section', {
            class: 'relative rounded-xl border border-slate-200 bg-[#f7f7f7] px-3 pb-3 pt-3 shadow-sm dark:border-white/10 dark:bg-[#1b1b1b]',
        }, [
            h('h3', {
                class: 'mb-3 text-center text-[18px] font-extrabold uppercase tracking-wide text-[#1f5a82] dark:text-sky-300',
            }, props.title),
            props.groups.length > 0
                ? h('div', {
                    class: props.layout === 'grid'
                        ? 'grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-4'
                        : 'grid grid-cols-1 gap-2 lg:grid-cols-2',
                }, props.groups.map(makeBusinessGroup))
                : h('p', { class: 'py-5 text-center text-xs text-slate-400 dark:text-slate-500' }, `Belum ada data ${props.title}.`),
        ]);
    },
});

const props = defineProps({
    businessCapabilities: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['switch-to-table']);

const searchQuery = ref('');
const selectedGroupBusiness = ref('');

const normalizeText = value => String(value ?? '').trim().toLowerCase();
const cleanLabel = value => String(value ?? '').trim() || '-';

const groupBusinessOptions = computed(() => {
    return [...new Set(
        props.businessCapabilities
            .map(item => cleanLabel(item.group_business))
            .filter(option => option !== '-'),
    )].sort((left, right) => left.localeCompare(right));
});

const matchesSearch = (item) => {
    const query = normalizeText(searchQuery.value);
    if (!query) return true;

    return [
        item.group_business,
        item.group_function,
        item.subGroup_function,
        item.subSubGroup_function,
    ].some(value => normalizeText(value).includes(query));
};

const groupByCapabilityType = (type) => {
    const businessGroups = new Map();

    props.businessCapabilities
        .filter(item => normalizeText(item.group_function) === normalizeText(type))
        .filter(item => !selectedGroupBusiness.value || cleanLabel(item.group_business) === selectedGroupBusiness.value)
        .filter(matchesSearch)
        .forEach((item) => {
            const businessName = cleanLabel(item.group_business);
            const subgroupName = cleanLabel(item.subGroup_function);
            const capabilityName = cleanLabel(item.subSubGroup_function);

            if (!businessGroups.has(businessName)) {
                businessGroups.set(businessName, new Map());
            }

            const subgroups = businessGroups.get(businessName);
            if (!subgroups.has(subgroupName)) {
                subgroups.set(subgroupName, []);
            }

            subgroups.get(subgroupName).push({
                id: item.id,
                name: capabilityName,
            });
        });

    return [...businessGroups.entries()]
        .sort(([left], [right]) => left.localeCompare(right))
        .map(([businessName, subgroupMap]) => ({
            name: businessName,
            subgroups: [...subgroupMap.entries()]
                .sort(([left], [right]) => left.localeCompare(right))
                .map(([subgroupName, capabilities]) => ({
                    name: subgroupName,
                    capabilities: capabilities.sort((left, right) => left.name.localeCompare(right.name)),
                })),
        }));
};

const coreCapabilityGroups = computed(() => groupByCapabilityType('Core Capability'));
const enterpriseCapabilityGroups = computed(() => groupByCapabilityType('Enterprise Capability'));

const totalVisibleCapabilities = computed(() => {
    const countCapabilities = groups => groups.reduce((total, group) => {
        return total + group.subgroups.reduce((subTotal, subgroup) => subTotal + subgroup.capabilities.length, 0);
    }, 0);

    return countCapabilities(coreCapabilityGroups.value) + countCapabilities(enterpriseCapabilityGroups.value);
});
</script>