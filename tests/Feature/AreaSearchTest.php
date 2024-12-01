<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function it_can_search_areas_by_name()
    {
        $area1 = Area::factory()->create(['name' => 'PHP Programming', 'is_published' => true]);
        $area2 = Area::factory()->create(['name' => 'JavaScript Basics', 'is_published' => true]);
        $area3 = Area::factory()->create(['name' => 'Python Development', 'is_published' => true]);

        $response = $this->getJson(route('api.areas.search', ['query' => 'PHP']));

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['name' => 'PHP Programming']);
    }

    /** @test */
    public function it_only_returns_published_areas_for_guests()
    {
        $publishedArea = Area::factory()->create([
            'name' => 'Published Area',
            'is_published' => true
        ]);
        
        $unpublishedArea = Area::factory()->create([
            'name' => 'Unpublished Area',
            'is_published' => false
        ]);

        $response = $this->getJson(route('api.areas.search', ['query' => 'Area']));

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['name' => 'Published Area'])
                ->assertJsonMissing(['name' => 'Unpublished Area']);
    }

    /** @test */
    public function admin_can_search_all_areas()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $publishedArea = Area::factory()->create([
            'name' => 'Published Area',
            'is_published' => true
        ]);
        
        $unpublishedArea = Area::factory()->create([
            'name' => 'Unpublished Area',
            'is_published' => false
        ]);

        $response = $this->actingAs($admin)
                        ->getJson(route('api.areas.search', ['query' => 'Area']));

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data')
                ->assertJsonFragment(['name' => 'Published Area'])
                ->assertJsonFragment(['name' => 'Unpublished Area']);
    }

    /** @test */
    public function it_returns_empty_results_for_no_matches()
    {
        Area::factory()->create(['name' => 'PHP Programming', 'is_published' => true]);

        $response = $this->getJson(route('api.areas.search', ['query' => 'Ruby']));

        $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function it_requires_minimum_search_length()
    {
        $response = $this->getJson(route('api.areas.search', ['query' => 'a']));

        $response->assertStatus(422);
    }

    /** @test */
    public function it_paginates_search_results()
    {
        $areas = Area::factory()->count(15)->create(['is_published' => true]);
        
        $response = $this->getJson(route('api.areas.search', [
            'query' => 'Area',
            'page' => 1,
            'per_page' => 10
        ]));

        $response->assertStatus(200)
                ->assertJsonCount(10, 'data')
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }
}
