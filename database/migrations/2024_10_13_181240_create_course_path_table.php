<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_path', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('path_id')->constrained('paths');
            $table->unsignedInteger('sort')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id', 'path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_path');
    }
};
