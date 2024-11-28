<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Area;
use App\Models\Course;
use App\Models\Path;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;

class SlugGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buscar el usuario "Profe 1" que debería existir del seeder
        $this->user = User::where('email', 'profe1@example.com')->first();
        
        // Si no existe, crearlo con los mismos datos que usa el seeder
        if (!$this->user) {
            // Asegurarnos de que existe el rol 'teacher'
            if (!Role::where('name', 'teacher')->exists()) {
                Role::create(['name' => 'teacher', 'guard_name' => 'web']);
            }
            
            $this->user = User::create([
                'name' => 'Profe 1',
                'email' => 'profe1@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]);
            $this->user->assignRole('teacher');
        }
    }

    #[Test]
    public function it_generates_slug_when_creating_area()
    {
        $area = Area::create([
            'name' => 'Test Area Name',
            'description' => 'Test description',
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('test-area-name', $area->slug);
    }

    #[Test]
    public function it_updates_slug_when_updating_area_name()
    {
        $area = Area::create([
            'name' => 'Test Area Name',
            'description' => 'Test description',
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $area->update(['name' => 'Updated Area Name']);

        $this->assertEquals('updated-area-name', $area->slug);
    }

    #[Test]
    public function it_generates_unique_slugs_for_areas_with_same_name()
    {
        $area1 = Area::create([
            'name' => 'Test Area',
            'description' => 'Test description',
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $area2 = Area::create([
            'name' => 'Test Area',
            'description' => 'Another description',
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('test-area', $area1->slug);
        $this->assertEquals('test-area-1', $area2->slug);
    }

    #[Test]
    public function it_generates_slug_when_creating_course()
    {
        $course = Course::create([
            'title' => 'Test Course Title',
            'description' => 'Test description',
            'status' => 'draft',
            'author_id' => $this->user->id,
        ]);

        $this->assertEquals('test-course-title', $course->slug);
    }

    #[Test]
    public function it_generates_slug_when_creating_path()
    {
        $area = Area::create([
            'name' => 'Test Area',
            'description' => 'Test description',
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $path = Path::create([
            'name' => 'Test Path Name',
            'description' => 'Test description',
            'status' => 'draft',
            'user_id' => $this->user->id,
            'area_id' => $area->id,
        ]);

        $this->assertEquals('test-path-name', $path->slug);
    }
}
