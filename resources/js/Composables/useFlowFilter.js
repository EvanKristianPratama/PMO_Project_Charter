import { ref, computed } from 'vue';

/**
 * Composable to handle filtering a list based on an active status flow selection.
 *
 * @param {Function} getItems - A getter function that returns the full list of items (e.g., `() => props.initiatives`).
 * @param {Function} statusIdGetter - A function to extract the status ID from an item. Defaults to `(item) => item.status`.
 * @returns {Object} - Contains `activeFlowFilter`, `filteredItems`, and `toggleFilter`.
 */
export function useFlowFilter(getItems, statusIdGetter = (item) => item.status) {
    const activeFlowFilter = ref(null);

    const toggleFilter = (statusId) => {
        if (activeFlowFilter.value === statusId) {
            activeFlowFilter.value = null; // Toggle off if already selected
        } else {
            activeFlowFilter.value = statusId; // Select the new filter
        }
    };

    const filteredItems = computed(() => {
        const items = getItems() || [];

        if (activeFlowFilter.value === null) {
            return []; // At the beginning it should be empty
        }

        return items.filter((item) => {
            const itemStatus = statusIdGetter(item);
            return Number(itemStatus) === Number(activeFlowFilter.value);
        });
    });

    return {
        activeFlowFilter,
        filteredItems,
        toggleFilter,
    };
}
