import sys
import re
import subprocess
from datetime import datetime
from collections import defaultdict

def clean_message(msg):
    # Remove prefix like feat:, fix:, chore:, fiz:
    msg = re.sub(r'^(feat|fix|chore|fiz|docs|style|refactor|test|build|ci|perf):\s*', '', msg, flags=re.IGNORECASE)
    # Remove Merge branch messages if they are too generic, or just clean them
    if msg.startswith("Merge branch"):
        # You might want to keep or skip these. PRD says "Ambil isi commit message".
        # Let's keep them but capitalize.
        pass
    if msg:
        msg = msg[0].upper() + msg[1:]
    return msg.strip()

def format_time_ampm(dt):
    # Indonesian format often uses 24h or AM/PM. PRD shows AM/PM.
    return dt.strftime("%I:%M:%S %p").lstrip('0')

def format_date_dmy(dt):
    return f"{dt.day}/{dt.month}/{dt.year}"

def get_branches_for_hash(commit_hash):
    try:
        # Get all branches containing this commit
        result = subprocess.check_output(['git', 'branch', '-a', '--contains', commit_hash], stderr=subprocess.STDOUT).decode('utf-8')
        branches = []
        for line in result.split('\n'):
            line = line.strip().replace('*', '').strip()
            if not line: continue
            if 'HEAD detached' in line: continue
            
            # Clean branch name
            if ' -> ' in line:
                line = line.split(' -> ')[-1]
            if line.startswith('remotes/origin/'):
                line = line.replace('remotes/origin/', '')
            elif line.startswith('origin/'):
                line = line.replace('origin/', '')
            elif line.startswith('remotes/'):
                line = line.replace('remotes/', '')
            
            if line and line != 'HEAD':
                branches.append(line)
        return list(set(branches))
    except:
        return []

def process_commits(log_output):
    daily_commits = defaultdict(list)
    
    lines = log_output.strip().split('\n')
    for line in lines:
        if not line: continue
        parts = line.split('|')
        if len(parts) < 4: continue
        
        raw_date = parts[0]
        msg = parts[1]
        refs = parts[2]
        commit_hash = parts[3]
        
        dt = datetime.strptime(raw_date[:19], "%Y-%m-%d %H:%M:%S")
        date_key = dt.date()
        
        branches = []
        if refs:
            ref_parts = [r.strip() for r in refs.replace('HEAD ->', '').split(',')]
            for r in ref_parts:
                b = r
                if ' -> ' in b: b = b.split(' -> ')[-1]
                if '/' in b: b = b.split('/')[-1]
                if b and b != 'HEAD':
                    branches.append(b)
        
        # If no branches in refs, or we want to be thorough
        if not branches:
            branches = get_branches_for_hash(commit_hash)
        
        daily_commits[date_key].append({
            'time': dt,
            'message': clean_message(msg),
            'branches': branches
        })

    sorted_days = sorted(daily_commits.keys())
    
    output = []
    for day in sorted_days:
        commits = sorted(daily_commits[day], key=lambda x: x['time'])
        
        unique_messages = []
        seen_messages = set()
        all_branches = set()
        
        for c in commits:
            m = c['message']
            # Merge similar messages rule (Rule 8)
            # Basic deduplication for now.
            if m.lower() not in [sm.lower() for sm in seen_messages]:
                unique_messages.append(m)
                seen_messages.add(m)
            for b in c['branches']:
                all_branches.add(b)
        
        if not all_branches:
            all_branches.add('main')

        start_time = format_time_ampm(commits[0]['time'])
        end_time = format_time_ampm(commits[-1]['time'])
        date_str = format_date_dmy(day)
        # Sort branches for consistency
        sorted_branches = sorted(list(all_branches))
        branches_str = ", ".join(sorted_branches)
        
        header_bracket = f"[{date_str} | {start_time} | {end_time} | {branches_str}]:"
        header_line = f"{date_str}    {start_time}    {end_time}    {branches_str}"
        
        output.append(header_bracket)
        output.append(header_line)
        output.append("")
        
        for i, m in enumerate(unique_messages, 1):
            output.append(f"{i}. {m}")
        
        output.append("")

    return "\n".join(output)

if __name__ == "__main__":
    log_data = sys.stdin.read()
    print(process_commits(log_data))
