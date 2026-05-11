<template>
  <div v-if="show" class="flex border-b border-[#3b82f6] last:border-0">
    <!-- Section 1: Label & Data (Left) -->
    <div class="flex flex-1 border-r border-[#3b82f6]">
      <div class="bar-sub-mini flex items-center shrink-0 min-w-[130px] justify-center border-r border-[#3b82f6] relative">
        <span v-if="isFirst && !hideLabelText">Status Review</span>
        
        <!-- Visual merge hack: cover the bottom border if not the last row -->
        <div v-if="!isLast && !hideLabelText" class="absolute -bottom-[1px] left-0 right-0 h-[1.5px] bg-[#2e6ea2] z-10"></div>
      </div>
      <div class="panel-body-mini flex items-center flex-1 justify-start px-4 text-left gap-2">
        <span class="font-medium text-slate-600 shrink-0">{{ statusLabel }}</span>
        <span v-if="statusValue && statusValue !== '-'" 
            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wider shadow-sm"
            :class="getStatusClass(statusValue)">
            {{ statusValue }}
        </span>
      </div>
    </div>

    <!-- Section 2: Mini Roadmap Grid (Middle) -->
    <div class="panel-body-mini flex-1 p-0 overflow-hidden relative min-h-[32px]">
        <!-- Mini Roadmap Grid (Only if expanded) -->
        <div v-if="isRoadmapExpanded" class="absolute inset-0 flex">
          <div v-for="year in roadmapYears" :key="year"
               class="flex-1 border-r-[#3b82f6] border-r-[1.5px] last:border-0 flex flex-col">
            <div class="flex-1 flex">
              <div v-for="q in 4" :key="`q-${year}-${q}`" class="flex-1 border-r border-slate-100 last:border-0"></div>
            </div>
          </div>
        </div>

        <!-- Markers (Only if expanded) -->
        <div v-if="isRoadmapExpanded" class="absolute inset-0 flex items-center">
          <div class="relative w-full h-2">
            <template v-if="markers && markers.length > 0">
                <div v-for="(marker, idx) in markers" 
                    :key="idx"
                    :title="'Status Review: ' + marker.label + ' (' + marker.status + ')'"
                    class="absolute border-2 border-white shadow"
                    :style="getMarkerStyle(marker.left, marker.color)">
                </div>
            </template>
          </div>
        </div>
    </div>
      
    <!-- Section 3: Status Updated (Right - Outside Year Structure) -->
    <div class="panel-body-mini w-[300px] border-l border-[#3b82f6] flex items-center font-medium bg-slate-50/50 py-2">
        <span class="break-words w-full">{{ statusUpdated }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: { type: Boolean, default: true },
  isRoadmapExpanded: { type: Boolean, default: false },
  roadmapYears: { type: Array, default: () => [] },
  markers: { type: Array, default: () => [] },
  statusLabel: { type: String, default: '' },
  statusValue: { type: String, default: '-' },
  statusUpdated: { type: String, default: '-' },
  isFirst: { type: Boolean, default: true },
  isLast: { type: Boolean, default: true },
  hideLabelText: { type: Boolean, default: false },
});

const getStatusClass = (status) => {
  const s = String(status || '').toLowerCase();
  if (s.includes('done')) return 'bg-emerald-500 text-white';
  if (s.includes('progress')) return 'bg-blue-500 text-white';
  if (s.includes('review')) return 'bg-orange-500 text-white';
  return 'bg-slate-500 text-white';
};

const getMarkerStyle = (left, color) => ({
  left: left || '0%',
  backgroundColor: color || '#1e4f8f',
  top: '50%',
  transform: 'translate(-50%, -50%) rotate(45deg)',
  borderRadius: '1px',
  boxShadow: '0 0 0 1.5px #ffffff, 0 0 0 2px #1e4f8f26',
  width: '7px',
  height: '7px',
});
</script>

<style scoped>
.bar-sub-mini {
  background: #2e6ea2;
  color: #fff;
  padding: 3px 12px;
  font-size: 11px;
  line-height: 1;
  text-align: center;
  min-height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.panel-body-mini {
  padding: 3px 12px;
  background: #fff;
  min-height: 32px;
  display: flex;
  align-items: center;
  font-size: 12px;
  color: #0f172a;
}
</style>
