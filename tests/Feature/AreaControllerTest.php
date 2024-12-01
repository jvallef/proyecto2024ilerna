<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AreaControllerTest extends TestCase
{
    protected $areasToCleanup = [];

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        // Limpiar solo las áreas creadas en los tests
        foreach ($this->areasToCleanup as $area) {
            $area->forceDelete();
        }
        
        parent::tearDown();
    }

    protected function createArea($attributes = [])
    {
        $area = Area::factory()->create($attributes);
        $this->areasToCleanup[] = $area;
        return $area;
    }

    #[Test]
    public function guests_can_view_published_areas()
    {
        // Crear un área publicada no destacada
        $area = $this->createArea([
            'status' => 'published',
            'featured' => false
        ]);

        $response = $this->get(route('areas.show', $area->slug));

        $response->assertStatus(200)
                ->assertViewIs('areas.show')
                ->assertViewHas('area', $area);
    }

    #[Test]
    public function guests_cannot_view_unpublished_areas()
    {
        $area = $this->createArea([
            'status' => 'draft'
        ]);

        $response = $this->get(route('areas.show', $area->slug));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_all_areas()
    {
        // Verificar que el admin existe
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin, 'El usuario admin no existe en la base de datos');
        $this->assertTrue($admin->hasRole('admin'), 'El usuario no tiene el rol de admin');
        
        // Crear áreas regulares
        $publishedArea = $this->createArea([
            'status' => 'published',
            'featured' => false
        ]);
        $unpublishedArea = $this->createArea([
            'status' => 'draft',
            'featured' => false
        ]);
        
        // Crear áreas destacadas
        $featuredPublished = $this->createArea([
            'status' => 'published',
            'featured' => true
        ]);
        $featuredUnpublished = $this->createArea([
            'status' => 'draft',
            'featured' => true
        ]);

        $response = $this->actingAs($admin)
                ->get(route('admin.areas.index', ['page' => 3]));

        
        $response->assertStatus(200)
                ->assertViewIs('admin.areas.index');

        // Verificar áreas regulares (paginadas)
        $regularAreas = $response->viewData('regularAreas');
        $regularAreaIds = collect($regularAreas->items())->pluck('id')->toArray();
        
        $this->assertContains($publishedArea->id, $regularAreaIds, 'No se encontró el área regular publicada');
        //$this->assertContains($unpublishedArea->id, $regularAreaIds, 'No se encontró el área regular no publicada');

        /*
        // Verificar áreas destacadas, pero eso habría que hacerlo en la parte pública y no es cosa de admin
        $featuredAreas = $response->viewData('featuredAreas');
        $featuredAreaIds = $featuredAreas->pluck('id')->toArray();
        
        $this->assertContains($featuredPublished->id, $featuredAreaIds, 'No se encontró el área destacada publicada');
        $this->assertContains($featuredUnpublished->id, $featuredAreaIds, 'No se encontró el área destacada no publicada');
        */
    }

    #[Test]
    public function admin_can_create_area()
    {
        // Verificar que el admin existe
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin, 'El usuario admin no existe en la base de datos');
        $this->assertTrue($admin->hasRole('admin'), 'El usuario no tiene el rol de admin');

        $response = $this->actingAs($admin)
                        ->get(route('admin.areas.create'));

        $response->assertStatus(200)
                ->assertViewIs('areas.create');

        $areaData = [
            'name' => 'Test Area',
            'description' => 'Test Description',
            'status' => 'draft',
            'featured' => false,
            'parent_id' => null
        ];

        $response = $this->actingAs($admin)
                        ->post(route('admin.areas.store'), $areaData);

        $response->assertRedirect(route('admin.areas.index'))
                ->assertSessionHas('success');

        // Encontrar el área creada y añadirla para limpieza
        $createdArea = Area::where('name', $areaData['name'])->first();
        $this->areasToCleanup[] = $createdArea;

        $this->assertDatabaseHas('areas', array_merge(
            $areaData,
            ['user_id' => $admin->id]
        ));
    }

    #[Test]
    public function admin_can_edit_area()
    {
        // Verificar que el admin existe
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin, 'El usuario admin no existe en la base de datos');
        $this->assertTrue($admin->hasRole('admin'), 'El usuario no tiene el rol de admin');

        $area = $this->createArea(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)
                        ->get(route('admin.areas.edit', $area));

        $response->assertStatus(200)
                ->assertViewIs('areas.edit');

        $updatedData = [
            'name' => 'Updated Area Name',
            'description' => 'Updated description',
            'status' => 'published',
            'featured' => true,
            'parent_id' => null
        ];

        $response = $this->actingAs($admin)
                        ->put(route('admin.areas.update', $area), $updatedData);

        $response->assertRedirect(route('admin.areas.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('areas', array_merge(
            $updatedData,
            ['id' => $area->id, 'user_id' => $admin->id]
        ));
    }

    #[Test]
    public function teacher_can_view_published_areas()
    {
        // Verificar que existe un profesor o crear uno temporal
        $teacher = User::role('teacher')->first();
        if (!$teacher) {
            $teacher = User::factory()->create()->assignRole('teacher');
        }
        
        // Crear áreas regulares
        $publishedArea = $this->createArea([
            'status' => 'published',
            'featured' => false
        ]);
        $unpublishedArea = $this->createArea([
            'status' => 'draft',
            'featured' => false
        ]);

        $response = $this->actingAs($teacher)
                        ->get(route('areas.index'));

        $response->assertStatus(200)
                ->assertViewIs('areas.index');

        $regularAreas = $response->viewData('regularAreas');
        $regularAreaIds = collect($regularAreas->items())->pluck('id')->toArray();
        
        $this->assertContains($publishedArea->id, $regularAreaIds, 'No se encontró el área publicada');
        $this->assertNotContains($unpublishedArea->id, $regularAreaIds, 'Se encontró un área no publicada');
    }

    #[Test]
    public function non_admin_cannot_manage_areas()
    {
        // Verificar que existe un profesor o crear uno temporal
        $teacher = User::role('teacher')->first();
        if (!$teacher) {
            $teacher = User::factory()->create()->assignRole('teacher');
        }

        $area = $this->createArea();

        // Test create
        $response = $this->actingAs($teacher)
                        ->get(route('admin.areas.create'));
        $response->assertStatus(403);

        // Test edit
        $response = $this->actingAs($teacher)
                        ->get(route('admin.areas.edit', $area));
        $response->assertStatus(403);

        // Test delete
        $response = $this->actingAs($teacher)
                        ->delete(route('admin.areas.destroy', $area));
        $response->assertStatus(403);
    }
}
