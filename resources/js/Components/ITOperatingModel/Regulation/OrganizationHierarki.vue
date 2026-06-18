<template>
    <tr v-if="regulationsByOrgHierarchy.length === 0">
        <td colspan="10" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
            Belum ada data Regulasi.
        </td>
    </tr>

    <tr
        v-for="(reg, index) in regulationsByOrgHierarchy"
        :key="'org-flat-' + reg.id"
        class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 animate-fade-in"
    >
        <!-- No -->
        <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-white/10 w-10">
            {{ index + 1 }}
        </td>

        <!-- Judul -->
        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[300px] break-words text-slate-900 dark:text-white font-bold leading-relaxed">
            {{ reg.judul }}
        </td>

        <!-- Nomor -->
        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[120px] break-words">
            {{ reg.nomor || '-' }}
        </td>

        <!-- Tipe -->
        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-28">
            <span
                :class="[
                    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                    reg.tipe === 'Policy'
                        ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400'
                        : reg.tipe === 'Procedure'
                            ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                            : reg.tipe === 'Standart'
                                ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                : reg.tipe === 'Surat Keterangan'
                                    ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                    : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                ]"
            >
                {{ reg.tipe }} - {{ reg.stk || '-' }}
            </span>
        </td>

        <!-- Fungsi / Owner -->
        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[200px] break-words">
            {{ reg.owner }}
        </td>

        <!-- Organisasi — show org name -->
        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[140px] break-words">
            <div class="flex flex-col gap-0.5">
                <span v-if="reg.master" class="font-semibold text-slate-900 dark:text-white">
                    {{ reg.master.jabatan || reg.master.name }}
                </span>
                <span v-if="reg.organization" class="text-[10px] text-slate-500 dark:text-slate-400">
                    {{ reg.organization.jabatan || reg.organization.name }}
                </span>
                <span v-if="!reg.master && !reg.organization">-</span>
            </div>
        </td>

        <!-- Status -->
        <td class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10 w-24">
            <span
                v-if="reg.status"
                :class="[
                    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold border uppercase tracking-wider',
                    reg.status.toLowerCase() === 'aktif' || reg.status.toLowerCase() === 'active' || reg.status.toLowerCase() === 'berlaku'
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                        : reg.status.toLowerCase() === 'draft'
                            ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                            : reg.status.toLowerCase() === 'dicabut'
                                ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                ]"
            >
                {{ reg.status }}
            </span>
            <span v-else class="text-slate-400">-</span>
        </td>

        <!-- Revisi -->
        <td class="px-3 py-3 text-center font-mono font-semibold text-slate-800 dark:text-slate-200 border-r border-b border-slate-200 dark:border-white/10 w-16">
            {{ reg.revisi }}
        </td>

        <!-- Berlaku -->
        <td class="px-3 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs border-r border-b border-slate-200 dark:border-white/10 w-24">
            {{ formatDate(reg.berlaku) }}
        </td>

        <!-- Aksi -->
        <td class="px-3 py-3 border-b border-slate-200 dark:border-white/10 w-24">
            <div class="flex flex-col items-center justify-center gap-1">
                <button
                    @click="$emit('detail', reg)"
                    class="w-14 inline-flex items-center justify-center rounded-full bg-[#821f44] px-2 py-0.5 text-[10px] font-bold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95"
                    title="Lihat Detail Kebijakan"
                >
                    Detail
                </button>
                <button
                    @click="$emit('edit', reg)"
                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                    title="Edit Regulasi"
                >
                    Edit
                </button>
                <button
                    @click="$emit('delete', reg)"
                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                    title="Hapus Regulasi"
                >
                    Hapus
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        required: true,
    },
    formatDate: {
        type: Function,
        required: true,
    },
});

defineEmits(['detail', 'delete', 'edit']);

const getOrgDepth = (orgId) => {
    if (!orgId) return 0;
    let depth = 0;
    let currentId = orgId;
    let iterations = 0;
    
    const orgMap = {};
    props.organizations.forEach(org => {
        orgMap[org.id] = org.parent_id;
    });

    while (currentId && orgMap[currentId] && iterations < 20) {
        currentId = orgMap[currentId];
        depth++;
        iterations++;
    }
    return depth;
};

const getVisualOrgDepth = (depth) => {
    if (depth <= 0) return 0;
    if (depth === 1) return 1;
    return 2;
};

const regulationsByOrgHierarchy = computed(() => {
    const sortLocalRegulations = (regs) => {
        const map = {};
        const roots = [];
        
        regs.forEach(reg => {
            map[reg.id] = { ...reg, children: [] };
        });
        
        regs.forEach(reg => {
            const mapped = map[reg.id];
            if (reg.parent_id && map[reg.parent_id]) {
                map[reg.parent_id].children.push(mapped);
            } else {
                roots.push(mapped);
            }
        });
        
        const sorted = [];
        const traverseRegs = (regNode) => {
            sorted.push(regNode);
            regNode.children.forEach(child => {
                traverseRegs(child);
            });
        };
        
        roots.forEach(root => {
            traverseRegs(root);
        });
        
        return sorted;
    };

    const map = {};
    const roots = [];
    
    props.organizations.forEach(org => {
        map[org.id] = { ...org, children: [], regulations: [] };
    });
    
    props.regulations.forEach(reg => {
        if (reg.pic_id && map[reg.pic_id]) {
            map[reg.pic_id].regulations.push(reg);
        }
    });
    
    props.organizations.forEach(org => {
        const mapped = map[org.id];
        if (org.parent_id && map[org.parent_id]) {
            map[org.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });
    
    const orderedRegs = [];
    
    const traverse = (node, depth = 0) => {
        const sortedNodeRegs = sortLocalRegulations(node.regulations);
        sortedNodeRegs.forEach(reg => {
            orderedRegs.push({
                ...reg,
                org_depth: depth
            });
        });
        
        node.children.forEach(child => {
            traverse(child, depth + 1);
        });
    };
    
    roots.forEach(root => {
        traverse(root, 0);
    });
    
    const unassigned = props.regulations.filter(reg => !reg.pic_id);
    const sortedUnassigned = sortLocalRegulations(unassigned);
    sortedUnassigned.forEach(reg => {
        orderedRegs.push({
            ...reg,
            org_depth: 0
        });
    });
    
    return orderedRegs;
});
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
