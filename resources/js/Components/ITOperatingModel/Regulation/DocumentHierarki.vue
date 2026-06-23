<template>
    <!-- Empty state -->
    <tr v-if="visibleDocRows.length === 0">
        <td colspan="10" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
            Belum ada data Regulasi.
        </td>
    </tr>
    <tr
        v-for="(row, index) in visibleDocRows"
        :key="'doc-' + row.id"
        class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 animate-fade-in"
    >
        <!-- No -->
        <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-white/10 w-10">
            {{ index + 1 }}
        </td>
        <!-- Judul -->
        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[300px] break-words" :style="{ paddingLeft: (row.depth * 24 + 12) + 'px' }">
            <div class="flex items-center gap-1.5">
                <!-- Toggle Button -->
                <button 
                    v-if="row.hasChildren" 
                    @click.stop="toggleDocExpand(row.id)" 
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0"
                >
                    <svg v-if="row.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
                <span v-else-if="row.depth > 0" class="text-slate-300 dark:text-white/20 mr-1 font-mono shrink-0">├─</span>
                
                <span :class="[row.depth === 0 ? 'font-bold text-slate-900 dark:text-white' : 'font-medium text-slate-700 dark:text-slate-300']">
                    {{ row.judul }}
                </span>
            </div>
        </td>
        <!-- Nomor -->
        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[120px] break-words">
            {{ row.nomor || '-' }}
        </td>
        <!-- Tipe -->
        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-28">
            <span
                :class="[
                    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                    row.tipe === 'Policy'
                        ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400'
                        : row.tipe === 'Procedure'
                            ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                            : row.tipe === 'Standart'
                                ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                : row.tipe === 'Surat Keputusan'
                                    ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                    : row.tipe === 'Surat Perintah'
                                        ? 'bg-sky-50 border-sky-200 text-sky-700 dark:bg-sky-500/10 dark:border-sky-500/20 dark:text-sky-400'
                                        : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                ]"
            >
                {{ row.tipe }} - {{ row.stk || '-' }}
            </span>
        </td>
        <!-- Pemilik Dokumen -->
        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[200px] break-words">
            {{ row.owner }}
        </td>
        <!-- Akses Role / Pemilik Dokumen (Internal) -->
        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[140px] break-words">
            <div class="flex flex-col gap-0.5">
                <span v-if="row.master" class="font-semibold text-slate-900 dark:text-white">
                    {{ row.master.jabatan || row.master.name }}
                </span>
                <span v-if="row.organization" class="text-[10px] text-slate-500 dark:text-slate-400">
                    {{ row.organization.jabatan || row.organization.name }}
                </span>
                <span v-if="!row.master && !row.organization">-</span>
            </div>
        </td>

        <!-- Status -->
        <td class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10 w-24">
            <span
                v-if="row.status"
                :class="[
                    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold border uppercase tracking-wider',
                    row.status.toLowerCase() === 'aktif' || row.status.toLowerCase() === 'active' || row.status.toLowerCase() === 'berlaku'
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                        : row.status.toLowerCase() === 'draft'
                            ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                            : row.status.toLowerCase() === 'dicabut'
                                ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                ]"
            >
                {{ row.status }}
            </span>
            <span v-else class="text-slate-400">-</span>
        </td>

        <!-- Revisi -->
        <td class="px-3 py-3 text-center font-mono font-semibold text-slate-800 dark:text-slate-200 border-r border-b border-slate-200 dark:border-white/10 w-16">
            {{ row.revisi }}
        </td>
        <!-- Berlaku -->
        <td class="px-3 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs border-r border-b border-slate-200 dark:border-white/10 w-24">
            {{ formatDate(row.berlaku) }}
        </td>
        <!-- Aksi -->
        <td class="px-3 py-3 border-b border-slate-200 dark:border-white/10 w-24">
            <div class="flex flex-col items-center justify-center gap-1">
                <button
                    @click="$emit('detail', row)"
                    class="w-14 inline-flex items-center justify-center rounded-full bg-[#821f44] px-2 py-0.5 text-[10px] font-bold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95"
                    title="Lihat Detail Kebijakan"
                >
                    Detail
                </button>
                <button
                    @click="$emit('edit', row)"
                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                    title="Edit Regulasi"
                >
                    Edit
                </button>
                <button
                    @click="$emit('delete', row)"
                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                    title="Delete Regulasi"
                >
                    Delete
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    formatDate: {
        type: Function,
        required: true,
    },
});

defineEmits(['detail', 'delete', 'edit']);

// Tree hierarchy computing
// If a child's parent is absent from the filtered list (orphaned), treat it as a root.
const documentTree = computed(() => {
    const map = {};
    const roots = [];

    props.regulations.forEach(reg => {
        map[reg.id] = { ...reg, children: [] };
    });

    props.regulations.forEach(reg => {
        const mapped = map[reg.id];
        if (reg.parent_id && map[reg.parent_id]) {
            // Parent exists in the current filtered list → attach as child
            map[reg.parent_id].children.push(mapped);
        } else {
            // No parent in the filtered list → promote to root
            roots.push(mapped);
        }
    });

    return roots;
});

const expandedDocIds = ref(new Set());

const toggleDocExpand = (id) => {
    if (expandedDocIds.value.has(id)) {
        expandedDocIds.value.delete(id);
    } else {
        expandedDocIds.value.add(id);
    }
    expandedDocIds.value = new Set(expandedDocIds.value);
};

const visibleDocRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        const hasChildren = node.children && node.children.length > 0;
        const isExpanded = expandedDocIds.value.has(node.id);
        
        rows.push({
            ...node,
            depth,
            hasChildren,
            isExpanded
        });
        
        if (hasChildren && isExpanded) {
            node.children.forEach(child => {
                traverse(child, depth + 1);
            });
        }
    };
    
    documentTree.value.forEach(root => {
        traverse(root, 0);
    });
    
    return rows;
});

const initializeExpandedDocs = () => {
    const ids = new Set();
    props.regulations.forEach(reg => {
        const isParent = props.regulations.some(r => r.parent_id === reg.id);
        if (isParent) {
            ids.add(reg.id);
        }
    });
    expandedDocIds.value = ids;
};

onMounted(() => {
    initializeExpandedDocs();
});

// Re-initialize expanded state whenever filtered regulations change
watch(
    () => props.regulations,
    () => {
        initializeExpandedDocs();
    },
    { deep: false }
);
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
