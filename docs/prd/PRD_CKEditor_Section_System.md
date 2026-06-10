# PRD – Rich Document Editor berbasis CKEditor 5 + Structured Section System

## 1. Ringkasan Produk
Sistem ini adalah document editor berbasis web yang menggunakan CKEditor 5 sebagai editor utama, dengan kemampuan menyimpan konten dalam struktur terpisah:

- tko_section → struktur bagian dokumen (heading / section)
- tko_content → isi dari setiap section

Tujuan utama adalah mengubah editor WYSIWYG menjadi structured document system seperti Notion / Google Docs backend sederhana.

## 2. Problem Statement
CKEditor 5 menghasilkan output HTML yang tidak terstruktur terhadap kebutuhan database section-based.

Masalah:
- HTML CKEditor tidak langsung cocok dengan schema
- Sulit melakukan edit per section
- Tidak ada kontrol struktur dokumen

## 3. Goals
- Integrasi CKEditor 5 dengan sistem section-based database
- Menyimpan konten dalam struktur section → content
- Edit per section

## 4. Scope
In Scope:
- CKEditor 5 integration
- Parsing HTML → section/content
- Save & load structure

Out of Scope:
- Real-time collaboration
- Versioning advanced

## 5. User Flow
Create:
CKEditor → Save → HTML → Parser → DB

Load:
DB → Merge → HTML → CKEditor

Edit:
Load → Edit → Save ulang

## 6. Functional Requirements
### Editor
- Bold, italic, heading, list

### Parser
HTML → structured JSON:
- H1 = section
- P = content

### Database
tko_section:
- id, document_id, title, order

tko_content:
- id, section_id, content, order

### Save Mechanism
- Parse HTML
- Insert section & content

### Load Mechanism
- Join section + content
- Convert to HTML

## 7. Architecture
CKEditor → HTML → Parser → JSON → DB

## 8. Non Functional Requirements
- Parsing < 200ms
- Secure HTML sanitization
- Support 100+ sections

## 9. Future Improvements
- Drag & drop section
- Notion-like block editor
- Real-time collaboration

## 10. Risks
- HTML inconsistent
- Parsing error
- Performance overhead

## 11. Success Metrics
- 100% parse success
- <1s load time
- Stable structure per document
