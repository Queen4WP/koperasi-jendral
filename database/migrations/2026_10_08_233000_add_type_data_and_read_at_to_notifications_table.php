<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->nullable()->after('user_id');
            $table->json('data')->nullable()->after('message');
            $table->timestamp('read_at')->nullable()->after('is_read');

            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropColumn(['type', 'data', 'read_at']);
        });
    }
};
