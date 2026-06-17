# File Storage Schema

## Tujuan

Menyediakan mekanisme penyimpanan dokumen yang aman untuk file bersifat confidential tanpa menggunakan layanan penyimpanan pihak ketiga (AWS S3, Google Drive, Dropbox, dan sebagainya).

---

# Arsitektur Penyimpanan

```text
Client
   │
   ▼
Laravel Application
   │
   ├── Database (Metadata File)
   │
   └── Storage Server
         └── storage/app/private
```

File fisik disimpan pada server internal menggunakan Laravel Storage, sedangkan database hanya menyimpan metadata file.

---

# Struktur Direktori

```text
storage/
└── app/
    └── private/
        ├── strategic-pillars/
        ├── project-charters/
        ├── implementation-reports/
        ├── review-documents/
        └── temporary/
```

Contoh:

```text
storage/app/private/strategic-pillars/8f3d9a7c.pdf
storage/app/private/review-documents/2a7b4c8d.docx
```

---

# Struktur Database

## Tabel documents

| Kolom         | Tipe         | Keterangan                     |
| ------------- | ------------ | ------------------------------ |
| id            | bigint       | Primary Key                    |
| uuid          | uuid         | Identifier unik                |
| entity_type   | varchar(100) | Jenis modul yang memiliki file |
| entity_id     | bigint       | ID data pemilik file           |
| original_name | varchar(255) | Nama file asli                 |
| stored_name   | varchar(255) | Nama file di storage           |
| path          | text         | Lokasi file                    |
| mime_type     | varchar(100) | Tipe file                      |
| extension     | varchar(20)  | Ekstensi file                  |
| size          | bigint       | Ukuran file (bytes)            |
| uploaded_by   | bigint       | User pengunggah                |
| created_at    | timestamp    | Waktu upload                   |
| updated_at    | timestamp    | Waktu update                   |

---

# Relasi Data

## Contoh Strategic Pillar

```text
strategic_pillars
└── id = 10

documents
└── entity_type = strategic_pillar
└── entity_id = 10
```

## Contoh Project Charter

```text
projects
└── id = 25

documents
└── entity_type = project
└── entity_id = 25
```

Dengan pendekatan ini satu tabel `documents` dapat digunakan oleh seluruh modul sistem.

---

# Proses Upload

1. User memilih file.
2. Laravel melakukan validasi file.
3. Sistem membuat nama file unik (UUID).
4. File disimpan ke Storage Laravel.
5. Metadata disimpan ke tabel `documents`.

Flow:

```text
User Upload
      │
      ▼
Validation
      │
      ▼
Generate UUID Filename
      │
      ▼
Store File
      │
      ▼
Save Metadata
```

---

# Contoh Penyimpanan

## File Asli

```text
Strategic_House_2026.pdf
```

## Nama di Storage

```text
b7c2d4a8-5d9f-4f20-bb7c-1d4a0e8f9a11.pdf
```

## Record Database

```json
{
  "id": 1,
  "uuid": "b7c2d4a8-5d9f-4f20-bb7c-1d4a0e8f9a11",
  "entity_type": "strategic_pillar",
  "entity_id": 10,
  "original_name": "Strategic_House_2026.pdf",
  "stored_name": "b7c2d4a8-5d9f-4f20-bb7c-1d4a0e8f9a11.pdf",
  "path": "private/strategic-pillars/b7c2d4a8-5d9f-4f20-bb7c-1d4a0e8f9a11.pdf",
  "mime_type": "application/pdf",
  "size": 512000
}
```

---

# Mekanisme Download

File tidak boleh diakses langsung menggunakan URL.

Semua akses file harus melalui Controller Laravel.

```text
User Request Download
          │
          ▼
Authentication
          │
          ▼
Authorization
          │
          ▼
Retrieve Metadata
          │
          ▼
Read Storage File
          │
          ▼
Return Download Response
```

---

# Keamanan

## Wajib

* File disimpan pada `storage/app/private`
* Nama file menggunakan UUID
* Download melalui Controller
* Menggunakan Laravel Policy
* Menggunakan Role-Based Access Control (RBAC)

## Opsional

* Enkripsi file sebelum penyimpanan
* Audit log aktivitas file
* Versioning dokumen
* Digital signature

---

# Rekomendasi untuk Sistem Enterprise

```text
Database
├── users
├── roles
├── permissions
├── documents
└── audit_logs

Storage
└── storage/app/private

Application
├── Authentication
├── Authorization
├── Document Management
└── Audit Trail
```

Skema ini cocok untuk sistem manajemen dokumen internal perusahaan yang membutuhkan keamanan tinggi, kontrol akses berbasis role, serta kemudahan integrasi dengan berbagai modul aplikasi.
