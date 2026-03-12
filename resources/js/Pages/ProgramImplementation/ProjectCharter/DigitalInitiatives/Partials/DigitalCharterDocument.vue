<script setup>
const props = defineProps({
    initiative: { type: Object, required: true },
});

const displayValue = (value) => {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' ? '-' : trimmed;
};

const statusLabel = () => {
    const statusRef = props.initiative?.statusRef?.name;
    return displayValue(statusRef ?? props.initiative?.status);
};
</script>

<template>
    <article class="mx-auto w-full max-w-5xl bg-white text-slate-900 shadow-sm print:shadow-none">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-lg font-bold text-slate-900">
                    Digital Initiative: {{ displayValue(initiative.no) }}
                </h1>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                    {{ statusLabel() }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-600">
                {{ displayValue(initiative.useCase || initiative.desc) }}
            </p>
        </div>

        <div class="grid gap-6 px-6 py-5 md:grid-cols-2">
            <section class="space-y-3">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">General Information</h2>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Type</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.type) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Tipe Inisiatif</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.tipe_inisiative) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Project Owner</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.projectOwner) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Use Case</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.useCase) }}</span>
                    </div>
                </div>
            </section>

            <section class="space-y-3">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Details & Classification</h2>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Value</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.value) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Urgency</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.urgency) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">RJJP</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.rjjp) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">COE</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.coe) }}</span>
                    </div>
                </div>
            </section>
        </div>

        <div class="px-6 pb-6">
            <section class="space-y-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">SC Implementation Status</h2>
                <div class="grid gap-2 text-sm md:grid-cols-2">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Status SC</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.sc_status) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Review SC</span>
                        <span class="font-medium text-slate-800">{{ displayValue(initiative.sc_review_status) }}</span>
                    </div>
                </div>
            </section>
        </div>
    </article>
</template>
