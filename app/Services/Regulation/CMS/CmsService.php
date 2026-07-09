<?php

namespace App\Services\Regulation\CMS;

use App\Models\MstDocument;
use App\Models\MstRegulation;
use App\Models\TrsDocumentRegulation;

class CmsService
{
    /**
     * Ambil semua MstDocument beserta regulations yang sudah dimapping.
     * Hanya load kolom yang dibutuhkan frontend (selective columns).
     */
    public function getDocumentsWithRegulations(): array
    {
        $documents = MstDocument::with([
            'regulations:id,judul,nomor,tipe,status',
        ])
        ->orderBy('name')
        ->get();

        return $documents->map(function ($doc) {
            return [
                'id'           => $doc->id,
                'name'         => $doc->name,
                'url'          => $doc->url,
                'created_at'   => $doc->created_at ? $doc->created_at->format('d M Y') : '-',
                'updated_at'   => $doc->updated_at ? $doc->updated_at->format('d M Y') : '-',
                'regulations'  => $doc->regulations->map(fn($reg) => [
                    'id'     => $reg->id,
                    'judul'  => $reg->judul,
                    'nomor'  => $reg->nomor,
                    'tipe'   => $reg->tipe,
                    'status' => $reg->status,
                ])->values()->all(),
            ];
        })->all();
    }

    /**
     * Simpan mapping antara document dan regulation.
     * Cek duplikasi sebelum insert.
     */
    public function mapRegulation(int $documentId, int $regulationId): bool
    {
        $exists = TrsDocumentRegulation::where('document_id', $documentId)
            ->where('regulation_id', $regulationId)
            ->exists();

        if ($exists) {
            return false; // duplikasi
        }

        TrsDocumentRegulation::create([
            'document_id'   => $documentId,
            'regulation_id' => $regulationId,
        ]);

        return true;
    }

    /**
     * Hapus mapping antara document dan regulation.
     */
    public function unmapRegulation(int $documentId, int $regulationId): void
    {
        TrsDocumentRegulation::where('document_id', $documentId)
            ->where('regulation_id', $regulationId)
            ->delete();
    }

    /**
     * Ambil daftar regulation sebagai opsi dropdown.
     * Hanya kolom yang dibutuhkan (selective).
     */
    public function getRegulationOptions(): array
    {
        return MstRegulation::orderBy('judul')
            ->get(['id', 'judul', 'nomor', 'tipe', 'status'])
            ->map(fn($reg) => [
                'id'     => $reg->id,
                'judul'  => $reg->judul,
                'nomor'  => $reg->nomor,
                'tipe'   => $reg->tipe,
                'status' => $reg->status,
            ])
            ->all();
    }

    /**
     * Ambil semua MstRegulation beserta dokumen yang sudah dimapping.
     * Digunakan di tab Regulation untuk mengisi kolom URL (Document URL).
     * Hanya load kolom yang dibutuhkan (selective columns).
     */
    public function getRegulationsWithDocuments(): array
    {
        $regulations = MstRegulation::with([
            'documents:id,name,url',
        ])
        ->orderBy('judul')
        ->get(['id', 'judul', 'nomor', 'tipe', 'status']);

        return $regulations->keyBy('id')
            ->map(fn($reg) => [
                'id'        => $reg->id,
                'documents' => $reg->documents->map(fn($doc) => [
                    'id'   => $doc->id,
                    'name' => $doc->name,
                    'url'  => $doc->url,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }
}
