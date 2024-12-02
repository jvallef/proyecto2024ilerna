<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['content', 'book', 'theme', 'lesson', 'quizz'])->default('content');
            $table->string('title');
            $table->string('slug')->unique(); // id del content
            $table->json('content')->index('gin')->nullable()->default('{"content": "A placeholder"}');
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('parent_id')->nullable()->constrained('contents');
            $table->enum('status', ['draft', 'published', 'suspended'])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('author_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
