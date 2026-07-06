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
- [x] Menyelesaikan error routing Ziggy `itom.policy.procedure.index`
- [x] Update dokumentasi `docs/agent/MEMORY.md`

---

## 🚀 Progres Saat Ini
- [x] Mengubah prefiks route dan nama dari `proses-bisnis-v2` menjadi `business-process-v2` di `modules/ITOM/Routes/web.php`
- [x] Mengubah nama method controller (`storeProsesBisnisV2`, `updateProsesBisnisV2`, `destroyProsesBisnisV2` -> `storeBusinessProcessV2`, `updateBusinessProcessV2`, `destroyBusinessProcessV2`) di `BusinessProcessController.php`
- [x] Memindahkan komponen frontend dari `Architecture/ProsesBisnis` ke `BusinessProcess` dan memperbarui semua import/pemanggilan komponen terkait
- [x] Menyelesaikan error Ziggy route `itom.policy.procedure.index` dengan mendaftarkan alias route group duplikat di `web.php` agar tetap kompatibel dengan komponen frontend lama yang masih merujuk ke prefix `policy.procedure`
- [x] Melakukan verifikasi dengan `php artisan route:list` dan build frontend `npm run build` sukses tanpa error ✅

---

## 📦 Perubahan Terakhir

### File Baru / Dipindahkan
**`resources/js/Components/modules/ITOM/BusinessProcess/*`** (Pindahan dari `Architecture/ProsesBisnis`)
- Memindahkan semua file komponen proses bisnis ke direktori yang tepat sesuai konvensi modular baru.

### File Dimodifikasi
**`modules/ITOM/Routes/web.php`**
- Mengubah route name/prefix `proses-bisnis-v2` ke `business-process-v2`
- Menambahkan route group duplikat dengan prefix name `policy.procedure.` untuk backward compatibility / alias bagi `regulation.procedure.` guna menyelesaikan error Ziggy.

**`modules/ITOM/Controllers/BusinessProcess/BusinessProcess/BusinessProcessController.php`**
- Mengubah penamaan method controller agar sesuai dengan konvensi nama bahasa Inggris (`BusinessProcess`).

**`resources/js/Pages/modules/ITOM/BusinessProcess/Index.vue`**
- Mengubah referensi route/prefiks komponen Vue agar menggunakan `business-process-v2`.

**`resources/js/Components/modules/ITOM/BusinessProcess/BusinessProcessV2/BusinessProcessV2.vue`**
- Memperbarui path import dan pemanggilan API/route yang relevan.

**`resources/js/Composables/useNavigation.js`**
- Mengubah navigasi sidebar menu dari `proses-bisnis-v2` menjadi `business-process-v2`.

**`docs/standart/ui/headerContent.md`**
- Pembaruan kecil terkait penamaan standar UI.

### File/Folder Dihapus
**`resources/js/Components/modules/ITOM/Architecture/ProsesBisnis/`**
- Dihapus karena telah didepresiasi dan dipindahkan ke `resources/js/Components/modules/ITOM/BusinessProcess/`.

---

## ⚠️ Kendala & Solusi
1. **Ziggy Route Error (`itom.policy.procedure.index` not in route list)**:
   - **Penyebab**: Perubahan grup route dari `/policy` ke `/regulation` menyebabkan nama route di Ziggy berubah, sementara komponen frontend lama/shared masih memanggil route lama.
   - **Solusi**: Mendaftarkan kembali grup route duplikat/alias di `web.php` dengan prefix `policy.procedure.` agar kompatibilitas ke belakang tetap terjaga tanpa merusak frontend.
2. **Inkonsistensi Prefiks Bahasa Indonesia & Inggris**:
   - **Penyebab**: Percampuran istilah `ProsesBisnis` dengan `BusinessProcess` pada modul ITOM.
   - **Solusi**: Melakukan refactoring menyeluruh agar konsisten menggunakan penamaan bahasa Inggris (`BusinessProcess` dan `business-process-v2`).

---

## 📋 Rencana Langkah Selanjutnya
Menunggu instruksi tugas selanjutnya dari user.