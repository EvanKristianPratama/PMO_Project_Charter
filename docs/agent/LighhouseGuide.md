# Lighthouse Optimization Agent Guide

Dokumen ini menjelaskan cara membaca dan menindaklanjuti hasil audit Lighthouse, khususnya bagian Diagnostics (critical issues).

---

# 1. Prioritas Utama

## Minify JavaScript
- Bundle terlalu besar
- Solusi: production build + minify

## Reduce Unused JavaScript
- Banyak kode tidak terpakai
- Solusi: tree shaking + dynamic import

## Minimize Main Thread Work
- JS terlalu berat
- Solusi: lazy load + kurangi blocking

## Long Tasks
- Eksekusi >50ms
- Solusi: split task / web worker

---

# 2. Network & Payload

## Enormous Network Payload
- File terlalu besar
- Solusi: compress + optimize images

## Render Blocking Requests
- CSS/JS menghambat render
- Solusi: defer + async + critical CSS

## Document Latency
- Server lambat
- Solusi: caching + optimize query

---

# 3. Media & Best Practice

## Image Optimization
- Gunakan WebP/AVIF
- Resize sesuai kebutuhan

## Missing width/height
- Menyebabkan layout shift
- Tambahkan atribut width & height

## Cache Optimization
- Gunakan Cache-Control header

---

# 4. Urutan Fix

1. Fix JS (minify + unused)
2. Reduce main thread work
3. Optimize network payload
4. Optimize backend
5. Fix minor issues

---

# 5. Target Performance

- LCP < 2.5s
- INP < 200ms
- CLS < 0.1
- JS < 300KB ideal
