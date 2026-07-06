# CIS Modular MVC System - Memory

Laravel 12 + Vue 3 monolith untuk pengelolaan Project Management Office (PMO).

---

## 🏛️ Core Architecture Decision
- Sistem menggunakan arsitektur **CIS (Collaboration Information System)** sebagai portal induk (shell)
- Framework: Laravel + Inertia.js
- Frontend: Vue (Inertia Pages)
- Sistem berbasis **modular architecture (plugin-based modules)**

---

## 🚫 Database Strategy
- **TIDAK menggunakan Laravel Migrations**
- Semua schema database dikelola manual langsung di DBMS
- Modul tidak boleh memiliki migration
- Seeder hanya opsional untuk data awal

---

## 🧩 Modular System Principle
- Setiap modul harus:
  - Self-contained
  - Portable antar repository
  - Fully decoupled dari modul lain
- Modul utama yang ada:
  - ITSP (IT Strategic Planning)
  - ITOM (IT Operating Model)
  - EA (Enterprise Architecture)

---

## 🏗️ System Roles

### Induk CIS (Portal Shell)
- Mengelola:
  - Authentication (SSO Google/Lokal)
  - Authorization (akses modul)
  - Layout global (sidebar/navbar)
- Tidak menangani business logic modul

---

## 📦 Backend Structure (Modules)
- Semua modul berada di folder:
  - `modules/<ModuleName>/`

Setiap modul wajib memiliki:
- Controllers
- Models (explicit table binding wajib)
- Providers (Service Provider modul)
- Routes (web & api)

---

## ⚙️ Routing Convention
- Routing modul wajib menggunakan:
  - prefix otomatis berbasis nama modul
  - name prefix sesuai modul
- Semua routing di-load melalui ServiceProvider modul
- Tidak boleh mengubah `routes/web.php` induk untuk modul

---

## 🔌 Service Provider Rule
- Setiap modul wajib memiliki Service Provider
- Provider bertugas:
  - load routes web & api
  - set prefix otomatis
- Tidak diperbolehkan menggunakan migration loader

---

## 🗄️ Model Rules
- Semua model wajib:
  - mendefinisikan `$table` secara eksplisit
  - mendefinisikan `$primaryKey` jika tidak default
  - disable increment jika diperlukan
  - menggunakan `$fillable`

---

## 🎨 Frontend Structure (Inertia Vue)
- Frontend modul berada di:
  - `resources/js/Pages/modules/<ModuleName>/`

Struktur:
- Components/ (internal module components)
- SubFeature folders (mapping fitur backend)

---

## 🔁 Module Portability Rule
Untuk memindahkan modul:
- Copy backend folder `modules/<Module>`
- Copy frontend folder `resources/js/Pages/modules/<Module>`
- Register Service Provider di `bootstrap/providers.php`
- Jalankan `composer dump-autoload`

---

## 🔗 Shared Core Rules
- Model shared (User, Organization, dll):
  - tetap di `App\Models`
  - tidak boleh dipindahkan ke modul
- Relasi antar modul harus menggunakan namespace explicit

---

## 🧠 Coding Standards
- Tidak boleh hardcode URL → harus gunakan route name
- Vue wajib menggunakan Inertia route helper
- Semua style Vue wajib scoped (`<style scoped>`)
- Business logic harus berada di controller/module, bukan di frontend

---

## 🚫 Hard Restrictions
- Dilarang membuat folder `Database/Migrations` di modul
- Dilarang menggunakan `$this->loadMigrationsFrom()`
- Dilarang import layout induk via relative path di frontend
- Dilarang memindahkan shared core models ke modul

---

## 📌 System Philosophy
- Modular = independen + reusable + portable
- Induk CIS hanya sebagai gateway + authorization layer
- Semua business logic harus berada di dalam modul masing-masing

---

## 🎯 Target Utama Sesi Ini
- [x] Refaktor referensi `Architecture/ProsesBisnis` ke `BusinessProcess` (baik di backend, routes, controller, maupun frontend)
- [x] Menyelesaikan error routing Ziggy `itom.policy.procedure.index` dan `itom.regulation.procedure.manage`
- [x] Update dokumentasi `docs/agent/MEMORY.md`

---

## 🚀 Progres Saat Ini
- [x] Mengubah prefiks route dan nama dari `proses-bisnis-v2` menjadi `business-process-v2` di `modules/ITOM/Routes/web.php`
- [x] Mengubah nama method controller (`storeProsesBisnisV2`, `updateProsesBisnisV2`, `destroyProsesBisnisV2` -> `storeBusinessProcessV2`, `updateBusinessProcessV2`, `destroyBusinessProcessV2`) di `BusinessProcessController.php`
- [x] Memindahkan komponen frontend dari `Architecture/ProsesBisnis` ke `BusinessProcess` dan memperbarui semua import/pemanggilan komponen terkait
- [x] Menyelesaikan error Ziggy route `itom.regulation.procedure.manage` dengan melakukan refaktorisasi penuh pada 10 komponen frontend Vue agar menggunakan route prefix `itom.regulation.procedure.*` secara konsisten, lalu menghapus grup route duplikat `policy.procedure.` di `web.php` yang sebelumnya menyebabkan konflik penimpaan route.
- [x] Melakukan verifikasi dengan `php artisan route:list` dan build frontend `npm run build` sukses tanpa error ✅

---

## 📦 Perubahan Terakhir

### File Baru / Dipindahkan
**`resources/js/Components/modules/ITOM/BusinessProcess/*`** (Pindahan dari `Architecture/ProsesBisnis`)
- Memindahkan semua file komponen proses bisnis ke direktori yang tepat sesuai konvensi modular baru.

### File Dimodifikasi
**`modules/ITOM/Routes/web.php`**
- Mengubah route name/prefix `proses-bisnis-v2` ke `business-process-v2`
- Menghapus grup route duplikat `policy.procedure.` yang menimpa `regulation.procedure.` di Laravel RouteCollection.

**`resources/js/Pages/modules/ITOM/Regulation/PolicyStandartProcedure/Guidance/Index.vue`**
- Mengubah route target `itom.policy.procedure.index` menjadi `itom.regulation.procedure.index`.

**`resources/js/Components/modules/ITOM/Regulation/Procedure/FlowChart.vue`**
- Mengubah route target `itom.policy.procedure.diagram.*` menjadi `itom.regulation.procedure.diagram.*`.

**`resources/js/Components/modules/ITOM/Regulation/Procedure/FungsiEditor.vue`**
- Mengubah route target `itom.policy.procedure.actor.*` menjadi `itom.regulation.procedure.actor.*`.

**`resources/js/Components/modules/ITOM/Regulation/Procedure/ProsedurEditor.vue`**
- Mengubah route target `itom.policy.procedure.category.*` dan `itom.policy.procedure.sop.*` menjadi `itom.regulation.procedure.category.*` dan `itom.regulation.procedure.sop.*`.

**`resources/js/Components/modules/ITOM/Regulation/Procedure/SectionEditor.vue`**
- Mengubah route target `itom.policy.procedure.section.*` menjadi `itom.regulation.procedure.section.*`.

**`resources/js/Components/modules/ITOM/Regulation/Policy/General.vue`**
- Mengubah route target `itom.policy.procedure.index` menjadi `itom.regulation.procedure.index`.

**`resources/js/Components/modules/ITOM/BusinessProcess/RegulationMap/RegulationMap.vue`**
- Mengubah route target `itom.policy.procedure.index` menjadi `itom.regulation.procedure.index`.

**`resources/js/Components/modules/ITOM/BusinessProcess/RegulationMap/FunctionMap.vue`**
- Mengubah route target `itom.policy.procedure.index` menjadi `itom.regulation.procedure.index`.

**`resources/js/Components/modules/ITOM/BusinessProcess/BusinessProcessV2/BusinessProcessV2.vue`**
- Mengubah route target `itom.policy.procedure.index` menjadi `itom.regulation.procedure.index`.

**`resources/js/Components/modules/ITOM/ITOperatingModel/Regulation/Procedure/ManageSection.vue`**
- Mengubah route target `itom.policy.procedure.tko-content.store` menjadi `itom.regulation.procedure.tko-content.store`.

**`modules/ITOM/Controllers/BusinessProcess/BusinessProcess/BusinessProcessController.php`**
- Mengubah penamaan method controller agar sesuai dengan konvensi nama bahasa Inggris (`BusinessProcess`).

**`resources/js/Pages/modules/ITOM/BusinessProcess/Index.vue`**
- Mengubah referensi route/prefiks komponen Vue agar menggunakan `business-process-v2`.

**`resources/js/Composables/useNavigation.js`**
- Mengubah navigasi sidebar menu dari `proses-bisnis-v2` menjadi `business-process-v2`.

**`docs/standart/ui/headerContent.md`**
- Pembaruan kecil terkait penamaan standar UI.

### File/Folder Dihapus
**`resources/js/Components/modules/ITOM/Architecture/ProsesBisnis/`**
- Dihapus karena telah didepresiasi dan dipindahkan ke `resources/js/Components/modules/ITOM/BusinessProcess/`.

---

## ⚠️ Kendala & Solusi
1. **Ziggy Route Error (`itom.policy.procedure.index` / `itom.regulation.procedure.manage` not in route list)**:
   - **Penyebab**: Definisi route alias duplikat dengan URI yang sama persis di `web.php` menyebabkan Laravel menimpa route sebelumnya (`regulation.procedure.`) dan hanya mendaftarkan yang terakhir (`policy.procedure.`). Hal ini memicu error ketika kode memanggil route name yang tertimpa.
   - **Solusi**: Menyelesaikan refaktorisasi penuh pada sisi frontend (10 file Vue) agar konsisten merujuk ke prefix baru `itom.regulation.procedure.*` lalu menghapus total route duplikat `policy.procedure.*` dari backend.
2. **Inkonsistensi Prefiks Bahasa Indonesia & Inggris**:
   - **Penyebab**: Percampuran istilah `ProsesBisnis` dengan `BusinessProcess` pada modul ITOM.
   - **Solusi**: Melakukan refactoring menyeluruh agar konsisten menggunakan penamaan bahasa Inggris (`BusinessProcess` dan `business-process-v2`).
3. **Restorasi Standar Service**:
   - **Penyebab**: File `docs/standart/service.md` terdeteksi kosong karena tidak sengaja terhapus dalam commit sebelumnya (`c1f63c0d`).
   - **Solusi**: Mengembalikan konten standar optimasi service dari riwayat Git (commit `f28ada7c`) agar dapat dijadikan referensi.

---

## 🚀 Progres Terbaru (Optimasi Halaman & Service ITOM Operating Model)
- [x] Restorasi file dokumentasi standar `docs/standart/service.md` dari riwayat Git.
- [x] Refaktorisasi 5 halaman frontend di bawah `resources/js/Pages/modules/ITOM/OperatingModel/` agar menggunakan standar **Inertia v2 Deferred Loading** dan komponen **TableSkeleton**:
  - `ItGovernance/Index.vue`
  - `ItManagement/Index.vue`
  - `RaciAnalysis/Index.vue`
  - `RaciAnalysis/Manage.vue` (Dilengkapi dengan `watch` untuk inisialisasi reactive state saat deferred props terisi)
  - `RaciAnalysis/Responsible.vue`
- [x] Penerapan standard query di backend controller & service layer:
  - Defer props (`steeringRows`, `organizationOptions`, `organizationStructureRows`, `objectives`, `roles`, `mappings`, `responsibles`) di `OperatingModelController`, `PracticeRoleController`, dan `ResponsibleController`.
  - Optimasi **Selective Columns Eager Loading** untuk relasi `organization` di `ItGovernance.php` dan `company` di `ItManagement.php`.
  - Penghapusan **Redundant Eager Loading** `practices.roles` di `PracticeRoleController.php` (karena data lookup di frontend langsung menggunakan mapping array).
  - Pembatasan select kolom spesifik untuk `MstObjective`, `MstPractice`, dan `MstResponsible`.
- [x] Melakukan build aset produksi (`npm run build`) dengan sukses tanpa error compile ✅

---

## 📋 Rencana Langkah Selanjutnya
Menunggu instruksi tugas selanjutnya dari user.