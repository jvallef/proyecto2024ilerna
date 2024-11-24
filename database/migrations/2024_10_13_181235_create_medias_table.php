<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->morphs('mediable');
            $table->enum('type', ['picture', 'file', 'video', 'audio'])->default('picture');
            $table->string('url')->nullable();
            $table->string('path')->nullable();
            $table->json('extra')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('mediable_id');
            $table->index('mediable_type');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
