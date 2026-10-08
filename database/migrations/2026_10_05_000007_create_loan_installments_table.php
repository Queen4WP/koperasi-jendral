<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->restrictOnDelete();
            $table->integer('angsuran_ke');
            $table->date('jatuh_tempo');
            $table->decimal('pokok_angsuran', 15, 2);
            $table->decimal('jasa_angsuran', 15, 2);
            $table->decimal('total_angsuran', 15, 2);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2);
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->timestamps();
            
            $table->unique(['loan_id', 'angsuran_ke']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('loan_installments');
    }
};
