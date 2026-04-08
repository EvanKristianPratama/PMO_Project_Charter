# Product Requirements Document (PRD)

## 1. Overview
Dokumen ini menjelaskan kebutuhan untuk memindahkan menu **Resource Management** ke dalam sub menu **Program Implementation**, menggantikan menu **RKAP**. Selain itu, halaman Resource Management akan menampilkan data **Project Charter** berdasarkan model `TrsProject` dan `TrsProjectCharter` dengan pembatasan kolom tertentu.

---

## 2. Objective
- Memindahkan lokasi menu Resource Management agar lebih relevan dengan konteks Program Implementation.
- Mengganti menu RKAP dengan Resource Management.
- Menyediakan tampilan data Project Charter yang fokus pada informasi resource, yaitu:
  - Budget
  - Key Personnel

---

## 3. Scope
### In Scope
- Perubahan struktur navigasi menu.
- Integrasi data dari model:
  - `TrsProject`
  - `TrsProjectCharter`
- Pembuatan halaman index Resource Management.
- Filtering kolom yang ditampilkan (budget & key_personnel).

### Out of Scope
- CRUD penuh untuk Project Charter.
- Perubahan struktur database.
- Penambahan field baru.

---

## 4. User Flow
1. User membuka aplikasi.
2. User masuk ke menu:
   - Program Implementation
   - Resource Management (submenu baru)
3. Sistem menampilkan daftar Project Charter.
4. User melihat informasi:
   - Budget
   - Key Personnel

---

## 5. Functional Requirements

### 5.1 Navigation
- Menu **Resource Management** dipindahkan ke:
  - `Program Implementation > Resource Management`
- Menu **RKAP** dihapus/digantikan.

### 5.2 Data Source
- Data diambil dari:
  - `TrsProject`
  - `TrsProjectCharter`

### 5.3 Data Mapping
Relasi:
- `TrsProject` (parent)
- `TrsProjectCharter` (child / detail)

Field yang ditampilkan dari `TrsProjectCharter`:
- `budget`
- `key_personnel`

### 5.4 Data Filtering
- Hanya menampilkan data yang memiliki relasi valid antara project dan project charter.

### 5.5 UI Component
- Table: **Resource Management Table**

Kolom:
- Project Name (dari TrsProject)
- Budget
- Key Personnel

---

## 6. Non-Functional Requirements
- Response time < 2 detik untuk load data.
- Query harus dioptimasi (gunakan eager loading).
- UI konsisten dengan halaman lain di Program Implementation.

---

## 7. Technical Requirements

### 7.1 Backend
Gunakan eager loading:
```php
TrsProject::with('projectCharter')->get();
```

Mapping data:
```php
$projects = TrsProject::with('projectCharter')->get();

foreach ($projects as $project) {
    $budget = $project->projectCharter->budget ?? null;
    $keyPersonnel = $project->projectCharter->key_personnel ?? null;
}
```

### 7.2 Frontend
- Lokasi menu di file navigation:
  - `ProgramImplementation/ResourceManagement`

- Gunakan reusable table component jika tersedia.

---

## 8. Acceptance Criteria
- [ ] Menu Resource Management muncul di bawah Program Implementation
- [ ] Menu RKAP tidak lagi tersedia
- [ ] Halaman Resource Management dapat diakses tanpa error
- [ ] Data Project Charter ditampilkan
- [ ] Hanya kolom Budget dan Key Personnel yang ditampilkan
- [ ] Data sesuai dengan relasi project

---

## 9. Risks & Considerations
- Data null pada relasi project charter
- Performa jika jumlah project besar

---

## 10. Future Enhancement
- Filter berdasarkan project
- Export data (Excel/PDF)
- Detail view per project

---

## 11. Appendix

### Model Referensi
- `TrsProject`
- `TrsProjectCharter`

### Catatan
- Pastikan relasi antar model sudah didefinisikan dengan benar (hasOne / belongsTo).
