const BADGE_CLASS_BY_STATUS_ID = {
    1: 'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300',
    2: 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400',
    3: 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400',
    4: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400',
    5: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400',
};

const FLOW_CLASS_BY_INDEX = [
    {
        circleClass: 'border-slate-400 bg-slate-500 text-white',
        lineClass: 'bg-blue-300 dark:bg-blue-500/40',
    },
    {
        circleClass: 'border-blue-500 bg-blue-500 text-white',
        lineClass: 'bg-amber-300 dark:bg-amber-500/40',
    },
    {
        circleClass: 'border-amber-500 bg-amber-500 text-white',
        lineClass: 'bg-emerald-300 dark:bg-emerald-500/40',
    },
    {
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
    {
        circleClass: 'border-emerald-500 bg-emerald-500 text-white',
        lineClass: 'bg-slate-200 dark:bg-white/10',
    },
];

export const normalizeStatusId = (status) => {
    const parsed = Number(status);

    return Number.isInteger(parsed) ? parsed : null;
};

export const resolveStatusOption = (status, statusOptions = []) => {
    const statusId = normalizeStatusId(status);

    if (statusId === null) {
        return null;
    }

    return statusOptions.find((item) => Number(item?.id) === statusId) ?? null;
};

export const statusLabelFromOptions = (status, statusOptions = []) => {
    const option = resolveStatusOption(status, statusOptions);

    if (option?.label) {
        return option.label;
    }

    if (option?.name) {
        return String(option.name)
            .replace(/_/g, ' ')
            .replace(/\b\w/g, (char) => char.toUpperCase());
    }

    return 'Drafting';
};

export const statusBadgeClassById = (status) => {
    const statusId = normalizeStatusId(status);

    return BADGE_CLASS_BY_STATUS_ID[statusId] ?? BADGE_CLASS_BY_STATUS_ID[1];
};

export const statusFlowClassByIndex = (index) => {
    return FLOW_CLASS_BY_INDEX[index] ?? FLOW_CLASS_BY_INDEX[FLOW_CLASS_BY_INDEX.length - 1];
};
