import { execSync } from 'child_process';
import fs from 'fs';

/**
 * Script to generate activity recap from Git commits.
 * Based on docs/prd/PRD_Rekap_Aktivitas.md
 */

const author = "jethroGIT";
const since = "2026-02-11T00:00:00";
const until = "2026-02-14T23:59:59";

function getCommits() {
    const format = "%H|%ai|%s";
    const cmd = `git log --all --author="${author}" --since="${since}" --until="${until}" --pretty=format:"${format}"`;
    try {
        const output = execSync(cmd).toString().replace(/\r/g, '').trim();
        if (!output) return [];
        return output.split('\n').map(line => {
            const [hash, date, message] = line.split('|');
            return { hash, date, message: message.trim() };
        });
    } catch (e) {
        return [];
    }
}

function getBranchesForCommit(hash) {
    // Only local branches to keep it cleaner
    const cmd = `git branch --contains ${hash}`;
    try {
        const output = execSync(cmd).toString().replace(/\r/g, '').trim();
        if (!output) return [];
        const branches = output.split('\n').map(line => {
            let branch = line.replace('*', '').trim();
            if (branch.includes(' -> ')) {
                branch = branch.split(' -> ')[1];
            }
            return branch;
        }).filter(b => b !== 'HEAD' && !b.includes('HEAD') && !b.includes('('));
        
        return Array.from(new Set(branches));
    } catch (e) {
        return [];
    }
}

function cleanMessage(msg) {
    if (msg.toLowerCase().startsWith('merge ')) return msg;
    // Remove prefixes like "fix:", "feat:", etc.
    let cleaned = msg.replace(/^(fix|feat|chore|docs|style|refactor|perf|test|build|ci|revert):\s*/i, '');
    // Capitalize first letter
    cleaned = cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
    return cleaned.trim();
}

function formatToDate(dateObj) {
    const d = new Date(dateObj);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

function formatToTime(dateObj) {
    const d = new Date(dateObj);
    return d.toLocaleTimeString('en-US', { hour12: true, hour: 'numeric', minute: '2-digit', second: '2-digit' });
}

const commits = getCommits();
const dailyData = {};

if (commits.length === 0) {
    console.log(`No commits found for author "${author}" in range ${since} to ${until}`);
    process.exit(0);
}

const sortedCommits = commits.sort((a, b) => new Date(a.date) - new Date(b.date));

sortedCommits.forEach(commit => {
    const d = new Date(commit.date);
    const dateStr = formatToDate(d);
    
    if (!dailyData[dateStr]) {
        dailyData[dateStr] = {
            commits: [],
            branches: new Set(),
            firstCommitDate: d,
            lastCommitDate: d
        };
    }
    
    const branches = getBranchesForCommit(commit.hash);
    branches.forEach(b => dailyData[dateStr].branches.add(b));
    
    const cleanedMsg = cleanMessage(commit.message);
    
    // Add to commits if not "similar" to existing ones (keep longer version)
    let isSimilar = false;
    for (let i = 0; i < dailyData[dateStr].commits.length; i++) {
        let existing = dailyData[dateStr].commits[i];
        let existingLower = existing.toLowerCase();
        let newLower = cleanedMsg.toLowerCase();
        
        if (newLower.includes(existingLower)) {
            dailyData[dateStr].commits[i] = cleanedMsg;
            isSimilar = true;
            break;
        } else if (existingLower.includes(newLower)) {
            isSimilar = true;
            break;
        }
    }

    if (!isSimilar) {
        dailyData[dateStr].commits.push(cleanedMsg);
    }
    
    if (d < dailyData[dateStr].firstCommitDate) dailyData[dateStr].firstCommitDate = d;
    if (d > dailyData[dateStr].lastCommitDate) dailyData[dateStr].lastCommitDate = d;
});

let finalOutput = '';
const dateKeys = Object.keys(dailyData).sort((a, b) => {
    const partsA = a.split('/').map(Number);
    const partsB = b.split('/').map(Number);
    return new Date(partsA[2], partsA[1]-1, partsA[0]) - new Date(partsB[2], partsB[1]-1, partsB[0]);
});

dateKeys.forEach(date => {
    const data = dailyData[date];
    const branchesArr = Array.from(data.branches).sort();
    const displayBranches = branchesArr.join(', ');
    
    const startTime = formatToTime(data.firstCommitDate);
    const endTime = formatToTime(data.lastCommitDate);
    
    finalOutput += `[${date} | ${startTime} | ${endTime} | ${displayBranches}]:\n`;
    finalOutput += `${date}    ${startTime}    ${endTime}    ${displayBranches}\n\n`;
    
    data.commits.forEach((msg, index) => {
        finalOutput += `${index + 1}. ${msg}\n`;
    });
    finalOutput += '\n';
});

console.log(finalOutput);
fs.writeFileSync('rekap_aktivitas.txt', finalOutput);
console.log('Report saved to rekap_aktivitas.txt');
