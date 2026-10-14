<?php

use Illuminate\Support\Facades\Route;

// Public Institutional Website Routes
Route::get('/', [\App\Http\Controllers\PublicController::class, 'index'])->name('home');
Route::get('/tentang-kami', [\App\Http\Controllers\PublicController::class, 'about'])->name('public.about');
Route::get('/about', fn() => redirect()->route('public.about'));
Route::get('/simpanan', [\App\Http\Controllers\PublicController::class, 'savings'])->name('public.savings');
Route::get('/pinjaman', [\App\Http\Controllers\PublicController::class, 'loans'])->name('public.loans');
Route::get('/cara-kerja', [\App\Http\Controllers\PublicController::class, 'howItWorks'])->name('public.how-it-works');
Route::get('/kontak', [\App\Http\Controllers\PublicController::class, 'contact'])->name('public.contact');
Route::get('/contact', fn() => redirect()->route('public.contact'));


// Member Guest Authentication Routes
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
    
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store'])->name('register.store');
});

// Member Logout Route
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])->middleware('auth:web')->name('logout');

// Admin Authentication Routes (Dedicated admin guard)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'store'])->name('login.store');
    });

    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'destroy'])->middleware('auth:admin')->name('logout');
});

// Member Protected Portal
Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('member.profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('member.profile.update');
    Route::post('/profile/resubmit', [\App\Http\Controllers\ProfileController::class, 'resubmit'])->name('member.profile.resubmit');
    Route::post('/members/{member}/resubmit', [\App\Http\Controllers\ProfileController::class, 'resubmitMember'])->name('member.resubmit');
    
    Route::get('/savings', [\App\Http\Controllers\SavingsController::class, 'index'])->name('member.savings.index');
    Route::get('/savings/pokok/create', [\App\Http\Controllers\SavingsController::class, 'createPokok'])->name('member.savings.pokok.create');
    Route::get('/savings/wajib/create', [\App\Http\Controllers\SavingsController::class, 'createWajib'])->name('member.savings.wajib.create');
    Route::post('/savings/pokok', [\App\Http\Controllers\SavingsController::class, 'storePokok'])->name('member.savings.pokok.store');
    Route::post('/savings/wajib', [\App\Http\Controllers\SavingsController::class, 'storeWajib'])->name('member.savings.wajib.store');
    Route::post('/savings/{saving}/pay', [\App\Http\Controllers\SavingsController::class, 'pay'])->name('member.savings.pay');
    Route::post('/savings/pay-bundle', [\App\Http\Controllers\SavingsController::class, 'payBundle'])->name('member.savings.pay-bundle');
    
    Route::get('/loans', [\App\Http\Controllers\LoanController::class, 'index'])->name('member.loans.index');
    Route::get('/loans/create', [\App\Http\Controllers\LoanController::class, 'create'])->name('member.loans.create');
    Route::post('/loans', [\App\Http\Controllers\LoanController::class, 'store'])->name('member.loans.store');
    Route::get('/loans/{loan}', [\App\Http\Controllers\LoanController::class, 'show'])->name('member.loans.show');
    Route::post('/loans/{loan}/documents', [\App\Http\Controllers\LoanDocumentController::class, 'store'])->name('member.loans.documents.store');
    Route::get('/loans/{loan}/documents/{document}', [\App\Http\Controllers\LoanDocumentController::class, 'show'])->name('member.loans.documents.show')->scopeBindings();
    
    Route::post('/installments/{installment}/pay', [\App\Http\Controllers\LoanPaymentController::class, 'store'])->name('member.loan-payments.store');

    // Payment Proofs (member own or admin)
    Route::get('/payments/savings-transactions/{transaction}/proof', [\App\Http\Controllers\PaymentProofController::class, 'showSavingsProof'])->name('payments.savings.proof');
    Route::get('/payments/loan-payments/{payment}/proof', [\App\Http\Controllers\PaymentProofController::class, 'showLoanProof'])->name('payments.loans.proof');

    // Notifications (Authenticated)
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
});

// Admin Protected Portal
Route::middleware(['auth:admin', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Member Management
    Route::get('/members', [\App\Http\Controllers\Admin\MembershipController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [\App\Http\Controllers\Admin\MembershipController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/review', [\App\Http\Controllers\Admin\MembershipController::class, 'showReview'])->name('members.review');
    Route::post('/members/{member}/review', [\App\Http\Controllers\Admin\MembershipController::class, 'review'])->name('members.review.store');
    
    // Savings Management
    Route::get('/savings', [\App\Http\Controllers\Admin\SavingsController::class, 'index'])->name('savings.index');
    Route::get('/savings/{saving}', [\App\Http\Controllers\Admin\SavingsController::class, 'show'])->name('savings.show');
    Route::post('/savings/transactions/{transaction}/approve', [\App\Http\Controllers\Admin\SavingsController::class, 'approvePayment'])->name('savings.approve');
    Route::post('/savings/transactions/{transaction}/reject', [\App\Http\Controllers\Admin\SavingsController::class, 'rejectPayment'])->name('savings.reject');
    
    // Loan Management & Review
    Route::get('/loans', [\App\Http\Controllers\Admin\LoanReviewController::class, 'index'])->name('loans.index');
    Route::get('/loans/{loan}', [\App\Http\Controllers\Admin\LoanReviewController::class, 'show'])->name('loans.show');
    Route::get('/loans/{loan}/review', [\App\Http\Controllers\Admin\LoanReviewController::class, 'show'])->name('loans.review');
    Route::post('/loans/{loan}/start-review', [\App\Http\Controllers\Admin\LoanReviewController::class, 'startReview'])->name('loans.start_review');
    Route::post('/loans/{loan}/approve', [\App\Http\Controllers\Admin\LoanReviewController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [\App\Http\Controllers\Admin\LoanReviewController::class, 'reject'])->name('loans.reject');
    
    Route::get('/loans/{loan}/disburse', [\App\Http\Controllers\Admin\DisbursementController::class, 'create'])->name('loans.disburse');
    Route::post('/loans/{loan}/disburse', [\App\Http\Controllers\Admin\DisbursementController::class, 'store'])->name('loans.disburse.store');
    
    Route::scopeBindings()->group(function () {
        Route::post('/loans/{loan}/documents/{document}/verify', [\App\Http\Controllers\Admin\LoanDocumentVerificationController::class, 'verify'])->name('loans.documents.verify');
        Route::post('/loans/{loan}/documents/{document}/reject', [\App\Http\Controllers\Admin\LoanDocumentVerificationController::class, 'reject'])->name('loans.documents.reject');
    });

    // Payment Verification
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'index'])->name('payments.index');
    Route::get('/payments/savings/{transaction}', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'showSaving'])->name('payments.savings.show');
    Route::post('/payments/savings/{transaction}/approve', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'approveSaving'])->name('payments.savings.approve');
    Route::post('/payments/savings/{transaction}/reject', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'rejectSaving'])->name('payments.savings.reject');
    Route::get('/payments/loans/{payment}', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'showLoanPayment'])->name('payments.loans.show');
    Route::post('/payments/loans/{payment}/approve', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'approveLoanPayment'])->name('payments.loans.approve');
    Route::post('/payments/loans/{payment}/reject', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'rejectLoanPayment'])->name('payments.loans.reject');

    // Direct Office Payments
    Route::post('/savings/{saving}/office-pay', [\App\Http\Controllers\Admin\OfficePaymentController::class, 'storeSaving'])->name('savings.office_pay');
    Route::post('/installments/{installment}/office-pay', [\App\Http\Controllers\Admin\OfficePaymentController::class, 'storeInstallment'])->name('installments.office_pay');

    // Audit / Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity_logs.index');
});

// ==========================================
// FINANCE PORTAL ROUTES (Guard: finance)
// ==========================================
Route::prefix('finance')->name('finance.')->group(function () {
    Route::middleware('guest:finance')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Finance\Auth\FinanceLoginController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Finance\Auth\FinanceLoginController::class, 'store'])->name('login.store');
    });

    Route::post('/logout', [\App\Http\Controllers\Finance\Auth\FinanceLoginController::class, 'destroy'])
        ->middleware('auth:finance')
        ->name('logout');

    Route::middleware(['auth:finance', \App\Http\Middleware\FinanceMiddleware::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Finance\FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/loans/{loan}', [\App\Http\Controllers\Finance\FinanceController::class, 'showLoan'])->name('loans.show');
        Route::post('/loans/{loan}/approve', [\App\Http\Controllers\Finance\FinanceController::class, 'approveLoan'])->name('loans.approve');
        Route::post('/loans/{loan}/reject', [\App\Http\Controllers\Finance\FinanceController::class, 'rejectLoan'])->name('loans.reject');
        Route::post('/closings/{closing}/verify', [\App\Http\Controllers\Finance\FinanceController::class, 'verifyClosing'])->name('closings.verify');
    });
});

// ==========================================
// KASIR PORTAL ROUTES (Guard: kasir)
// ==========================================
Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::middleware('guest:kasir')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Kasir\Auth\KasirLoginController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Kasir\Auth\KasirLoginController::class, 'store'])->name('login.store');
    });

    Route::post('/logout', [\App\Http\Controllers\Kasir\Auth\KasirLoginController::class, 'destroy'])
        ->middleware('auth:kasir')
        ->name('logout');

    Route::middleware(['auth:kasir', \App\Http\Middleware\KasirMiddleware::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Kasir\KasirController::class, 'dashboard'])->name('dashboard');
        
        // Counter Cash Savings Deposit
        Route::get('/savings/deposit', [\App\Http\Controllers\Kasir\KasirController::class, 'createSavingsDeposit'])->name('savings.deposit');
        Route::post('/savings/deposit', [\App\Http\Controllers\Kasir\KasirController::class, 'storeSavingsDeposit'])->name('savings.deposit.store');
        
        // Counter Cash Withdrawal
        Route::get('/savings/withdraw', [\App\Http\Controllers\Kasir\KasirController::class, 'createWithdrawal'])->name('savings.withdraw');
        Route::post('/savings/withdraw', [\App\Http\Controllers\Kasir\KasirController::class, 'storeWithdrawal'])->name('savings.withdraw.store');
        
        // Counter Cash Loan Disbursement
        Route::get('/loans/{loan}/disburse', [\App\Http\Controllers\Kasir\KasirController::class, 'showLoanDisbursement'])->name('loans.disburse');
        Route::post('/loans/{loan}/disburse', [\App\Http\Controllers\Kasir\KasirController::class, 'storeLoanDisbursement'])->name('loans.disburse.store');
        
        // Counter Cash Installment Payment
        Route::get('/loans/installment', [\App\Http\Controllers\Kasir\KasirController::class, 'createInstallmentPayment'])->name('loans.installment');
        Route::post('/loans/installment', [\App\Http\Controllers\Kasir\KasirController::class, 'storeInstallmentPayment'])->name('loans.installment.store');
        
        // Daily Cash Closing (16:00)
        Route::post('/closing', [\App\Http\Controllers\Kasir\KasirController::class, 'submitDailyClosing'])->name('closing.store');
        
        // Printable Receipts (BKM & BKK)
        Route::get('/receipts/bkm/{transaction}', [\App\Http\Controllers\Kasir\KasirController::class, 'printBkm'])->name('receipt.bkm');
        Route::get('/receipts/bkm-installment/{payment}', [\App\Http\Controllers\Kasir\KasirController::class, 'printBkmInstallment'])->name('receipt.bkm_installment');
        Route::get('/receipts/bkk-withdrawal/{withdrawal}', [\App\Http\Controllers\Kasir\KasirController::class, 'printBkkWithdrawal'])->name('receipt.bkk_withdrawal');
        Route::get('/receipts/bkk-disbursement/{disbursement}', [\App\Http\Controllers\Kasir\KasirController::class, 'printBkkDisbursement'])->name('receipt.bkk_disbursement');
    });
});

// ==========================================
// KARYAWAN PORTAL ROUTES (Guard: karyawan)
// ==========================================
Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::middleware('guest:karyawan')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Karyawan\Auth\KaryawanLoginController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Karyawan\Auth\KaryawanLoginController::class, 'store'])->name('login.store');
    });

    Route::post('/logout', [\App\Http\Controllers\Karyawan\Auth\KaryawanLoginController::class, 'destroy'])
        ->middleware('auth:karyawan')
        ->name('logout');

    Route::middleware(['auth:karyawan', \App\Http\Middleware\KaryawanMiddleware::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Karyawan\KaryawanController::class, 'dashboard'])->name('dashboard');
        Route::post('/payroll-setting', [\App\Http\Controllers\Karyawan\KaryawanController::class, 'updatePayrollSetting'])->name('payroll_setting.update');
        Route::post('/simulate-payroll-cut', [\App\Http\Controllers\Karyawan\KaryawanController::class, 'simulatePayrollCut'])->name('simulate_payroll_cut');
    });
});

