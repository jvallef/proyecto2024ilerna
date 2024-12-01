<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@example.com')->first();
    }

    /** @test */
    public function areas_can_have_parent_areas()
    {
        $parentArea = Area::factory()->create();
        $childArea = Area::factory()->create(['parent_id' => $parentArea->id]);

        $this->assertEquals($parentArea->id, $childArea->parent_id);
        $this->assertTrue($parentArea->children->contains($childArea));
    }

    /** @test */
    public function areas_can_have_multiple_children()
    {
        $parentArea = Area::factory()->create();
        $childAreas = Area::factory()->count(3)->create(['parent_id' => $parentArea->id]);

        $this->assertEquals(3, $parentArea->children->count());
        foreach ($childAreas as $child) {
            $this->assertTrue($parentArea->children->contains($child));
        }
    }

    /** @test */
    public function child_areas_know_their_parent()
    {
        $parentArea = Area::factory()->create();
        $childArea = Area::factory()->create(['parent_id' => $parentArea->id]);

        $this->assertEquals($parentArea->id, $childArea->parent->id);
        $this->assertEquals($parentArea->name, $childArea->parent->name);
    }

    /** @test */
    public function areas_can_be_root_level()
    {
        $rootArea = Area::factory()->create(['parent_id' => null]);

        $this->assertNull($rootArea->parent_id);
        $this->assertNull($rootArea->parent);
    }

    /** @test */
    public function deleting_parent_does_not_delete_children()
    {
        $parentArea = Area::factory()->create();
        $childArea = Area::factory()->create(['parent_id' => $parentArea->id]);

        $this->actingAs($this->admin)
             ->delete(route('admin.areas.destroy', $parentArea));

        $this->assertSoftDeleted($parentArea);
        $this->assertNotSoftDeleted($childArea);
        $this->assertDatabaseHas('areas', ['id' => $childArea->id]);
    }

    /** @test */
    public function can_reassign_children_to_new_parent()
    {
        $oldParent = Area::factory()->create();
        $newParent = Area::factory()->create();
        $childArea = Area::factory()->create(['parent_id' => $oldParent->id]);

        $response = $this->actingAs($this->admin)
                        ->put(route('admin.areas.update', $childArea), [
                            'name' => $childArea->name,
                            'description' => $childArea->description,
                            'is_published' => $childArea->is_published,
                            'parent_id' => $newParent->id
                        ]);

        $this->assertEquals($newParent->id, $childArea->fresh()->parent_id);
    }

    /** @test */
    public function cannot_create_circular_reference()
    {
        $parentArea = Area::factory()->create();
        $childArea = Area::factory()->create(['parent_id' => $parentArea->id]);

        $response = $this->actingAs($this->admin)
                        ->put(route('admin.areas.update', $parentArea), [
                            'name' => $parentArea->name,
                            'description' => $parentArea->description,
                            'is_published' => $parentArea->is_published,
                            'parent_id' => $childArea->id
                        ]);

        $response->assertSessionHasErrors('parent_id');
        $this->assertNull($parentArea->fresh()->parent_id);
    }

    /** @test */
    public function can_get_all_ancestors()
    {
        $grandparent = Area::factory()->create();
        $parent = Area::factory()->create(['parent_id' => $grandparent->id]);
        $child = Area::factory()->create(['parent_id' => $parent->id]);

        $ancestors = $child->ancestors;

        $this->assertTrue($ancestors->contains($grandparent));
        $this->assertTrue($ancestors->contains($parent));
    }

    /** @test */
    public function can_get_all_descendants()
    {
        $parent = Area::factory()->create();
        $child1 = Area::factory()->create(['parent_id' => $parent->id]);
        $child2 = Area::factory()->create(['parent_id' => $parent->id]);
        $grandchild = Area::factory()->create(['parent_id' => $child1->id]);

        $descendants = $parent->descendants;

        $this->assertTrue($descendants->contains($child1));
        $this->assertTrue($descendants->contains($child2));
        $this->assertTrue($descendants->contains($grandchild));
    }
}
