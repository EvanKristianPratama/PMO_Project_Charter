<template>
    <ModulLayout title="Regulation">
        <div class="animate-fade-in-up space-y-6">
            <!-- Controls: View Modes & Filters — all in one row -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Toggles / Tabs for View Modes -->
                <div
                    class="flex items-center gap-1 rounded-xl bg-slate-100 p-0.5 dark:bg-white/5 shrink-0"
                >
                    <button
                        @click="activeViewMode = 'document'"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-bold transition-all duration-200 active:scale-95',
                            activeViewMode === 'document'
                                ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                                : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white',
                        ]"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-3 h-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                            />
                        </svg>
                        Document
                    </button>
                    <button
                        @click="activeViewMode = 'organization'"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-bold transition-all duration-200 active:scale-95',
                            activeViewMode === 'organization'
                                ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                                : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white',
                        ]"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-3 h-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
                            />
                        </svg>
                        Organization
                    </button>
                </div>

                <!-- Divider -->
                <div
                    class="h-6 w-px bg-slate-200 dark:bg-white/10 shrink-0"
                ></div>

                <!-- Search Input -->
                <div class="relative flex-1 min-w-[160px] max-w-xs">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="pointer-events-none absolute inset-y-0 left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                        />
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari judul, nomor, tipe..."
                        class="w-full appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10"
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        type="button"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="w-3.5 h-3.5"
                        >
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Filter by Status (Multi-select Dropdown) -->
                <div class="relative inline-block text-left shrink-0">
                    <button
                        type="button"
                        @click="isStatusDropdownOpen = !isStatusDropdownOpen"
                        class="inline-flex items-center justify-between gap-1 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 cursor-pointer min-w-[110px] text-left select-none active:scale-[0.98] transition-transform duration-100"
                    >
                        <span class="truncate">
                            {{
                                selectedStatuses.length === 0
                                    ? "Semua Status"
                                    : selectedStatuses.length + " Status"
                            }}
                        </span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="w-3.5 h-3.5 text-slate-400 shrink-0"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <div
                        v-if="isStatusDropdownOpen"
                        class="fixed inset-0 z-30"
                        @click="isStatusDropdownOpen = false"
                    ></div>

                    <div
                        v-if="isStatusDropdownOpen"
                        class="absolute left-0 mt-1 w-40 rounded-lg bg-white border border-slate-200 shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 z-40 p-1.5 space-y-0.5"
                    >
                        <label
                            v-for="status in uniqueStatuses"
                            :key="status"
                            class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-white/5 transition duration-150 cursor-pointer select-none text-xs text-slate-700 dark:text-slate-300 font-medium"
                        >
                            <input
                                type="checkbox"
                                :value="status"
                                v-model="selectedStatuses"
                                class="rounded border-slate-300 text-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-black/20"
                            />
                            <span>{{ status }}</span>
                        </label>
                        <div
                            v-if="selectedStatuses.length > 0"
                            class="border-t border-slate-100 dark:border-white/5 pt-1 mt-0.5 flex justify-end"
                        >
                            <button
                                type="button"
                                @click="selectedStatuses = []"
                                class="text-[10px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-bold"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter by Company -->
                <div class="relative shrink-0">
                    <select
                        v-model="selectedCompanyId"
                        class="appearance-none bg-white text-slate-700 border border-slate-200 rounded-xl pl-2.5 pr-7 py-1.5 text-[11px] font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[120px]"
                    >
                        <option value="">Semua Company</option>
                        <option
                            v-for="company in companies"
                            :key="company.id"
                            :value="company.id"
                        >
                            {{ company.name }}
                        </option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3 h-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </div>
                </div>

                <!-- Expand Level Filter (only for document hierarchy) -->
                <div
                    v-if="activeViewMode === 'document'"
                    class="relative shrink-0"
                >
                    <select
                        v-model="expandLevel"
                        class="appearance-none bg-white text-slate-700 border border-slate-200 rounded-xl pl-2.5 pr-7 py-1.5 text-[11px] font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[100px]"
                    >
                        <option value="custom" disabled>Level...</option>
                        <option value="0">Collapse All</option>
                        <option
                            v-for="depth in maxDepth + 1"
                            :key="depth"
                            :value="depth"
                        >
                            Level {{ depth }}
                        </option>
                        <option value="all">Expand All</option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3 h-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </div>
                </div>

                <button
                    @click="openAddModal"
                    type="button"
                    class="shrink-0 inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-slate-700 transition-all hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2.5"
                        stroke="currentColor"
                        class="w-3 h-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15"
                        />
                    </svg>
                    Add Regulation
                </button>

                <!-- Active filters badge -->
                <button
                    v-if="
                        searchQuery ||
                        selectedStatuses.length ||
                        selectedCompanyId
                    "
                    @click="
                        searchQuery = '';
                        selectedStatuses = [];
                        selectedCompanyId = '';
                    "
                    type="button"
                    class="shrink-0 inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-[11px] font-bold text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400 transition"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="w-3 h-3"
                    >
                        <path
                            d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                        />
                    </svg>
                    Reset
                </button>
            </div>

            <!-- Regulations Table Components -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400"
                    >
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300"
                        >
                            <tr>
                                <th
                                    scope="col"
                                    class="px-3 py-3 w-28 border-r border-b border-slate-200 dark:border-white/10 text-left"
                                >
                                    Company
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Judul
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Nomor
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center w-28 border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Tipe
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Pemilik Dokumen
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Pemilik Dokumen (Mapping)
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center w-24 border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center w-16 border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Revisi
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 w-24 border-r border-b border-slate-200 dark:border-white/10"
                                >
                                    Berlaku
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3 text-center w-24 border-b border-slate-200 dark:border-white/10"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="dark:bg-transparent">
                            <!-- Document Hierarchy Mode -->
                            <template v-if="activeViewMode === 'document'">
                                <DocumentHierarki
                                    :regulations="filteredRegulations"
                                    :formatDate="formatDate"
                                    :expand-level="expandLevel"
                                    @update:expand-level="expandLevel = $event"
                                    @detail="handleDetailClick"
                                    @edit="openEditModal"
                                    @delete="deleteRegulation"
                                />
                            </template>

                            <!-- Organization Hierarchy Mode -->
                            <template v-if="activeViewMode === 'organization'">
                                <OrganizationHierarki
                                    :regulations="filteredRegulations"
                                    :organizations="organizations"
                                    :formatDate="formatDate"
                                    @detail="handleDetailClick"
                                    @edit="openEditModal"
                                    @delete="deleteRegulation"
                                />
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Manage Regulation Modal Component -->
            <ManageRegulation
                ref="manageRegulationModal"
                :regulations="regulations"
                :organizations="organizations"
                :companies="companies"
                :bods="bods"
            />
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ModulLayout from "@/Layouts/ModulLayout.vue";
import Swal from "sweetalert2";
import DocumentHierarki from "@/Components/modules/ITOM/Regulation/DocumentHierarki.vue";
import OrganizationHierarki from "@/Components/modules/ITOM/Regulation/OrganizationHierarki.vue";
import ManageRegulation from "@/Components/modules/ITOM/Regulation/ManageRegulation.vue";

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        default: () => [],
    },
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
});

// View mode state
const activeViewMode = ref("document"); // 'document', 'organization'

// Filters
const searchQuery = ref("");
const selectedCompanyId = ref("");
const selectedStatuses = ref([]);
const isStatusDropdownOpen = ref(false);
const expandLevel = ref("all");

// Compute depth of a regulation
const getDepth = (id) => {
    let depth = 0;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = props.regulations.find((r) => r.id === currentId);
        if (node?.parent_id) {
            depth++;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return depth;
};

const maxDepth = computed(() => {
    let max = 0;
    props.regulations.forEach((item) => {
        const d = getDepth(item.id);
        if (d > max) max = d;
    });
    return max;
});

const uniqueStatuses = computed(() => {
    const statusSet = new Set();
    props.regulations.forEach((reg) => {
        if (reg.status) {
            statusSet.add(reg.status);
        }
    });
    return Array.from(statusSet).sort((a, b) => a.localeCompare(b));
});

const filteredRegulations = computed(() => {
    let result = props.regulations;

    // Search filter — matches judul, nomor, owner, tipe
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase().trim();
        const allRegs = props.regulations;

        const collectAncestorIds = (reg) => {
            const ids = new Set();
            let current = reg;
            while (current.parent_id) {
                const parent = allRegs.find((r) => r.id === current.parent_id);
                if (!parent) break;
                ids.add(parent.id);
                current = parent;
            }
            return ids;
        };

        const collectDescendantIds = (parentId) => {
            const ids = new Set();
            const queue = [parentId];
            while (queue.length > 0) {
                const current = queue.shift();
                allRegs.forEach((reg) => {
                    if (reg.parent_id === current && !ids.has(reg.id)) {
                        ids.add(reg.id);
                        queue.push(reg.id);
                    }
                });
            }
            return ids;
        };

        const matched = result.filter(
            (r) =>
                (r.judul || "").toLowerCase().includes(q) ||
                (r.nomor || "").toLowerCase().includes(q) ||
                (r.owner || "").toLowerCase().includes(q) ||
                (r.tipe || "").toLowerCase().includes(q) ||
                (r.stk || "").toLowerCase().includes(q),
        );

        const includedIds = new Set(matched.map((r) => r.id));
        matched.forEach((reg) => {
            collectAncestorIds(reg).forEach((id) => includedIds.add(id));
            collectDescendantIds(reg.id).forEach((id) => includedIds.add(id));
        });

        result = result.filter((r) => includedIds.has(r.id));
    }

    if (selectedStatuses.value && selectedStatuses.value.length > 0) {
        const allRegs = props.regulations;

        // Helper: collect all descendant IDs recursively
        const collectDescendantIds = (parentId) => {
            const ids = new Set();
            const queue = [parentId];
            while (queue.length > 0) {
                const current = queue.shift();
                allRegs.forEach((reg) => {
                    if (reg.parent_id === current && !ids.has(reg.id)) {
                        ids.add(reg.id);
                        queue.push(reg.id);
                    }
                });
            }
            return ids;
        };

        // Helper: collect all ancestor IDs (walk up parent chain)
        const collectAncestorIds = (reg) => {
            const ids = new Set();
            let current = reg;
            while (current.parent_id) {
                const parent = allRegs.find((r) => r.id === current.parent_id);
                if (!parent) break;
                ids.add(parent.id);
                current = parent;
            }
            return ids;
        };

        // Step 1: find all docs within current result that match the status
        const matchedByStatus = result.filter(
            (reg) => reg.status && selectedStatuses.value.includes(reg.status),
        );

        const includedIds = new Set();

        matchedByStatus.forEach((reg) => {
            // Include the matched doc itself
            includedIds.add(reg.id);

            // Include all ancestors so hierarchy is visible
            const ancestorIds = collectAncestorIds(reg);
            ancestorIds.forEach((id) => includedIds.add(id));

            // Include all descendants of the matched doc
            const descendantIds = collectDescendantIds(reg.id);
            descendantIds.forEach((id) => includedIds.add(id));
        });

        result = result.filter((reg) => includedIds.has(reg.id));
    }

    if (selectedCompanyId.value) {
        const targetCompanyId = Number(selectedCompanyId.value);
        const allRegs = props.regulations;

        // Helper: collect all descendant IDs recursively
        const collectDescendantIds = (parentId) => {
            const ids = new Set();
            const queue = [parentId];
            while (queue.length > 0) {
                const current = queue.shift();
                allRegs.forEach((reg) => {
                    if (reg.parent_id === current && !ids.has(reg.id)) {
                        ids.add(reg.id);
                        queue.push(reg.id);
                    }
                });
            }
            return ids;
        };

        // Helper: collect all ancestor IDs
        const collectAncestorIds = (reg) => {
            const ids = new Set();
            let current = reg;
            while (current.parent_id) {
                const parent = allRegs.find((r) => r.id === current.parent_id);
                if (!parent) break;
                ids.add(parent.id);
                current = parent;
            }
            return ids;
        };

        // Find all docs that match the company (via company_id → MstBod → company_id = MstCompany.id)
        const matchedByCompany = result.filter(
            (reg) => reg.company?.company_id === targetCompanyId,
        );

        const includedIds = new Set();
        matchedByCompany.forEach((reg) => {
            includedIds.add(reg.id);

            const ancestorIds = collectAncestorIds(reg);
            ancestorIds.forEach((id) => includedIds.add(id));

            const descendantIds = collectDescendantIds(reg.id);
            descendantIds.forEach((id) => includedIds.add(id));
        });

        result = result.filter((reg) => includedIds.has(reg.id));
    }

    return result;
});

const manageRegulationModal = ref(null);

function openAddModal() {
    manageRegulationModal.value?.openAddModal();
}

function openEditModal(reg) {
    manageRegulationModal.value?.openEditModal(reg);
}

// Actions
// ─────────────────────────────────────────────────────────────────────────────
function handleDetailClick(reg) {
    const targetRoute =
        String(reg.tipe || "").toLowerCase() === "procedure"
            ? "itom.policy.regulation.procedure.index"
            : "itom.policy.general.index";

    router.visit(route(targetRoute, { regulation_id: reg.id }));
}

function deleteRegulation(reg) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: `Anda akan menghapus Regulasi: "${reg.judul}". Tindakan ini tidak dapat dibatalkan!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("itom.policy.regulation.destroy", reg.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: "Dihapus!",
                        text: "Regulasi berhasil dihapus.",
                        icon: "success",
                        confirmButtonColor: "#821f44",
                        timer: 2000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: "Error!",
                        text: "Gagal menghapus data.",
                        icon: "error",
                        confirmButtonColor: "#821f44",
                    });
                },
            });
        }
    });
}

// ---------------------------------------------------
// DATE FORMATTER HELPER
// ---------------------------------------------------
function formatDate(dateString) {
    if (!dateString) return "-";
    try {
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return dateString;
        return d.toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    } catch (e) {
        return dateString;
    }
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
