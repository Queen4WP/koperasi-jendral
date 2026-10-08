<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loan_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->restrictOnDelete();
            $table->enum('jenis_dokumen', ['ktp', 'bukti_penghasilan', 'rekening_bank', 'bukti_usaha', 'jaminan']);
            $table->string('file_path');
            $table->enum('status_verifikasi', ['pending', 'sesuai', 'tidak_sesuai'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->unique(['loan_id', 'jenis_dokumen']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('loan_documents');
    }
};
