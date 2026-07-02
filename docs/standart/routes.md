# Standar Struktur Routing Laravel

Dokumen ini menjelaskan standar penulisan route di Laravel menggunakan nested prefix, name-prefix, dan controller binding.

## 1. Konsep Grup & Nesting (Route Grouping)

Untuk meningkatkan keterbacaan, kemudahan pemeliharaan, serta performa pencocokan route, penulisan route harus dikelompokkan berdasarkan modul dan sub-modul yang sejenis.

### Keuntungan Utama
- **DRY (Don't Repeat Yourself)**: Menghindari pengulangan prefix URL yang sama.
- **Konsistensi Penamaan**: Nama route di-scope secara hierarki melalui method `name()`, mencegah pengetikan ulang prefix nama route.
- **Dekopling Controller**: Dengan method `controller()`, class controller hanya ditulis satu kali di tingkat grup, sedangkan setiap route di dalamnya hanya merujuk pada nama method (string).
- **Efisiensi Middleware**: Middleware cukup dipasangkan sekali di tingkat grup induk.

---

## 2. Pola Implementasi Standar

Berikut pola standar pengelompokan sub-menu dan sub-sub-menu:

```php
Route::middleware(['auth'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {

        Route::prefix('user')
            ->name('user.')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/create', 'create')->name('create');
                Route::put('/{id}', 'update')->name('update');
            });

        Route::prefix('role')
            ->name('role.')
            ->controller(RoleController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
            });
    });
```

---

## 3. Contoh Kasus Nyata: Business Process Group

Berikut implementasi standar pada modul `business-process` di ITOM:

```php
Route::prefix("business-process")
    ->name("business-process.")
    ->group(function () {
        
        // APQC Sub-Menu
        Route::prefix("apqc")
            ->name("apqc.")
            ->controller(ApqcController::class)
            ->group(function () {
                Route::get("/", "index")->name("index");
                Route::post("/", "store")->name("store");
                Route::put("/{id}", "update")->name("update");
                Route::delete("/{id}", "destroy")->name("destroy");
            });

        // Function Sub-Menu
        Route::prefix("function")
            ->name("function.")
            ->controller(FunctionController::class)
            ->group(function () {
                Route::get("/", "index")->name("index");
                Route::post("/", "storeFunction")->name("store");
                Route::put("/{id}", "updateFunction")->name("update");
                Route::delete("/{id}", "destroyFunction")->name("destroy");
            });
    });
```
