<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Ubah enum role di tabel users agar mendukung 5 role (admin, member, karyawan, kasir, finance)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('member')->change();
        });

        // 2. Ubah enum jenis_simpanan di tabel savings agar mendukung sukarela
        Schema::table('savings', function (Blueprint $table) {
            $table->string('jenis_simpanan', 30)->change();
        });

        // 3. Tambahkan approval_note dan finance_id ke tabel loans
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'approval_note')) {
                $table->text('approval_note')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('loans', 'finance_id')) {
                $table->foreignId('finance_id')->nullable()->after('reviewed_by')->constrained('users')->nullOnDelete();
            }
        });

        // 4. Buat tabel savings_withdrawals untuk penarikan tunai tabungan di kasir
        if (!Schema::hasTable('savings_withdrawals')) {
            Schema::create('savings_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
                $table->foreignId('kasir_id')->constrained('users')->restrictOnDelete();
                $table->decimal('nominal', 15, 2);
                $table->string('nomor_referensi')->unique();
                $table->dateTime('tanggal_penarikan');
                $table->enum('status', ['success', 'failed'])->default('success');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 5. Buat tabel cashier_closings untuk serah terima kas harian kasir ke finance jam 16:00
        if (!Schema::hasTable('cashier_closings')) {
            Schema::create('cashier_closings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kasir_id')->constrained('users')->restrictOnDelete();
                $table->foreignId('finance_id')->nullable()->constrained('users')->nullOnDelete();
                $table->date('tanggal_closing');
                $table->decimal('total_kas_masuk', 15, 2)->default(0);
                $table->decimal('total_kas_keluar', 15, 2)->default(0);
                $table->decimal('total_fisik_diserahkan', 15, 2)->default(0);
                $table->enum('status', ['pending', 'verified'])->default('pending');
                $table->dateTime('verified_at')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 6. Buat tabel employee_payroll_settings untuk potong gaji simpanan sukarela karyawan
        if (!Schema::hasTable('employee_payroll_settings')) {
            Schema::create('employee_payroll_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->decimal('nominal_sukarela_bulanan', 15, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_payroll_settings');
        Schema::dropIfExists('cashier_closings');
        Schema::dropIfExists('savings_withdrawals');

        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'finance_id')) {
                $table->dropForeign(['finance_id']);
                $table->dropColumn('finance_id');
            }
            if (Schema::hasColumn('loans', 'approval_note')) {
                $table->dropColumn('approval_note');
            }
        });
    }
};
