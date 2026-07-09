# Standar Zero Backend Request & Request-Level Cache Memoization

Dokumen ini mendefinisikan standar arsitektur untuk mengeliminasi request jaringan berlebih (*redundant network requests*) saat melakukan navigasi tab/halaman lokal yang datanya sudah dimuat secara penuh (*eagerly loaded*) di sisi client, dikombinasikan dengan arsitektur **Request-Level Memoization** di sisi backend.

---

## 1. Konsep Utama

Saat memuat dokumen modular atau dokumen terstruktur (seperti Kebijakan yang memiliki beberapa Bab/Section):
1. **Single Eager Load**: Seluruh data yang diperlukan oleh dokumen (dari Bab I hingga Bab IV) dimuat dalam satu request awal (atau dikirim bersamaan via Deferred Loading).
2. **Local State Navigation**: Pergantian Bab atau Section dalam satu dokumen yang sama **tidak boleh** memicu kunjungan URL baru via Inertia (`router.visit` atau `Link`). Navigasi harus ditangani sepenuhnya oleh local state (`ref`) di sisi Vue.
3. **Request-Level Memoization**: Di sisi backend Service Layer, query database berat yang dipanggil berulang kali dalam satu request lifecycle (karena dipisah di dalam helper `Inertia::defer`) wajib menggunakan cache statis memori PHP untuk mencegah duplikasi eksekusi query SQL.

---

## 2. Standar Frontend (Vue Component)

Gunakan variabel lokal `ref` untuk melacak Bab, Section, atau Tab aktif daripada menggunakan query parameter URL Inertia untuk navigasi lokal.

### Pola Implementasi (Index.vue):
```vue
<template>
    <ModulLayout :title="layoutTitle">
        <!-- Sidebar Navigation Pane -->
        <NavigationPane
            :all-sections="chapters"
            :active-tab="activeChapter"
            @update:active-tab="switchChapter"
        />

        <!-- Main Content Area -->
        <main class="content-area">
            <component
                :is="activeComponent"
                v-bind="componentProps"
            />
        </main>
    </ModulLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    activeChapter: { type: String, required: true },
    policies: { type: Array, default: () => [] },
    objectives: { type: Array, default: () => [] },
});

// 1. Inisialisasi dari prop backend, namun kelola secara lokal
const activeChapter = ref(props.activeChapter || 'bab1');

// 2. Sinkronisasi jika prop di-update dari luar (misal: ganti regulasi induk)
watch(() => props.activeChapter, (newVal) => {
    if (newVal) {
        activeChapter.value = newVal;
    }
});

// 3. Pergantian Bab dilakukan secara instan tanpa memicu kunjungan jaringan
function switchChapter(chapterKey) {
    activeChapter.value = chapterKey;
}
</script>
```

---

## 3. Standar Backend (Service Layer Memoization)

Ketika menggunakan properti deferred Inertia v2, masing-masing closure pemanggil `Inertia::defer()` berjalan secara paralel dan terisolasi. Hal ini dapat memicu pemanggilan ganda pada fungsi Service yang sama dalam satu pemuatan halaman.

Untuk mencegah *database query duplication*, terapkan pola **Memoization** menggunakan array statis di sisi PHP Service.

### Pola Implementasi (GeneralPolicyService.php):
```php
namespace App\Services\Regulation;

class GeneralPolicyService
{
    // 1. Buat kontainer cache statis
    protected static $cachedData = [];

    public function getGeneralPolicyData(?int $selectedRegulationId): array
    {
        // 2. Kembalikan data dari cache jika sudah pernah dieksekusi dalam request lifecycle yang sama
        if (isset(self::$cachedData[$selectedRegulationId])) {
            return self::$cachedData[$selectedRegulationId];
        }

        // 3. Eksekusi query database jika cache miss
        $regulations = MstRegulation::with('generalPolicies')->get();
        $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        $policies = $selectedRegulation ? $selectedRegulation->generalPolicies : collect([]);

        // 4. Simpan ke dalam cache statis sebelum mengembalikan data
        self::$cachedData[$selectedRegulationId] = [
            'policies' => $policies,
            'regulations' => $regulations,
        ];

        return self::$cachedData[$selectedRegulationId];
    }
}
```

---

## 4. Keuntungan Arsitektur
- **Zero-Latency Navigation**: Transisi antar Bab terasa instan (<1ms) dan berjalan mulus tanpa visual loading-spinner atau skeleton loader yang berkedip.
- **Efisiensi Database**: Melindungi database dari lonjakan N+1 query yang dipicu oleh Inertia partial reloads / deferred parallel closures.
- **Reduksi CPU Server**: Server PHP tidak membuang resource memproses serialisasi model Eloquent yang sama secara berulang.
