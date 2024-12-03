<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;

class AreaControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Limpiar la base de datos antes de cada test
        $this->artisan('migrate:fresh');
        
        // Crear el rol admin si no existe
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        
        Storage::fake('public');
    }

    /**
     * Test de visualización pública de áreas
     * 
     * #[Test]
     */
    #[Test]
    public function publicIndex_should_show_published_areas()
    {
        // Obtener el valor de paginación del entorno
        $perPage = env('PAGINATION_PER_PAGE', 10);

        // Obtener las áreas que deberían estar visibles
        $expectedFeaturedAreas = Area::published()->where('featured', true)->get();
        $expectedRegularAreas = Area::published()->where('featured', false)->paginate($perPage);

        // Realizar petición a la ruta pública
        $response = $this->get(route('areas.index'));

        // Verificar respuesta exitosa
        $response->assertStatus(200);
        $response->assertViewIs('areas.index');
        
        // Verificar que las variables están presentes en la vista
        $response->assertViewHas(['featuredAreas', 'regularAreas']);
        
        // Obtener las colecciones de la vista
        $featuredAreasInView = $response->viewData('featuredAreas');
        $regularAreasInView = $response->viewData('regularAreas');

        // Verificar que las áreas destacadas coinciden
        $this->assertEquals(
            $expectedFeaturedAreas->pluck('id')->sort()->values()->toArray(),
            $featuredAreasInView->pluck('id')->sort()->values()->toArray()
        );

        // Verificar que las áreas regulares coinciden (solo la página actual)
        $this->assertEquals(
            $expectedRegularAreas->pluck('id')->sort()->values()->toArray(),
            $regularAreasInView->pluck('id')->sort()->values()->toArray()
        );

        // Verificar la paginación
        $this->assertEquals(
            $expectedRegularAreas->currentPage(),
            $regularAreasInView->currentPage()
        );
        $this->assertEquals(
            $perPage,
            $regularAreasInView->perPage()
        );
        $this->assertEquals(
            $expectedRegularAreas->total(),
            $regularAreasInView->total()
        );
    }

    /**
     * Test de visualización administrativa de áreas
     * 
     * #[Test]
     */
    #[Test]
    public function privateIndex_requires_admin_role()
    {
        // TODO: Implementar test que verifique:
        // - Solo admins pueden acceder
        // - Muestra todas las áreas (no solo publicadas)
        // - Incluye áreas en cualquier estado
        // - Paginación y búsqueda funcionan
    }

    /**
     * Test de creación de área
     * 
     * #[Test]
     */
    #[Test]
    public function store_creates_new_area_with_valid_data()
    {
        // TODO: Implementar test que verifique:
        // - Solo admins pueden crear
        // - Valida datos requeridos
        // - Genera slug único
        // - Maneja imagen de portada
        // - Asigna usuario creador
        // - Respeta jerarquía si es subárea
    }

    /**
     * Test de actualización de área
     * 
     * #[Test]
     */
    #[Test]
    public function update_modifies_existing_area()
    {
        // TODO: Implementar test que verifique:
        // - Solo admins pueden actualizar
        // - Valida datos
        // - Mantiene o actualiza imagen
        // - No permite ciclos en jerarquía
        // - Mantiene integridad de datos
    }

    /**
     * Test de eliminación suave
     * 
     * #[Test]
     */
    #[Test]
    public function destroy_performs_soft_delete()
    {
        // TODO: Implementar test que verifique:
        // - Solo admins pueden eliminar
        // - Realiza soft delete
        // - Mantiene integridad referencial
        // - No afecta a otras áreas
    }

    /**
     * Test de restauración
     * 
     * #[Test]
     */
    #[Test]
    public function restore_recovers_soft_deleted_area()
    {
        // TODO: Implementar test que verifique:
        // - Solo admins pueden restaurar
        // - Restaura área correctamente
        // - Mantiene relaciones y datos
    }

    /**
     * Test de eliminación permanente
     * 
     * #[Test]
     */
    #[Test]
    public function forceDelete_permanently_removes_area()
    {
        // TODO: Implementar test que verifique:
        // - Solo admins pueden eliminar permanentemente
        // - Elimina área y sus relaciones
        // - Limpia archivos asociados
        // - No afecta a otras áreas
    }

    /**
     * Test de gestión de imágenes
     * 
     * #[Test]
     */
    #[Test]
    public function cover_image_management()
    {
        // TODO: Implementar test que verifique:
        // - Subida de imagen válida
        // - Validación de tipos de archivo
        // - Validación de dimensiones
        // - Actualización de imagen existente
        // - Eliminación de imagen
    }

    /**
     * Test de validaciones de estado
     * 
     * #[Test]
     */
    #[Test]
    public function status_transitions_are_valid()
    {
        // TODO: Implementar test que verifique:
        // - Transiciones de estado válidas
        // - Permisos necesarios por estado
        // - Validación de estados permitidos
    }

    /**
     * Test de jerarquía
     * 
     * #[Test]
     */
    #[Test]
    public function hierarchy_maintains_integrity()
    {
        // TODO: Implementar test que verifique:
        // - No permite ciclos
        // - Mantiene integridad al eliminar padre
        // - Validación de profundidad
        // - Ordenamiento correcto
    }
}
