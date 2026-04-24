# 📄 Product Requirements Document (PRD)
## Optimasi Performa Halaman Strategic House

---

## 1. 📌 Latar Belakang

Halaman **Strategic House** mengalami performa lambat saat load awal dikarenakan:

- Banyaknya query yang redundan antar service
- Penggunaan Eloquent ORM yang berlebihan (hydration cost tinggi)
- Overfetching data (mengambil semua data sekaligus)
- Tidak adanya pemisahan loading antar sub menu
- Payload JSON terlalu besar

---

## 2. 🎯 Tujuan

Meningkatkan performa halaman Strategic House dengan target:

- ⏱️ Mengurangi waktu load hingga **50–70%**
- 📦 Mengurangi ukuran payload JSON
- ⚡ Mengurangi jumlah query ke database
- 🧠 Meningkatkan efisiensi memory usage
- 🚀 Meningkatkan scalability sistem

---

## 3. 🧩 Ruang Lingkup

### Backend:
- StrategicHousePageService
- StrategicPillarPageService
- BusinessStrategyService
- ItBuildingBlockService

### Frontend:
- Inertia.js rendering
- Submenu lazy loading

---

## 4. 🛠️ Solusi & Strategi

### 4.1 Konsolidasi Query
Ambil data `MstInitiative` satu kali di root service dan reuse.

### 4.2 Penggunaan Query
- Eloquent → relasi kompleks
- Query Builder → data referensi
- Raw SQL → agregasi

### 4.3 Optimasi Query
Gunakan select terbatas dan eager loading spesifik.

### 4.4 DTO Mapping
Hindari kirim full model ke frontend.

### 4.5 Lazy Loading
Gunakan Inertia lazy/defer untuk load bertahap.

### 4.6 Cache Strategy
Gunakan granular cache dengan tagging.

### 4.7 Filtering
Gunakan dynamic filtering untuk menghindari overfetching.

### 4.8 Index Database
Tambahkan index pada kolom penting.

### 4.9 Aggregation Optimization
Gunakan DB::raw untuk operasi agregasi.

### 4.10 Modular Service
Pisahkan service berdasarkan domain.

---

## 5. 🧪 Non-Functional Requirements

| Kriteria | Target |
|--------|------|
| Load Time | < 2 detik |
| Query Count | ↓ 50% |
| Payload Size | ↓ 40% |
| Scalability | > 10k data |

---

## 6. 📊 Risiko

| Risiko | Mitigasi |
|------|--------|
| Cache stale | Cache tagging |
| Data tidak sinkron | Invalidation |
| Refactor kompleks | Bertahap |

---

## 7. 🚀 Roadmap

### Phase 1
- Konsolidasi query
- Select optimization

### Phase 2
- DTO + Cache

### Phase 3
- Lazy loading

### Phase 4
- Index + refactor

---

## 8. ✅ Success Metrics

- Load lebih cepat
- UX lebih responsif
- Server load menurun

---

## 9. 📌 Kesimpulan

Pendekatan utama:
**Load minimal data first, defer the rest**
