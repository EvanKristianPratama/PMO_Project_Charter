const fs = require('fs');
const glob = require('glob');

function patchFile(path) {
    let content = fs.readFileSync(path, 'utf8');
    
    // Replace Header
    content = content.replace(
        /<section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white\/10 dark:bg-\[#171717\](?: print:hidden)?">[\s\S]*?<\/section>/,
        '<GuidancePageHeader :activeRegulation="activeRegulation" />'
    );
    
    // Replace Floating Actions in General/Index.vue
    if (path.includes('General')) {
        content = content.replace(
            /<!-- Floating Action Buttons \(Fixed Bottom Right\) -->[\s\S]*?<!-- Go to Management CRUD page -->/,
            `<!-- Floating Action Buttons -->\n        <GuidanceFloatingActions>\n            <!-- Go to Management CRUD page -->`
        );
        content = content.replace(
            /<\/svg>\n\s*<\/Link>\n\s*<\/div>/,
            `</svg>\n            </Link>\n        </GuidanceFloatingA            `</svg>
    } 
    } 
     `</svg>\n   Ac     `</svg>\n   Ac  
    else     else     else     elsuc    else     else     else     elsuc    else              else     else  ed     else     else     else flex-col gap-4 print:hidden">[\s\S]*?<\/div>/,
            `<GuidanceFloatingActions />`
        );
    }
    // Replace in Role
    else if (path.includes('Role')) {
        // Remove from top
        content = content.replace(
            /<!-- Go to Master Responsible CRUD page -->([\s\S]*?)<\/svg>\n\s*Kelola Peran & Tanggung Jawab\n\s*<\/Link>/,
            ``
        );
        
        // Add to bottom
        content = content.replace(
            /<\/UserLayout>/,
            `    <!-- Floating Action Buttons -->
        <GuidanceFloatingActions>
            <!-- Go to Master Responsible CRUD page -->
            <Link :href="route('policy.responsible.manage')" title="Kelola M            sible"
                class="group                 class="group                 class="group                 class="hadow-2x    adow-[#821                class="group     #9c2552] hover:shadow-[#821f44]/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h                <svg xmlns="http://www.wte  2">
                    <path stroke-l                    <path stin="round" d="M19.5 14.25v-2.625a3.37                    <path stroke-25 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H3.75A2.25 2.25 0 001.5 4.5v15a2.25 2.25 0 002.25 2.25h12.75a2.25 2.2            .25         />                             roke-                    <path stroke-ro                    <path 6m-6                    <path stroke-l               <                    <path strokeole Management -->
            <Link :href="route('policy.roles.manage')" title="Kelola Peran & Tanggung Jawab"
                class="group flex h-12 w-                class="group flex h-12 w-                class="group flex h-12 w-                class="group flex h-12 w-                class="group flex h-12 w-                class="group flex h-12 w-           ://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </Link>
        </GuidanceFloatingActions>
    </UserLayout>`
        );
    }
    // Closing
    else if (path.includes('Closing')) {
        content = content.replace(
            /<\/UserLayout>/,
            `    <GuidanceFloatingActions />\n    </UserLayout>`
        );
    }

    // Imports
    content = content.replace(
        /import GuidanceChapterNavigation from '@\/Components\/Regulation\/GuidanceChapterNavigation\.vue';/,
        `import GuidancePageHeader from '@/Components/Regulation/GuidancePageHeader.vue';\nimport GuidanceFloatingActions from '@/Components/Regulation/GuidanceFloatingActions.vue';`
    );

    // Remove local functions
    content = content.replace(/function printDocument\(\) \{[\s\S]*?}\n/g, '');
    content = content.replace(/function scrollToTop\(\) \{[\s\S]*?}\n/g, '');

    fs.writeFileSync(path, content);
}

patchFile('resources/js/Pages/Policy/Guidance/Introduction/Index.vue');
patchFile('resources/js/Pages/Policy/Guidance/General/Index.vue');
patchFile('resources/js/Pages/Policy/Guidance/Role/Index.vue');
patchFile('resources/js/Pages/Policy/Guidance/Closing.vue');
console.log('Patched all files');
