# PMO Project Charter - Sistem Manajemen Project Management Office

Laravel 12 + Vue 3 monolith untuk pengelolaan Project Management Office (PMO).

---

## 🎯 Target Utama Sesi Ini
- [x] Inisialisasi protokol memori fisik (`MEMORY.md`)
- [x] Memetakan struktur proyek secara menyeluruh
- [x] Menambahkan navigasi tab di halaman Procedure Index (mirip GuidanceChapterNavigation)

---

## 🚀 Progres Saat Ini
- [x] Mapping tech stack & struktur direktori
- [x] Membuat `ProcedureSectionNavigation.vue` komponen navigasi horizontal
- [x] Update `Index.vue` - tab-based content switching (Fungsi | Prosedur | Diagram Alir)
- [x] Update `Manage.vue` - navigasi + section IDs
- [x] Build frontend berhasil ✅

---

## 📦 Perubahan Terakhir

### File Baru
**`resources/js/Components/Regulation/ProcedureSectionNavigation.vue`**
- Navigasi horizontal 3 tombol: **Fungsi/Unit** | **Prosedur** | **Diagram Alir**
- Styling identik dengan `GuidanceChapterNavigation.vue`
- v-model binding: `@update:activeSection` emit ke parent
- Icon SVG inline (Users, Document, Diagram)

### File Dimodifikasi
**`resources/js/Pages/Policy/Procedure/Index.vue`**
- Import `ProcedureSectionNavigation` dengan `v-model="activeTab"`
- **Manage template & View template** keduanya punya tab navigasi
- Content switching via `v-show="activeTab === 'fungsi'/'prosedur'/'diagram'"`

**`resources/js/Pages/Policy/Procedure/Manage.vue`**
- Import `ProcedureSectionNavigation`
- ID anchors + scroll-mt-24
- Navigasi setelah header (sementara pakai scroll-to-section)

---

## ⚠️ Kendala & Solusi
1. **Salah file**: Awalnya cuma update `Index.vue` tapi halaman Manage pakai `Manage.vue` → sudah diperbaiki
2. **Scroll vs Tab**: Awal pakai scroll-to-section, user minta tab-based switching → diubah ke `v-show` tabs
3. **Non-manage view**: Tab navigasi juga ditambahkan di template `v-else`

---

## 📋 Rencana Langkah Selanjutnya
Menunggu instruksi tugas selanjutnya dari user.