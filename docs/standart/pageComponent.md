# Standar Optimasi Halaman dengan Inertia v2 Deferred Loading

Dokumen ini mendefinisikan standar implementasi optimasi halaman untuk meningkatkan performa (skor Lighthouse: *First Contentful Paint*, *Largest Contentful Paint*, *Minimize Main Thread Work*, dan *Render Blocking Requests*) menggunakan fitur **Deferred Loading** pada Inertia v2 dan komponen **TableSkeleton**.

---

## 1. Konsep Utama
Untuk halaman yang memuat data tabular atau relasi database yang kompleks, data tidak boleh dikirim secara sinkron bersamaan dengan respons awal HTML. 
Sebaliknya:
- Halaman HTML/shell Vue harus dimuat secara instan (*non-blocking*).
- Data berat dikirim secara asinkron setelah halaman ter-render.
- Tampilkan *skeleton loader* yang merepresentasikan struktur tabel asli selama data sedang diambil.

---

## 2. Standar Backend (Laravel Controller)

Gunakan helper `Inertia::defer()` untuk membungkus properti data yang memerlukan query database atau kalkulasi berat.

### Contoh Implementasi:
```php
use Inertia\Inertia;
use Inertia\Response;
use App\Services\BusinessProcess\ApqcService;

public function index(ApqcService $apqcService): Response
{
    return Inertia::render('modules/ITOM/BusinessProcess/APQC/Index', [
        // Gunakan Inertia::defer untuk menunda pemuatan query berat secara asinkron
        'apqcList' => Inertia::defer(fn() => $apqcService->getApqcList()),
    ]);
}
```

---

## 3. Standar Frontend (Vue Component)

1. Import `Deferred` dari `@inertiajs/vue3`.
2. Import `TableSkeleton` dari `@/Components/Shared/TableSkeleton.vue`.
3. Bungkus komponen utama tabel dengan `<Deferred>` menggunakan atribut `data` atau `:data` yang sesuai.
4. Sediakan fallback `<TableSkeleton />` pada slot `#fallback`.

### Contoh Implementasi:
```vue
<template>
    <ModulLayout title="Dokumen Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">
            <!-- Bungkus tabel utama dengan Deferred -->
            <Deferred data="apqcList">
                <template #fallback>
                    <!-- Tampilkan skeleton selama data dimuat -->
                    <TableSkeleton />
                </template>
                <!-- Render tabel setelah properti apqcList terisi -->
                <APQCTable :apqc-list="apqcList" />
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import APQCTable from '@/Components/modules/ITOM/BusinessProcess/APQC/APQCTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import { Deferred } from '@inertiajs/vue3';

defineProps({
    apqcList: {
        type: Array,
        default: () => [],
    },
});
</script>
```

---

## 4. Keuntungan Arsitektur
- **FCP & LCP Instan**: Browser dapat merender shell halaman, sidebar, dan header tanpa harus menunggu pemrosesan database selesai di sisi server.
- **Sleek UX**: Transisi loading terasa premium karena *skeleton pulse* menyamai struktur layout asli daripada blank screen atau spinner melingkar standar.

---

## 5. Optimasi Main-Thread Work (Pohon Tree/Table)
Untuk mencegah main-thread blocking yang parah (>30 detik) akibat pemuatan terlalu banyak komponen secara rekursif saat inisiasi awal halaman (*mounting/hydration*), ikuti aturan berikut:
1. **Collapsed by Default**: Komponen data berbentuk pohon (seperti `prosesBisnisV2`, `apqcList`, atau `regulations`) harus dinonaktifkan perluasan penuhnya saat dimuat pertama kali.
2. Inisialisasikan `expandLevel` ke nilai `'0'` (Collapse All) dan set `expandedIds` sebagai `new Set()` kosong saat komponen me-mount.

### Contoh Standar Inisialisasi:
```javascript
const expandedIds = ref(new Set());
const expandLevel = ref('0');

const initializeExpanded = () => {
    // Biarkan semua child tertutup secara default saat halaman dimuat pertama kali
    expandedIds.value = new Set();
    expandLevel.value = '0';
};

onMounted(() => {
    initializeExpanded();
});
```

---

## 6. Build Aset Produksi (Minifikasi, Unused JS, & Render Blocking)
Jika hasil audit Lighthouse mendeteksi masalah ukuran aset JavaScript tidak diminifikasi atau terdapat JavaScript yang tidak digunakan:
1. **Lighthouse Dev Server Warning**: Evaluasi performa yang dilakukan pada dev server (`npm run dev`) akan selalu menampilkan skor performa yang rendah karena berkas JavaScript disajikan secara mentah (unminified) lengkap dengan Hot Module Replacement (HMR) script.
2. **Standard Production Build**: Untuk meluncurkan halaman ke tahap produksi dan mendapatkan hasil audit Lighthouse yang valid, lakukan build aset dengan perintah:
   ```bash
   npm run build
   ```
   Langkah ini akan secara otomatis memicu proses minifikasi kode menggunakan Vite, melakukan eliminasi kode mati (*tree-shaking*), serta melakukan pembagian aset (*code splitting*) sehingga file JS tidak menghambat render awal (*non-blocking*).

