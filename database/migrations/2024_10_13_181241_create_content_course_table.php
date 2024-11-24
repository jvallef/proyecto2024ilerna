<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents');
            $table->foreignId('course_id')->constrained('courses');
            $table->unsignedInteger('sort')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['content_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_course');
    }
};
