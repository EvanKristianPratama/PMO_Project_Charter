<template>
    <component
        :is="embedded ? 'div' : ModulLayout"
        v-bind="embedded ? {} : { title: 'IT Ops - Operating Model' }"
    >
        <div class="space-y-4 animate-fade-in-up">
            <!-- PDF Viewer Container -->
            <div class="relative bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-2 shadow-sm">
                <!-- Loading indicator -->
                <div
                    v-if="isLoading"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/80 dark:bg-zinc-900/80 z-10"
                >
                    <svg
                        class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    <span class="mt-3 text-xs font-semibold text-slate-500 dark:text-zinc-400">
                        Memuat dokumen PDF...
                    </span>
                </div>

                <!-- PDF iframe viewer with native toolbar enabled -->
                <iframe
                    src="/frameworks/ITOpModell_RIP_final.pdf"
                    class="w-full h-[750px] border-0 rounded-none bg-slate-100 dark:bg-zinc-800"
                    @load="onIframeLoad"
                ></iframe>
            </div>
        </div>
    </component>
</template>

<script setup>
import { ref } from "vue";
import ModulLayout from "@/Layouts/ModulLayout.vue";

defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
});

const isLoading = ref(true);

const onIframeLoad = () => {
    isLoading.value = false;
};
</script>