<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('private')->default(false);
            $table->foreignId('user_to_id')->nullable()->constrained('users');
            $table->foreignId('area_id')->nullable()->constrained('areas');
            $table->foreignId('path_id')->nullable()->constrained('paths');
            $table->foreignId('course_id')->nullable()->constrained('courses');
            $table->foreignId('content_id')->nullable()->constrained('contents');

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('user_to_id');
            $table->index('area_id');
            $table->index('path_id');
            $table->index('course_id');
            $table->index('content_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
