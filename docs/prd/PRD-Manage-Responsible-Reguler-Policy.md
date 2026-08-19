# PRD - Manage Responsible Reguler Policy

  Item       Detail
  ---------- -----------------------------------
  Feature    Manage Responsible Reguler Policy
  Module     Regulation Management
  Priority   Medium
  Status     Draft

## 1. Latar Belakang

Saat ini halaman **Manage Responsible** hanya digunakan untuk Regulation
dengan tipe **Policy Mapping**. Diperlukan halaman khusus untuk
mengelola data Bab 3 (Tanggung Jawab) pada **Reguler Policy**
berdasarkan regulation yang sedang diakses.

## 2. Permasalahan

-   Halaman Manage Responsible hanya mendukung Policy Mapping.
-   Belum terdapat halaman khusus untuk Reguler Policy.
-   Tampilan masih menggunakan view Policy Mapping.

## 3. Tujuan

Mengembangkan halaman **Manage Responsible Reguler Policy** untuk
mengelola data tanggung jawab berdasarkan regulation yang sedang
diakses.

## 4. Ruang Lingkup

### In Scope

-   Membuat halaman khusus Manage Responsible Reguler Policy.
-   Menampilkan daftar Role dan Responsible berdasarkan Regulation
    aktif.
-   Menyesuaikan tampilan berdasarkan tipe Regulation.

### Out of Scope

-   Perubahan struktur database.
-   Perubahan flow Policy Mapping.
-   Perubahan approval Regulation.

## 5. Database

### Model

-   `MstPolicyRoles`
-   `TrsResponsible`
-   `MstRegulation`

## 6. Business Rules

-   Hanya menampilkan data `MstPolicyRoles`.
-   Hanya menampilkan `TrsResponsible` milik `MstRegulation` yang sedang
    diakses.
-   Tidak menampilkan Responsible milik Regulation lain.
-   Halaman hanya digunakan untuk Reguler Policy.

## 7. Technical Requirement

Baca dan ikuti standar berikut: - `docs/agent/MEMORY.md` -
`docs/agent/SKILL.md` - `docs/standart/service.md` -
`docs/standart/pageComponent.md`

## 8. Existing Files

### Controller

-   `RegulerPolicyController`

### Services

-   `DefinitionService.php`
-   `ProcedureService.php`

### Component

-   `ManageResponsblie.vue`

## 9. Functional Requirements

-   FR-01: Sistem menyediakan halaman khusus Manage Responsible Reguler
    Policy.
-   FR-02: Sistem mengambil data Role dari `MstPolicyRoles`.
-   FR-03: Sistem mengambil data Responsible dari `TrsResponsible`.
-   FR-04: Sistem hanya menampilkan data Responsible milik Regulation
    aktif.
-   FR-05: Sistem menyesuaikan tampilan berdasarkan tipe Regulation.

## 10. User Flow

1.  User membuka Regulation.
2.  User memilih menu **Manage Responsible**.
3.  Sistem memeriksa tipe Regulation.
4.  Jika Reguler Policy, sistem membuka halaman khusus.
5.  Sistem mengambil data Role.
6.  Sistem mengambil data Responsible.
7.  Sistem menampilkan seluruh data.

## 11. Before vs After

### Before

-   Menggunakan view untuk Policy Mapping.
-   Belum ada halaman khusus Reguler Policy.

### After

-   Menggunakan halaman khusus Reguler Policy.
-   View mengikuti tipe Regulation.
-   Data Responsible hanya berasal dari Regulation yang sedang diakses.

## 12. Acceptance Criteria

  No   Scenario                            Expected Result
  ---- ----------------------------------- ------------------------------------------
  1    Membuka Regulation Reguler Policy   Halaman tampil
  2    Membuka halaman                     Data Role berhasil dimuat
  3    Membuka halaman                     Data Responsible sesuai Regulation aktif
  4    Tidak ada Responsible               Halaman tetap tampil tanpa error
  5    Membuka Regulation lain             Data berubah sesuai Regulation

## 13. Dampak Perubahan

### Backend

-   Penyesuaian `RegulerPolicyController`.
-   Penyesuaian Service.

### Frontend

-   Penyesuaian `ManageResponsblie.vue`.

### Database

Tidak ada perubahan struktur database.

## 14. Risiko

-   Data dapat tidak sesuai jika query tidak difilter berdasarkan
    Regulation.
-   Reuse komponen lama dapat menyebabkan dependensi terhadap Policy
    Mapping.

## 15. Catatan

Implementasi wajib mengikuti seluruh standar project yang telah
ditentukan.
