# PRD: Manage Regulation

# Feature: Manage Regulation

## Goal

Merubah mekanisme referensi dokumen pada page **Manage Procedure** yang sebelumnya menggunakan:

- `TrsTkoSection === 'REFERENSI'`
- `TrsTkoContent`

menjadi menggunakan mekanisme mapping regulation melalui:

- `MstRegulation`
- `TrsRelatedRegulation`

Tujuan utama adalah membuat referensi dokumen dapat digunakan secara otomatis berdasarkan regulation yang sudah dimapping.

---

## Problem Statement

Saat ini referensi dokumen pada Manage Procedure masih menggunakan mekanisme manual.

Flow existing:

```
User
 |
Tambah referensi dokumen
 |
TrsTkoSection
 |
TrsTkoContent
 |
Referensi tampil di Procedure
```

Permasalahan:
- Referensi harus dibuat secara manual.
- Data regulation yang sudah tersedia belum dapat digunakan kembali.
- Potensi duplikasi data referensi.
- Maintenance data menjadi lebih sulit.

---

## Objective

Menyediakan mekanisme mapping regulation untuk mengelola referensi dokumen.

Target:
- Mengambil data regulation existing.
- Menampilkan referensi berdasarkan mapping.
- Mengurangi input manual.
- Menjaga konsistensi data referensi.

---

## Model

### TrsTkoSection

Digunakan pada mekanisme lama untuk menyimpan section referensi.

---

### TrsTkoContent

Digunakan pada mekanisme lama untuk menyimpan content referensi.

---

### MstRegulation

Master data regulation.

Fungsi:
- Menyimpan data regulation yang tersedia.
- Menjadi sumber referensi dokumen.

---

### TrsRelatedRegulation

Table mapping regulation.

Fungsi:
- Menyimpan relasi regulation yang digunakan pada procedure.

Relationship:

```
MstRegulation
        |
        |
TrsRelatedRegulation
        |
        |
Procedure
```

---

## Business Rules

### Rule 1

System hanya menampilkan data:

```
MstRegulation
```

yang sudah memiliki mapping melalui:

```
TrsRelatedRegulation
```

---

### Rule 2

Data regulation yang belum memiliki mapping tidak ditampilkan sebagai referensi.

---

### Rule 3

Referensi dokumen tidak lagi ditambahkan secara manual melalui:

```
TrsTkoSection
TrsTkoContent
```

melainkan berasal dari hasil mapping regulation.

---

## Existing Files

### Controller

```
ProcedureController.php
```

Responsibility:
- Handle request terkait Procedure.
- Menyediakan endpoint data Manage Procedure.

---

### Services

```
ProcedureService.php
```

Responsibility:
- Mengatur business logic procedure.
- Mengambil data regulation berdasarkan mapping.
- Menyediakan data referensi untuk frontend.

---

### Component

```
ManageRefrence.vue
```

Responsibility:
- Menampilkan daftar referensi regulation.
- Menampilkan hasil mapping regulation.

---

### Page

```
Procedure/Manage.vue
```

Responsibility:
- Halaman utama Manage Procedure.
- Mengintegrasikan component ManageReference.

---

## Development Standard

Implementasi wajib mengikuti standar dokumentasi:

```
pageComponent.md
service.md
modal.md
```

Standard digunakan untuk memastikan:
- Struktur component konsisten.
- Service layer sesuai pattern.
- Modal mengikuti standar existing project.

---

# Current Flow (Before)

```
Consultant
        |
        |
Tambah Referensi Manual
        |
        |
TrsTkoSection
        |
        |
TrsTkoContent
        |
        |
Referensi tampil
```

Keterbatasan:
- Tidak menggunakan mapping regulation.
- Data harus dibuat ulang.
- Risiko duplicate reference.

---

# Expected Flow (After)

```
Consultant
        |
        |
Mapping Regulation
        |
        |
TrsRelatedRegulation
        |
        |
MstRegulation
        |
        |
Referensi otomatis tampil
```

---

## Functional Requirement

### FR-01 Display Regulation Reference

System harus dapat mengambil data regulation yang sudah memiliki mapping.

Source:

```
MstRegulation
JOIN
TrsRelatedRegulation
```

Expected:
- Regulation dengan mapping tampil.
- Regulation tanpa mapping tidak tampil.

---

### FR-02 Automatic Reference Loading

System menampilkan referensi dokumen berdasarkan hasil mapping regulation.

Expected:
- User tidak perlu memasukkan referensi secara manual.

---

### FR-03 Remove Manual Reference Dependency

System tidak lagi bergantung pada:

```
TrsTkoSection
TrsTkoContent
```

untuk menampilkan referensi regulation.

---

## Non Functional Requirement

### Performance

- Query menggunakan relationship antar model.
- Hindari query berulang pada frontend.
- Pastikan indexing tersedia pada:

```
TrsRelatedRegulation.regulation_id
```

---

### Maintainability

Business logic wajib berada pada:

```
ProcedureService.php
```

Controller hanya bertanggung jawab terhadap:
- Request handling.
- Validation.
- Response.

---

## Acceptance Criteria

### Scenario 1 - Regulation Has Mapping

Given:
- Regulation sudah memiliki data pada `TrsRelatedRegulation`.

When:
- User membuka halaman Manage Procedure.

Then:
- Regulation tampil sebagai referensi dokumen.

---

### Scenario 2 - Regulation Without Mapping

Given:
- Regulation tidak memiliki mapping.

When:
- User membuka halaman Manage Procedure.

Then:
- Regulation tidak ditampilkan.

---

### Scenario 3 - Automatic Reference

Given:
- Regulation sudah dimapping.

When:
- Procedure dibuka.

Then:
- Referensi dokumen tampil otomatis tanpa input manual.

---

## Result

## Before

- Referensi dokumen hanya dapat ditambahkan manual melalui:

```
TrsTkoSection
TrsTkoContent
```

---

## After

- Referensi dokumen menggunakan mekanisme:

```
MstRegulation
        |
TrsRelatedRegulation
```

Hasil:
- Referensi otomatis.
- Data lebih konsisten.
- Mengurangi duplikasi.
- Maintenance lebih mudah.
