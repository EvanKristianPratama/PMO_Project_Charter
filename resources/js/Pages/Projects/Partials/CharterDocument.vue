<template>
    <div class="bg-white shadow-xl min-h-[29.7cm] w-full max-w-[21cm] mx-auto p-8 text-sm text-slate-800 printable">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6 border-b-2 border-sky-800 pb-2">
            <h1 class="text-2xl font-bold text-slate-900">Project Charter: {{ project.name }}</h1>
            <div class="text-right">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Pertamina_Logo.svg/1024px-Pertamina_Logo.svg.png" alt="Pertamina" class="h-8 object-contain" />
            </div>
        </div>

        <!-- Top Info Bar -->
        <div class="grid grid-cols-12 gap-1 mb-2 bg-sky-700 text-white text-xs font-semibold py-1 px-2 items-center">
             <div class="col-span-2">Category</div>
             <div class="col-span-4 bg-white text-slate-900 px-2 py-0.5 mx-1">
                 <EditableText v-model="form.category" :editable="editable" placeholder="Category" />
             </div>
             <div class="col-span-1 text-right pr-2">Duration</div>
             <div class="col-span-2 bg-white text-slate-900 px-2 py-0.5 mx-1">
                 <EditableText v-model="form.duration" :editable="editable" placeholder="e.g. 2 years" />
             </div>
             <div class="col-span-1 text-right pr-2">Project Owner</div>
             <div class="col-span-2 bg-white text-slate-900 px-2 py-0.5 mx-1 truncate">
                 {{ project.owner?.name || 'Unassigned' }}
             </div>
        </div>

        <!-- Project Information Section -->
        <div class="bg-sky-600 text-white px-2 py-1 font-bold text-xs mb-1">Project information</div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- Background -->
            <div class="bg-sky-100/30 p-2 min-h-[120px]">
                <div class="font-bold text-sky-900 mb-1 border-b border-sky-200 pb-1">Background – Current gap/opportunities</div>
                <EditableArea v-model="form.background" :editable="editable" placeholder="• There's no standard guideline..." />
            </div>
            <!-- Objectives -->
            <div class="bg-sky-100/30 p-2 min-h-[120px]">
                <div class="font-bold text-sky-900 mb-1 border-b border-sky-200 pb-1">Objectives</div>
                <EditableArea v-model="form.objectives" :editable="editable" placeholder="• To define standard best practice..." />
            </div>
        </div>

        <!-- Impact & Resources Section -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- Impact -->
            <div>
                 <div class="bg-sky-600 text-white px-2 py-1 font-bold text-xs mb-1">Impact and value for Pertamina</div>
                 <div class="bg-sky-100/30 p-2 h-[calc(100%-24px)]">
                     <EditableArea v-model="form.impact_value" :editable="editable" placeholder="• A unified UI/UX enhances navigation..." />
                 </div>
            </div>
            <!-- Resources -->
            <div>
                <div class="bg-sky-600 text-white px-2 py-1 font-bold text-xs mb-1">Required resources</div>
                <div class="bg-sky-100/30 p-2 h-[calc(100%-24px)] grid grid-cols-2 gap-4">
                     <div>
                         <div class="font-bold text-xs mb-1">Key Personnel</div>
                         <EditableArea v-model="form.key_personnel" :editable="editable" placeholder="• UI/UX Designers..." />
                     </div>
                     <div>
                         <div class="font-bold text-xs mb-1">Key Items / Budget</div>
                         <EditableArea v-model="form.key_items" :editable="editable" placeholder="• Consultant..." />
                         <div class="mt-2 pt-2 border-t border-sky-200">
                             <div class="font-bold text-xs text-slate-500">Indicative Budget</div>
                             <EditableText v-model="form.budget" :editable="editable" placeholder="~3-8 mn USD" />
                         </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- Risk Section -->
        <div class="bg-sky-600 text-white px-2 py-1 font-bold text-xs mb-1">Potential risks</div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- Risk ID -->
            <div class="bg-sky-100/30 p-2 min-h-[120px]">
                <div class="font-bold text-sky-900 mb-1 border-b border-sky-200 pb-1">Risk identified</div>
                <EditableArea v-model="form.risks_identified" :editable="editable" placeholder="• Standardization may limit customization..." />
            </div>
             <!-- Mitigation -->
            <div class="bg-sky-100/30 p-2 min-h-[120px]">
                <div class="font-bold text-sky-900 mb-1 border-b border-sky-200 pb-1">Risk mitigation</div>
                <EditableArea v-model="form.risk_mitigation" :editable="editable" placeholder="• Use flexible design systems..." />
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-[10px] text-slate-400 mt-8 pt-4 border-t border-slate-200 flex justify-between">
            <div>Source: Team analysis</div>
            <div>{{ project.code }}</div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    project: { type: Object, required: true },
    form: { type: Object, required: true },
    editable: { type: Boolean, default: false },
});
</script>

<script>
import { h } from 'vue';

const EditableText = (props, { emit }) => {
    if (!props.editable) return h('span', props.modelValue || '-');
    return h('input', {
        value: props.modelValue,
        class: 'w-full bg-transparent border-0 p-0 text-inherit focus:ring-0 placeholder-slate-300',
        placeholder: props.placeholder,
        onInput: (e) => emit('update:modelValue', e.target.value)
    });
};

const EditableArea = (props, { emit }) => {
    if (!props.editable) {
        const content = props.modelValue || '';
        return h('div', { class: 'whitespace-pre-wrap' }, content || '-');
    }
    return h('textarea', {
        value: props.modelValue,
        class: 'w-full h-full bg-transparent border-0 p-0 text-inherit focus:ring-0 resize-none placeholder-slate-300 min-h-[80px]',
        placeholder: props.placeholder,
        onInput: (e) => emit('update:modelValue', e.target.value)
    });
};
</script>

<style scoped>
.printable {
    font-family: 'Inter', sans-serif;
}
@media print {
    .printable {
        box-shadow: none;
        max-width: 100%;
        margin: 0;
        padding: 0;
    }
}
</style>
