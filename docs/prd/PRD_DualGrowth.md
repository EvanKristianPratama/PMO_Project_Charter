# Product Requirement Document (PRD)
## Component: DualGrowth.vue (Mockup-Based)

---

## 1. Overview  
Komponen **DualGrowth.vue** merupakan komponen visual berbasis mockup yang digunakan untuk menampilkan distribusi strategic initiatives dalam bentuk **horizontal roadmap grid**.

Tampilan ini mengacu pada mockup yang diberikan, dengan pembagian utama menjadi 3 bagian:
- **A — Maximizing Value**
- **B — Expand to new markets & adjacencies**
- **C — Building low carbon business**

Setiap bagian merepresentasikan data dari tabel **TrsThemes**.

---

## 2. Objectives  
- Menyajikan visualisasi strategi dalam bentuk grid horizontal  
- Mengelompokkan data berdasarkan **themes (A, B, C)**  
- Menyerupai tampilan mockup sebagai referensi utama UI  
- Mempermudah analisis distribusi initiative  

---

## 3. Scope  

### In Scope
- Rendering section A, B, C  
- Mapping data dari `TrsThemes`  
- Menampilkan item initiative dalam bentuk box/grid  
- Styling mengikuti mockup  

### Out of Scope
- CRUD data  
- Advanced filtering  
- Drag & drop positioning (future)  

---

## 4. Data Source  

### 4.1 TrsThemes
Field yang digunakan:
- `id`
- `theme_code` (A, B, C)
- `theme_name`
- `order`

### 4.2 (Optional) Related Data
- Initiatives / Programs  
- Project Charter  

---

## 5. UI / UX (Mockup Reference)

### 5.1 Layout Structure
- 3 row horizontal (A, B, C)  
- Setiap row memiliki:
  - Label (A/B/C)
  - Judul theme
  - Grid berisi item

### 5.2 Visual Style
- Background berbeda tiap section:
  - A: Biru muda  
  - B: Abu muda  
  - C: Hijau muda  

- Item berbentuk box kecil  
- Warna item:
  - Biru: Core system  
  - Hijau: Operational  
  - Kuning: Business / analytics  
  - Merah outline: Highlight / prioritas  

---

## 6. Functional Requirements  

### 6.1 Data Fetching
Endpoint:
GET /api/dual-growth

### 6.2 Data Grouping
Data harus di-group berdasarkan:
- A
- B
- C

### 6.3 Rendering
- Loop themes  
- Render row per theme  
- Render item dalam bentuk grid horizontal  

---

## 7. Component Structure  

### Parent Component
- DualGrowth.vue  

### Child Components
- ThemeRow.vue  
- InitiativeCard.vue  

---

## 8. Props & State  

### Props
themes: Array  
initiatives: Array  

### State
groupedThemes:  
A: []  
B: []  
C: []  

---

## 9. Interaction  

### Hover
- Highlight card  
- Optional tooltip  

### Click
- Redirect ke detail initiative / project  

---

## 10. Styling Guidelines  

### Container
- Flex column  
- Gap antar section  

### Row
- Flex horizontal  
- Align center  

### Grid
- Flex wrap / grid  
- Gap kecil (8px)  

### Card
- Border radius kecil  
- Padding compact  
- Font kecil (12–14px)  

---

## 11. Example Response  

{
  "A": [
    { "name": "Predictive Maintenance", "type": "core" }
  ],
  "B": [],
  "C": []
}

---

## 12. Performance Considerations  
- Gunakan lazy rendering jika data besar  
- Hindari nested loop berat  
- Gunakan key unik pada setiap item  

---

## 13. Future Enhancements  
- Filtering (year, category)  
- Drag & drop  
- Export PDF/image  
- Zoom canvas  

---

## 14. Acceptance Criteria  

- Section A, B, C tampil sesuai data TrsThemes  
- Layout menyerupai mockup  
- Data ter-group dengan benar  
- UI responsif  
- Card tidak overlap  
- Click navigation berjalan  

---

## 15. Notes  
- Mockup adalah referensi utama UI  
- Gunakan Tailwind / CSS modular  
- Pastikan horizontal scroll jika overflow  

---

**End of Document**
