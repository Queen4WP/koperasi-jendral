<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
    
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('member.profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('member.profile.update');
    
    Route::get('/savings', [\App\Http\Controllers\SavingsController::class, 'index'])->name('member.savings.index');
    Route::get('/savings/pokok/create', [\App\Http\Controllers\SavingsController::class, 'createPokok'])->name('member.savings.pokok.create');
    Route::get('/savings/wajib/create', [\App\Http\Controllers\SavingsController::class, 'createWajib'])->name('member.savings.wajib.create');
    Route::post('/savings/pokok', [\App\Http\Controllers\SavingsController::class, 'storePokok'])->name('member.savings.pokok.store');
    Route::post('/savings/wajib', [\App\Http\Controllers\SavingsController::class, 'storeWajib'])->name('member.savings.wajib.store');
    Route::post('/savings/{saving}/pay', [\App\Http\Controllers\SavingsController::class, 'pay'])->name('member.savings.pay');
    
    Route::get('/loans', [\App\Http\Controllers\LoanController::class, 'index'])->name('member.loans.index');
    Route::get('/loans/create', [\App\Http\Controllers\LoanController::class, 'create'])->name('member.loans.create');
    Route::post('/loans', [\App\Http\Controllers\LoanController::class, 'store'])->name('member.loans.store');
    Route::get('/loans/{loan}', [\App\Http\Controllers\LoanController::class, 'show'])->name('member.loans.show');
    Route::post('/loans/{loan}/documents', [\App\Http\Controllers\LoanDocumentController::class, 'store'])->name('member.loans.documents.store');
    Route::get('/loans/{loan}/documents/{document}', [\App\Http\Controllers\LoanDocumentController::class, 'show'])->name('member.loans.documents.show');
    
    Route::post('/installments/{installment}/pay', [\App\Http\Controllers\LoanPaymentController::class, 'store'])->name('member.loan-payments.store');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/members/{member}/review', [\App\Http\Controllers\Admin\MembershipController::class, 'showReview'])->name('members.review');
    Route::post('/members/{member}/review', [\App\Http\Controllers\Admin\MembershipController::class, 'review'])->name('members.review.store');
    
    Route::get('/loans/{loan}/review', [\App\Http\Controllers\Admin\LoanReviewController::class, 'show'])->name('loans.review');
    Route::post('/loans/{loan}/start-review', [\App\Http\Controllers\Admin\LoanReviewController::class, 'startReview'])->name('loans.start_review');
    Route::post('/loans/{loan}/approve', [\App\Http\Controllers\Admin\LoanReviewController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [\App\Http\Controllers\Admin\LoanReviewController::class, 'reject'])->name('loans.reject');
    
    Route::get('/loans/{loan}/disburse', [\App\Http\Controllers\Admin\DisbursementController::class, 'create'])->name('loans.disburse');
    Route::post('/loans/{loan}/disburse', [\App\Http\Controllers\Admin\DisbursementController::class, 'store'])->name('loans.disburse.store');
    
    Route::post('/loans/{loan}/documents/{document}/verify', [\App\Http\Controllers\Admin\LoanDocumentVerificationController::class, 'verify'])->name('loans.documents.verify');
    Route::post('/loans/{loan}/documents/{document}/reject', [\App\Http\Controllers\Admin\LoanDocumentVerificationController::class, 'reject'])->name('loans.documents.reject');
});
