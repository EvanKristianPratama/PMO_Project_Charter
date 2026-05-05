<template>
  <div class="rounded-lg bg-white shadow-sm dark:bg-[#171717] overflow-hidden">
    <div class="overflow-x-auto">
      
      <table class="w-full text-sm text-left border-collapse border border-slate-200 dark:border-slate-700/50">
        <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400">
          <tr>
            <th scope="col" class="px-4 py-3 w-24 text-center border border-slate-200 dark:border-slate-700/50">No</th>
            <th scope="col" class="px-4 py-3 border border-slate-200 dark:border-slate-700/50">KBUMN Mission</th>
            <th scope="col" class="px-4 py-3 border border-slate-200 dark:border-slate-700/50">Prioritas Inisiatif Strategis Perusahaan</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(m, idx) in missions" :key="m.code ?? idx" class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
            <td class="px-4 py-3 text-center border border-slate-200 dark:border-slate-700/50 align-top">
              <div class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-semibold">
                {{ m.code ?? (idx + 1) }}
              </div>
            </td>
            <td class="px-4 py-3 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700/50 align-top">
              {{ m.name }}
            </td>
            <td class="px-4 py-3 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700/50 align-top">
              <ul v-if="m.prioritas_strategy && m.prioritas_strategy.length > 0" class="list-disc list-outside ml-4 space-y-1">
                <li v-for="strategy in m.prioritas_strategy" :key="strategy.id">
                  <span class="font-semibold text-sky-700 dark:text-sky-400 mr-1">{{ strategy.code }}</span> {{ strategy.strategy }}
                </li>
              </ul>
              <span v-else class="text-slate-400 italic">Belum ada prioritas strategis.</span>
            </td>
          </tr>
          <tr v-if="!missions || Object.keys(missions).length === 0">
            <td colspan="3" class="px-4 py-4 text-center text-slate-500 border border-slate-200 dark:border-slate-700/50">
              Belum ada misi BUMN.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  missions: { type: [Array, Object], default: () => [] },
  title: { type: String, default: 'Misi BUMN' },
});
</script>

<style scoped>
/* intentionally minimal — styling handled via utility classes */
</style>
