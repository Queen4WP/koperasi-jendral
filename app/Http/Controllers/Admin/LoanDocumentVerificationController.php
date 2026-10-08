<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Services\LoanDocumentVerificationService;
use App\Http\Requests\Admin\VerifyLoanDocumentRequest;
use Illuminate\Support\Facades\Gate;
use Exception;
use RuntimeException;
use Illuminate\Http\Request;

class LoanDocumentVerificationController extends Controller
{
    protected $verificationService;

    public function __construct(LoanDocumentVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function verify(Request $request, Loan $loan, LoanDocument $document)
    {
        // 1. Cross-Resource / IDOR Protection
        if ($document->loan_id !== $loan->id) {
            abort(404, 'Dokumen tidak ditemukan untuk pinjaman ini.');
        }

        // 2. Authorization
        Gate::authorize('verify', $document);

        // 3. Admin ID
        $adminId = auth()->id();

        // 4. Call Service
        try {
            $this->verificationService->verifyAsSuitable($document, $adminId);
            return redirect()->back()->with('success', 'Dokumen berhasil diverifikasi sebagai Sesuai.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memverifikasi dokumen.');
        }
    }

    public function reject(VerifyLoanDocumentRequest $request, Loan $loan, LoanDocument $document)
    {
        // 1. Cross-Resource / IDOR Protection
        if ($document->loan_id !== $loan->id) {
            abort(404, 'Dokumen tidak ditemukan untuk pinjaman ini.');
        }

        // 2. Authorization
        Gate::authorize('verify', $document);

        // 3. Admin ID
        $adminId = auth()->id();

        // 4. Call Service
        try {
            $this->verificationService->verifyAsUnsuitable($document, $adminId, $request->catatan);
            return redirect()->back()->with('success', 'Dokumen berhasil ditolak sebagai Tidak Sesuai.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menolak dokumen.');
        }
    }
}
