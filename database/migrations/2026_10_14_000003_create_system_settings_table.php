<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('loan');
            $table->string('type')->default('string'); // string, integer, float, boolean, json
            $table->string('description')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Insert default loan limits for Karyawan and Anggota
        DB::table('system_settings')->insert([
            [
                'key' => 'loan_karyawan_min_limit',
                'value' => '1000000',
                'group' => 'loan_karyawan',
                'type' => 'integer',
                'description' => 'Batas minimum pengajuan pinjaman modal karyawan (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loan_karyawan_max_limit',
                'value' => '20000000',
                'group' => 'loan_karyawan',
                'type' => 'integer',
                'description' => 'Batas maksimum plafon pinjaman modal karyawan via payroll (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loan_karyawan_rate_percent',
                'value' => '5.0',
                'group' => 'loan_karyawan',
                'type' => 'float',
                'description' => 'Jasa/Bunga pinjaman modal karyawan flat per tahun (%)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loan_anggota_min_limit',
                'value' => '5000000',
                'group' => 'loan_anggota',
                'type' => 'integer',
                'description' => 'Batas minimum pengajuan pinjaman modal anggota umum (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loan_anggota_max_limit',
                'value' => '50000000',
                'group' => 'loan_anggota',
                'type' => 'integer',
                'description' => 'Batas maksimum plafon pinjaman modal anggota umum (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
