<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;

class AreaHierarchyTest extends TestCase
{
    use WithFaker;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Obtener el primer usuario admin existente
        $this->admin = User::role('admin')->first();
    }

    /**
     * Test que verifica la obtención de la ruta completa (breadcrumb)
     */
    #[Test]
    public function it_should_get_full_hierarchical_path()
    {
        // 1. Obtener la Plaza de la Ciencia (id: 8)
        $plaza = Area::find(8);
        $this->assertNotNull($plaza);
        $this->assertEquals('Plaza de la Ciencia', $plaza->name);
        $this->assertEquals(7, $plaza->parent_id);
        
        // 2. Probar diferentes formas de obtener el parent
        $parent1 = Area::find($plaza->parent_id);
        $parent2 = $plaza->parent()->first();
        $plaza->load('parent');
        $parent3 = $plaza->parent;
        
        \Log::info('Diferentes formas de obtener el parent:', [
            'via_find' => $parent1 ? $parent1->name : null,
            'via_relation_first' => $parent2 ? $parent2->name : null,
            'via_eager_load' => $parent3 ? $parent3->name : null
        ]);
        
        // 3. Verificar que todas las formas funcionan
        $this->assertEquals('Barrio de la Ciencia', $parent1->name, 'parent via find falló');
        $this->assertEquals('Barrio de la Ciencia', $parent2->name, 'parent via relation->first() falló');
        $this->assertEquals('Barrio de la Ciencia', $parent3->name, 'parent via eager load falló');
    }

    /**
     * Test que verifica la obtención de áreas hijas
     */
    #[Test]
    public function it_should_get_children_areas()
    {
        $area = Area::find(7); // Barrio de la Ciencia
        $children = $area->children()->get();
        
        $this->assertCount(4, $children);
        $this->assertEquals([
            'Plaza de la Ciencia',
            'Plaza de la Tecnología',
            'Plaza de las Matemáticas',
            'Plaza de la Física'
        ], $children->pluck('name')->toArray());
    }

    /**
     * Test que verifica la obtención de la lista jerárquica
     */
    #[Test]
    public function it_should_get_hierarchical_list()
    {
        $this->actingAs($this->admin);
        
        // Obtener la lista jerárquica a través del controlador
        $response = $this->get(route('admin.areas.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas(['featuredAreas', 'regularAreas']);
        
        // Obtener las áreas regulares de la respuesta
        $areas = $response->viewData('regularAreas');
        $this->assertNotEmpty($areas);
        
        // Verificar que podemos ver el Barrio de la Ciencia
        $ciencia = $areas->firstWhere('name', 'Barrio de la Ciencia');
        $this->assertNotNull($ciencia);
        
        // Verificar que podemos ver la Plaza de la Ciencia como hijo
        $this->assertTrue($ciencia->children->contains('name', 'Plaza de la Ciencia'));
    }

    /**
     * Test que verifica que no se pueden crear ciclos en la jerarquía
     */
    #[Test]
    public function it_should_prevent_hierarchical_cycles()
    {
        $this->actingAs($this->admin);
        
        $padre = Area::find(7);  // Barrio de la Ciencia
        $hijo = Area::find(8);   // Plaza de la Ciencia
        
        $this->assertNotNull($padre, 'No se encontró el área padre');
        $this->assertNotNull($hijo, 'No se encontró el área hija');
        
        // Intentar actualizar el padre para que sea hijo de su propio hijo
        $response = $this->put(route('admin.areas.update', $padre), [
            'name' => $padre->name,
            'parent_id' => $hijo->id,
            'status' => $padre->status
        ]);
        
        $response->assertSessionHasErrors('parent_id');
    }
}
