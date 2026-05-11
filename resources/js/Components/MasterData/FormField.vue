<template>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <slot />
        <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>
</template>

<script setup>
defineProps({
    label:    { type: String, required: true },
    error:    { type: String, default: null },
    required: { type: Boolean, default: false },
});
</script>
