# COBIT Roles RACI Matrix Integration Documentation

This document explains how to integrate the newly created public GET API into other web applications and provides a complete, styled HTML/CSS/JS component to render the RACI matrix exactly like the reference screenshot.

---

## 📡 API Specification

### Endpoint
```http
GET https://cobit2019.divusi.co.id/api/cobit/roles-matrix
```

### Query Parameters
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `objective_id` | `string` | No | Filters the practices by objective (e.g. `EDM03`, `APO01`, etc.) |

---

## 🎨 Premium Table Frontend Code (Single File HTML Template)

Here is a fully functional, self-contained HTML file. You can save this as `index.html` and run it in any browser. It fetches the matrix data from your API and renders it with the exact design and styles from your screenshot:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBIT 2019 RACI Matrix</title>
    <!-- Import Google Font Outfit & Inter for premium modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-bg: #f8fafc;
            --header-bg: #5a6b7c; /* Premium dark grey header from your screenshot */
            --header-text: #ffffff;
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --row-even: #f8fafc;
            --row-odd: #ffffff;
            --cell-hover: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-dark);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .matrix-container {
            width: 100%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        /* Banner Header matching the screenshot style */
        .matrix-banner {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 16px 24px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        /* Row heights and border styles */
        th, td {
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        /* Styling for the first column (Key Management Practices) */
        .practice-col {
            font-weight: 500;
            color: var(--text-dark);
            width: 50%; /* Gives the practice text generous room */
            min-width: 300px;
        }

        /* Styling for the Role Headers (Rotated Vertically) */
        .role-header {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--text-dark);
            text-align: center;
            vertical-align: bottom;
            padding: 24px 8px 12px 8px;
            width: 60px;
            min-width: 60px;
            max-width: 60px;
        }

        .role-header span {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        /* Center-align RACI cells and highlight values */
        .raci-cell {
            text-align: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            width: 60px;
            min-width: 60px;
        }

        /* Coloring the letters specifically for RACI */
        .raci-cell.val-a {
            color: #3b82f6; /* Accountable - Royal Blue */
        }

        .raci-cell.val-r {
            color: #10b981; /* Responsible - Emerald Green */
        }

        .raci-cell.val-c {
            color: #f59e0b; /* Consulted - Amber Gold */
        }

        .raci-cell.val-i {
            color: #8b5cf6; /* Informed - Violet */
        }

        /* Zebra Striping */
        tbody tr:nth-child(odd) {
            background-color: var(--row-odd);
        }

        tbody tr:nth-child(even) {
            background-color: var(--row-even);
        }

        /* Micro-animations and Hover Effects */
        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: var(--cell-hover);
        }

        /* Loading / Error States */
        .status-message {
            padding: 40px;
            text-align: center;
            font-family: 'Outfit', sans-serif;
            color: var(--text-muted);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<div class="matrix-container">
    <!-- Top banner title -->
    <div id="banner-title" class="matrix-banner">
        Loading RACI Matrix...
    </div>

    <div class="table-responsive">
        <div id="loading" class="status-message">Fetching COBIT Roles Data...</div>
        <table id="matrix-table" style="display: none;">
            <thead>
                <tr id="header-row">
                    <th class="practice-col">Key Management Practice</th>
                    <!-- Dynamic role headers will be injected here -->
                </tr>
            </thead>
            <tbody id="matrix-body">
                <!-- Dynamic rows will be injected here -->
            </tbody>
        </table>
    </div>
</div>

<script>
    // Configuration: point this to your backend server URL
    const API_URL = 'https://cobit2019.divusi.co.id/api/cobit/roles-matrix?objective_id=EDM03';

    async function loadRACIMatrix() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            if (!data.success || !data.matrix.length) {
                showStatus('No matrix data found.');
                return;
            }

            // 1. Update Banner Title dynamically
            const firstRow = data.matrix[0];
            document.getElementById('banner-title').innerText = 
                `B. Component: Organizational Structures for ${firstRow.objective_id} — ${firstRow.objective_name}`;

            // 2. Render Headers (Roles)
            const headerRow = document.getElementById('header-row');
            const roles = data.roles;
            
            roles.forEach(role => {
                const th = document.createElement('th');
                th.className = 'role-header';
                th.innerHTML = `<span>${role.role_name}</span>`;
                headerRow.appendChild(th);
            });

            // 3. Render Rows (Practices & RACI Values)
            const matrixBody = document.getElementById('matrix-body');
            const practices = data.matrix;

            practices.forEach(row => {
                const tr = document.createElement('tr');
                
                // Practice Name Cell
                const tdPractice = document.createElement('td');
                tdPractice.className = 'practice-col';
                tdPractice.innerHTML = `<strong>${row.practice_id}</strong> ${row.practice_name}`;
                tr.appendChild(tdPractice);

                // Add cell for each role
                roles.forEach(role => {
                    const tdRaci = document.createElement('td');
                    tdRaci.className = 'raci-cell';
                    
                    const value = row.role_assignments[role.role_id] || '';
                    tdRaci.innerText = value;
                    
                    // Assign class based on RACI values for text styling
                    if (value) {
                        tdRaci.classList.add(`val-${value.toLowerCase()}`);
                    }
                    
                    tr.appendChild(tdRaci);
                });

                matrixBody.appendChild(tr);
            });

            // Hide loading message and show table
            document.getElementById('loading').style.display = 'none';
            document.getElementById('matrix-table').style.display = 'table';

        } catch (error) {
            console.error('Error fetching data:', error);
            showStatus('Failed to load RACI matrix data. Make sure the server is online and accessible.');
        }
    }

    function showStatus(msg) {
        const loadingDiv = document.getElementById('loading');
        loadingDiv.innerText = msg;
        loadingDiv.style.color = '#ef4444'; // Red color for errors
    }

    // Initialize
    loadRACIMatrix();
</script>

</body>
</html>
```

---

## 🛠️ Key CSS Explanation for "Rotated Vertical Headers"

To achieve the beautiful **vertical text column layout** as seen in the COBIT screenshot, we use the CSS `writing-mode` property:

```css
.role-header span {
    writing-mode: vertical-rl; /* Writes text from top to bottom, right to left */
    transform: rotate(180deg);  /* Flips the text so it reads bottom-to-top naturally */
    white-space: nowrap;        /* Disables line breaking */
    display: inline-block;
}
```

This guarantees that the table is compact, matching standard executive COBIT reports perfectly.
