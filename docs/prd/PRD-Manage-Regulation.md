# PRD: Manage Regulation

## Feature: Manage Regulation

## Goal

-   Merubah mekanisme `TrsTkoSection === 'REFERENSI'` dan
    `TrsTkoContent` pada page Manage Procedure menggunakan mekanisme
    mapping `MstRegulation` di `TrsRelatedRegulation`.

## Model

-   TrsTkoSection
-   TrsTkoContent
-   MstRegulation
-   TrsRelatedRegulation

## Problem Statement

Saat ini referensi dokumen pada Manage Procedure masih ditambahkan
secara manual melalui `TrsTkoSection` dan `TrsTkoContent`.

Permasalahan: - Data referensi harus dibuat ulang secara manual. - Tidak
memanfaatkan data regulation yang sudah tersedia. - Risiko duplikasi
data meningkat.

## Objective

-   Menggunakan kembali data regulation existing melalui mekanisme
    mapping.
-   Menampilkan referensi dokumen secara otomatis.
-   Menjaga konsistensi data antar regulation.

## Business Rules

### Rule 1

System hanya menampilkan data `MstRegulation` yang sudah memiliki
mapping melalui `TrsRelatedRegulation`.

Relationship:

    MstRegulation
          |
    TrsRelatedRegulation
          |
    MstRegulation

### Rule 2

Referensi dokumen tidak lagi bergantung pada input manual melalui:

    TrsTkoSection
    TrsTkoContent

### Rule 3

Regulation existing dapat digunakan kembali melalui mekanisme mapping.

## Existing Files

### Controller

    ProcedureController.php

Responsibility: - Handle request Manage Procedure. - Menyediakan data
untuk frontend.

### Services

    ProcedureService.php

Responsibility: - Mengelola business logic procedure. - Mengambil data
regulation reference.

### Component

    ManageRefrence.vue

Responsibility: - Menampilkan referensi regulation. - Menampilkan hasil
mapping.

## Development Standard

Implementasi mengikuti standar:

-   `pageComponent.md`
-   `service.md`
-   `modal.md`

## Current Flow (Before)

    Consultant
        |
    Tambah referensi manual
        |
    TrsTkoSection
        |
    TrsTkoContent
        |
    Referensi tampil

## Expected Flow (After)

    Consultant
        |
    Mapping Regulation
        |
    TrsRelatedRegulation
        |
    MstRegulation
        |
    Referensi otomatis tampil

## Functional Requirement

### FR-01 Display Related Regulation

System menampilkan regulation yang sudah memiliki mapping pada
`TrsRelatedRegulation`.

### FR-02 Automatic Reference Loading

System mengambil referensi dokumen berdasarkan hasil mapping regulation.

### FR-03 Remove Manual Dependency

System tidak lagi membutuhkan input manual untuk menampilkan referensi
regulation.

## Non Functional Requirement

### Performance

Pastikan query menggunakan relationship yang optimal dan indexing
tersedia pada:

    TrsRelatedRegulation.regulation_id

### Maintainability

Business logic berada pada Service Layer.

Controller hanya menangani: - Request - Validation - Response

## Acceptance Criteria

### Scenario 1 - Regulation Has Mapping

Given: - Regulation memiliki mapping pada `TrsRelatedRegulation`.

When: - Consultant membuka Manage Procedure.

Then: - Regulation tampil sebagai referensi.

### Scenario 2 - Regulation Without Mapping

Given: - Regulation tidak memiliki mapping.

When: - Consultant membuka Manage Procedure.

Then: - Regulation tidak ditampilkan.

### Scenario 3 - Automatic Reference

Given: - Regulation sudah dimapping.

When: - Procedure dibuka.

Then: - Referensi dokumen tampil otomatis.

## Result

### Before

-   Referensi dokumen hanya dapat ditambahkan manual melalui:
    -   `TrsTkoSection`
    -   `TrsTkoContent`

### After

-   Referensi dokumen menggunakan:
    -   `MstRegulation`
    -   `TrsRelatedRegulation`
-   Data referensi otomatis.
-   Data lebih konsisten.
-   Mengurangi duplikasi.
