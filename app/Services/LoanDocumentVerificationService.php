<?php

namespace App\Services;

use App\Models\LoanDocument;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LoanDocumentVerificationService
{
    public function verifyAsSuitable(LoanDocument $document, int $adminId): LoanDocument
    {
        return DB::transaction(function () use ($document, $adminId) {
            $doc = LoanDocument::lockForUpdate()->find($document->id);
            if (!$doc) {
                throw new RuntimeException('Dokumen tidak ditemukan.');
            }

            if ($doc->status_verifikasi !== 'pending') {
                throw new RuntimeException("Tidak dapat memverifikasi dokumen yang sudah memiliki status {$doc->status_verifikasi}.");
            }

            $doc->status_verifikasi = 'sesuai';
            $doc->catatan = null; // Clear catatan on success just to be clean
            $doc->save();

            // We could potentially log this action using an ActivityLog service if it exists.
            // But as instructed, it is deferred if not readily available.

            return $doc;
        });
    }

    public function verifyAsUnsuitable(LoanDocument $document, int $adminId, string $catatan): LoanDocument
    {
        if (trim($catatan) === '') {
            throw new RuntimeException('Catatan penolakan wajib diisi dan tidak boleh hanya spasi kosong.');
        }

        return DB::transaction(function () use ($document, $adminId, $catatan) {
            $doc = LoanDocument::lockForUpdate()->find($document->id);
            if (!$doc) {
                throw new RuntimeException('Dokumen tidak ditemukan.');
            }

            if ($doc->status_verifikasi !== 'pending') {
                throw new RuntimeException("Tidak dapat menolak dokumen yang sudah memiliki status {$doc->status_verifikasi}.");
            }

            $doc->status_verifikasi = 'tidak_sesuai';
            $doc->catatan = trim($catatan);
            $doc->save();

            // Log deferred

            return $doc;
        });
    }
}
