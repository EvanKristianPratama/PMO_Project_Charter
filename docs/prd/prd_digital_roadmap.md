# PRD: Digital Initiative Roadmap Index Page

## 1. Overview
Halaman ini bertujuan untuk menampilkan roadmap milestone digital initiatives dalam bentuk visual timeline seperti pada sheet “Digital Roadmap Detail”.

Path:
ProgramPlanning/ProgramDefinition/DigitalInitiatives/Roadmap

Data source:
trs_master_milestone

---

## 2. Objectives
- Menampilkan roadmap per initiative dalam bentuk timeline (quarter-based)
- Menyamakan tampilan dengan sheet Digital Roadmap Detail
- Membuat komponen reusable
- Mendukung multiple version roadmap

---

## 3. Data Structure

Table: trs_master_milestone

| Field          | Type         | Description |
|----------------|-------------|------------|
| initiative_id  | int         | ID initiative |
| activity       | text        | Nama aktivitas |
| startYear      | year        | Tahun mulai |
| startQ         | varchar(2)  | Quarter mulai |
| endYear        | year        | Tahun selesai |
| endQ           | varchar(2)  | Quarter selesai |
| version        | varchar(100)| Versi roadmap |

---

## 4. Dummy Data

```json
[
  {"initiative_id":1,"initiative_name":"Buspro-X","activity":"Release 1","startYear":2024,"startQ":"Q1","endYear":2024,"endQ":"Q3","version":"v1.0"},
  {"initiative_id":1,"initiative_name":"Buspro-X","activity":"Release 2","startYear":2024,"startQ":"Q4","endYear":2025,"endQ":"Q2","version":"v1.0"},
  {"initiative_id":2,"initiative_name":"AI Drilling","activity":"Phase 1","startYear":2024,"startQ":"Q2","endYear":2024,"endQ":"Q4","version":"v1.0"},
  {"initiative_id":13,"initiative_name":"Digital Twin","activity":"Real Time Opt. Piloting","startYear":2024,"startQ":"Q1","endYear":2025,"endQ":"Q2","version":"v1.0"},
  {"initiative_id":25,"initiative_name":"IML Control","activity":"Physical Infrastructure","startYear":2024,"startQ":"Q1","endYear":2026,"endQ":"Q1","version":"v1.0"}
]
```

---

## 5. UI/UX Requirements

- Timeline horizontal berbasis quarter
- Format:
Initiative | Activity | 2024 Q1 | Q2 | Q3 | Q4 | 2025 Q1 ...

### Visual Rules
- Activity berupa bar horizontal
- Warna konsisten per initiative
- Tooltip untuk detail
- Header menampilkan tahun & quarter

---

## 6. Component Design

Component:
<DigitalRoadmap />

### Props

| Prop        | Type   | Description |
|------------|--------|------------|
| data        | array  | Data milestone |
| startYear   | int    | Tahun awal |
| endYear     | int    | Tahun akhir |

### Struktur
DigitalRoadmap
 ├── HeaderTimeline
 ├── InitiativeRow
 │     ├── ActivityBar

---

## 7. Timeline Logic

Mapping:
2024 Q1 = index 0

Rumus:
index = (year - startYear) * 4 + quarterIndex

---

## 8. Backend

Controller: RoadmapController

```php
public function index()
{
    $data = TrsMasterMilestone::all();
    return view('ProgramPlanning.ProgramDefinition.DigitalInitiatives.Roadmap.index', compact('data'));
}
```

---

## 9. Folder Structure

resources/views/
└── ProgramPlanning/
    └── ProgramDefinition/
        └── DigitalInitiatives/
            └── Roadmap/
                ├── index.blade.php
                └── components/
                    └── digital-roadmap.blade.php

---

## 10. Index Usage

```blade
<x-digital-roadmap :data="$data" :startYear="2024" :endYear="2026" />
```

---

## 11. Future Enhancement

- Filter initiative
- Filter version
- Export Excel
- Drag & drop timeline
- Color legend
- Zoom timeline

---

## 12. Acceptance Criteria

- Data tampil sesuai timeline
- Activity sesuai start-end
- Tampilan sesuai roadmap
- Component reusable
- Tidak hardcode data
