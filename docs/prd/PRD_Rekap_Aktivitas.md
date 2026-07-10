Saya ingin membuat rekap aktivitas berdasarkan commit Git.

Ambil semua commit dari SELURUH branch dalam repository, dengan kriteria berikut:

* Range tanggal: [01/06/2026] sampai [30/06/2026] 
* Author: "jethroGIT"

Kemudian olah data commit tersebut menjadi laporan harian dengan format:

[tanggal | waktu mulai | waktu selesai | branch]:
DD/MM/YYYY    HH:MM:SS    HH:MM:SS    branch1, branch2, branch3

1. [commit message]
2. [commit message]
3. [commit message]
   ...

Aturan pengolahan:

1. Kelompokkan commit berdasarkan tanggal (per hari).
2. Urutkan commit dalam setiap hari berdasarkan waktu.
3. Waktu mulai = waktu commit paling awal dalam hari tersebut.
4. Waktu selesai = waktu commit paling akhir dalam hari tersebut.
5. Ambil semua branch yang digunakan pada commit di hari tersebut:

   * Jika 1 commit muncul di beberapa branch, tetap dihitung 1
   * Tampilkan unique branch saja (tidak duplikat)
   * Format: branchA, branchB, branchC
6. Ambil hanya isi commit message tanpa hash.
7. Bersihkan commit message:

   * Hilangkan prefix seperti "fix:", "feat:", "chore:" jika ada
   * Rapikan huruf kapital di awal kalimat
8. Jika ada commit message yang mirip atau duplikat, gabungkan menjadi satu poin.
9. Gunakan bahasa asli commit (jangan diterjemahkan).
10. Format output harus rapi seperti contoh berikut:

Contoh output:

[3/10/2026 | 8:00:00 AM | 4:00:00 PM | main, develop, feature/status-implementation]:
3/10/2026    8:00:00 AM    4:00:00 PM    main, develop, feature/status-implementation

1. Fix error delete activity roadmap per versi project charter
2. Membuat fitur status implementation di page Program Implementation / IT Initiatives / Status Implementation
3. Fix error add roadmap
4. Update tampilan komponen Status Implementation
5. Membuat fitur hide / show initiative, timeline, roadmap
6. Update tampilan hapus tanda # kolom rjpp
7. Update field scope charter compendium