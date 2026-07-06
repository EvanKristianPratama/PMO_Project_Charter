# Standar UI Modal & Field Form (Modal & Form Input UI Standard)

Dokumen ini mendefinisikan standar desain, struktur HTML/Vue, class utility Tailwind CSS untuk komponen modal (dialog) serta input field form di seluruh aplikasi.

---

## 1. Komponen Modal Standar (ConfirmationModal)

Semua modal harus menggunakan komponen `ConfirmationModal` (`@/Components/ConfirmationModal.vue`). **Jangan** membuat modal secara manual menggunakan `<div v-if="..." class="fixed inset-0 ...">`.

### A. Import & Penggunaan Dasar

```vue
<script setup>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
</script>
```

### B. Props Standar

| Prop | Tipe | Default | Keterangan |
|------|------|---------|------------|
| `show` | Boolean | `false` | Mengontrol visibilitas modal |
| `title` | String | `'Konfirmasi Aksi'` | Judul modal |
| `message` | String | — | Pesan deskripsi di bawah judul |
| `confirm-text` | String | `'Ya, Lanjutkan'` | Teks tombol konfirmasi |
| `cancel-text` | String | `'Batal'` | Teks tombol batal (kosongkan `""` untuk menyembunyikan) |
| `type` | String | `'danger'` | Warna tema: `danger`, `warning`, `info`, `success` |
| `loading` | Boolean | `false` | Tampilkan spinner pada tombol konfirmasi |
| `max-width` | String | `'md'` | Lebar maksimum: `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl` |

### C. Events

- `@close` — Dipanggil saat modal ditutup (klik tombol batal / klik backdrop)
- `@confirm` — Dipanggil saat tombol konfirmasi diklik

### D. Contoh Penggunaan

```html
<ConfirmationModal
    :show="isModalOpen"
    :title="isEditing ? 'Edit Data' : 'Tambah Data Baru'"
    :message="isEditing ? 'Silakan sesuaikan data di bawah ini.' : 'Silakan isi formulir di bawah ini.'"
    confirm-text="Simpan"
    cancel-text="Batal"
    type="info"
    :loading="form.processing"
    @close="isModalOpen = false"
    @confirm="submitForm"
>
    <div class="mt-4 space-y-4 text-left">
        <!-- Konten form ditaruh di sini sebagai slot -->
    </div>
</ConfirmationModal>
```

---

## 2. Struktur Field Input Form

### A. Label Form
- **Label Standar:** `text-xs font-semibold text-slate-700 dark:text-slate-300`
- **Label Kecil (Sub-field):** `text-[10px] font-semibold text-slate-600 dark:text-slate-400`
- **Field Container (Spasi antar field):** `flex flex-col gap-1.5`

### B. Input Text & Dropdown Select Standar
- **Tailwind Classes:** `w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition`

---

## 3. Komponen Seleksi Kustom — Single Select (Custom Selection List / Autocomplete)

Ketika membutuhkan pemetaan data/seleksi tunggal dengan pencarian langsung di dalam modal, gunakan listbox seleksi kustom berikut.

### A. Kontainer Luar Listbox
- **Tailwind Classes:** `rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden`

### B. Kolom Pencarian Listbox (Search Bar)
- **Container Section:** `px-2 py-1.5 border-b border-slate-200 dark:border-white/10`
- **Input Search:** `w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400`

### C. Daftar Pilihan Scrollable
- **HTML Element (`ul`):** `<ul class="max-h-44 overflow-y-auto">`
- **Item List (`li`):**
  - **Status Biasa:** `flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none hover:bg-slate-50 dark:hover:bg-white/5`
  - **Status Terpilih (Selected):** `bg-blue-50 dark:bg-blue-500/10`
  - **Level Indentasi (Nested/Hierarchy):** `:style="{ paddingLeft: \`\${8 + (item._level || 0) * 14}px\` }"`
  - **Bullet Separator:** `<span class="mr-1.5 shrink-0 font-medium text-slate-400 dark:text-slate-500">—</span>`

### D. Label Item Terpilih (di bawah listbox)
- **Tailwind Classes:** `text-[10px] text-blue-600 dark:text-blue-400 font-medium`
- **Format:** `✓ Dipilih: {{ selectedItemName }}`

---

## 4. Komponen Seleksi Kustom — Multi Select

Ketika membutuhkan pemetaan data/seleksi **lebih dari satu item** (misalnya memilih beberapa regulasi), gunakan pola multi-select berikut. Strukturnya sama dengan Single Select (Section 3), dengan perbedaan:

- **Klik item** berfungsi sebagai toggle (pilih/batal pilih), bukan mengganti seleksi.
- **Item yang terpilih ditampilkan sebagai daftar di bawah listbox** sebagai kartu yang bisa di-remove.

### A. Item List (Toggle Click)
Sama seperti Section 3.C, namun `@click` melakukan toggle (tambah/hapus dari array):

```html
<li
    v-for="item in filteredItems"
    :key="item.id"
    @click="toggleItem(item.id)"
    class="flex items-center cursor-pointer py-1.5 px-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
    :class="selectedIds.includes(item.id)
        ? 'bg-blue-50 dark:bg-blue-500/10'
        : 'hover:bg-slate-50 dark:hover:bg-white/5'"
>
    <span class="mr-1.5 shrink-0 font-medium text-slate-400 dark:text-slate-500">—</span>
    <span
        :class="selectedIds.includes(item.id)
            ? 'font-semibold text-blue-700 dark:text-blue-300'
            : 'text-slate-600 dark:text-slate-400'"
    >{{ item.name }}</span>
</li>
```

### B. Daftar Item Terpilih (Di bawah Listbox)
Tampilkan item terpilih sebagai kartu scrollable dengan tombol hapus:

- **Counter Label:** `text-[10px] text-blue-600 dark:text-blue-400 font-medium` — Format: `✓ {{ count }} item dipilih`
- **Container List:** `space-y-1 max-h-32 overflow-y-auto pr-1`
- **Kartu Item Terpilih:** `flex items-center justify-between px-3 py-1.5 rounded-lg border border-slate-100 bg-white dark:border-white/5 dark:bg-[#1a1a1a] hover:bg-slate-50 dark:hover:bg-white/5 transition`
- **Dot Indicator:** `<span class="flex h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>`
- **Teks Item:** `text-[11px] font-medium text-slate-800 dark:text-slate-200`
- **Tombol Hapus:** `inline-flex items-center justify-center rounded-md p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition shrink-0`

---

## 5. Contoh Implementasi

### A. Single Select (Pilih Satu)

```html
<!-- Autocomplete Selection List -->
<div class="flex flex-col gap-2">
    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">
        Pilih Jabatan / Fungsi
    </label>
    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
        <!-- Search bar -->
        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
            <input
                v-model="searchTerm"
                type="text"
                placeholder="Cari..."
                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
            />
        </div>
        <!-- Scrollable list -->
        <ul class="max-h-44 overflow-y-auto">
            <li
                v-for="item in items"
                :key="item.id"
                @click="selectId(item.id)"
                class="flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none hover:bg-slate-50 dark:hover:bg-white/5"
                :class="selectedId === item.id ? 'bg-blue-50 dark:bg-blue-500/10' : ''"
            >
                <span class="mr-1.5 shrink-0 font-medium text-slate-400 dark:text-slate-500">—</span>
                <span :class="selectedId === item.id ? 'font-semibold text-blue-700 dark:text-blue-300' : 'text-slate-600 dark:text-slate-400'">{{ item.name }}</span>
            </li>
        </ul>
    </div>
    <!-- Selected label -->
    <p v-if="selectedId" class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">
        ✓ Dipilih: {{ selectedItemName }}
    </p>
</div>
```

### B. Multi Select (Pilih Banyak) dengan Daftar Terpilih

```html
<!-- Multi-Select List -->
<div class="flex flex-col gap-2">
    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">
        Petakan ke Regulasi
    </label>
    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
        <!-- Search bar -->
        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
            <input
                v-model="searchTerm"
                type="text"
                placeholder="Cari regulasi..."
                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
            />
        </div>
        <!-- Scrollable list -->
        <ul class="max-h-44 overflow-y-auto">
            <li
                v-for="item in filteredItems"
                :key="item.id"
                @click="toggleItem(item.id)"
                class="flex items-center cursor-pointer py-1.5 px-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
                :class="selectedIds.includes(item.id)
                    ? 'bg-blue-50 dark:bg-blue-500/10'
                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
            >
                <span class="mr-1.5 shrink-0 font-medium text-slate-400 dark:text-slate-500">—</span>
                <span
                    :class="selectedIds.includes(item.id)
                        ? 'font-semibold text-blue-700 dark:text-blue-300'
                        : 'text-slate-600 dark:text-slate-400'"
                >{{ item.name }}</span>
            </li>
        </ul>
    </div>

    <!-- Selected Items Display -->
    <div v-if="selectedItems.length > 0" class="space-y-1.5">
        <p class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">
            ✓ {{ selectedItems.length }} item dipilih
        </p>
        <div class="space-y-1 max-h-32 overflow-y-auto pr-1">
            <div
                v-for="item in selectedItems"
                :key="'sel-' + item.id"
                class="flex items-center justify-between px-3 py-1.5 rounded-lg border border-slate-100 bg-white dark:border-white/5 dark:bg-[#1a1a1a] hover:bg-slate-50 dark:hover:bg-white/5 transition"
            >
                <div class="flex items-center gap-2">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>
                    <span class="text-[11px] font-medium text-slate-800 dark:text-slate-200">{{ item.name }}</span>
                </div>
                <button
                    type="button"
                    @click="removeItem(item.id)"
                    class="inline-flex items-center justify-center rounded-md p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition shrink-0"
                    title="Hapus"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
```
