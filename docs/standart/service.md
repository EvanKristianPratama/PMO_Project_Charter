# Standar Optimasi Service - Query & Tree Traversal

Dokumen ini menjelaskan standar optimasi pada service layer untuk meningkatkan performa aksesbilitas data, efisiensi memori, dan kecepatan response API.

## 1. Menghindari Eager Loading yang Tidak Diperlukan (Redundant Eager Loading)

### Masalah
Menggunakan `.with('relation')` pada Eloquent query secara otomatis mengambil relasi dari database dan menyematkannya ke dalam model. Ketika data di-serialize (misalnya untuk dikirim ke frontend melalui Inertia/API), seluruh objek relasi tersebut ikut dikonversi menjadi JSON. 

If frontend hanya membutuhkan `foreign_key` (seperti `parent_id`) untuk menyusun hierarki secara mandiri di sisi client, memuat objek relasi penuh (`parent` relation) adalah pemborosan:
1. Menimbulkan query database tambahan (N+1 query overhead).
2. Duplikasi alokasi instance model Eloquent di memori server.
3. Ukuran payload JSON membengkak karena serialisasi relasi yang redundan/rekursif.

### Standar Solusi
Hapus eager loading (`with`) jika objek relasi tersebut tidak dirender langsung oleh view/frontend. Cukup gunakan kolom `foreign_key` yang sudah ada di tabel utama.

### Eager Loading Bersyarat & Nested Relations (Dot Notation)
Jika model memiliki relasi bertingkat (nested relations), gunakan dot notation (seperti `with(['relationA.relationB'])`). 
Namun, tetap pastikan bahwa relasi tersebut benar-benar dirender atau dibaca oleh view/frontend.
- **Contoh Kasus**: Eager load `['company', 'kpis', 'regulations.sopCategories']` diperbolehkan jika frontend memang menampilkan data KPI, Company, dan Kategori SOP di dalam list/tabel. Namun, relasi `parent` tetap harus dilepas dari `with` jika frontend hanya mengandalkan kolom `parent_id` untuk penyusunan hierarki (seperti pada pembangunan tree di Javascript).

---

## 2. Optimasi Penghitungan Level & Depth Hierarki (Tree Traversal)

### Masalah (Pendekatan Naif)
Untuk menghitung level/depth dari data bertingkat (parent-child), pendekatan naif seringkali melakukan traversal ke atas menggunakan loop `while` untuk setiap item:
```php
foreach ($items as $item) {
    $level = 1;
    $current = $item;
    while ($current->parent_id && isset($itemsMap[$current->parent_id])) {
        $level++;
        $current = $itemsMap[$current->parent_id];
    }
}
```
Pendekatan ini memiliki kompleksitas waktu $O(N \cdot d)$ (di mana $d$ adalah kedalaman pohon). Pada pohon yang sangat dalam atau jumlah data yang besar, server akan melakukan pencarian parent secara berulang untuk simpul yang sama, sehingga menurunkan performa secara drastis.

### Standar Solusi (Memoized Traversal)
Gunakan teknik **Memoization** untuk menyimpan hasil level/depth yang sudah dihitung sebelumnya. Dengan cara ini, level dari setiap node hanya dihitung tepat satu kali, menghasilkan kompleksitas waktu linear $O(N)$.

---

## 3. Contoh Implementasi Standar

Berikut perbandingan implementasi suboptimal vs optimal pada pengambilan data hierarki (seperti data APQC):

### Suboptimal (Sebelum Optimasi)
```php
public function getApqcList()
{
    // Menggunakan eager loading 'parent' padahal tidak digunakan oleh frontend
    $items = MstApqc::with('parent')->orderBy('id')->get();
    
    $itemsMap = $items->keyBy('id');
    
    foreach ($items as $item) {
        $level = 1;
        $current = $item;
        // Traversal redundan yang tidak dimemoisasi
        while ($current->parent_id && isset($itemsMap[$current->parent_id])) {
            $level++;
            $current = $itemsMap[$current->parent_id];
        }
        $item->setAttribute('level', $level);
        $item->setAttribute('depth', $level - 1);
    }
    
    return $items;
}
```

### Optimal (Standar Service)
```php
public function getApqcList()
{
    // 1. Tanpa eager loading 'parent' untuk menghemat memori & payload
    $items = MstApqc::orderBy('id')->get();
    
    $itemsMap = $items->keyBy('id');
    
    // 2. Menggunakan Memoization dengan kompleksitas O(N) & loop prevention
    $levels = [];
    foreach ($items as $item) {
        $id = $item->id;
        if (isset($levels[$id])) {
            continue;
        }
        
        $path = [];
        $visitedInPath = [];
        $current = $item;
        
        while ($current && !isset($levels[$current->id])) {
            if (isset($visitedInPath[$current->id])) {
                break; // Mencegah infinite loop jika ada circular reference
            }
            $visitedInPath[$current->id] = true;
            $path[] = $current;
            $current = $current->parent_id ? ($itemsMap[$current->parent_id] ?? null) : null;
        }
        
        $baseLevel = $current ? $levels[$current->id] : 0;
        
        // Isi level untuk semua simpul dalam path traversal yang dilakukan
        for ($i = count($path) - 1; $i >= 0; $i--) {
            $baseLevel++;
            $levels[$path[$i]->id] = $baseLevel;
        }
    }
    
    foreach ($items as $item) {
        $level = $levels[$item->id] ?? 1;
        $item->setAttribute('level', $level);
        $item->setAttribute('depth', $level - 1);
    }
    
    return $items;
}
```

---

## 4. Query Select Kolom Spesifik (Selective Columns Query)

### Masalah
Secara default, Laravel Eloquent melakukan `select *` yang mengambil seluruh kolom dari database. Ketika data bertambah besar dan tabel memiliki kolom teks besar, blob, atau kolom yang tidak dibutuhkan oleh frontend, memuat seluruh kolom akan memperlambat I/O database dan meningkatkan alokasi RAM server PHP.

### Standar Solusi
Gunakan method `select(['id', 'kolom1', 'kolom2'])` secara eksplisit pada query builder jika frontend hanya merender subset kolom tertentu (seperti pada data master atau data referensi sederhana).

---

## 5. Menghindari Overhead Instansiasi Collection pada Pengolahan Payload Sederhana

### Masalah
Penggunaan helper `collect($payload)->only([...])->map(...)` sangat nyaman namun memicu overhead instansiasi objek `Collection` baru serta beberapa lapis callback. Di dalam service layer yang melayani hot-paths (seperti proses penyimpanan massal atau request API berfrekuensi tinggi), overhead ini menumpuk dan memboroskan memori PHP.

### Standar Solusi
Gunakan array asli (native array) PHP dengan perulangan `foreach` untuk memfilter dan menormalisasi payload masukan.

#### Contoh Implementasi Optimal (Native PHP Array)
```php
private function normalizedPayload(array $payload): array
{
    $keys = [
        'group_business',
        'group_function',
        'subGroup_function',
        'subSubGroup_function',
    ];

    $normalized = [];
    foreach ($keys as $key) {
        if (array_key_exists($key, $payload)) {
            $val = $payload[$key];
            $val = is_string($val) ? trim($val) : $val;
            $normalized[$key] = $val === '' ? null : $val;
        }
    }

    return $normalized;
}
```

## 6. Membatasi Kolom pada Eager Loaded Relations

### Masalah
Saat melakukan eager loading (seperti `with(['relation'])`), Laravel secara default mengambil seluruh kolom dari tabel relasi tersebut. Ini memboroskan memori RAM dan memperlambat response payload jika kita hanya membutuhkan beberapa kolom dasar untuk dirender di frontend.

### Standar Solusi
Gunakan format `namaRelasi:kolom1,kolom2,kolom3` untuk membatasi kolom yang ditarik dari tabel relasi.
*Catatan Penting*: Anda **harus selalu menyertakan kunci penghubung** (seperti primary key `id` pada target relasi, atau foreign key terkait) agar Eloquent dapat menghubungkan data relasi tersebut dengan benar. Jika tidak disertakan, data relasi akan menghasilkan nilai `null`.

#### Contoh:
```php
// Membatasi kolom relasi company, regulations, dan organizations
return MstFunction::with([
    'company:id,name,singkatan',
    'regulations:id,judul,nomor,tipe,status',
    'organizations:id,name,alias'
])->orderBy('name')->get();
```
