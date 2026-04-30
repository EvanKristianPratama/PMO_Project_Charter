<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <section
                class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <button @click="goBack"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </button>

                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10" />

                    <label for="initiative-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih
                        Initiative</label>
                    <select id="initiative-nav" v-model="selectedInitiativeId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                        <option value="" disabled>-- Pilih Initiative --</option>
                        <option v-for="option in (initiativeOptions ?? [])" :key="`initiative-opt-${option.id}`"
                            :value="String(option.id)">
                            {{ formatInitiativeLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5">
                        <button v-for="tab in tabs" :key="tab"
                            @click="activeTab = tab" class="rounded-md px-3 py-1 text-xs font-semibold transition-all"
                            :class="activeTab === tab ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'">
                            {{ tab }}
                        </button>
                    </div>
                </div>
            </section>

            <Planning
                v-if="activeTab === 'Planning'"
                :initiative-master="initiativeMaster"
                :compendium-data="compendiumData"
                :appendix-data="appendixData"
                :roadmap-items="roadmapItems"
                :roadmap-start-year="roadmapStartYear"
                :roadmap-end-year="roadmapEndYear"
                :coe-options="coeOptions"
                :source-options="sourceOptions"
                :theme-options="themeOptions"
                :organization-options="organizationOptions"
                :computed-appendix-data="computedAppendixData"
                :format-date="formatDate"
                :get-status-class="getStatusClass"
            />

            <Implementation
                v-else-if="activeTab === 'Implementation'"
                :unified-initiative="unifiedInitiative"
                :status-implementations="statusImplementations"
                :roadmap-items="roadmapItems"
                :roadmap-start-year="roadmapStartYear"
                :roadmap-end-year="roadmapEndYear"
            />

            <Evaluation
                v-else
                :unified-initiative="unifiedInitiative"
                :status-implementations="statusImplementations"
                :summary-review-notes="summaryReviewNotes"
                :roadmap-items="roadmapItems"
                :roadmap-start-year="roadmapStartYear"
                :roadmap-end-year="roadmapEndYear"
                :computed-appendix-data="computedAppendixData"
                :months="months"
                :note-years="noteYears"
                :note-form="noteForm"
                :editing-note-id="editingNoteId"
                :roadmap-duration="roadmapDuration"
                :roadmap-years="roadmapYears"
                :roadmap-bar-style="roadmapBarStyle"
                :go-live-bar-style="goLiveBarStyle"
                :status-review-markers="statusReviewMarkers"
                @submit-note="submitNote"
                @edit-note="editNote"
                @cancel-edit-note="cancelEditNote"
                @delete-note="deleteNote"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import Evaluation from './Evaluation.vue';
import Implementation from './Implementation.vue';
import Planning from './Planning.vue';

const props = defineProps({
    initiativeMaster: { type: Object, default: () => ({}) },
    projectCharter: { type: Object, default: null },
    compendiumData: { type: Object, default: null },
    appendixData: { type: Object, default: null },
    roadmapItems: { type: Array, default: () => [] },
    roadmapStartYear: { type: Number, default: 2024 },
    roadmapEndYear: { type: Number, default: 2029 },
    statusImplementations: { type: Array, default: () => [] },
    summaryReviewNotes: { type: Array, default: () => [] },
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
});

const tabs = ['Planning', 'Implementation', 'Evaluation'];
const activeTab = ref('Planning');
const route = useRouteHelper();

const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const noteYears = computed(() => {
    const currentYear = new Date().getFullYear();
    const list = [];
    for (let i = currentYear - 2; i <= currentYear + 5; i++) {
        list.push(i);
    }
    return list;
});

const editingNoteId = ref(null);
const noteForm = useForm({
    initiative_id: props.initiativeMaster?.id,
    month: months[new Date().getMonth()],
    year: new Date().getFullYear(),
    notes: '',
});

const submitNote = (options = {}) => {
    const requestOptions = {
        onSuccess: () => {
            if (options.onSuccess) options.onSuccess();
            
            Swal.fire({
                title: 'Berhasil!',
                text: editingNoteId.value ? 'Catatan review berhasil diperbarui.' : 'Catatan review berhasil ditambahkan.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });

            if (editingNoteId.value) {
                cancelEditNote();
            } else {
                noteForm.reset('notes');
            }
        },
    };

    if (editingNoteId.value) {
        noteForm.put(route('program-evaluation.summary-review.notes.update', editingNoteId.value), requestOptions);
        return;
    }

    noteForm.post(route('program-evaluation.summary-review.notes.store'), requestOptions);
};

const editNote = (note) => {
    editingNoteId.value = note.id;
    noteForm.month = note.month;
    noteForm.year = note.year;
    noteForm.notes = note.notes;
};

const cancelEditNote = () => {
    editingNoteId.value = null;
    noteForm.reset('notes', 'month', 'year');
};

const deleteNote = (id) => {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Catatan ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('program-evaluation.summary-review.notes.destroy', id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Catatan berhasil dihapus.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
            });
        }
    });
};

const goBack = () => {
    window.history.back();
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
};

const initiativeId = computed(() => Number(props.initiativeMaster?.id ?? 0));

const selectedInitiativeId = computed({
    get: () => (initiativeId.value > 0 ? String(initiativeId.value) : ''),
    set: (value) => {
        const selectedValue = String(value ?? '').trim();
        if (!selectedValue) return;
        if (initiativeId.value > 0 && selectedValue === String(initiativeId.value)) return;
        router.visit(route('program-planning.program-definition.digital-initiatives.summary.index', selectedValue));
    },
});

const formatInitiativeLabel = (option) => {
    const code = String(option?.code ?? '').replace(/#/g, '').trim();
    const name = String(option?.name ?? '').trim();
    if (code && name) return `[${code}] ${name}`;
    return name || code || `Initiative #${option?.id ?? '-'}`;
};

const pageTitle = computed(() => `Capsule Summary - ${props.initiativeMaster?.code}`);

const unifiedInitiative = computed(() => ({
    ...props.initiativeMaster,
    appendix_data: props.appendixData,
    project_charter: props.projectCharter,
}));

const computedAppendixData = computed(() => {
    const a = props.appendixData;
    if (!a) return null;

    const getLabel = (val) => {
        if (val === 1) return 'High';
        if (val === 2) return 'Medium';
        if (val === 3) return 'Low';
        return '-';
    };

    let signBy = a?.sign_by ?? [];
    if (typeof signBy === 'string') {
        try {
            signBy = JSON.parse(signBy);
        } catch {
            signBy = signBy ? [signBy] : [];
        }
    }

    const themeMap = new Map((props.themeOptions ?? []).map((theme) => [Number(theme.id), theme]));
    const rjppThemes = (a?.rjpp_tagging_ids ?? []).map((id) => themeMap.get(Number(id))).filter(Boolean);

    return {
        usecase: a?.usecase ?? '-',
        description: a?.description ?? '-',
        owner: a?.owner ?? '-',
        coe: a?.coe ?? '-',
        value_label: getLabel(a?.value),
        urgency_label: getLabel(a?.urgency),
        organization: a?.organization ?? '-',
        update_doc: a?.update_doc ?? '-',
        situation: a?.situation ?? '-',
        key_functionalities: a?.key_functionalities ?? '-',
        value_rationale: a?.value_rationale ?? '-',
        value_matrics: a?.value_matrics ?? '-',
        urgency_rationale: a?.urgency_rationale ?? '-',
        urgency_expected: a?.urgency_expected ?? '-',
        expected_q: a?.expected_q,
        year_q: a?.year_q,
        ease_label: getLabel(a?.ease),
        ease_rationale: a?.ease_rationale ?? '-',
        ease_detail: a?.ease_detail ?? '-',
        resource_label: getLabel(a?.resource),
        resource_rationale: a?.resource_rationale ?? '-',
        resource_detail: a?.resource_detail ?? '-',
        predecessor: a?.predecessor ?? '-',
        successor: a?.successor ?? '-',
        otherBU: a?.otherBU ?? '-',
        sign_by: signBy,
        rjppThemes,
    };
});

const getStatusClass = (status) => {
    const s = String(status || '').toLowerCase();
    if (s.includes('draft')) return 'bg-slate-100 text-slate-600 ring-1 ring-slate-300';
    if (s.includes('propose')) return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
    if (s.includes('review')) return 'bg-amber-100 text-amber-700 ring-1 ring-amber-300';
    if (s.includes('baseline')) return 'bg-purple-100 text-purple-700 ring-1 ring-purple-300';
    if (s.includes('approve')) return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
    if (s.includes('postpone')) return 'bg-rose-100 text-rose-700 ring-1 ring-rose-300';
    return 'bg-slate-100 text-slate-500 ring-1 ring-slate-200';
};

const roadmapDuration = computed(() => {
    if (!props.roadmapItems || props.roadmapItems.length === 0) return null;

    const startYears = props.roadmapItems.map((item) => Number(item.startYear)).filter((year) => year > 0);
    const endYears = props.roadmapItems.map((item) => Number(item.endYear)).filter((year) => year > 0);

    if (startYears.length === 0 || endYears.length === 0) return null;

    const minStartYear = Math.min(...startYears);
    const maxEndYear = Math.max(...endYears);

    const startQs = props.roadmapItems
        .filter((item) => Number(item.startYear) === minStartYear)
        .map((item) => {
            const match = String(item.startQ).match(/Q?([1-4])/);
            return match ? Number(match[1]) : 1;
        });
    const minStartQ = Math.min(...startQs);

    const endQs = props.roadmapItems
        .filter((item) => Number(item.endYear) === maxEndYear)
        .map((item) => {
            const match = String(item.endQ).match(/Q?([1-4])/);
            return match ? Number(match[1]) : 4;
        });
    const maxEndQ = Math.max(...endQs);

    const years = maxEndYear - minStartYear + 1;
    const yearLabel = years > 1 ? `${years} Years` : `${years} Year`;

    if (minStartYear === maxEndYear) {
        return `${yearLabel} - (Q${minStartQ} - Q${maxEndQ} ${minStartYear})`;
    }

    return `${yearLabel} - (Q${minStartQ} ${minStartYear} - Q${maxEndQ} ${maxEndYear})`;
});

const roadmapYears = computed(() => {
    const start = props.roadmapStartYear || 2024;
    const end = props.roadmapEndYear || 2029;
    const list = [];
    for (let year = start; year <= end; year++) {
        list.push(year);
    }
    return list;
});

const roadmapBarStyle = computed(() => {
    if (!props.roadmapItems || props.roadmapItems.length === 0) return { width: '0%', left: '0%' };

    const startYears = props.roadmapItems.map((item) => Number(item.startYear)).filter((year) => year > 0);
    const endYears = props.roadmapItems.map((item) => Number(item.endYear)).filter((year) => year > 0);

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;

    if (startYears.length === 0 || endYears.length === 0) return { width: '0%', left: '0%' };

    const minStartYear = Math.max(minGlobalYear, Math.min(...startYears));
    const maxEndYear = Math.min(maxGlobalYear, Math.max(...endYears));

    const startQs = props.roadmapItems
        .filter((item) => Number(item.startYear) === minStartYear)
        .map((item) => {
            const match = String(item.startQ).match(/Q?([1-4])/);
            return match ? Number(match[1]) : 1;
        });
    const minStartQ = startQs.length ? Math.min(...startQs) : 1;

    const endQs = props.roadmapItems
        .filter((item) => Number(item.endYear) === maxEndYear)
        .map((item) => {
            const match = String(item.endQ).match(/Q?([1-4])/);
            return match ? Number(match[1]) : 4;
        });
    const maxEndQ = endQs.length ? Math.max(...endQs) : 4;

    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;

    const startQuarterIndex = (minStartYear - minGlobalYear) * 4 + (minStartQ - 1);
    const endQuarterIndex = (maxEndYear - minGlobalYear) * 4 + (maxEndQ - 1);

    return {
        left: `${(startQuarterIndex / totalQuarters) * 100}%`,
        width: `${((endQuarterIndex - startQuarterIndex + 1) / totalQuarters) * 100}%`,
    };
});

const goLiveBarStyle = computed(() => {
    if (!computedAppendixData.value?.expected_q || !computedAppendixData.value?.year_q) {
        return { width: '0%', left: '0%', display: 'none' };
    }

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;
    const year = Number(computedAppendixData.value.year_q);

    const match = String(computedAppendixData.value.expected_q).match(/Q?([1-4])/);
    const quarter = match ? Number(match[1]) : 1;

    if (year < minGlobalYear || year > maxGlobalYear) {
        return { width: '0%', left: '0%', display: 'none' };
    }

    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;
    const quarterIndex = (year - minGlobalYear) * 4 + (quarter - 1);

    return {
        left: `${(quarterIndex / totalQuarters) * 100}%`,
        width: `${(1 / totalQuarters) * 100}%`,
    };
});

const statusReviewMarkers = computed(() => {
    if (!props.statusImplementations || props.statusImplementations.length === 0) return [];

    const monthMap = {
        jan: 1, january: 1, feb: 2, february: 2, mar: 3, march: 3, apr: 4, april: 4,
        may: 5, mei: 5, jun: 6, june: 6, jul: 7, july: 7, aug: 8, augus: 8, august: 8, agu: 8,
        sep: 9, sept: 9, september: 9, oct: 10, octo: 10, october: 10, okt: 10,
        nov: 11, november: 11, dec: 12, december: 12, des: 12,
    };

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;
    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;

    const getStatusColor = (status) => {
        const s = String(status || '').toLowerCase();
        if (s.includes('done')) return '#10b981';
        if (s.includes('review')) return '#f97316';
        if (s.includes('progress')) return '#3b82f6';
        return '#1e4f8f';
    };

    return props.statusImplementations.map((impl) => {
        const monthStr = String(impl.start || '').trim().toLowerCase();
        const yearNum = Number(impl.year) || NaN;

        if (!monthStr || isNaN(yearNum)) return null;

        let monthNum = Number(monthStr);
        if (!Number.isFinite(monthNum) || monthNum <= 0 || monthNum > 12) {
            const key = monthStr.slice(0, 3);
            monthNum = monthMap[key] || monthMap[monthStr] || NaN;
        }

        if (!Number.isFinite(monthNum)) return null;
        if (yearNum < minGlobalYear || yearNum > maxGlobalYear) return null;

        const quarter = Math.floor((monthNum - 1) / 3) + 1;
        const quarterIndex = (yearNum - minGlobalYear) * 4 + (quarter - 1);

        return {
            left: `${(quarterIndex / totalQuarters) * 100}%`,
            label: `${impl.start} ${impl.year}`,
            status: impl.review_status || '-',
            color: getStatusColor(impl.review_status),
            statusUpdated: impl.status_updated || '-',
        };
    }).filter(Boolean);
});
</script>
