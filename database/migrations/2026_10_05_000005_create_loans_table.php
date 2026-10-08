<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->string('nomor_pinjaman')->unique();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('tenor');
            $table->string('tujuan_pinjaman');
            $table->text('keterangan')->nullable();
            $table->decimal('persen_jasa', 5, 2);
            $table->decimal('total_jasa', 15, 2);
            $table->decimal('total_kewajiban', 15, 2);
            $table->decimal('angsuran_per_bulan', 15, 2);
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_owner');
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected', 'disbursed', 'active', 'paid_off'])->default('submitted');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('disbursed_at')->nullable();
            $table->dateTime('paid_off_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('loans');
    }
};
