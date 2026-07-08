# PRD: Glossary Management Module

## Feature

Glossary Management

---

# 1. Goal

Mengubah konsep pengelolaan **Glossary / Pengertian** pada halaman **Manage Procedure** dari penggunaan model lama:

```
TrsTkoSection
        |
        └── TrsTkoContent
```

dengan kondisi:

```
TrsTkoSection.section === 'Pengertian'
```

menjadi menggunakan konsep master definition:

```
MstDefinition
        |
        └── TrsDefinitionRegulation
                |
                └── MstRegulation
```

Tujuan perubahan ini adalah menyatukan pengelolaan glossary COBIT menggunakan model `MstDefinition` sehingga definition dapat digunakan kembali pada berbagai modul berdasarkan regulation mapping.

---

# 2. Scope

## Included

* Migrasi tampilan management glossary pada halaman Manage Procedure.

* Mengganti sumber data glossary dari:

  * `TrsTkoSection`
  * `TrsTkoContent`

  menjadi:

  * `MstDefinition`
  * `TrsDefinitionRegulation`

* Menggunakan component standar:

```
DefinitionTable.vue
```

* Membatasi data glossary berdasarkan regulation yang sedang aktif.

---

## Excluded

* Migrasi data lama secara otomatis.
* Perubahan struktur tabel database.
* Perubahan konsep regulation.
* Perubahan UI component DefinitionTable secara global.

---

# 3. Current Condition (Before)

## Existing Flow

Manage Procedure saat ini mengambil data glossary menggunakan:

```
ProcedureController.php

        |
        ↓

TrsTkoSection

        |
        |
        | section = "Pengertian"
        |
        ↓

TrsTkoContent
```

Konsep data:

```
TrsTkoSection
------------------
id
section
procedure_id


TrsTkoContent
------------------
id
section_id
content
```

Permasalahan:

* Definition hanya melekat pada procedure.
* Tidak reusable antar module.
* Tidak memiliki relasi langsung dengan regulation.
* Sulit digunakan untuk fitur glossary global.

---

# 4. Target Condition (After)

## New Flow

```
Manage Procedure

        |
        ↓

DefinitionController

        |
        ↓

DefinitionService

        |
        ↓

MstDefinition

        |
        ↓

TrsDefinitionRegulation

        |
        ↓

MstRegulation
```

---

# 5. User

## Primary User

Consultant / Administrator

## User Capability

User dapat:

* Melihat glossary berdasarkan regulation aktif.
* Mengelola definition yang sudah terhubung dengan regulation.
* Menggunakan glossary yang sama pada module lain.

---

# 6. User Flow

## View Glossary

```
User membuka Manage Procedure

        ↓

System mendapatkan regulation aktif

        ↓

System mencari mapping:

TrsDefinitionRegulation

        ↓

Filter:

definition.regulation_id =
current regulation

        ↓

Menampilkan data:

MstDefinition

menggunakan:

DefinitionTable.vue
```

---

## Management Flow

```
Create Definition

        ↓

Save MstDefinition

        ↓

Mapping Regulation

        ↓

Save TrsDefinitionRegulation

        ↓

Definition tampil pada Manage Procedure
```

---

# 7. Database Relationship

## Existing Model

```
TrsTkoSection

      1
      |
      |
      *
      
TrsTkoContent
```

Tidak digunakan untuk glossary baru.

---

## New Model

```
MstDefinition

        1
        |
        |
        *

TrsDefinitionRegulation

        *

        |
        |

        1

MstRegulation
```

---

# 8. Data Requirement

## MstDefinition

Source utama glossary.

Expected field:

```
id
title
description
created_at
updated_at
```

---

## TrsDefinitionRegulation

Mapping definition dengan regulation.

Required:

```
definition_id
regulation_id
```

---

## MstRegulation

Digunakan sebagai filter context.

Example:

```
COBIT 2019
ISO 27001
```

---

# 9. Business Rules

## Regulation Filtering Rule

Definition hanya boleh tampil jika memiliki mapping:

```
MstDefinition
        |
        ↓
TrsDefinitionRegulation
        |
        ↓
Current MstRegulation
```

Contoh:

Current regulation:

```
COBIT 2019
```

Data:

```
Definition A
 |
 └── COBIT 2019 ✅


Definition B
 |
 └── ISO 27001 ❌
```

Result:

```
Definition A tampil
Definition B tidak tampil
```

---

# 10. Technical Requirement

## Backend

Framework:

```
Laravel
```

Pattern:

```
Controller
        |
        ↓
Service Layer
        |
        ↓
Repository / Model
```

---

## Controller Change

### Before

```
ProcedureController.php
```

Mengelola:

```
TrsTkoSection
TrsTkoContent
```

---

### After

Gunakan:

```
Definition/DefinitionController.php
```

Responsibilities:

* Retrieve definitions.
* Filter berdasarkan regulation.
* Handle CRUD definition jika diperlukan.

---

# 11. Service Layer

## DefinitionService.php

Responsibilities:

```
getDefinitionsByRegulation()

createDefinition()

updateDefinition()

deleteDefinition()
```

Business logic tidak boleh berada di Controller.

---

# 12. Frontend Requirement

## Component

Gunakan:

```
DefinitionTable.vue
```

Location:

```
components/Definition/DefinitionTable.vue
```

---

## Component Behavior

Pada halaman:

```
Manage Procedure
```

Component hanya menerima data:

```
MstDefinition
```

yang sudah difilter backend.

Component tidak melakukan:

* Filtering regulation.
* Mapping logic.
* Query data.

---

# 13. Page Standard

Implementasi harus mengikuti:

```
pageComponent.md
```

Standard mencakup:

* Struktur component.
* Props handling.
* State management.
* Loading state.
* Error handling.

---

# 14. Implementation Task Breakdown

## Backend

### Task 1

Create/update:

```
DefinitionController.php
```

Support:

* index
* store
* update
* destroy

---

### Task 2

Implement:

```
DefinitionService.php
```

Method:

```
getByRegulation($regulationId)
```

Return:

```
Collection<MstDefinition>
```

---

### Task 3

Implement relation:

```
MstDefinition

hasMany

TrsDefinitionRegulation
```

dan:

```
MstRegulation

hasMany

TrsDefinitionRegulation
```

---

# Frontend

## Task 4

Update:

```
DefinitionTable.vue
```

Support:

* display definition
* pagination
* action button
* loading state

---

## Task 5

Replace Manage Procedure glossary section.

Remove:

```
TrsTkoSection
TrsTkoContent
```

usage.

Replace:

```
DefinitionTable.vue
```

---

# 15. Acceptance Criteria

Feature dianggap selesai apabila:

## Data

✅ Definition berasal dari:

```
MstDefinition
```

bukan:

```
TrsTkoContent
```

## Regulation

✅ Definition hanya tampil jika memiliki mapping:

```
TrsDefinitionRegulation
```

## UI

✅ Manage Procedure menggunakan:

```
DefinitionTable.vue
```

## Architecture

✅ Controller tidak memiliki business logic.

✅ Logic berada di:

```
DefinitionService.php
```

## Regression

✅ Procedure management lain tetap berjalan.

---

# 16. Expected Result

Before:

```
Manage Procedure

Pengertian Section

        |
        ↓

TrsTkoContent
```

After:

```
Manage Procedure

Glossary

        |
        ↓

DefinitionTable.vue

        |
        ↓

MstDefinition

        |
        ↓

Filtered By:

TrsDefinitionRegulation

        |
        ↓

Current MstRegulation
```

---

# Final Objective

Membangun sistem glossary terpusat berbasis `MstDefinition` yang dapat digunakan lintas modul, dengan filtering berdasarkan regulation aktif dan mengikuti standar arsitektur Laravel Service Pattern serta standar component Vue/Inertia yang telah ditentukan.
