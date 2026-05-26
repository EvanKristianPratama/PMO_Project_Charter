const fs = require('fs');

const files = [
    'resources/js/Pages/Policy/Guidance/Introduction/Index.vue',
    'resources/js/Pages/Policy/Guidance/General/Index.vue',
    'resources/js/Pages/Policy/Guidance/Role/Index.vue',
    'resources/js/Pages/Policy/Guidance/Closing.vue'
];

for (const file of files) {
    let text = fs.readFileSync(file, 'utf8');

    // 1. Replace the entire <section ...> to </section> before the <div class="max-w-4xl (page preview)
    const sectionMatch = text.match(/<section class="relative overflow-hidden[\s\S]*?<\/section>/);
    if (sectionMatch) {
         text = text.replace(sectionMatch[0], '<GuidancePageHeader :activeRegulation="activeRegulation" />');
    }

    // 2. Replace GuidanceChapterNavigation import
    text = text.replace(
        /import GuidanceChapterNavigation from '@\/Components\/Regulation\/GuidanceChapterNavigation\.vue';/,
        "import GuidancePageHeader from '@/Components/Regulation/GuidancePageHeader.vue';\nimport GuidanceFloatingActions from '@/Components/Regulation/GuidanceFloatingActions.vu        "import GuidancePageHeader from '@/Components/Regulatioct        "import GuidancePageHeader frn printDocument\(\) \{[\s\S]*?\n\s*}\s*\n/, '');
    text = text.replace(/function scrollToTop\(\) \{[\s\S]*?\n\s*}\s*\n/, '');

    fs.w    fs.w    fs.w   text);
}
