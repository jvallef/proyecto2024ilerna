<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaValidationTest extends TestCase
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
    public function name_is_required()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'description' => 'Test Description',
                            'is_published' => true
                        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function name_must_be_unique()
    {
        Area::factory()->create(['name' => 'Test Area']);

        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => 'Test Area',
                            'description' => 'Test Description',
                            'is_published' => true
                        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function name_can_be_same_when_updating_own_area()
    {
        $area = Area::factory()->create(['name' => 'Test Area']);

        $response = $this->actingAs($this->admin)
                        ->put(route('admin.areas.update', $area), [
                            'name' => 'Test Area',
                            'description' => 'Updated Description',
                            'is_published' => true
                        ]);

        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function name_must_be_at_least_3_characters()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => 'Ab',
                            'description' => 'Test Description',
                            'is_published' => true
                        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function name_must_not_exceed_255_characters()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => str_repeat('a', 256),
                            'description' => 'Test Description',
                            'is_published' => true
                        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function description_is_optional()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => 'Test Area',
                            'is_published' => true
                        ]);

        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function is_published_must_be_boolean()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => 'Test Area',
                            'description' => 'Test Description',
                            'is_published' => 'not-a-boolean'
                        ]);

        $response->assertSessionHasErrors('is_published');
    }

    /** @test */
    public function slug_is_automatically_generated()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => 'Test Area Name',
                            'description' => 'Test Description',
                            'is_published' => true
                        ]);

        $this->assertDatabaseHas('areas', [
            'name' => 'Test Area Name',
            'slug' => 'test-area-name'
        ]);
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Area::factory()->create(['name' => 'Test Area']);

        $response = $this->actingAs($this->admin)
                        ->post(route('admin.areas.store'), [
                            'name' => 'Test Area',
                            'description' => 'Different Description',
                            'is_published' => true
                        ]);

        $this->assertDatabaseCount('areas', 1);
        $response->assertSessionHasErrors('name');
    }
}
