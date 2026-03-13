<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MasterDataController extends Controller
{
    public function __invoke(): Response
    {
        $previewLimit = 10;
        $tables = [];
        $error = null;

        try {
            $tableNames = collect(Schema::getTableListing())
                ->filter(fn (string $table): bool => Str::startsWith(Str::lower($table), 'mst_'))
                ->sort()
                ->values();

            $tables = $tableNames
                ->map(fn (string $table): array => $this->tablePayload($table, $previewLimit))
                ->values()
                ->all();
        } catch (Throwable $exception) {
            report($exception);
            $error = 'Gagal memuat data master. Periksa koneksi database terlebih dahulu.';
        }

        return Inertia::render('MasterData/Index', [
            'tables' => $tables,
            'previewLimit' => $previewLimit,
            'error' => $error,
        ]);
    }

    private function tablePayload(string $table, int $previewLimit): array
    {
        $columns = Schema::getColumnListing($table);
        $rows = DB::table($table)
            ->limit($previewLimit)
            ->get()
            ->map(fn ($row): array => (array) $row)
            ->values()
            ->all();

        return [
            'name' => $table,
            'columns' => $columns,
            'rows' => $rows,
            'total_rows' => DB::table($table)->count(),
        ];
    }
}
