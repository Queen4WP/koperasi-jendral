<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nik')->unique();
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('telepon');
            $table->string('pekerjaan');
            $table->string('perusahaan');
            $table->enum('status', ['pending', 'active', 'rejected', 'inactive'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('members');
    }
};
