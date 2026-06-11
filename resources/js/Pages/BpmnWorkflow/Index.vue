<template>
    <UserLayout title="BPMN Workflow Controller">
        <div class="bpmn-container" :class="{ 'is-fullscreen': isFullscreen }">
            <!-- HEADER TOOLBAR -->
            <div class="bpmn-toolbar border-b border-slate-200 dark:border-white/10 bg-white dark:bg-[#141414]">
                <div class="toolbar-left">
                    <div class="flex items-center gap-1.5">
                        <select 
                            v-model="selectedWorkflowId" 
                            class="workflow-selector h-[34px] rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 shadow-sm transition dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                            @change="loadWorkflow"
                        >
                            <option :value="null">-- Pilih Workflow --</option>
                            <option v-for="w in workflows" :key="w.id" :value="w.id">{{ w.name }}</option>
                        </select>
                        
                        <button 
                            type="button" 
                            class="btn btn-secondary h-[34px]" 
                            @click="createNewWorkflow"
                            title="Buat Workflow Baru"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Baru
                        </button>
                    </div>
                </div>

                <div class="toolbar-center">
                    <div class="workflow-info flex items-center gap-2" v-if="activeWorkflow">
                        <input 
                            v-model="activeWorkflow.name" 
                            class="workflow-name-input h-[34px]"
                            placeholder="Nama Alur Kerja..."
                            @blur="autoSaveName"
                        />
                        <select
                            v-model="activeWorkflow.sop_type"
                            class="h-[34px] rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 shadow-sm transition dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                        >
                            <option :value="null">Tanpa Asosiasi SOP</option>
                            <option value="A">SOP A (Penyusunan RSTI)</option>
                            <option value="B">SOP B (Reviu RSTI)</option>
                        </select>
                        <span class="workflow-badge">BPMN 2.0</span>
                    </div>
                </div>

                <div class="toolbar-right">
                    <div class="flex items-center gap-1.5">
                        <button 
                            type="button" 
                            class="btn btn-secondary h-[34px]" 
                            @click="exportBpmn"
                            title="Ekspor Desain ke File BPMN 2.0 XML"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Ekspor
                        </button>
                        
                        <label class="btn btn-secondary h-[34px] cursor-pointer" title="Impor Desain dari File BPMN 2.0 XML">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 mr-1 inline">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            Impor
                            <input type="file" class="hidden" accept=".bpmn,.xml" @change="importBpmn" />
                        </label>

                        <button 
                            v-if="activeWorkflow.sop_type && activeWorkflow.id"
                            type="button" 
                            class="btn h-[34px] border-sky-500/50 hover:bg-sky-50 text-sky-700 dark:border-sky-500/30 dark:hover:bg-sky-500/10 dark:text-sky-400 font-semibold"
                            @click="syncFromSop"
                            :disabled="isSyncing"
                            title="Sinkronkan diagram BPMN dari Database SOP saat ini"
                        >
                            <svg v-if="isSyncing" class="h-3.5 w-3.5 animate-spin mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Sync dari SOP
                        </button>

                        <button 
                            type="button" 
                            class="btn btn-primary h-[34px]" 
                            @click="saveWorkflowToDb"
                            :disabled="isSaving"
                        >
                            <svg v-if="isSaving" class="h-3.5 w-3.5 animate-spin mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ activeWorkflow && activeWorkflow.sop_type ? 'Simpan & Sync SOP' : 'Simpan ke DB' }}
                        </button>

                        <button 
                            type="button" 
                            class="btn btn-secondary px-3" 
                            @click="toggleFullscreen"
                            :title="isFullscreen ? 'Kecilkan Tampilan' : 'Tampilan Penuh (Fullscreen)'"
                        >
                            <svg v-if="isFullscreen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9V4.5M15 9h4.5M15 9l5.25-5.25M15 15v4.5M15 15h4.5M15 15l5.25 5.25" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0-5.25-5.25" />
                            </svg>
                            {{ isFullscreen ? 'Kecilkan' : 'Fullscreen' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- CORE INTERFACE -->
            <div class="bpmn-workspace">
                <!-- EDITOR CANVAS -->
                <div class="bpmn-canvas-area bg-slate-50 dark:bg-[#0c0c0e]">
                    <BpmnModeler
                        ref="modelerRef"
                        :bpmnXml="currentXml"
                        @element-selected="onElementSelected"
                        @element-deselected="onElementDeselected"
                        @xml-changed="onXmlChanged"
                        @modeler-ready="onModelerReady"
                    />

                    <!-- Gateway Branch Decision Dialog Overlay -->
                    <div v-if="gatewayDecisionModal" class="decision-overlay animate-fade-in">
                        <div class="decision-modal bg-white dark:bg-[#1d1d1d] border border-amber-500 shadow-xl">
                            <div class="modal-hdr bg-amber-500 text-white font-bold px-4 py-2 text-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-1.5 animate-bounce">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                Keputusan Gateway: {{ gatewayDecisionModal.nodeLabel }}
                            </div>
                            <div class="modal-body p-4 text-xs">
                                <p class="mb-4 text-slate-600 dark:text-slate-300 font-semibold">
                                    Simulasi tiba di percabangan. Silakan pilih jalur alur kerja selanjutnya:
                                </p>
                                <div class="flex flex-col gap-2">
                                    <button 
                                        v-for="branch in gatewayDecisionModal.branches" 
                                        :key="branch.edgeId"
                                        type="button" 
                                        class="btn btn-secondary w-full justify-start text-left font-semibold text-xs border-amber-300 hover:bg-amber-50 dark:hover:bg-amber-500/10"
                                        @click="resolveGatewayBranch(branch)"
                                    >
                                        Menuju ke: <strong class="ml-1 text-amber-700 dark:text-amber-300">[{{ branch.targetLabel }}]</strong>
                                        <span v-if="branch.edgeLabel" class="ml-auto text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded dark:bg-amber-900/40 dark:text-amber-200">
                                            {{ branch.edgeLabel }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR PROPERTIES INSPECTOR -->
                <div class="relative flex">
                    <!-- Floating Collapse Toggle Button on the left edge -->
                    <button 
                        type="button" 
                        class="absolute top-1/2 -left-4 z-40 flex h-10 w-4 -translate-y-1/2 items-center justify-center rounded-l-md border border-r-0 border-slate-200 bg-white shadow-md hover:bg-slate-50 dark:border-white/10 dark:bg-[#1f1f1f] dark:hover:bg-zinc-800 focus:outline-none transition-all duration-300 cursor-pointer"
                        @click="isInspectorOpen = !isInspectorOpen"
                        :title="isInspectorOpen ? 'Sembunyikan Konfigurasi' : 'Tampilkan Konfigurasi'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3 text-slate-500 dark:text-zinc-400 transition-transform duration-300" :class="{ 'rotate-180': !isInspectorOpen }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>

                    <div 
                        class="bpmn-inspector border-l border-slate-200 dark:border-white/10 bg-white dark:bg-[#141414]"
                        :class="isInspectorOpen ? 'w-[280px] p-4 opacity-100' : 'w-0 p-0 border-l-0 opacity-0 overflow-hidden'"
                    >
                        <div v-if="selectedElement" class="inspector-content">
                            <div class="inspector-header">
                                <h3>Konfigurasi Node</h3>
                                <span class="node-type-badge">{{ displayType }}</span>
                            </div>

                            <div class="inspector-form space-y-4">
                                <!-- COMMON LABEL -->
                                <div class="form-group">
                                    <label class="control-label">Nama Elemen</label>
                                    <input 
                                        v-model="elementName" 
                                        class="form-control" 
                                        placeholder="e.g. Persetujuan PMO" 
                                        @input="updateElementName"
                                    />
                                </div>

                                <!-- BPMN QUICK TOOLKIT -->
                                <div class="border-t border-slate-200 dark:border-white/5 pt-4">
                                    <h4 class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-2">BPMN Quick Toolkit</h4>
                                    
                                    <!-- Tab Switchers -->
                                    <div class="flex rounded-lg bg-slate-100 p-0.5 dark:bg-[#1e1e1e] mb-3">
                                        <button 
                                            type="button"
                                            class="flex-1 rounded-md py-1 text-[11px] font-semibold text-center transition cursor-pointer"
                                            :class="activeToolTab === 'append' ? 'bg-white text-slate-800 shadow-sm dark:bg-[#2b2b2b] dark:text-slate-100' : 'text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                                            @click="activeToolTab = 'append'"
                                        >
                                            Hubungkan Baru
                                        </button>
                                        <button 
                                            type="button"
                                            class="flex-1 rounded-md py-1 text-[11px] font-semibold text-center transition cursor-pointer"
                                            :class="activeToolTab === 'transform' ? 'bg-white text-slate-800 shadow-sm dark:bg-[#2b2b2b] dark:text-slate-100' : 'text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                                            @click="activeToolTab = 'transform'"
                                        >
                                            Ubah Tipe
                                        </button>
                                    </div>

                                    <!-- Category Filter Buttons -->
                                    <div class="grid grid-cols-3 gap-1 mb-3">
                                        <button 
                                            type="button"
                                            class="rounded py-1 text-[10px] font-bold border text-center transition cursor-pointer"
                                            :class="selectedToolCategory === 'activity' ? 'border-blue-500/30 bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'border-slate-200 bg-white text-slate-600 dark:border-white/5 dark:bg-[#18181b] dark:text-zinc-400'"
                                            @click="selectedToolCategory = 'activity'"
                                        >
                                            Aktivitas
                                        </button>
                                        <button 
                                            type="button"
                                            class="rounded py-1 text-[10px] font-bold border text-center transition cursor-pointer"
                                            :class="selectedToolCategory === 'gateway' ? 'border-amber-500/30 bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'border-slate-200 bg-white text-slate-600 dark:border-white/5 dark:bg-[#18181b] dark:text-zinc-400'"
                                            @click="selectedToolCategory = 'gateway'"
                                        >
                                            Gateway
                                        </button>
                                        <button 
                                            type="button"
                                            class="rounded py-1 text-[10px] font-bold border text-center transition cursor-pointer"
                                            :class="selectedToolCategory === 'event' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'border-slate-200 bg-white text-slate-600 dark:border-white/5 dark:bg-[#18181b] dark:text-zinc-400'"
                                            @click="selectedToolCategory = 'event'"
                                        >
                                            Event
                                        </button>
                                    </div>

                                    <!-- Elements Grid List -->
                                    <div class="max-h-[220px] overflow-y-auto pr-1 space-y-1.5">
                                        <button 
                                            v-for="item in filteredElementTypes" 
                                            :key="item.label + '-' + item.type + '-' + (item.eventDefinitionType || '')"
                                            type="button"
                                            class="w-full text-left rounded-lg p-2 border bg-gradient-to-r flex items-center transition cursor-pointer hover:scale-[1.01] hover:shadow-sm"
                                            :class="item.color"
                                            @click="executeQuickAction(item)"
                                            :title="item.description"
                                        >
                                            <span class="mr-2 flex items-center justify-center">
                                                <svg v-if="item.icon === 'UserIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                                <svg v-else-if="item.icon === 'CpuChipIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21M6.75 6.75h10.5a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V7.5a.75.75 0 0 1 .75-.75Zm3 3h4.5v4.5h-4.5v-4.5Z" /></svg>
                                                <svg v-else-if="item.icon === 'CommandLineIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" /></svg>
                                                <svg v-else-if="item.icon === 'PaperAirplaneIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                                                <svg v-else-if="item.icon === 'InboxArrowDownIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.152 1.6L3 14.186M9 3.75V1.5m0 2.25H15m0 0V1.5m0 2.25h2.088a2.25 2.25 0 0 1 2.152 1.6L21 14.186M9 11.25v2.25m0-2.25L7.5 12.75M9 11.25l1.5 1.5M3 14.186V19.5a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 19.5v-5.314m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0H6.75a2.25 2.25 0 0 0 2.25 2.25h6a2.25 2.25 0 0 0 2.25-2.25H21" /></svg>
                                                <svg v-else-if="item.icon === 'HandRaisedIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a2.25 2.25 0 1 0-3.18 3.18l3.18-3.18Zm0 0-3.18 3.18M12.03 3.6a2.25 2.25 0 1 0-3.18 3.18l3.18-3.18Zm0 0-3.18 3.18M14.01 4.575a2.25 2.25 0 1 0-3.18 3.18l3.18-3.18Zm0 0-3.18 3.18M16.5 7.5v9a4.5 4.5 0 0 1-9 0V7.5" /></svg>
                                                <svg v-else-if="item.icon === 'ScaleIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.567 48.567 0 0 1 12 4.5c-2.33 0-4.595.16-6.75.47m13.5 0c1.18.118 2.085 1.064 2.14 2.262l.223 4.809c.032.694-.448 1.28-1.144 1.28H15.75m3.001-8.351L15.75 12m-7.5 0H3.003m0 0c-.696 0-1.176-.586-1.144-1.28l.222-4.81c.056-1.197.96-2.143 2.14-2.261m0 0A48.556 48.556 0 0 1 8.25 4.5m0 7.5L3.003 3.675M8.25 12H15.75" /></svg>
                                                <svg v-else-if="item.icon === 'ArrowsRightLeftIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                                                <svg v-else-if="item.icon === 'PlusIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                                <svg v-else-if="item.icon === 'CircleStackIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75m-16.5-3.75v3.75" /></svg>
                                                <svg v-else-if="item.icon === 'ClockIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                                <svg v-else-if="item.icon === 'PlayIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653Z" /></svg>
                                                <svg v-else-if="item.icon === 'EnvelopeIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                                <svg v-else-if="item.icon === 'EnvelopeOpenIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488a2.25 2.25 0 0 1-2.178 0L5.433 11.89A2.25 2.25 0 0 1 4.25 9.906V9M21.75 9a2.25 2.25 0 0 0-2.25-2.25h-15A2.25 2.25 0 0 0 2.25 9m19.5 0-7.74 5.16a2.25 2.25 0 0 1-2.52 0L2.25 9" /></svg>
                                                <svg v-else-if="item.icon === 'StopIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" /></svg>
                                                <svg v-else-if="item.icon === 'ExclamationTriangleIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                                <svg v-else-if="item.icon === 'XMarkIcon'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[10px] font-bold tracking-wide">{{ item.label }}</div>
                                                <div class="text-[8.5px] leading-tight opacity-85 truncate mt-0.5">{{ item.description }}</div>
                                            </div>
                                        </button>
                                    </div>
                                </div>

                                <!-- TASK SPECIFIC ACTION TRIGGER -->
                                <div v-if="isTaskNode" class="task-config-section border-t border-slate-200 dark:border-white/5 pt-4">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Mekanisme Pemicu Aksi</h4>
                                    
                                    <div class="form-group">
                                        <label class="control-label">Aksi Trigger</label>
                                        <select 
                                            v-model="selectedElementConfig.action_type" 
                                            class="form-control"
                                            @change="onActionTypeChange"
                                        >
                                            <option value="none">Tanpa Aksi (Simulasi Saja)</option>
                                            <option value="update_status">Update Status Inisiatif</option>
                                            <option value="sync_db">Sinkronisasi Cloud Data</option>
                                            <option value="log_activity">Catat Log Audit Kustom</option>
                                        </select>
                                    </div>

                                    <!-- UPDATE INITIATIVE CONFIG -->
                                    <div v-if="selectedElementConfig.action_type === 'update_status'" class="space-y-3 mt-3 animate-fade-in">
                                        <div class="form-group">
                                            <label class="control-label">Pilih Inisiatif Target</label>
                                            <select 
                                                v-model="selectedElementConfig.initiative_id" 
                                                class="form-control text-xs font-sans"
                                            >
                                                <option :value="null">-- Pilih Inisiatif --</option>
                                                <option 
                                                    v-for="init in mstInitiatives" 
                                                    :key="init.id" 
                                                    :value="init.id"
                                                >
                                                    [{{ init.code || 'ID:'+init.id }}] - {{ init.name }} ({{ init.tipe_initiative === 1 ? 'Digital' : 'IT' }})
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label">Ubah Menjadi Status</label>
                                            <select 
                                                v-model="selectedElementConfig.status_value" 
                                                class="form-control"
                                            >
                                                <option value="drafting">Drafting</option>
                                                <option value="propose">Propose</option>
                                                <option value="review">Review</option>
                                                <option value="approved">Approved (Lolos Proyek)</option>
                                                <option value="postpone">Postponed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- AUDIT LOG MESSAGE CONFIG -->
                                    <div v-if="selectedElementConfig.action_type === 'log_activity'" class="space-y-3 mt-3 animate-fade-in">
                                        <div class="form-group">
                                            <label class="control-label">Pesan Catatan Log</label>
                                            <input 
                                                v-model="selectedElementConfig.custom_message" 
                                                class="form-control" 
                                                placeholder="e.g. Alur kerja menyetujui anggaran proyek"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- EDGE CONFIGURATOR ON ACTIVE CONNECTIONS -->
                                <div class="border-t border-slate-200 dark:border-white/5 pt-4">
                                    <button 
                                        type="button" 
                                        class="btn btn-danger w-full justify-center" 
                                        @click="deleteSelectedElement"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Hapus Elemen
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- NOTHING SELECTED -->
                        <div v-else class="inspector-empty">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 9.152c.582.448 1.148.89 1.676 1.345m-1.676-1.345c-.528-.407-1.09-.817-1.676-1.226m1.676 1.345-.824 2.986m.824-2.986 3.048-.775m-4.108 5.002-.528-.407m-.57 2.062 1.676 1.345m-1.676-1.345c-.528-.407-1.148-.89-1.676-1.345m1.676 1.345-.824 2.986m.824-2.986 3.048-.775m-6.14 1.226H3.75A1.5 1.5 0 0 1 2.25 15V4.5A1.5 1.5 0 0 1 3.75 3h15A1.5 1.5 0 0 1 20.25 4.5V10.5m-16.5 6h2.89L12 10.89l-2.89-2.89L3.75 16.5Z" />
                                </svg>
                                <p class="text-xs text-slate-400">Pilih elemen atau koneksi di kanvas untuk mulai mengonfigurasikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SIMULATOR CONTROL CONSOLE PANEL (MySQL Workbench Style - Plain Analog Grid) -->
            <div 
                class="bpmn-simulation-console border-t border-slate-200 bg-slate-50 text-slate-700 shadow-lg dark:border-white/10 dark:bg-[#141416] dark:text-slate-200"
                :class="isConsoleOpen ? 'h-[190px]' : 'h-[32px] overflow-hidden'"
            >
                <div class="console-header border-b border-slate-200 bg-slate-100 px-4 py-1.5 flex items-center justify-between dark:border-white/10 dark:bg-[#1c1c1f]">
                    <div class="flex items-center gap-2">
                        <h3 class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action Output</h3>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Live Execution Switcher -->
                        <label class="relative inline-flex cursor-pointer items-center text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                            <input type="checkbox" v-model="liveExecution" class="peer sr-only" :disabled="isSimulating">
                            <div class="peer h-3.5 w-6.5 rounded-full bg-slate-300 after:absolute after:left-[2px] after:top-[2px] after:h-2.5 after:w-2.5 after:rounded-full after:border after:border-slate-400 after:bg-white after:transition-all after:content-[''] peer-checked:bg-rose-500 peer-checked:after:translate-x-3 peer-checked:after:border-white peer-focus:outline-none dark:bg-zinc-700"></div>
                            <span class="ml-2 uppercase tracking-wide">
                                {{ liveExecution ? 'Live Execution (Real DB)' : 'Visual Simulation Only' }}
                            </span>
                        </label>

                        <!-- Premium Divider -->
                        <div class="h-3 w-px bg-slate-200 dark:bg-white/10"></div>

                        <!-- Collapse/Expand Chevron Toggle Button -->
                        <button
                            type="button"
                            class="flex items-center justify-center p-1 rounded hover:bg-slate-200 dark:hover:bg-zinc-800 focus:outline-none transition-colors duration-200 cursor-pointer"
                            @click="isConsoleOpen = !isConsoleOpen"
                            :title="isConsoleOpen ? 'Sembunyikan Konsol' : 'Tampilkan Konsol'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3 text-slate-500 dark:text-zinc-400 transition-transform duration-300" :class="{ 'rotate-180': !isConsoleOpen }">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="console-body flex">
                    <!-- CONTROLS LEFT -->
                    <div class="console-controls flex flex-col gap-2 border-r border-slate-200 bg-slate-100/50 p-3 dark:border-white/10 dark:bg-[#18181b]/50">
                        <button 
                            type="button" 
                            class="btn flex items-center justify-center font-bold text-xs"
                            :class="isSimulating ? 'btn-danger' : 'btn-emerald'"
                            @click="toggleSimulation"
                        >
                            <svg v-if="!isSimulating" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653Z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                            </svg>
                            {{ isSimulating ? 'Hentikan' : 'Jalankan' }}
                        </button>

                        <button 
                            type="button" 
                            class="btn btn-secondary justify-center text-xs" 
                            @click="resetSimulation"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Reset
                        </button>
                    </div>

                    <!-- ANALOG LOGGER RIGHT (MySQL Workbench Action Output Grid Style) -->
                    <div class="console-logs flex-1 overflow-y-auto" ref="logsContainer">
                        <table class="workbench-table">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">#</th>
                                    <th class="w-12 text-center">Status</th>
                                    <th class="w-24 text-center">Time</th>
                                    <th class="w-32">Action</th>
                                    <th>Message / Response</th>
                                    <th class="w-24 text-right">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!simLogs.length">
                                    <td colspan="6" class="text-slate-400 text-center py-10 italic text-[11px] dark:text-zinc-500 no-logs-msg">
                                        No action output. Start simulation to see execution logs in real-time.
                                    </td>
                                </tr>
                                <tr v-for="(log, idx) in simLogs" :key="idx">
                                    <td class="text-center font-mono text-[10px] col-num">
                                        {{ idx + 1 }}
                                    </td>
                                    <td class="text-center w-8 col-status">
                                        <div class="flex items-center justify-center">
                                            <!-- success checkmark -->
                                            <svg v-if="log.type === 'success'" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <!-- warning triangle -->
                                            <svg v-else-if="log.type === 'warning'" class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <!-- error icon -->
                                            <svg v-else-if="log.type === 'error'" class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            <!-- trigger execution lightning bolt -->
                                            <svg v-else-if="log.type === 'trigger'" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                            </svg>
                                            <!-- default slate info bubble -->
                                            <svg v-else class="w-3.5 h-3.5 text-slate-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="text-center font-mono text-[10px] col-time">
                                        {{ log.time }}
                                    </td>
                                    <td class="font-mono text-[10px] col-action">
                                        <span class="workbench-action-text font-mono" :class="getActionBadgeClass(log.type)">
                                            {{ log.action }}
                                        </span>
                                    </td>
                                    <td class="font-mono text-[10.5px] col-message">
                                        {{ log.text }}
                                    </td>
                                    <td class="font-mono text-[10px] text-right col-duration">
                                        {{ log.duration }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, defineAsyncComponent, nextTick, onMounted, onBeforeUnmount, ref, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';

// Load the BPMN modeler lazily so the workflow page stays small until it is used.
const BpmnModeler = defineAsyncComponent(() => import('@/Components/Bpmn/BpmnModeler.vue'));

// Layout & Navigation Helper
import UserLayout from '@/Layouts/UserLayout.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const route = useRouteHelper();
const page = usePage();

const props = defineProps({
    workflows: {
        type: Array,
        default: () => [],
    },
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
});

// Canvas states
const modelerRef = ref(null);
const currentXml = ref('');
const isModelerLoaded = ref(false);

// Selection states
const selectedElement = ref(null);
const elementName = ref('');

// Saved workflows refs
const selectedWorkflowId = ref(null);
const activeWorkflow = ref({
    id: null,
    name: 'Workflow Approval Standard',
    description: 'Workflow persetujuan inisiatif default',
    sop_type: null,
});

// UI states
const isSaving = ref(false);
const liveExecution = ref(false);
const isInspectorOpen = ref(true);
const isConsoleOpen = ref(true);

// Fullscreen states
const isFullscreen = ref(false);
const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
    setTimeout(() => {
        if (modelerRef.value) {
            const modeler = modelerRef.value.getModeler();
            if (modeler) {
                const canvas = modeler.get('canvas');
                canvas.resized();
            }
        }
    }, 100);
};

// Keyboard listener for ESC to exit fullscreen
const handleKeyDown = (e) => {
    if (e.key === 'Escape' && isFullscreen.value) {
        toggleFullscreen();
    }
};

// Custom action configurations map
const actionConfigs = ref({});

// BPMN QUICK TOOLKIT ELEMENTS & HANDLERS
const activeToolTab = ref('append'); // 'append' | 'transform'
const selectedToolCategory = ref('activity'); // 'activity' | 'gateway' | 'event'

const bpmnElementTypes = [
    // ACTIVITIES
    {
        category: 'activity',
        type: 'bpmn:UserTask',
        eventDefinitionType: null,
        label: 'User Task',
        icon: 'UserIcon',
        description: 'Tugas yang dikerjakan oleh aktor manusia.',
        color: 'from-blue-500/10 to-blue-600/5 hover:from-blue-500/20 hover:to-blue-600/10 border-blue-500/20 dark:border-blue-500/30 text-blue-700 dark:text-blue-300'
    },
    {
        category: 'activity',
        type: 'bpmn:ServiceTask',
        eventDefinitionType: null,
        label: 'Service Task',
        icon: 'CpuChipIcon',
        description: 'Tugas integrasi sistem otomatis / API.',
        color: 'from-purple-500/10 to-purple-600/5 hover:from-purple-500/20 hover:to-purple-600/10 border-purple-500/20 dark:border-purple-500/30 text-purple-700 dark:text-purple-300'
    },
    {
        category: 'activity',
        type: 'bpmn:ScriptTask',
        eventDefinitionType: null,
        label: 'Script Task',
        icon: 'CommandLineIcon',
        description: 'Tugas eksekusi script pemrograman.',
        color: 'from-indigo-500/10 to-indigo-600/5 hover:from-indigo-500/20 hover:to-indigo-600/10 border-indigo-500/20 dark:border-indigo-500/30 text-indigo-700 dark:text-indigo-300'
    },
    {
        category: 'activity',
        type: 'bpmn:SendTask',
        eventDefinitionType: null,
        label: 'Send Task',
        icon: 'PaperAirplaneIcon',
        description: 'Tugas mengirimkan pesan/notifikasi/email.',
        color: 'from-sky-500/10 to-sky-600/5 hover:from-sky-500/20 hover:to-sky-600/10 border-sky-500/20 dark:border-sky-500/30 text-sky-700 dark:text-sky-300'
    },
    {
        category: 'activity',
        type: 'bpmn:ReceiveTask',
        eventDefinitionType: null,
        label: 'Receive Task',
        icon: 'InboxArrowDownIcon',
        description: 'Tugas menunggu pesan masuk atau callback.',
        color: 'from-cyan-500/10 to-cyan-600/5 hover:from-cyan-500/20 hover:to-cyan-600/10 border-cyan-500/20 dark:border-cyan-500/30 text-cyan-700 dark:text-cyan-300'
    },
    {
        category: 'activity',
        type: 'bpmn:ManualTask',
        eventDefinitionType: null,
        label: 'Manual Task',
        icon: 'HandRaisedIcon',
        description: 'Tugas fisik/offline tanpa sistem komputer.',
        color: 'from-slate-500/10 to-slate-600/5 hover:from-slate-500/20 hover:to-slate-600/10 border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300'
    },
    {
        category: 'activity',
        type: 'bpmn:BusinessRuleTask',
        eventDefinitionType: null,
        label: 'Business Rule',
        icon: 'ScaleIcon',
        description: 'Mengevaluasi keputusan berdasarkan aturan bisnis.',
        color: 'from-teal-500/10 to-teal-600/5 hover:from-teal-500/20 hover:to-teal-600/10 border-teal-500/20 dark:border-teal-500/30 text-teal-700 dark:text-teal-300'
    },
    
    // GATEWAYS
    {
        category: 'gateway',
        type: 'bpmn:ExclusiveGateway',
        eventDefinitionType: null,
        label: 'Exclusive Gateway (XOR)',
        icon: 'ArrowsRightLeftIcon',
        description: 'Percabangan satu jalur sesuai kondisi.',
        color: 'from-amber-500/10 to-amber-600/5 hover:from-amber-500/20 hover:to-amber-600/10 border-amber-500/20 dark:border-amber-500/30 text-amber-700 dark:text-amber-300'
    },
    {
        category: 'gateway',
        type: 'bpmn:ParallelGateway',
        eventDefinitionType: null,
        label: 'Parallel Gateway (AND)',
        icon: 'PlusIcon',
        description: 'Menjalankan beberapa jalur bersamaan.',
        color: 'from-orange-500/10 to-orange-600/5 hover:from-orange-500/20 hover:to-orange-600/10 border-orange-500/20 dark:border-orange-500/30 text-orange-700 dark:text-orange-300'
    },
    {
        category: 'gateway',
        type: 'bpmn:InclusiveGateway',
        eventDefinitionType: null,
        label: 'Inclusive Gateway (OR)',
        icon: 'CircleStackIcon',
        description: 'Percabangan satu atau lebih jalur kondisional.',
        color: 'from-yellow-500/10 to-yellow-600/5 hover:from-yellow-500/20 hover:to-yellow-600/10 border-yellow-500/20 dark:border-yellow-500/30 text-yellow-700 dark:text-yellow-300'
    },
    {
        category: 'gateway',
        type: 'bpmn:EventBasedGateway',
        eventDefinitionType: null,
        label: 'Event-Based Gateway',
        icon: 'ClockIcon',
        description: 'Percabangan berdasarkan event tercepat.',
        color: 'from-rose-500/10 to-rose-600/5 hover:from-rose-500/20 hover:to-rose-600/10 border-rose-500/20 dark:border-rose-500/30 text-rose-700 dark:text-rose-300'
    },

    // EVENTS
    {
        category: 'event',
        type: 'bpmn:StartEvent',
        eventDefinitionType: null,
        label: 'Start Event (None)',
        icon: 'PlayIcon',
        description: 'Titik awal alur kerja standard.',
        color: 'from-emerald-500/10 to-emerald-600/5 hover:from-emerald-500/20 hover:to-emerald-600/10 border-emerald-500/20 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300'
    },
    {
        category: 'event',
        type: 'bpmn:StartEvent',
        eventDefinitionType: 'bpmn:MessageEventDefinition',
        label: 'Message Start',
        icon: 'EnvelopeIcon',
        description: 'Alur dipicu pesan/webhook eksternal.',
        color: 'from-emerald-500/15 to-teal-600/5 hover:from-emerald-500/25 hover:to-teal-600/10 border-emerald-500/30 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-300'
    },
    {
        category: 'event',
        type: 'bpmn:StartEvent',
        eventDefinitionType: 'bpmn:TimerEventDefinition',
        label: 'Timer Start',
        icon: 'ClockIcon',
        description: 'Alur kerja dijadwalkan secara berkala.',
        color: 'from-emerald-500/15 to-lime-600/5 hover:from-emerald-500/25 hover:to-lime-600/10 border-emerald-500/30 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-300'
    },
    {
        category: 'event',
        type: 'bpmn:IntermediateCatchEvent',
        eventDefinitionType: 'bpmn:TimerEventDefinition',
        label: 'Timer Catch Event',
        icon: 'ClockIcon',
        description: 'Menunda alur selama durasi tertentu.',
        color: 'from-blue-500/10 to-indigo-600/5 hover:from-blue-500/20 hover:to-indigo-600/10 border-blue-500/20 dark:border-blue-500/30 text-blue-700 dark:text-blue-300'
    },
    {
        category: 'event',
        type: 'bpmn:IntermediateCatchEvent',
        eventDefinitionType: 'bpmn:MessageEventDefinition',
        label: 'Message Catch Event',
        icon: 'EnvelopeOpenIcon',
        description: 'Menunggu callback/pesan dari sistem luar.',
        color: 'from-cyan-500/10 to-teal-600/5 hover:from-cyan-500/20 hover:to-teal-600/10 border-cyan-500/20 dark:border-cyan-500/30 text-cyan-700 dark:text-cyan-300'
    },
    {
        category: 'event',
        type: 'bpmn:IntermediateThrowEvent',
        eventDefinitionType: 'bpmn:MessageEventDefinition',
        label: 'Message Throw Event',
        icon: 'PaperAirplaneIcon',
        description: 'Mengirimkan pesan/notifikasi di tengah alur.',
        color: 'from-indigo-500/10 to-blue-600/5 hover:from-indigo-500/20 hover:to-blue-600/10 border-indigo-500/20 dark:border-indigo-500/30 text-indigo-700 dark:text-indigo-300'
    },
    {
        category: 'event',
        type: 'bpmn:EndEvent',
        eventDefinitionType: null,
        label: 'End Event (None)',
        icon: 'StopIcon',
        description: 'Titik akhir alur kerja normal.',
        color: 'from-rose-500/10 to-rose-600/5 hover:from-rose-500/20 hover:to-rose-600/10 border-rose-500/20 dark:border-rose-500/30 text-rose-700 dark:text-rose-300'
    },
    {
        category: 'event',
        type: 'bpmn:EndEvent',
        eventDefinitionType: 'bpmn:MessageEventDefinition',
        label: 'Message End',
        icon: 'EnvelopeIcon',
        description: 'Mengirim sinyal penyelesaian ke sistem luar.',
        color: 'from-rose-500/15 to-pink-600/5 hover:from-rose-500/25 hover:to-pink-600/10 border-rose-500/30 dark:border-rose-500/40 text-rose-700 dark:text-rose-300'
    },
    {
        category: 'event',
        type: 'bpmn:EndEvent',
        eventDefinitionType: 'bpmn:ErrorEventDefinition',
        label: 'Error End',
        icon: 'ExclamationTriangleIcon',
        description: 'Selesai dengan memicu kesalahan/exception.',
        color: 'from-rose-600/10 to-red-600/5 hover:from-rose-600/20 hover:to-red-600/10 border-rose-600/20 dark:border-rose-600/30 text-rose-800 dark:text-rose-300'
    },
    {
        category: 'event',
        type: 'bpmn:EndEvent',
        eventDefinitionType: 'bpmn:TerminateEventDefinition',
        label: 'Terminate End',
        icon: 'XMarkIcon',
        description: 'Menghentikan seluruh alur kerja seketika.',
        color: 'from-red-600/10 to-zinc-600/5 hover:from-red-600/20 hover:to-zinc-600/10 border-red-600/20 dark:border-red-600/30 text-red-800 dark:text-red-300'
    }
];

const filteredElementTypes = computed(() => {
    return bpmnElementTypes.filter(item => item.category === selectedToolCategory.value);
});

const executeQuickAction = (item) => {
    if (activeToolTab.value === 'append') {
        handleAppendElement(item.type, item.eventDefinitionType);
    } else {
        handleReplaceElement(item.type, item.eventDefinitionType);
    }
};

const handleAppendElement = (targetType, eventDefType = null) => {
    if (!selectedElement.value || !modelerRef.value) return;
    
    const sourceId = selectedElement.value.id;
    const newElement = modelerRef.value.appendElement(sourceId, targetType, eventDefType);
    
    if (newElement) {
        addLog(`Tambahkan elemen baru [${newElement.id}] terhubung dari [${sourceId}]`, 'success', 'APPEND_ELEMENT', '0.015 sec');
    }
};

const handleReplaceElement = (targetType, eventDefType = null) => {
    if (!selectedElement.value || !modelerRef.value) return;
    
    const oldId = selectedElement.value.id;
    const oldConfig = actionConfigs.value[oldId] ? { ...actionConfigs.value[oldId] } : null;
    
    const newElement = modelerRef.value.replaceElement(oldId, targetType, eventDefType);
    
    if (newElement) {
        const newId = newElement.id;
        
        // Preserve DB config if any
        if (oldConfig) {
            actionConfigs.value[newId] = oldConfig;
            // Clean up old ID config
            if (newId !== oldId) {
                delete actionConfigs.value[oldId];
            }
        }
        
        addLog(`Ubah tipe elemen [${oldId}] menjadi [${newId}] (${targetType.replace('bpmn:', '')})`, 'success', 'CHANGE_TYPE', '0.010 sec');
    }
};

// Default PMO BPMN 2.0 XML Template
const DEFAULT_BPMN_TEMPLATE = `<?xml version="1.0" encoding="UTF-8"?>
<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL"
                  xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI"
                  xmlns:dc="http://www.omg.org/spec/DD/20100524/DC"
                  xmlns:di="http://www.omg.org/spec/DD/20100524/DI"
                  id="Definitions_1"
                  targetNamespace="http://bpmn.io/schema/bpmn">
  <bpmn:process id="Process_1" isExecutable="true">
    <bpmn:startEvent id="start-1" name="Mulai Pengusulan">
      <bpmn:outgoing>Flow_1</bpmn:outgoing>
    </bpmn:startEvent>
    <bpmn:userTask id="task-1" name="Drafting Piagam Proyek">
      <bpmn:incoming>Flow_1</bpmn:incoming>
      <bpmn:outgoing>Flow_2</bpmn:outgoing>
    </bpmn:userTask>
    <bpmn:userTask id="task-2" name="Tinjauan Teknis PMO">
      <bpmn:incoming>Flow_2</bpmn:incoming>
      <bpmn:outgoing>Flow_3</bpmn:outgoing>
    </bpmn:userTask>
    <bpmn:exclusiveGateway id="gateway-1" name="Apakah Disetujui PMO?">
      <bpmn:incoming>Flow_3</bpmn:incoming>
      <bpmn:outgoing>Flow_4</bpmn:outgoing>
      <bpmn:outgoing>Flow_5</bpmn:outgoing>
    </bpmn:exclusiveGateway>
    <bpmn:userTask id="task-3" name="Persetujuan Akhir &amp; Terbitkan">
      <bpmn:incoming>Flow_4</bpmn:incoming>
      <bpmn:outgoing>Flow_6</bpmn:outgoing>
    </bpmn:userTask>
    <bpmn:userTask id="task-4" name="Penundaan Inisiatif">
      <bpmn:incoming>Flow_5</bpmn:incoming>
      <bpmn:outgoing>Flow_7</bpmn:outgoing>
    </bpmn:userTask>
    <bpmn:endEvent id="end-1" name="Selesai &amp; Sinkron">
      <bpmn:incoming>Flow_6</bpmn:incoming>
      <bpmn:incoming>Flow_7</bpmn:incoming>
    </bpmn:endEvent>
    <bpmn:sequenceFlow id="Flow_1" sourceRef="start-1" targetRef="task-1" />
    <bpmn:sequenceFlow id="Flow_2" sourceRef="task-1" targetRef="task-2" />
    <bpmn:sequenceFlow id="Flow_3" sourceRef="task-2" targetRef="gateway-1" />
    <bpmn:sequenceFlow id="Flow_4" name="Ya / Disetujui" sourceRef="gateway-1" targetRef="task-3" />
    <bpmn:sequenceFlow id="Flow_5" name="Tidak / Tunda" sourceRef="gateway-1" targetRef="task-4" />
    <bpmn:sequenceFlow id="Flow_6" sourceRef="task-3" targetRef="end-1" />
    <bpmn:sequenceFlow id="Flow_7" sourceRef="task-4" targetRef="end-1" />
  </bpmn:process>
  <bpmndi:BPMNDiagram id="BPMNDiagram_1">
    <bpmndi:BPMNPlane id="BPMNPlane_1" bpmnElement="Process_1">
      <bpmndi:BPMNShape id="start-1_di" bpmnElement="start-1">
        <dc:Bounds x="156" y="102" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="132" y="145" width="85" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="task-1_di" bpmnElement="task-1">
        <dc:Bounds x="250" y="80" width="100" height="80" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="task-2_di" bpmnElement="task-2">
        <dc:Bounds x="400" y="80" width="100" height="80" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="gateway-1_di" bpmnElement="gateway-1" isMarkerVisible="true">
        <dc:Bounds x="555" y="95" width="50" height="50" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="539" y="65" width="83" height="27" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="task-3_di" bpmnElement="task-3">
        <dc:Bounds x="660" y="30" width="100" height="80" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="task-4_di" bpmnElement="task-4">
        <dc:Bounds x="660" y="150" width="100" height="80" />
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="end-1_di" bpmnElement="end-1">
        <dc:Bounds x="822" y="102" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="798" y="145" width="85" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNEdge id="Flow_1_di" bpmnElement="Flow_1">
        <di:waypoint x="192" y="120" />
        <di:waypoint x="250" y="120" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_2_di" bpmnElement="Flow_2">
        <di:waypoint x="350" y="120" />
        <di:waypoint x="400" y="120" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_3_di" bpmnElement="Flow_3">
        <di:waypoint x="500" y="120" />
        <di:waypoint x="555" y="120" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_4_di" bpmnElement="Flow_4">
        <di:waypoint x="580" y="95" />
        <di:waypoint x="580" y="70" />
        <di:waypoint x="660" y="70" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="596" y="53" width="67" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_5_di" bpmnElement="Flow_5">
        <di:waypoint x="580" y="145" />
        <di:waypoint x="580" y="190" />
        <di:waypoint x="660" y="190" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="597" y="193" width="66" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_6_di" bpmnElement="Flow_6">
        <di:waypoint x="760" y="70" />
        <di:waypoint x="840" y="70" />
        <di:waypoint x="840" y="102" />
      </bpmndi:BPMNEdge>
      <bpmndi:BPMNEdge id="Flow_7_di" bpmnElement="Flow_7">
        <di:waypoint x="760" y="190" />
        <di:waypoint x="840" y="190" />
        <di:waypoint x="840" y="138" />
      </bpmndi:BPMNEdge>
    </bpmndi:BPMNPlane>
  </bpmndi:BPMNDiagram>
</bpmn:definitions>`;

const EMPTY_BPMN_TEMPLATE = `<?xml version="1.0" encoding="UTF-8"?>
<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL"
                  xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI"
                  xmlns:dc="http://www.omg.org/spec/DD/20100524/DC"
                  xmlns:di="http://www.omg.org/spec/DD/20100524/DI"
                  id="Definitions_1"
                  targetNamespace="http://bpmn.io/schema/bpmn">
  <bpmn:process id="Process_1" isExecutable="true">
    <bpmn:startEvent id="start-event" name="Mulai">
      <bpmn:outgoing>Flow_1</bpmn:outgoing>
    </bpmn:startEvent>
    <bpmn:endEvent id="end-event" name="Selesai">
      <bpmn:incoming>Flow_1</bpmn:incoming>
    </bpmn:endEvent>
    <bpmn:sequenceFlow id="Flow_1" sourceRef="start-event" targetRef="end-event" />
  </bpmn:process>
  <bpmndi:BPMNDiagram id="BPMNDiagram_1">
    <bpmndi:BPMNPlane id="BPMNPlane_1" bpmnElement="Process_1">
      <bpmndi:BPMNShape id="start-event_di" bpmnElement="start-event">
        <dc:Bounds x="156" y="102" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="162" y="145" width="25" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="end-event_di" bpmnElement="end-event">
        <dc:Bounds x="352" y="102" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="352" y="145" width="37" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNEdge id="Flow_1_di" bpmnElement="Flow_1">
        <di:waypoint x="192" y="120" />
        <di:waypoint x="352" y="120" />
      </bpmndi:BPMNEdge>
    </bpmndi:BPMNPlane>
  </bpmndi:BPMNDiagram>
</bpmn:definitions>`;

// Modeler event callbacks
const onModelerReady = () => {
    isModelerLoaded.value = true;
};

const onElementSelected = (element) => {
    selectedElement.value = element;
    elementName.value = element.businessObject?.name || '';
};

const onElementDeselected = () => {
    selectedElement.value = null;
    elementName.value = '';
};

const onXmlChanged = () => {
    // Keeps XML in sync if needed, though we read dynamically from modeler Ref
};

// Inspector bindings
const displayType = computed(() => {
    if (!selectedElement.value) return '';
    const rawType = selectedElement.value.type;
    return rawType.replace('bpmn:', '');
});

const isTaskNode = computed(() => {
    if (!selectedElement.value) return false;
    const t = selectedElement.value.type;
    return t.includes('Task') || t === 'bpmn:UserTask' || t === 'bpmn:ServiceTask' || t === 'bpmn:ScriptTask';
});

const selectedElementConfig = computed(() => {
    if (!selectedElement.value) return {};
    const id = selectedElement.value.id;
    if (!actionConfigs.value[id]) {
        actionConfigs.value[id] = {
            action_type: 'none',
            initiative_id: null,
            status_value: 'drafting',
            custom_message: '',
        };
    }
    return actionConfigs.value[id];
});

const updateElementName = () => {
    if (selectedElement.value && modelerRef.value) {
        modelerRef.value.updateElementProperties(selectedElement.value, {
            name: elementName.value
        });
    }
};

const onActionTypeChange = () => {
    if (selectedElementConfig.value) {
        selectedElementConfig.value.initiative_id = null;
        selectedElementConfig.value.status_value = 'drafting';
        selectedElementConfig.value.custom_message = '';
    }
};

// Element deletion
const deleteSelectedElement = () => {
    if (!selectedElement.value || !modelerRef.value) return;
    const elementId = selectedElement.value.id;
    
    // Remove custom action config
    if (actionConfigs.value[elementId]) {
        delete actionConfigs.value[elementId];
    }
    
    const modeler = modelerRef.value.getModeler();
    const modeling = modeler.get('modeling');
    modeling.removeElements([selectedElement.value]);
    
    selectedElement.value = null;
    elementName.value = '';
    addLog(`Elemen [${elementId}] deleted.`, 'warning', 'DELETE_ELEMENT', '0.020 sec');
};

// DATABASE SAVING / LOADING
const createNewWorkflow = () => {
    stopSimulation();
    selectedWorkflowId.value = null;
    activeWorkflow.value = {
        id: null,
        name: 'Workflow Baru ' + new Date().toLocaleDateString(),
        description: 'Tulis deskripsi workflow di sini...',
        sop_type: null,
    };

    currentXml.value = EMPTY_BPMN_TEMPLATE;
    actionConfigs.value = {};
    addLog("Kanvas dibersihkan untuk alur kerja baru.", "info", "CANVAS_CLEAR", "0.005 sec");
};

const loadWorkflow = () => {
    stopSimulation();
    const id = selectedWorkflowId.value;
    if (!id) {
        createNewWorkflow();
        return;
    }

    const workflow = props.workflows.find(w => w.id === id);
    if (workflow) {
        activeWorkflow.value = {
            id: workflow.id,
            name: workflow.name,
            description: workflow.description || '',
            sop_type: workflow.sop_type || null,
        };
        
        // Parse node custom action configurations from flow_data
        const parsedData = workflow.flow_data || {};
        actionConfigs.value = parsedData.actionConfigs || {};
        
        // Load BPMN XML
        if (workflow.bpmn_xml) {
            currentXml.value = workflow.bpmn_xml;
            if (modelerRef.value) {
                modelerRef.value.importXml(workflow.bpmn_xml);
            }
        } else {
            // Fallback for older non-XML data
            addLog("Data XML BPMN tidak ditemukan. Memuat template standard.", "warning", "XML_MISSING", "0.010 sec");
            currentXml.value = DEFAULT_BPMN_TEMPLATE;
            if (modelerRef.value) {
                modelerRef.value.importXml(DEFAULT_BPMN_TEMPLATE);
            }
        }
        
        addLog(`Workflow "${workflow.name}" successfully loaded.`, 'success', 'DB_LOAD', '0.340 sec');
    }
};

const saveWorkflowToDb = async () => {
    if (!activeWorkflow.value.name.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Nama Kosong',
            text: 'Nama alur kerja harus diisi!',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    if (!modelerRef.value) return;

    isSaving.value = true;
    const xmlData = await modelerRef.value.getXml();

    router.post(route('bpmn-workflow.store'), {
        id: activeWorkflow.value.id,
        name: activeWorkflow.value.name,
        description: activeWorkflow.value.description,
        sop_type: activeWorkflow.value.sop_type,
        bpmn_xml: xmlData,
        flow_data: {
            actionConfigs: actionConfigs.value,
        },
        is_active: true,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isSaving.value = false;
            const hasSop = activeWorkflow.value && activeWorkflow.value.sop_type;
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: hasSop 
                    ? 'Diagram BPMN berhasil disimpan & disinkronisasikan ke dokumen SOP.' 
                    : 'Diagram alur kerja BPMN berhasil disimpan ke database.',
                timer: 2500,
                showConfirmButton: false,
            });
            addLog(`Workflow "${activeWorkflow.value.name}" successfully saved.`, 'success', 'DB_SAVE', '0.820 sec');
            
            // Reload page state to make sure props are fresh
            setTimeout(() => {
                router.reload();
            }, 100);
        },
        onError: (errors) => {
            isSaving.value = false;
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: Object.values(errors).join('\n'),
                confirmButtonColor: '#3b82f6'
            });
        }
    });
};

const autoSaveName = () => {
    if (activeWorkflow.value.id) {
        addLog(`Alur name changed to: "${activeWorkflow.value.name}"`, 'info', 'CONFIG_CHANGE', '0.010 sec');
    }
};

const isSyncing = ref(false);

const syncFromSop = async () => {
    const id = activeWorkflow.value.id;
    if (!id) {
        Swal.fire({
            icon: 'warning',
            title: 'Gagal',
            text: 'Silakan simpan workflow terlebih dahulu sebelum melakukan sinkronisasi.',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    const sopType = activeWorkflow.value.sop_type;
    if (!sopType) {
        Swal.fire({
            icon: 'warning',
            title: 'Gagal',
            text: 'Workflow ini tidak memiliki Tipe SOP Asosiasi.',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    const result = await Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Sinkronisasi dari SOP akan memperbarui diagram BPMN sesuai data langkah SOP saat ini. Desain manual Anda di kanvas mungkin akan digantikan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Sinkronkan!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    isSyncing.value = true;
    try {
        const response = await axios.post(route('bpmn-workflow.sync-from-sop', id));
        if (response.data.success && response.data.bpmn_xml) {
            currentXml.value = response.data.bpmn_xml;
            if (modelerRef.value) {
                await modelerRef.value.importXml(response.data.bpmn_xml);
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.data.message,
                timer: 2000,
                showConfirmButton: false
            });
            addLog("BPMN synchronized from SOP database successfully.", "success", "SOP_SYNC", "0.450 sec");
            
            // Reload page to refresh workflows list props
            setTimeout(() => {
                router.reload();
            }, 100);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.data.message || 'Gagal menyinkronkan data.',
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Gagal terhubung ke server.',
        });
    } finally {
        isSyncing.value = false;
    }
};

// LOCAL BPMN XML IMPORT / EXPORT
const exportBpmn = async () => {
    if (!modelerRef.value) return;
    const xml = await modelerRef.value.getXml();
    if (!xml) return;

    const dataStr = "data:text/xml;charset=utf-8," + encodeURIComponent(xml);
    
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute("href", dataStr);
    downloadAnchor.setAttribute("download", `bpmn-workflow-${activeWorkflow.value.name.replace(/\s+/g, '-').toLowerCase()}.bpmn`);
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();

    addLog("Desain diagram successfully exported as BPMN 2.0 XML.", 'success', 'FILE_EXPORT', '0.120 sec');
};

const importBpmn = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async (e) => {
        try {
            const xml = e.target.result;
            if (!xml.includes('<bpmn:definitions') && !xml.includes('<definitions')) {
                throw new Error("Format file bukan file BPMN XML yang valid!");
            }

            stopSimulation();
            activeWorkflow.value = {
                id: null, // Reset ID for newly imported flow
                name: file.name.replace(/\.[^/.]+$/, "").replace(/-/g, ' '),
                description: 'Diimpor dari berkas lokal ' + file.name,
            };

            currentXml.value = xml;
            actionConfigs.value = {}; // Reset actions configs upon XML import
            
            if (modelerRef.value) {
                await modelerRef.value.importXml(xml);
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil di-Impor!',
                text: 'Desain alur kerja BPMN berhasil di-import dari file XML!',
                timer: 2000,
                showConfirmButton: false,
            });
            addLog(`Diagram BPMN "${activeWorkflow.value.name}" successfully imported.`, 'success', 'FILE_IMPORT', '0.210 sec');
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengimpor',
                text: 'Berkas XML rusak atau format salah: ' + err.message,
                confirmButtonColor: '#3b82f6'
            });
        }
    };
    reader.readAsText(file);
};

// SIMULATION ENGINE
const isSimulating = ref(false);
const simLogs = ref([]);
const logsContainer = ref(null);
const liveTimeouts = ref([]);
const gatewayDecisionModal = ref(null);

const addLog = (text, type = 'info', action = 'INFO', duration = '0.000 sec') => {
    const time = new Date().toLocaleTimeString();
    // Strip emojis from log text
    const cleanedText = text.replace(/[\u{1F300}-\u{1F9FF}]|[\u{2700}-\u{27BF}]|[\u{2600}-\u{26FF}]|[\u{2B50}]/gu, '');
    simLogs.value.push({ 
        time, 
        text: cleanedText.trim(), 
        type, 
        action: action.toUpperCase(), 
        duration 
    });
    nextTick(() => {
        if (logsContainer.value) {
            logsContainer.value.scrollTop = logsContainer.value.scrollHeight;
        }
    });
};

const getActionBadgeClass = (type) => {
    switch (type) {
        case 'success': return 'text-emerald-600 dark:text-emerald-400';
        case 'warning': return 'text-amber-600 dark:text-amber-500';
        case 'error': return 'text-rose-600 dark:text-rose-400';
        case 'trigger': return 'text-blue-600 dark:text-blue-400';
        default: return 'text-slate-600 dark:text-zinc-400';
    }
};

const toggleSimulation = () => {
    if (isSimulating.value) {
        stopSimulation();
    } else {
        startSimulation();
    }
};

const resetSimulation = () => {
    stopSimulation();
    simLogs.value = [];
    addLog("Action output cleared. Simulator ready.", "info", "CONSOLE_RESET", "0.001 sec");
};

const stopSimulation = () => {
    isSimulating.value = false;
    gatewayDecisionModal.value = null;
    // Clear timeouts
    liveTimeouts.value.forEach(t => clearTimeout(t));
    liveTimeouts.value = [];
    
    // Reset visual highlights
    if (modelerRef.value) {
        modelerRef.value.resetHighlights();
    }
    
    addLog("Simulation stopped and element highlights reset.", "warning", "STOP_SIM", "0.050 sec");
};

const startSimulation = () => {
    if (!modelerRef.value) return;

    const modeler = modelerRef.value.getModeler();
    const elementRegistry = modeler.get('elementRegistry');
    
    // Find Start Event
    const startEvent = elementRegistry.find(el => el.type === 'bpmn:StartEvent');
    if (!startEvent) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memulai',
            text: 'Diagram alur kerja BPMN Anda harus memiliki minimal satu [Start Event]!',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }

    isSimulating.value = true;
    simLogs.value = [];
    addLog(`Starting execution for workflow: "${activeWorkflow.value.name}"`, 'success', 'START_SIM', '0.002 sec');
    if (liveExecution.value) {
        addLog("LIVE EXECUTION MODE ACTIVE - Real operations will run on database!", "error", "LIVE_WARN", "0.000 sec");
    } else {
        addLog("VISUAL SIMULATION MODE ACTIVE - Changes simulated locally.", "info", "SIM_INFO", "0.000 sec");
    }

    // Step 1: Highlight start event as active
    modelerRef.value.highlightElement(startEvent.id, 'bpmn-highlight-active');
    addLog(`Starting from Start Event: "${startEvent.businessObject?.name || 'Mulai'}"`, 'info', 'START_EVENT', '1.500 sec');

    const timeout = setTimeout(() => {
        modelerRef.value.unhighlightElement(startEvent.id, 'bpmn-highlight-active');
        modelerRef.value.highlightElement(startEvent.id, 'bpmn-highlight-completed');
        runNextSteps(startEvent.id);
    }, 1500);
    liveTimeouts.value.push(timeout);
};

const runNextSteps = (currentNodeId) => {
    if (!isSimulating.value || !modelerRef.value) return;

    const modeler = modelerRef.value.getModeler();
    const elementRegistry = modeler.get('elementRegistry');
    const currentElement = elementRegistry.get(currentNodeId);

    if (!currentElement) {
        addLog(`Element ID [${currentNodeId}] not found.`, 'error', 'NOT_FOUND', '0.000 sec');
        stopSimulation();
        return;
    }

    // Identify outgoing sequence flows
    const outgoingFlows = currentElement.businessObject?.outgoing || [];
    
    if (outgoingFlows.length === 0) {
        if (currentElement.type === 'bpmn:EndEvent') {
            modelerRef.value.highlightElement(currentNodeId, 'bpmn-highlight-completed');
            addLog(`Arrived at End Event: "${currentElement.businessObject?.name || 'Selesai'}"`, 'success', 'END_EVENT', '0.005 sec');
            addLog(`Workflow execution "${activeWorkflow.value.name}" completed successfully.`, 'success', 'SUCCESS_EXEC', '0.000 sec');
            
            Swal.fire({
                icon: 'success',
                title: 'Alur Kerja Selesai',
                text: 'Selamat! Alur kerja BPMN berhasil dijalankan sampai ke End Event!',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
            });
            isSimulating.value = false;
        } else {
            addLog(`Workflow interrupted at Node [${currentNodeId}]. No outgoing sequence flows.`, 'error', 'FLOW_ERR', '0.000 sec');
            stopSimulation();
        }
        return;
    }

    // Highlight current node as active
    modelerRef.value.highlightElement(currentNodeId, 'bpmn-highlight-active');

    // Executing Task Logic
    const isTask = currentElement.type.includes('Task') || currentElement.type === 'bpmn:UserTask' || currentElement.type === 'bpmn:ServiceTask';
    if (isTask) {
        const taskConfig = actionConfigs.value[currentNodeId] || { action_type: 'none' };
        const actionType = taskConfig.action_type || 'none';
        
        if (actionType !== 'none') {
            addLog(`Triggering automatic task [${currentElement.businessObject?.name || currentNodeId}] (Action Type: ${actionType})`, 'trigger', 'TASK_RUN', '2.000 sec');
            
            if (liveExecution.value) {
                // Live execution via database trigger endpoint
                axios.post(route('bpmn-workflow.trigger-action'), {
                    action_type: actionType,
                    initiative_id: taskConfig.initiative_id,
                    status_value: taskConfig.status_value,
                    custom_message: taskConfig.custom_message,
                    workflow_name: activeWorkflow.value.name,
                    node_label: currentElement.businessObject?.name || currentNodeId,
                }).then(response => {
                    addLog(`[LIVE DATABASE] ${response.data.message}`, 'success', 'DB_WRITE', '0.450 sec');
                }).catch(err => {
                    const errorMsg = err.response?.data?.message || err.message;
                    addLog(`[LIVE DATABASE ERROR] Failed to execute: ${errorMsg}`, 'error', 'DB_ERR', '0.420 sec');
                });
            } else {
                // Visual Simulation Mock
                if (actionType === 'update_status') {
                    const init = props.mstInitiatives.find(i => i.id === taskConfig.initiative_id);
                    const initCode = init ? init.code : 'IT-01';
                    addLog(`[SIMULASI] Initiative status ${initCode} successfully updated locally to: '${taskConfig.status_value}'`, 'success', 'MOCK_WRITE', '0.050 sec');
                } else if (actionType === 'sync_db') {
                    addLog(`[SIMULASI] Local cloud DB sync simulated.`, 'success', 'MOCK_SYNC', '0.040 sec');
                } else if (actionType === 'log_activity') {
                    addLog(`[SIMULASI] Custom audit log recorded: "${taskConfig.custom_message}"`, 'success', 'MOCK_LOG', '0.030 sec');
                }
            }
        } else {
            addLog(`Manual task execution: "${currentElement.businessObject?.name || currentNodeId}"`, 'info', 'TASK_MANUAL', '2.000 sec');
        }
    }

    // Determine path routing
    if (currentElement.type === 'bpmn:ExclusiveGateway') {
        if (outgoingFlows.length > 1) {
            addLog(`Arrived at Exclusive Gateway: "${currentElement.businessObject?.name || 'Gateway'}". Awaiting user routing decision.`, 'warning', 'GATEWAY_WAIT', '0.010 sec');
            
            const branches = outgoingFlows.map(flow => {
                const targetNode = elementRegistry.get(flow.targetRef.id);
                return {
                    edgeId: flow.id,
                    targetId: flow.targetRef.id,
                    targetLabel: targetNode?.businessObject?.name || flow.targetRef.id,
                    edgeLabel: flow.name || 'Lajur Bebas',
                };
            });

            gatewayDecisionModal.value = {
                currentNodeId: currentNodeId,
                nodeLabel: currentElement.businessObject?.name || 'Exclusive Gateway',
                branches: branches
            };
            return; // Pause traversal, wait for branch choice
        }
    }

    // Default: Auto proceed to first outgoing connection
    const nextFlow = outgoingFlows[0];
    
    // Highlight the flow connection line
    modelerRef.value.highlightElement(nextFlow.id, 'bpmn-highlight-flow');
    
    const timeout = setTimeout(() => {
        modelerRef.value.unhighlightElement(currentNodeId, 'bpmn-highlight-active');
        modelerRef.value.highlightElement(currentNodeId, 'bpmn-highlight-completed');
        runNextSteps(nextFlow.targetRef.id);
    }, 2000);
    liveTimeouts.value.push(timeout);
};

const resolveGatewayBranch = (branch) => {
    if (!isSimulating.value || !modelerRef.value) return;

    addLog(`Selected route: "${branch.edgeLabel}" leading to [${branch.targetLabel}]`, 'success', 'GATEWAY_CHOSE', '1.500 sec');
    
    // Highlight chosen path edge
    modelerRef.value.highlightElement(branch.edgeId, 'bpmn-highlight-flow');
    
    const gatewayId = gatewayDecisionModal.value.currentNodeId;
    
    // Close modal
    gatewayDecisionModal.value = null;

    // Proceed past the gateway
    const timeout = setTimeout(() => {
        modelerRef.value.unhighlightElement(gatewayId, 'bpmn-highlight-active');
        modelerRef.value.highlightElement(gatewayId, 'bpmn-highlight-completed');
        runNextSteps(branch.targetId);
    }, 1500);
    liveTimeouts.value.push(timeout);
};

// LOAD INITIAL DATA ON MOUNT
onMounted(() => {
    if (props.workflows.length > 0) {
        selectedWorkflowId.value = props.workflows[0].id;
        loadWorkflow();
    } else {
        currentXml.value = DEFAULT_BPMN_TEMPLATE;
        addLog("INFO: Pre-loaded default Approval Process Template.", "info");
    }
});
</script>

<style scoped>
.bpmn-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 105px); /* Tampilan lebih luas / full-screen */
    overflow: hidden;
    font-family: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif;
}

/* TOOLBAR */
.bpmn-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 16px;
    height: 54px;
}

.toolbar-left, .toolbar-right {
    display: flex;
    align-items: center;
    gap: 6px;
}

.workflow-info {
    display: flex;
    align-items: center;
    gap: 6px;
}

.workflow-name-input {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    background: transparent;
    border: none;
    border-bottom: 1px dashed #cbd5e1;
    padding: 2px 4px;
    width: 170px;
    height: 30px;
    transition: all 0.2s;
    text-align: center;
}

.workflow-name-input:focus {
    outline: none;
    border-bottom-color: #3b82f6;
    background: #f8fafc;
}

:deep(.dark) .workflow-name-input {
    color: #f8fafc;
    border-bottom-color: #3a3a3a;
}
:deep(.dark) .workflow-name-input:focus {
    background: #1e1e1e;
}

.workflow-badge {
    font-size: 9px;
    font-weight: 700;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}

/* WORKSPACE LAYOUT */
.bpmn-workspace {
    display: flex;
    flex: 1;
    overflow: hidden;
    position: relative;
}

/* CANVAS AREA */
.bpmn-canvas-area {
    flex: 1;
    position: relative;
    overflow: hidden;
}

/* GATEWAY DECISION MODAL OVERLAY */
.decision-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(2px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.decision-modal {
    width: 320px;
    border-radius: 12px;
    overflow: hidden;
}

/* INSPECTOR PROPERTIES */
.bpmn-inspector {
    overflow-y: auto;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.inspector-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 12px;
}

:deep(.dark) .inspector-header {
    border-bottom-color: #27272a;
}

.inspector-header h3 {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
}

:deep(.dark) .inspector-header h3 {
    color: #cbd5e1;
}

.node-type-badge {
    font-size: 9px;
    font-weight: 700;
    background: #e2e8f0;
    color: #475569;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}

:deep(.dark) .node-type-badge {
    background: #27272a;
    color: #a1a1aa;
}

.control-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    color: #1e293b;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

:deep(.dark) .form-control {
    background: #18181b;
    border-color: #27272a;
    color: #f4f4f5;
}

:deep(.dark) .form-control:focus {
    border-color: #3b82f6;
}

.inspector-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 20px;
    text-align: center;
}

/* CONSOLE PANEL (MySQL Workbench Action Output Grid Style) */
.bpmn-simulation-console {
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.console-body {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.console-controls {
    width: 120px;
    background: #f6f8fa;
    border-right: 1px solid #d0d7de;
}

:deep(.dark) .console-controls {
    background: #18181c;
    border-right-color: rgba(255, 255, 255, 0.08);
}

/* BUTTON SYSTEMS */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 34px;
    padding: 0 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
    white-space: nowrap;
}

.btn-primary {
    background: #2563eb;
    color: #ffffff;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #334155;
}

:deep(.dark) .btn-secondary {
    background: #1f1f1f;
    border-color: rgba(255, 255, 255, 0.08);
    color: #cbd5e1;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

:deep(.dark) .btn-secondary:hover {
    background: #2b2b2b;
}

.btn-emerald {
    background: #10b981;
    color: #ffffff;
}

.btn-emerald:hover {
    background: #059669;
}

.btn-danger {
    background: #f43f5e;
    color: #ffffff;
}

.btn-danger:hover {
    background: #e11d48;
}

/* ANIMATIONS */
.animate-fade-in {
    animation: fadeIn 0.25s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* FULLSCREEN STYLING OVERRIDES */
.bpmn-container.is-fullscreen {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    background: #ffffff !important;
    margin: 0 !important;
    padding: 0 !important;
}

:deep(.dark) .bpmn-container.is-fullscreen {
    background: #141416 !important;
}

/* WORKBENCH TABLE ANALOG GRID STYLING */
.console-logs {
    background-color: #ffffff;
}
:deep(.dark) .console-logs {
    background-color: #0c0c0e;
}

.workbench-table {
    width: 100%;
    border-collapse: collapse !important;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 11px;
    background-color: #ffffff;
    color: #111111;
}

:deep(.dark) .workbench-table {
    background-color: #0c0c0e;
    color: #e2e8f0;
}

.workbench-table th {
    background-color: #f1f3f4;
    color: #475569;
    font-weight: 600;
    font-size: 10px;
    padding: 4px 10px;
    border: 1px solid #d1d5db !important;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    user-select: none;
    position: sticky;
    top: 0;
    z-index: 10;
}

:deep(.dark) .workbench-table th {
    background-color: #1a1a1c;
    color: #9ca3af;
    border: 1px solid #27272a !important;
}

.workbench-table td {
    padding: 4px 10px;
    border: 1px solid #e5e7eb !important;
    height: 24px;
    line-height: 1.4;
    font-size: 11px;
}

:deep(.dark) .workbench-table td {
    border: 1px solid #27272a !important;
}

/* Zebra row striping */
.workbench-table tr:nth-child(even) {
    background-color: #f9fafb;
}
:deep(.dark) .workbench-table tr:nth-child(even) {
    background-color: #141416;
}

.workbench-table tr:hover {
    background-color: #f0f7ff !important;
}
:deep(.dark) .workbench-table tr:hover {
    background-color: #1e293b !important;
}

.workbench-action-text {
    font-weight: 700;
    font-size: 10px;
    letter-spacing: 0.02em;
}

.no-logs-msg {
    color: #94a3b8;
    background-color: #ffffff;
}
:deep(.dark) .no-logs-msg {
    color: #4b5563;
    background-color: #0c0c0e;
}
</style>
