const fs = require('fs');

const path = 'resources/js/Pages/Policy/Guidance/General/Index.vue';
let content = fs.readFileSync(path, 'utf8');

// Header
content = content.replace(
    /<section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white\/10 dark:bg-\[#171717\] print:hidden">[\s\S]*?<\/section>/,
    '<GuidancePageHeader :activeRegulation="activeRegulation" />'
);

// Floating Actions
content = content.replace(
    /<!-- Floating Action Buttons \(Fixed Bottom Right\) -->[\s\S]*?<!-- Go to Management CRUD page -->/,
    `<!-- Floating Action Buttons -->
        <GuidanceFloatingActions>
            <!-- Go to Management CRUD page -->`
);

content = content.replace(
    /Kelola Kebijakan Khusus"\n\s*class="group flex[\s\S]*?<\/svg>\n\s*<\/Link>\n\s*<\/div>/,
    `Kelola Kebijakan Khusus"
                class="group flex h-12 w-12 items-center justify-center rounded-full bg-[#ffc107] text-[#821f44] shadow-2xl shadow                class="group flov                class="ghadow-yellow-500/40 active:scale-95">
                <svg xmlns="http:/                <svg xmlns="http:/        "0 0    24" st               " stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">                    
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1                    <path stroke-linecap="round" stroke-linejoin1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1            5.2                    <path stroke-li                       <path stroknce                    <path stroke-linecap="round(
                             vigation from '@\/Components\/Regulation\/GuidanceChapterNavigation\.vue';/,
    `import Guidanc    `import Guidanc    `impots    `import Guidanc    `import Guidanc    `impots    `import Guidanc    `import Guidanc    `impots    `import Guidanc    `import Guidanc    `impots    `import Guidanc    `import Guidanc    `impots    `import Guidanc    `imp[\s\S]*?}\n/, '');
content = content.replace(/function scrollToTop\(\) \{[\s\S]*?}\n/, '');

fs.writeFileSync(path, content);
