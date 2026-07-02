# Standar Sub-Menu Routing & Controller

Dokumen ini menjelaskan standar desain routing dan controller untuk sub-menu pada aplikasi web berbasis Inertia.

## 1. Pemisahan Route per Sub-Menu (Decoupled Sub-Menu Routes)

### Masalah
Di masa lalu, beberapa sub-menu yang merender tab berbeda pada satu halaman shell utama (seperti `BusinessProcess/Index.vue`) menggunakan route Laravel yang sama (misalnya `/business-process/proses-bisnis`) dengan membedakan tab hanya berdasarkan parameter query string `tab`.

Hal ini menyebabkan:
1. Satu Controller menangani terlalu banyak logika untuk tab yang berbeda (Melanggar *Single Responsibility Principle*).
2. Sulit mengontrol middleware, otorisasi, atau optimasi query database khusus untuk tab tertentu.
3. Struktur routing di Laravel tidak merepresentasikan menu yang ada pada UI.

### Standar Solusi
Setiap sub-menu harus didefinisikan dengan route Laravel yang unik dan ditangani oleh Controller spesifik masing-masing.

| Sub-Menu | Route URL | Nama Route | Controller |
| :--- | :--- | :--- | :--- |
| APQC | `/business-process/apqc` | `business-process.apqc.index` | `ApqcController` |
| Business Process | `/business-process/proses-bisnis-v2` | `business-process.proses-bisnis-v2.index` | `BusinessProcessController` |
| Function | `/business-process/function` | `business-process.function.index` | `FunctionController` |
| KPI | `/business-process/kpi` | `business-process.kpi.index` | `KpiController` |
| Regulation Mapping | `/business-process/regulation-mapping` | `business-process.regulation-mapping.index` | `RegulationMappingController` |

---

## 2. Koordinasi Routing Frontend & Deteksi Tab Aktif

Dalam standar baru, navigasi sub-menu di frontend (`useNavigation.js`) **tidak lagi menyertakan query parameter `?tab=...`**. Halaman frontend shell utama (`Index.vue`) secara dinamis menentukan tab aktif berdasarkan path URL.

### Pola Definisi Navigation
Setiap item navigasi didefinisikan bersih tanpa query parameter:
```javascript
{
    label: "Business Process",
    href: safeRoute("itom.business-process.proses-bisnis-v2.index"),
    icon: CubeIcon,
    active: (url) => (url || "").includes("/business-process/proses-bisnis-v2"),
}
```

### Deteksi Tab Aktif di Frontend (`Index.vue`)
Di sisi client, komponen `Index.vue` mendeteksi tab mana yang harus aktif berdasarkan kecocokan path pada `page.url`:
```javascript
const getTabFromUrl = () => {
    const url = page.url;
    if (url.includes('/business-process/proses-bisnis-v2')) return 'proses-bisnis-v2';
    if (url.includes('/business-process/function')) return 'function';
    if (url.includes('/business-process/kpi')) return 'kpi';
    if (url.includes('/business-process/regulation-mapping')) return 'regulation-map';
    if (url.includes('/business-process/apqc')) return 'apqc';

    // Fallback jika menggunakan query parameter lama
    const params = new URLSearchParams(url.split('?')[1] || '');
    return params.get('tab') || 'apqc';
};
```
