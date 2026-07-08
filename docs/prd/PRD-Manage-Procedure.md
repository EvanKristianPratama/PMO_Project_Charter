# PRD: Manage Procedure

## 1. Feature Overview

### Feature Name

Manage Procedure

### Goal

Menambahkan mekanisme mapping glossary yang sudah tersedia ke regulation
lain tanpa perlu membuat data glossary baru.

Feature ini bertujuan menjaga konsistensi data glossary dengan
menggunakan kembali data `MstDefinition` yang sudah ada melalui proses
mapping ke regulation tambahan.

------------------------------------------------------------------------

# 2. Problem Statement

Saat ini glossary hanya dapat digunakan pada regulation tertentu melalui
proses pembuatan data baru.

Permasalahan: - Data glossary yang sama dapat terduplikasi ketika
digunakan pada regulation berbeda. - Tidak ada mekanisme reuse glossary
yang sudah tersedia. - Konsistensi data sulit dijaga karena data yang
sama dapat tersimpan beberapa kali.

------------------------------------------------------------------------

# 3. Objective

Menyediakan kemampuan untuk: - Menampilkan glossary yang sudah
tersedia. - Melakukan mapping glossary existing ke regulation baru. -
Menghindari duplikasi data glossary. - Menjaga relasi antara glossary
(`MstDefinition`) dan regulation (`MstRegulation`).

------------------------------------------------------------------------

# 4. Scope

## Included

-   Menampilkan daftar glossary existing.
-   Filter glossary berdasarkan regulation yang sudah memiliki mapping.
-   Menambahkan mapping glossary existing ke regulation baru.
-   Menyimpan relasi mapping melalui tabel transaksi.

## Excluded

-   Membuat perubahan struktur data glossary utama.
-   Mengubah isi glossary existing.
-   Menghapus glossary existing.

------------------------------------------------------------------------

# 5. User

## Consultant

Role: - Mengelola glossary dan mapping procedure terhadap regulation.

------------------------------------------------------------------------

# 6. Business Rules

## Rule 1

Hanya menampilkan data `MstDefinition` yang sudah memiliki mapping
terhadap `MstRegulation`.

Relasi:

    MstDefinition
          |
    TrsDefinitionRegulation
          |
    MstRegulation

## Rule 2

Glossary existing dapat digunakan kembali untuk regulation lain melalui
proses mapping.

Contoh:

Before:

    Definition A
        |
    Regulation X

After:

    Definition A
        |
        +---- Regulation X
        |
        +---- Regulation Y

## Rule 3

Data glossary hanya dibuat satu kali pada `MstDefinition`.

Penggunaan pada regulation berbeda dilakukan melalui:

`TrsDefinitionRegulation`

------------------------------------------------------------------------

# 7. Data Model

## MstDefinition

Master data glossary.

Fungsi: - Menyimpan informasi glossary utama.

## MstRegulation

Master data regulation.

Fungsi: - Menyimpan daftar regulation.

## TrsDefinitionRegulation

Table mapping antara glossary dan regulation.

Relationship:

    MstDefinition.id
            |
    TrsDefinitionRegulation.definition_id
            |
    TrsDefinitionRegulation.regulation_id
            |
    MstRegulation.id

------------------------------------------------------------------------

# 8. Existing Files

## Controller

    ProcedureController.php

Responsibility: - Handle request manage procedure. - Menyediakan
endpoint untuk frontend.

------------------------------------------------------------------------

## Services

### DefinitionService.php

Responsibility: - Mengelola proses pengambilan data definition/glossary.

### ProcedureService.php

Responsibility: - Mengelola business logic procedure. - Mengatur proses
mapping glossary terhadap regulation.

------------------------------------------------------------------------

## Component

    ManageGlossary.vue

Responsibility: - Menampilkan daftar glossary. - Menyediakan UI mapping
glossary existing.

------------------------------------------------------------------------

# 9. Current Flow (Before)

    Consultant
        |
    Create glossary baru
        |
    Mapping ke regulation
        |
    Data tersimpan

Limitasi: - Glossary yang sama harus dibuat ulang ketika digunakan
regulation berbeda. - Risiko duplicate data tinggi.

------------------------------------------------------------------------

# 10. Expected Flow (After)

    Consultant
            |
    Open Manage Glossary
            |
    Load existing glossary
            |
    Select glossary
            |
    Select regulation tujuan
            |
    Create mapping
            |
    Save TrsDefinitionRegulation

------------------------------------------------------------------------

# 11. Functional Requirement

## FR-01 Display Existing Glossary

System harus dapat menampilkan daftar glossary existing.

Requirement: - Hanya menampilkan glossary yang sudah memiliki regulation
mapping. - Tidak menampilkan glossary tanpa relasi regulation.

------------------------------------------------------------------------

## FR-02 Mapping Existing Glossary

Consultant dapat memilih glossary existing untuk regulation baru.

Input: - Definition - Regulation tujuan

Output: - Data baru pada `TrsDefinitionRegulation`

------------------------------------------------------------------------

## FR-03 Prevent Duplicate Mapping

System harus melakukan validasi agar tidak terjadi duplicate:

    definition_id + regulation_id

------------------------------------------------------------------------

# 12. Non Functional Requirement

## Performance

-   Query menggunakan relationship antar tabel.
-   Pastikan indexing tersedia pada:

```{=html}
<!-- -->
```
    TrsDefinitionRegulation.definition_id
    TrsDefinitionRegulation.regulation_id

## Maintainability

Business logic mapping harus berada pada Service Layer.

Controller hanya menangani: - Request validation. - Response handling.

------------------------------------------------------------------------

# 13. Acceptance Criteria

## Scenario 1 - View Glossary

Given: - Database memiliki glossary yang sudah memiliki regulation
mapping.

When: - Consultant membuka Manage Glossary.

Then: - System menampilkan glossary tersebut.

------------------------------------------------------------------------

## Scenario 2 - Mapping Existing Glossary

Given: - Glossary sudah tersedia.

When: - Consultant memilih regulation baru.

Then: - System membuat data baru pada `TrsDefinitionRegulation`.

------------------------------------------------------------------------

## Scenario 3 - Duplicate Mapping

Given: - Mapping glossary dan regulation sudah tersedia.

When: - Consultant mencoba melakukan mapping ulang.

Then: - System menolak dan memberikan pesan duplicate mapping.

------------------------------------------------------------------------

# 14. Expected Result

## Before

-   Glossary hanya dapat digunakan melalui pembuatan data baru.
-   Regulation berbeda membutuhkan glossary baru.

## After

-   Glossary existing dapat digunakan kembali.
-   Mapping regulation dilakukan melalui `TrsDefinitionRegulation`.
-   Data glossary lebih konsisten.
-   Tidak terjadi duplikasi data.
