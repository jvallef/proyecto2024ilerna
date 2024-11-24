<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->boolean('featured')->default(false);
            $table->enum('age_group', ['0-6', '7-12', '13-20', '21+'])->nullable(); //NULL son todos
            $table->enum('status', ['draft', 'published', 'suspended'])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            $table->index('title');
            $table->index('slug');
            $table->index('author_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
