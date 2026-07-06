# Standar UI Tabel (Table Content UI Standard)

Dokumen ini mendefinisikan standar desain, ukuran, padding, dan class utility Tailwind CSS yang digunakan untuk komponen tabel di seluruh aplikasi CIS (Collaboration Information System).

---

## 1. Struktur Kontainer & Layout Tabel

Setiap tabel harus dibungkus dalam Card Kontainer yang rapi dengan spesifikasi berikut:
- **Card Container:** `overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]`
- **Card Header (Filter/Tombol Aksi/Search):** Kontrol filter, kolom pencarian, atau tombol tambah harus menyatu di bagian atas Card Container sebagai header tabel (bukan diletakkan pada card terpisah).
  - **Wrapper Styling:** `<div class="flex flex-row items-center justify-between gap-3 px-5 py-3 border-b border-slate-200 dark:border-white/10 flex-wrap">`
  - Gunakan `flex-wrap` agar elemen mengalir dengan baik saat ukuran layar menyusut.
- **Responsive Wrapper:** Menggunakan `<div class="overflow-x-auto">` di sekeliling tag `<table>` untuk memastikan tabel horizontal-scrollable pada layar kecil.
- **Table Tag:** `<table class="w-full text-left border-collapse">`

---

## 2. Struktur Baris & Kolom Tabel

### A. Kepala Tabel (Table Header - `thead`)
- **Row Styling (`tr`):** `border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/5 dark:bg-[#1f1f1f]/50 dark:text-slate-400`
- **Cell Padding (`th`):** `px-5 py-3` (atau disesuaikan sedikit untuk kolom tindakan/actions).

### B. Baris Data (Table Body - `tbody`)
- **Body Class (`tbody`):** `divide-y divide-slate-100 dark:divide-white/5 text-xs font-medium text-slate-700 dark:text-slate-300`
- **Row Hover Effect (`tr`):** `hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition duration-150`
- **Cell Padding (`td`):** `px-5 py-3.5` (Padding horizontal 20px, vertical 14px).

---

## 3. Standar Tombol Aksi di Kolom Tabel (Table Action Buttons)

Tabel seringkali memiliki tombol aksi seperti "Edit" atau "Delete/Hapus". Tombol aksi harus seragam menggunakan ukuran kecil yang nyaman di dalam sel.

### A. Tombol Edit (Secondary / Gray-bordered)
- **Tailwind Classes:** `inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5`

### B. Tombol Delete/Hapus (Danger / Red-bordered)
- **Tailwind Classes:** `inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:border-red-300 active:scale-95 dark:border-red-500/20 dark:bg-[#1a1a1a] dark:text-red-400 dark:hover:bg-red-500/10`
