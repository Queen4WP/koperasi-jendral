<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_id')->constrained('savings')->restrictOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('nominal_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['online', 'kantor']);
            $table->dateTime('tanggal_bayar');
            $table->string('payment_reference')->unique();
            $table->string('external_reference')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('savings_transactions');
    }
};
