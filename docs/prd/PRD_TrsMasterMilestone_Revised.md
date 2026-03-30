# Product Requirements Document (PRD)
## CRUD Page - TrsMasterMilestone (Revised)

---

## 1. Overview

Halaman ini bertujuan untuk mengelola data **Master Milestone** menggunakan operasi CRUD (Create, Read, Update, Delete).

Implementasi akan mengikuti referensi dari:
- Page: **IT Initiative Roadmap**
- Folder referensi: `ProgramImplementation/Roadmap`
- Komponen utama: `Roadmap/Digital/DigitalRoadmapComponent`

Lokasi implementasi halaman baru:
```
ProgramPlanning/DigitalInitiatives/Roadmap/
```

---

## 2. Objective

- Mengelola milestone berdasarkan initiative
- Menyediakan tampilan timeline berbasis Year & Quarter
- Konsisten dengan UI roadmap existing

---

## 3. Data Model

### Model: TrsMasterMilestone

| Field Name    | Type    | Description |
|--------------|--------|------------|
| id           | bigint | Primary Key |
| initiative_id| bigint | Relasi ke MstInitiative |
| startYear    | int    | Tahun mulai |
| startQ       | int    | Quarter mulai (1-4) |
| endYear      | int    | Tahun selesai |
| endQ         | int    | Quarter selesai (1-4) |
| activity     | string | Nama aktivitas milestone |
| version      | int    | Versi data |

### Relasi:
- BelongsTo → MstInitiative

---

## 4. UI/UX Design

### Layout

- Header (Title + Filter Initiative + Add Button)
- Roadmap Timeline (DigitalRoadmapComponent)
- Table View
- Modal Form

---

## 5. Features

### 5.1 List Milestone

#### Tampilan:
- Timeline (Year + Quarter)
- Table

#### Kolom Table:
- Initiative
- Activity
- Start (Year-Q)
- End (Year-Q)
- Version
- Action

---

### 5.2 Create Milestone

#### Field:
- Initiative (dropdown)
- Activity (required)
- Start Year (required)
- Start Quarter (1-4)
- End Year (required)
- End Quarter (1-4)
- Version

---

### 5.3 Update Milestone

- Prefilled form
- Same validation

---

### 5.4 Delete Milestone

- Confirmation dialog
- Soft delete (optional)

---

## 6. Validation Rules

| Field        | Rule |
|-------------|-----|
| initiative_id | required |
| activity     | required |
| startYear    | required |
| startQ       | required (1-4) |
| endYear      | required |
| endQ         | required (1-4) |
| end          | >= start |

---

## 7. Component Integration

Gunakan:
```
Roadmap/Digital/DigitalRoadmapComponent
```

### Mapping Data:

- startDate → combine(startYear, startQ)
- endDate → combine(endYear, endQ)
- label → activity

---

## 8. API Endpoint

GET /api/master-milestones  
POST /api/master-milestones  
PUT /api/master-milestones/{id}  
DELETE /api/master-milestones/{id}  

---

## 9. State Management

- milestones
- initiatives
- loading
- modalState
- selectedData

---

## 10. Acceptance Criteria

- CRUD berjalan normal
- Timeline tampil sesuai quarter
- Data terhubung dengan initiative
- UI konsisten dengan roadmap existing

---

## 11. Notes

- Quarter format: Q1–Q4
- Gunakan dropdown untuk initiative
- Hindari duplikasi komponen roadmap
