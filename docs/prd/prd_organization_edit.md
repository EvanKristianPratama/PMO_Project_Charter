urutan project owner & leader di page [Summary.vue](resources/js/Pages/ProgramEvaluation/ReviewDashboard/Summary.vue) diurutkan berdsarkan pembacaan digit kolom code. saat ini di [TrsOrganization.php](app/Models/TrsOrganization.php) memiliki 7 digit code, dengan urutan berikut: 
1 = digit pertama (Index Groub Holding /Subholding /All)
7 = digit kedua (Index Organisasi / perusahaan Induk)
1= digit ketiga (Index anak perusahaan induk level 1)
1= digit keempat (Index anak perusahaan induk level 2)
0= digit kelima (Index anak perusahaan induk level 3)
0= digit keenam (Index anak perusahaan induk level 4)
0= digit ketuju (Index anak perusahaan induk level 5)

Buat struktur digaram organisasi EIT dari model [TrsOrganization.vue](app\Models\TrsOrganization.php) di page [Index.vue](resources\js\Pages\Policy\Organization\Index.vue), berdsarkan struktur hirarki mulai dari Holding, SVP, VP, sampai Senior.

Guanakan komponen [ThreeView.vue](resources\js\Components\Architecture\Organization\ThreeView.vue)

Gunakan & Update controller [EITOrganizationController.php](Controllers\Policy\EITOrganizationController.php)