<template>
    <ModulLayout>
        <div class="space-y-6 pb-20">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <Link :href="route('policy.specific.mapping', { regulation_id: selectedRegulationId })" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                        </Link>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            Analisis Peran RACI (Accountable)
                        </h2>
                    </div>
                </div>

                <div class="w-full md:w-auto flex items-center gap-3">
                    <select 
                        v-model="regulationId" 
                        class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-[#152c5b] focus:border-[#152c5b] block w-full p-2.5 dark:bg-black/20 dark:border-white/10 dark:placeholder-slate-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 shadow-sm transition"
                        @change="changeRegulation"
                    >
                        <option v-for="reg in regulations" :key="reg.id" :value="reg.id">
                            {{ reg.judul }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isFetchingRolesCache" class="flex flex-col items-center justify-center p-12 bg-white dark:bg-[#171717] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm">
                <div class="w-10 h-10 border-4 border-slate-200 border-t-[#152c5b] dark:border-slate-700 dark:border-t-blue-500 rounded-full animate-spin mb-4"></div>
                <h3 class="text-slate-800 dark:text-white font-bold">Menganalisis Data RACI...</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Mengambil informasi dari matriks COBIT 2019</p>
            </div>

            <!-- Dashboard Content -->
            <div v-else-if="roleStatistics.length > 0" class="space-y-6">


                <!-- RACI Matrix Table -->
                <div class="bg-white dark:bg-[#171717] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <h3 class="font-bold text-slate-800 dark:text-white">Matriks Praktik vs Peran (Accountable)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs text-left">
                            <thead class="bg-[#d2e2d8] dark:bg-zinc-800 text-slate-800 dark:text-zinc-200">
                                <tr>
                                    <th class="p-2 border border-slate-200 text-left min-w-[260px] align-middle">
                                        MANAGEMENT PRACTICE OF GOVERNANCE AND MANAGEMENT OBJECTIVES
                                    </th>
                                    <th 
                                        v-for="role in raciMatrix.roles" 
                                        :key="role.id" 
                                        class="p-0 border border-slate-200 w-[44px] text-center align-middle"
                                    >
                                        <div class="vertical-text text-[10px] font-bold py-1 select-none text-slate-800 dark:text-zinc-300 uppercase">
                                            {{ role.name }}
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="prac in raciMatrix.practices" :key="prac.id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850 transition-colors">
                                    <td class="p-2 border border-slate-200 font-bold text-slate-800 dark:text-zinc-300 leading-normal truncate max-w-[400px]" :title="prac.id + ' ' + (prac.title || prac.name || '')">
                                        {{ prac.id }} {{ prac.title || prac.name || '' }}
                                    </td>
                                    <td 
                                        v-for="role in raciMatrix.roles" 
                                        :key="role.id" 
                                        class="p-2 border border-slate-300 dark:border-white/20 text-center align-middle"
                                    >
                                        <span v-if="raciMatrix.assignmentMap[prac.id] && raciMatrix.assignmentMap[prac.id][role.id] === 'A'" class="font-black text-red-600 dark:text-red-500 text-lg">
                                            A
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Empty State -->
            <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-400 mx-auto mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Data Pemetaan Tidak Ditemukan</p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Belum ada butir kebijakan yang dipetakan ke COBIT 2019 pada peraturan ini. Silakan lakukan pemetaan terlebih dahulu di halaman sebelumnya.
                </p>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import axios from 'axios';
import ModulLayout from '@/Layouts/ModulLayout.vue';

const props = defineProps({
    objectives: {
        type: Array,
        required: true
    },
    regulations: {
        type: Array,
        required: true
    },
    selectedRegulationId: {
        type: Number,
        required: false
    }
});

const regulationId = ref(props.selectedRegulationId);

const changeRegulation = () => {
    router.visit(route('policy.specific.mapping.analysis'), {
        data: { regulation_id: regulationId.value },
        preserveState: true,
        preserveScroll: true
    });
};

const rolesMatrixCache = ref({});
const isFetchingRolesCache = ref(false);

const fetchRolesAndCalculate = async () => {
    if (isFetchingRolesCache.value) return;
    isFetchingRolesCache.value = true;
    
    const uniqueCobitObjectives = new Set();
    if (props.objectives) {
        props.objectives.forEach(obj => {
            if (obj.cobit_mappings) {
                obj.cobit_mappings.forEach(map => {
                    if (map.cobit_objective) uniqueCobitObjectives.add(map.cobit_objective);
                });
            }
        });
    }
    
    const promises = Array.from(uniqueCobitObjectives).map(async (cobitObj) => {
        if (!rolesMatrixCache.value[cobitObj]) {
            try {
                const res = await axios.get(`https://cobit2019.divusi.co.id/api/cobit/roles-matrix?objective_id=${cobitObj}`);
                if (res.data && res.data.success && res.data.matrix && res.data.roles) {
                    rolesMatrixCache.value[cobitObj] = {
                        matrix: res.data.matrix,
                        roles: res.data.roles
                    };
                }
            } catch (e) {
                console.error("Failed to fetch roles for", cobitObj);
            }
        }
    });
    
    await Promise.all(promises);
    isFetchingRolesCache.value = false;
};

const parseCobitJson = (jsonStr) => {
    if (!jsonStr) return null;
    try {
        const obj = JSON.parse(jsonStr);
        if (obj && (obj.description || obj.purpose || (obj.practices && obj.practices.length > 0))) {
            return {
                description: obj.description || '',
                purpose: obj.purpose || '',
                practices: obj.practices || []
            };
        }
        return null;
    } catch(e) {
        return null;
    }
};

const rawStats = computed(() => {
    const roleData = {};
    let totalPracticesWithRole = 0;

    props.objectives.forEach(obj => {
        if (obj.cobit_mappings) {
            obj.cobit_mappings.forEach(map => {
                const cobitObjId = map.cobit_objective;
                const parsedDesc = parseCobitJson(map.description);
                if (parsedDesc && parsedDesc.practices) {
                    parsedDesc.practices.forEach(prac => {
                        const data = rolesMatrixCache.value[cobitObjId];
                        if (data && data.matrix && data.roles) {
                            const matrixItem = data.matrix.find(m => String(m.practice_id).replace(/"/g, '') === prac.id);
                            if (matrixItem && matrixItem.role_assignments) {
                                let hasAccountable = false;
                                Object.entries(matrixItem.role_assignments).forEach(([roleId, raci]) => {
                                    if (raci === 'A' || raci === 'a') {
                                        hasAccountable = true;
                                        const role = data.roles.find(r => String(r.role_id) === String(roleId));
                                        if (role && role.role_name) {
                                            const roleName = String(role.role_name).replace(/"/g, '');
                                            if (!roleData[roleName]) {
                                                roleData[roleName] = { count: 0, practices: [] };
                                            }
                                            roleData[roleName].count++;
                                            roleData[roleName].practices.push({
                                                local_obj: obj.judul,
                                                cobit_obj: cobitObjId,
                                                cobit_prac_id: prac.id,
                                                cobit_prac_title: prac.name || prac.title
                                            });
                                        }
                                    }
                                });
                                if (hasAccountable) totalPracticesWithRole++;
                            }
                        }
                    });
                }
            });
        }
    });

    return { roleData, totalPracticesWithRole };
});

const roleStatistics = computed(() => {
    const data = rawStats.value.roleData;
    const stats = Object.entries(data).map(([role_name, info]) => ({
        role_name,
        count: info.count,
        practices: info.practices
    }));
    return stats.sort((a, b) => b.count - a.count);
});

const totalMappedPractices = computed(() => rawStats.value.totalPracticesWithRole);

const raciMatrix = computed(() => {
    const rolesMap = new Map();
    const practicesMap = new Map();
    const assignmentMap = {};

    props.objectives.forEach(obj => {
        if (obj.cobit_mappings) {
            obj.cobit_mappings.forEach(map => {
                const cobitObjId = map.cobit_objective;
                const parsedDesc = parseCobitJson(map.description);
                if (parsedDesc && parsedDesc.practices) {
                    parsedDesc.practices.forEach(prac => {
                        const data = rolesMatrixCache.value[cobitObjId];
                        if (data && data.matrix && data.roles) {
                            const matrixItem = data.matrix.find(m => String(m.practice_id).replace(/"/g, '') === prac.id);
                            if (matrixItem && matrixItem.role_assignments) {
                                let hasAccountable = false;
                                
                                Object.entries(matrixItem.role_assignments).forEach(([roleId, raci]) => {
                                    if (raci === 'A' || raci === 'a') {
                                        hasAccountable = true;
                                        const role = data.roles.find(r => String(r.role_id) === String(roleId));
                                        if (role && role.role_name) {
                                            const roleName = String(role.role_name).replace(/"/g, '');
                                            if (!rolesMap.has(roleId)) {
                                                rolesMap.set(roleId, { id: roleId, name: roleName });
                                            }
                                            if (!assignmentMap[prac.id]) assignmentMap[prac.id] = {};
                                            assignmentMap[prac.id][roleId] = 'A';
                                        }
                                    }
                                });
                                
                                if (hasAccountable) {
                                    if (!practicesMap.has(prac.id)) {
                                        practicesMap.set(prac.id, {
                                            id: prac.id,
                                            title: prac.name || prac.title,
                                            obj: cobitObjId,
                                            local_objs: new Set()
                                        });
                                    }
                                    practicesMap.get(prac.id).local_objs.add(obj.objective);
                                }
                            }
                        }
                    });
                }
            });
        }
    });

    const roles = Array.from(rolesMap.values()).sort((a, b) => a.name.localeCompare(b.name));
    
    const domainOrder = { 'EDM': 1, 'APO': 2, 'BAI': 3, 'DSS': 4, 'MEA': 5 };
    const getDomainScore = (id) => {
        const domain = id.substring(0, 3);
        return domainOrder[domain] || 99;
    };
    
    const practices = Array.from(practicesMap.values()).map(p => ({
        ...p,
        local_objs: Array.from(p.local_objs)
    })).sort((a, b) => {
        const scoreA = getDomainScore(a.id);
        const scoreB = getDomainScore(b.id);
        if (scoreA !== scoreB) return scoreA - scoreB;
        return a.id.localeCompare(b.id, undefined, {numeric: true});
    });

    return { roles, practices, assignmentMap };
});



onMounted(() => {
    fetchRolesAndCalculate();
});

watch(() => props.objectives, () => {
    fetchRolesAndCalculate();
}, { deep: true });

</script>

<style scoped>
.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    white-space: nowrap;
    min-height: 150px;
    display: inline-block;
}
</style>
