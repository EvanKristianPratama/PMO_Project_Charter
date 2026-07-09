# Feature: Submenu Policy Menu Operating Model View Only Page

## Goal

-   Menampilkan page view only data Pedoman Tata Kelola
    (`MstRegulation.id == 1`) dan Pedoman Pengelolaan IT
    (`MstRegulation.id == 4`) dalam bentuk 2 tab navigasi:
    -   Pedoman Tata Kelola
    -   Pedoman Pengelolaan IT

## Scope

-   Penyesuaian submenu **Policy** pada menu **Operating Model**.
-   Penambahan navigasi tab untuk menampilkan dua jenis pedoman.

## Model
-   `MstRegulation`


## Business Rules

-   Hanya menampilkan data:
    -   `MstRegulation.id == 1` untuk Pedoman Tata Kelola.
    -   `MstRegulation.id == 4` untuk Pedoman Pengelolaan IT.
-   Halaman bersifat **view only**.


## Navigation Changes

### Sub Menu Policy

Menambahkan 2 tab navigasi: 1. **Pedoman Tata Kelola** - Data berasal
dari `MstRegulation.id == 1`.

2.  **Pedoman Pengelolaan IT**
    -   Data berasal dari `MstRegulation.id == 4`.

### Sub Menu Model Structure

Menambahkan 2 tab menu navigasi pada submenu Model Structure menu
Operating Model dengan konsep yang sama.

## Existing Files

### Controller

-   `ProcedureController.php`
-   `GeneralPolicyController.php`

### Services

-   `GeneralPolicyService.php`

### Components

-   `Introduction.vue`
-   `General.vue`
-   `Role.vue`
-   `Closing.vue`

Path:

    resources/js/Components/modules/ITOM/Regulation/Policy/

## Implementation Requirements

### Backend

-   Update query untuk mengambil data berdasarkan regulation tertentu:
    -   Pedoman Tata Kelola (`id = 1`)
    -   Pedoman Pengelolaan IT (`id = 4`)

### Frontend

-   Membuat tab navigation:
    -   Pedoman Tata Kelola
    -   Pedoman Pengelolaan IT
-   Menampilkan content berdasarkan tab aktif.
-   Menampilkan data dalam mode read only.
-   Menggunakan component existing untuk render section content.

## Result

### Before

-   Halaman kosong / placeholder.

### After

-   Tersedia dua tab navigasi:
    -   Pedoman Tata Kelola
    -   Pedoman Pengelolaan IT
-   Masing-masing tab menampilkan content sesuai regulation.
-   User dapat membaca pedoman tanpa melakukan perubahan data.
