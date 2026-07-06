# Standar UI Header Konten (Header Content UI Standard)

Dokumen ini menjelaskan standar desain, ukuran, dan class utility Tailwind CSS yang digunakan untuk header konten/modul dalam aplikasi PMO Project Charter. Acuan ini diambil dari komponen [ApqcMap.vue](file:///c:/Users/user/Documents/PMO_Project_Charter-main/resources/js/Components/modules/ITOM/BusinessProcess/APQC/ApqcMap.vue).

---

## 1. Template Kode (HTML / Vue)

Berikut adalah struktur dasar HTML menggunakan Tailwind CSS untuk header konten:

```html
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
    <div class="flex flex-col gap-3 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
        <!-- Bagian Kiri: Judul Konten -->
        <div>
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                Judul Konten/Modul
            </h2>
        </div>

        <!-- Bagian Kanan: Aksi / Navigasi / Button / Filter -->
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex shrink-0 gap-1">
                <!-- Tombol Inactive / Secondary -->
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <!-- Icon SVG -->
                    </svg>
                    Secondary Action
                </button>

                <!-- Tombol Active / Primary -->
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-600"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <!-- Icon SVG -->
                    </svg>
                    Primary Action
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 2. Detail Spesifikasi Desain & Ukuran

### A. Container Card (Pembungkus Luar)
* **Border & Border Radius:** `rounded-2xl border border-slate-200 dark:border-white/10`
* **Latar Belakang (Background):** `bg-white dark:bg-[#171717]`
* **Efek Bayangan (Shadow):** `shadow-sm`
* **Lainnya:** `overflow-hidden` untuk memastikan sudut konten tidak keluar dari radius border container.

### B. Spacing & Spacing Header Layout
* **Struktur Flexbox:** `flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between`
  * Tampilan mobile: Tersusun vertikal dengan jarak antar baris `gap-3`.
  * Tampilan tablet/desktop (`sm`): Tersusun horizontal, rata tengah secara vertikal (`items-center`), dan didorong ke sisi kiri & kanan (`justify-between`).
* **Padding:** `px-5 py-3.5` (Kiri & kanan 20px, atas & bawah 14px).

### C. Tipografi Judul (Title Typography)
* **Ukuran Font:** `text-sm` (14px)
* **Ketebalan Font:** `font-semibold` (Semi-bold, 600)
* **Warna Teks:** `text-slate-900 dark:text-white`
* **Elemen:** Menggunakan tag `h2` untuk hierarki struktur HTML yang tepat.

### D. Tombol Aksi (Action Buttons)
* **Dimensi Spacing Button:** `px-3 py-1.5` (Padding horizontal 12px, vertical 6px).
* **Ukuran Font Button:** `text-xs` (12px) dengan `font-semibold`.
* **Radius Sudut:** `rounded-lg` (Rounded 8px).
* **Transisi Hover:** Memiliki class `transition` untuk transisi warna yang halus saat di-hover.
* **Ukuran Icon SVG:** `h-3 w-3` (Tinggi & Lebar 12px) dengan `gap-1.5` antara icon dan teks.

#### Jenis Tombol:
1. **Secondary / Inactive Button:**
   * **Warna:** `bg-slate-100 text-slate-600 hover:bg-slate-200`
   * **Dark Mode:** `dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20`
2. **Primary / Active Button:**
   * **Warna:** `bg-blue-500 text-white hover:bg-blue-600`
   * **Efek Bayangan:** `shadow-sm`
