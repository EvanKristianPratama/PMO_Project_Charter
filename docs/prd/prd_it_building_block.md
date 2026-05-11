# PRD: IT Building Block Page

## 1. Overview
Halaman ini menampilkan mapping antara IT Building Blocks dan Digital Initiatives dalam bentuk grid seperti pada referensi visual.

Path menu:
ProgramImplementation/It Building Block

View:
resources/views/ProgramImplementation/ItBuildingBlocks/index.blade.php

---

## 2. Objectives
- Menampilkan relasi Primary, Secondary, dan Digital Initiatives
- Meniru tampilan visual seperti referensi (grouped box layout)
- Memudahkan analisis kontribusi IT terhadap initiative

---

## 3. Database Design

Table: trs_map_itbuilding

| Field          | Description |
|----------------|------------|
| id             | PK |
| primary        | FK ke mst_coe |
| secondary      | FK ke mst_coe |
| initiative_id  | FK ke mst_initiative |

---

## 4. Model Relationships

### TrsMapItBuilding.php

```php
public function primaryCoe()
{
    return $this->belongsTo(MstCoe::class, 'primary');
}

public function secondaryCoe()
{
    return $this->belongsTo(MstCoe::class, 'secondary');
}

public function initiative()
{
    return $this->belongsTo(MstInitiative::class, 'initiative_id');
}
```

---

## 5. Backend

Controller:
app/Http/Controllers/ItBuildingBlockController.php

```php
use App\Models\TrsMapItBuilding;

public function index()
{
    $data = TrsMapItBuilding::with([
        'primaryCoe',
        'secondaryCoe',
        'initiative'
    ])->get();

    $grouped = $data->groupBy(function ($item) {
        return $item->primaryCoe->name;
    });

    return view(
        'ProgramImplementation.ItBuildingBlocks.index',
        compact('grouped')
    );
}
```

---

## 6. Routes

```php
Route::get('/it-building-blocks', [ItBuildingBlockController::class, 'index']);
```

---

## 7. UI/UX Requirements

Layout:

IT Building Blocks | Digital Initiatives
----------------------------------------
Primary            | Initiative Boxes
  Secondary        | Initiative Boxes

---

## 8. Blade Structure

```blade
@foreach($grouped as $primary => $items)
    <div class="primary-block">
        <h3>{{ $primary }}</h3>

        @foreach($items->groupBy('secondaryCoe.name') as $secondary => $rows)
            <div class="secondary-block">
                <h5>{{ $secondary }}</h5>

                <div class="initiatives">
                    @foreach($rows as $row)
                        <div class="initiative-box">
                            {{ $row->initiative->name }}
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach

    </div>
@endforeach
```

---

## 9. Styling (Example)

```css
.primary-block {
    background: #1f4e79;
    color: white;
    padding: 10px;
    margin-bottom: 20px;
}

.secondary-block {
    background: #5b9bd5;
    padding: 8px;
    margin-top: 10px;
}

.initiatives {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.initiative-box {
    border: 1px solid #333;
    padding: 4px 8px;
    font-size: 12px;
    background: #fff;
}
```

---

## 10. Behavior
- Group by Primary → Secondary
- Initiative tampil sebagai box kecil
- Support multiple initiative dalam satu secondary
- Responsive layout

---

## 11. Future Enhancement
- Filter COE
- Search initiative
- Drag & drop mapping
- Export Excel / PPT

---

## 12. Acceptance Criteria
- Data tampil sesuai grouping
- Initiative muncul sebagai box
- Tampilan sesuai referensi
- Relasi model berjalan
- Tidak ada N+1 query
