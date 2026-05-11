Ringkasan update untuk optimasi Strategic House
1. Backend: filter view supaya server tahu tab mana yang aktif
ProgramPlanning/StrategicHouse/IndexRequest.php
Menambahkan validasi view
Menyertakan view dalam filter request
2. Backend: hanya hitung data yang diperlukan untuk tab aktif
StrategicHouse/IndexController.php
Menentukan props yang diminta berdasarkan nilai view
Memanggil StrategicHousePageService::getPageProps($filters, $selectedProps)
3. Service: dukung seleksi props dan memoisasi hitungan berat
StrategicHousePageService.php
Menambahkan parameter opsional $selectedProps
Memisahkan data ringan (selalu dikirim) dari data berat (hanya di-load bila diperlukan)
Menyimpan hasil komputasi berat agar tidak dihitung ulang berkali-kali dalam satu request
4. Frontend: pastikan tab yang di-restore dari useRemember memicu load props yang benar
StrategicHouse/Index.vue
Menambahkan reload saat mounted jika URL belum punya view tetapi client sudah ingat tab lain
Mencegah kondisi di mana halaman tampil tapi data tab yang aktif kosong karena server belum mengirim props sesuai tab
Efeknya
Load awal sekarang tidak memproses semua tab sekaligus
Hanya data tab aktif yang dibangun dan diteruskan
Mengurangi beban query / transformasi berat untuk Strategic House secara keseluruhan