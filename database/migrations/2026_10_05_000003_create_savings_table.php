<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->enum('jenis_simpanan', ['pokok', 'wajib']);
            $table->date('periode')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();
            
            $table->unique(['member_id', 'jenis_simpanan', 'periode']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('savings');
    }
};
