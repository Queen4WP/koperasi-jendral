<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('subject_type')->nullable()->after('activity');
            $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            $table->string('ip_address', 45)->nullable()->after('keterangan');
            $table->text('user_agent')->nullable()->after('ip_address');

            $table->index(['subject_type', 'subject_id']);
            $table->index('activity');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['subject_type', 'subject_id']);
            $table->dropIndex(['activity']);
            $table->dropColumn(['subject_type', 'subject_id', 'ip_address', 'user_agent']);
        });
    }
};
