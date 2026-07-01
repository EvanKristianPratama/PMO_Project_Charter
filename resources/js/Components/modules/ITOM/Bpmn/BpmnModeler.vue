<template>
    <div class="bpmn-modeler-wrapper" ref="wrapperRef">
        <div class="bpmn-canvas" ref="canvasRef"></div>
    </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
import BpmnModeler from 'bpmn-js/lib/Modeler';

const props = defineProps({
    bpmnXml: {
        type: String,
        default: '',
    },
});

const emit = defineEmits([
    'xml-changed',
    'element-selected',
    'element-deselected',
    'modeler-ready',
]);

const canvasRef = ref(null);
const wrapperRef = ref(null);
let modeler = null;

onMounted(async () => {
    modeler = new BpmnModeler({
        container: canvasRef.value,
        keyboard: {
            bindTo: document,
        },
    });

    // Listen for selection changes
    const eventBus = modeler.get('eventBus');

    eventBus.on('selection.changed', (e) => {
        if (e.newSelection && e.newSelection.length > 0) {
            const element = e.newSelection[0];
            emit('element-selected', element);
        } else {
            emit('element-deselected');
        }
    });

    // Listen for diagram changes
    eventBus.on('commandStack.changed', () => {
        emit('xml-changed');
    });

    // Import initial XML
    if (props.bpmnXml) {
        try {
            await modeler.importXML(props.bpmnXml);
            const canvas = modeler.get('canvas');
            canvas.zoom('fit-viewport');
        } catch (err) {
            console.error('Failed to import BPMN XML:', err);
        }
    }

    emit('modeler-ready', modeler);
});

onBeforeUnmount(() => {
    if (modeler) {
        modeler.destroy();
        modeler = null;
    }
});

// Watch for external XML changes (e.g. loading from DB)
watch(() => props.bpmnXml, async (newXml) => {
    if (modeler && newXml) {
        try {
            await modeler.importXML(newXml);
            const canvas = modeler.get('canvas');
            canvas.zoom('fit-viewport');
        } catch (err) {
            console.error('Failed to import new BPMN XML:', err);
        }
    }
});

/**
 * Export the current diagram as BPMN 2.0 XML string.
 */
const getXml = async () => {
    if (!modeler) return '';
    try {
        const result = await modeler.saveXML({ format: true });
        return result.xml;
    } catch (err) {
        console.error('Failed to export XML:', err);
        return '';
    }
};

/**
 * Import a BPMN 2.0 XML string into the modeler.
 */
const importXml = async (xml) => {
    if (!modeler) return;
    try {
        await modeler.importXML(xml);
        const canvas = modeler.get('canvas');
        canvas.zoom('fit-viewport');
    } catch (err) {
        console.error('Failed to import XML:', err);
    }
};

/**
 * Export the current diagram as SVG.
 */
const getSvg = async () => {
    if (!modeler) return '';
    try {
        const result = await modeler.saveSVG();
        return result.svg;
    } catch (err) {
        console.error('Failed to export SVG:', err);
        return '';
    }
};

/**
 * Highlight a specific BPMN element by adding a CSS marker class.
 */
const highlightElement = (elementId, markerClass = 'bpmn-highlight-active') => {
    if (!modeler) return;
    try {
        const canvas = modeler.get('canvas');
        canvas.addMarker(elementId, markerClass);
    } catch (err) {
        // Element might not exist
    }
};

/**
 * Remove highlight from a specific element.
 */
const unhighlightElement = (elementId, markerClass = 'bpmn-highlight-active') => {
    if (!modeler) return;
    try {
        const canvas = modeler.get('canvas');
        canvas.removeMarker(elementId, markerClass);
    } catch (err) {
        // Element might not exist
    }
};

/**
 * Remove all highlight markers from all elements.
 */
const resetHighlights = () => {
    if (!modeler) return;
    try {
        const elementRegistry = modeler.get('elementRegistry');
        const canvas = modeler.get('canvas');
        elementRegistry.forEach((element) => {
            canvas.removeMarker(element.id, 'bpmn-highlight-active');
            canvas.removeMarker(element.id, 'bpmn-highlight-completed');
            canvas.removeMarker(element.id, 'bpmn-highlight-flow');
        });
    } catch (err) {
        // Ignore
    }
};

/**
 * Get the raw bpmn-js modeler instance for advanced operations.
 */
const getModeler = () => modeler;

/**
 * Update a BPMN element's properties (name, etc.) via the modeling API.
 */
const updateElementProperties = (element, properties) => {
    if (!modeler) return;
    const modeling = modeler.get('modeling');
    modeling.updateProperties(element, properties);
};

/**
 * Get all flow elements from the current process.
 */
const getProcessElements = () => {
    if (!modeler) return [];
    const elementRegistry = modeler.get('elementRegistry');
    return elementRegistry.filter((el) => {
        return el.type !== 'label' && el.type !== 'bpmn:Collaboration' && el.type !== 'bpmn:Participant';
    });
};

/**
 * Zoom to fit the entire diagram viewport.
 */
const zoomToFit = () => {
    if (!modeler) return;
    const canvas = modeler.get('canvas');
    canvas.zoom('fit-viewport');
};

/**
 * Append a new BPMN element connected to the source element.
 * Automatically handles sizing and horizontal spacing coordinates.
 */
const appendElement = (sourceId, targetType, targetEventDefinitionType = null) => {
    if (!modeler) return null;
    try {
        const modeling = modeler.get('modeling');
        const elementRegistry = modeler.get('elementRegistry');
        const elementFactory = modeler.get('elementFactory');
        const moddle = modeler.get('moddle');
        
        const sourceElement = elementRegistry.get(sourceId);
        if (!sourceElement) return null;
        
        // Define sizes based on type
        let width = 100;
        let height = 80;
        
        if (targetType.includes('Gateway')) {
            width = 50;
            height = 50;
        } else if (targetType.includes('Event')) {
            width = 36;
            height = 36;
        }
        
        // Target coordinates relative to the source shape center
        const sourceWidth = sourceElement.width || 0;
        const sourceHeight = sourceElement.height || 0;
        
        const targetX = sourceElement.x + sourceWidth + 140;
        const targetY = sourceElement.y + (sourceHeight / 2) - (height / 2);
        
        // Create standard business object and shape
        const shapeConfig = { type: targetType };
        const newShape = elementFactory.createShape(shapeConfig);
        
        // If event definition is provided, attach it to the business object
        if (targetEventDefinitionType) {
            const eventDefinition = moddle.create(targetEventDefinitionType);
            newShape.businessObject.eventDefinitions = [eventDefinition];
            eventDefinition.$parent = newShape.businessObject;
        }
        
        // Append shape and let bpmn-js create the sequence flow automatically
        const resultShape = modeling.appendShape(sourceElement, newShape, { 
            x: targetX + (width / 2), 
            y: targetY + (height / 2) 
        });
        
        // Update selection to the new element
        const selection = modeler.get('selection');
        selection.select(resultShape);
        
        return resultShape;
    } catch (err) {
        console.error('Failed to append BPMN element:', err);
        return null;
    }
};

/**
 * Replace a BPMN element with another type (e.g. changing event type or task type).
 */
const replaceElement = (elementId, targetType, targetEventDefinitionType = null) => {
    if (!modeler) return null;
    try {
        const bpmnReplace = modeler.get('bpmnReplace');
        const elementRegistry = modeler.get('elementRegistry');
        const element = elementRegistry.get(elementId);
        
        if (!element) return null;
        
        const replaceOptions = {
            type: targetType
        };
        
        if (targetEventDefinitionType) {
            replaceOptions.eventDefinitionType = targetEventDefinitionType;
        }
        
        const newElement = bpmnReplace.replaceElement(element, replaceOptions);
        
        // Update selection to the replaced element
        if (newElement) {
            const selection = modeler.get('selection');
            selection.select(newElement);
        }
        
        return newElement;
    } catch (err) {
        console.error('Failed to replace BPMN element:', err);
        return null;
    }
};

defineExpose({
    getXml,
    importXml,
    getSvg,
    highlightElement,
    unhighlightElement,
    resetHighlights,
    getModeler,
    updateElementProperties,
    getProcessElements,
    zoomToFit,
    appendElement,
    replaceElement,
});
</script>

<style scoped>
.bpmn-modeler-wrapper {
    width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.bpmn-canvas {
    width: 100%;
    height: 100%;
}
</style>

<style>
/* ─── bpmn-js base styles ─── */
@import 'bpmn-js/dist/assets/diagram-js.css';
@import 'bpmn-js/dist/assets/bpmn-js.css';
@import 'bpmn-js/dist/assets/bpmn-font/css/bpmn-embedded.css';

/* ─── Simulation highlight markers ─── */
.bpmn-highlight-active .djs-visual > :nth-child(1) {
    stroke: #2563eb !important;
    stroke-width: 3px !important;
    fill: rgba(37, 99, 235, 0.08) !important;
}

.bpmn-highlight-active .djs-outline {
    fill: none !important;
    stroke: #3b82f6 !important;
    stroke-width: 2px !important;
    stroke-dasharray: 4 3 !important;
    visibility: visible !important;
    shape-rendering: geometricPrecision !important;
    opacity: 0.7;
}

.bpmn-highlight-completed .djs-visual > :nth-child(1) {
    stroke: #16a34a !important;
    stroke-width: 2.5px !important;
    fill: rgba(22, 163, 74, 0.06) !important;
}

.bpmn-highlight-flow .djs-visual > path {
    stroke: #3b82f6 !important;
    stroke-width: 3px !important;
}

.bpmn-highlight-flow .djs-visual > polygon {
    fill: #3b82f6 !important;
    stroke: #3b82f6 !important;
}

/* ─── Dark mode overrides for bpmn-js ─── */
.dark .bjs-container {
    background: #0c0c0e !important;
}

.dark .djs-palette {
    background: #141414 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.dark .djs-palette .entry {
    color: #a1a1aa !important;
}

.dark .djs-palette .entry:hover {
    color: #e4e4e7 !important;
}

.dark .djs-context-pad .entry {
    background: #1e1e20 !important;
    color: #a1a1aa !important;
}

.dark .djs-context-pad .entry:hover {
    background: #27272a !important;
    color: #e4e4e7 !important;
}

.dark .djs-popup {
    background: #1e1e20 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e4e4e7 !important;
}

.dark .djs-popup .entry:hover {
    background: #27272a !important;
}

.dark .djs-visual > text {
    fill: #d4d4d8 !important;
}

.dark .djs-visual > :nth-child(1) {
    fill: #1a1a1c !important;
    stroke: #52525b !important;
}

/* Dark mode for sequence flows */
.dark .djs-connection .djs-visual > path {
    stroke: #71717a !important;
}
.dark .djs-connection .djs-visual > polygon {
    fill: #71717a !important;
    stroke: #71717a !important;
}

/* Grid dot pattern in dark mode */
.dark .djs-grid-line {
    stroke: rgba(255, 255, 255, 0.06) !important;
}
</style>
