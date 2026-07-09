# PRD Template

# Feature: Mapping Document Regulation

## Goal

-   Membuat mekanisme mapping antara `MstDocument` terhadap
    `MstRegulation` melalui tabel relasi `TrsDocumentRegulation`.
-   Memungkinkan satu dokumen (`MstDocument`) memiliki relasi dengan
    satu atau lebih regulation (`MstRegulation`).

## Model

-   `MstRegulation`
-   `MstDocument`
-   `TrsDocumentRegulation`

## Business Rules

-   List `MstDocument` harus menampilkan daftar `MstRegulation` yang
    sudah dilakukan mapping.
-   Mapping antara `MstDocument` dan `MstRegulation` disimpan melalui
    `TrsDocumentRegulation`.
-   Satu `MstDocument` dapat memiliki lebih dari satu `MstRegulation`.
-   Satu `MstRegulation` dapat memiliki lebih dari satu `MstDocument`
    jika dibutuhkan.

## Existing Files

### Controller

-   `CmsController.php`

### Services

-   `CmsService.php`

### Component

-   `Document.vue`
-   `RegulationDocument.vue`

### Page

-   `CMS/Index.vue`

## Feature Flow

1.  User membuka halaman CMS Document.
2.  Sistem menampilkan daftar `MstDocument`.
3.  User memilih document yang akan dilakukan mapping.
4.  User memilih satu atau beberapa `MstRegulation`.
5.  Sistem menyimpan relasi melalui `TrsDocumentRegulation`.
6.  Sistem menampilkan regulation yang sudah terhubung pada
    masing-masing document.

## Result

### Before

-   Sistem hanya menampilkan data `MstDocument`.
-   Belum terdapat mekanisme mapping antara `MstDocument` dengan
    `MstRegulation`.
-   User tidak dapat mengetahui regulation apa saja yang berkaitan
    dengan suatu document.

### After

-   Sistem dapat melakukan mapping antara `MstDocument` dengan
    `MstRegulation`.
-   Sistem dapat menyimpan mapping melalui `TrsDocumentRegulation`.
-   Sistem menampilkan list `MstRegulation` yang sudah dimapping
    terhadap masing-masing `MstDocument`.
