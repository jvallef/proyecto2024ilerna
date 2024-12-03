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
        Schema::table('paths', function (Blueprint $table) {
            // Ordenamiento y destacados
            $table->integer('sort_order')->default(0)->after('status');
            
            // Metadatos adicionales (SEO, configuraciones, etc.)
            $table->json('meta')->nullable()->after('featured')
                  ->comment('Para guardar metadatos adicionales, como SEO, configuraciones, etc.');

            // Índices para ordenamiento y búsqueda
            $table->index('sort_order');
            $table->index(['parent_id', 'sort_order']); // Para ordenar dentro de cada padre
            $table->index(['featured', 'sort_order']); // Para listar destacados ordenados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paths', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['parent_id', 'sort_order']);
            $table->dropIndex(['featured', 'sort_order']);

            // Eliminar columnas
            $table->dropColumn(['sort_order', 'meta']);
        });
    }
};
