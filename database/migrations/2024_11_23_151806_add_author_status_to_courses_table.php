<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('author_active')->default(true)->after('user_id');
            $table->timestamp('author_deactivated_at')->nullable()->after('author_active');
            $table->boolean('author_permanently_deleted')->default(false)->after('author_deactivated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('author_active');
            $table->dropColumn('author_deactivated_at');
            $table->dropColumn('author_permanently_deleted');
        });
    }
};
