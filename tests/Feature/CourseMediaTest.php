<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_upload_cover_image()
    {
        $course = Course::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg', 800, 600);

        $course->addMedia($file)
               ->toMediaCollection('cover');

        $this->assertTrue($course->getFirstMedia('cover') !== null);
        $this->assertTrue($course->getFirstMedia('cover')->hasGeneratedConversion('thumb'));
        $this->assertTrue($course->getFirstMedia('cover')->hasGeneratedConversion('medium'));
        $this->assertTrue($course->getFirstMedia('cover')->hasGeneratedConversion('large'));
    }

    /** @test */
    public function it_can_upload_banner_image()
    {
        $course = Course::factory()->create();
        $file = UploadedFile::fake()->image('banner.jpg', 1920, 400);

        $course->addMedia($file)
               ->toMediaCollection('banner');

        $this->assertTrue($course->getFirstMedia('banner') !== null);
        $this->assertTrue($course->getFirstMedia('banner')->hasGeneratedConversion('banner'));
    }

    /** @test */
    public function it_can_upload_course_files()
    {
        $course = Course::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $course->addMedia($file)
               ->toMediaCollection('files');

        $this->assertTrue($course->getMedia('files')->count() === 1);
    }

    /** @test */
    public function it_cleans_up_media_when_course_is_deleted()
    {
        $course = Course::factory()->create();
        
        // Añadir medios
        $cover = UploadedFile::fake()->image('cover.jpg');
        $banner = UploadedFile::fake()->image('banner.jpg');
        $file = UploadedFile::fake()->create('document.pdf');

        $course->addMedia($cover)->toMediaCollection('cover');
        $course->addMedia($banner)->toMediaCollection('banner');
        $course->addMedia($file)->toMediaCollection('files');

        // Verificar que los medios existen
        $this->assertTrue($course->getFirstMedia('cover') !== null);
        $this->assertTrue($course->getFirstMedia('banner') !== null);
        $this->assertTrue($course->getMedia('files')->count() === 1);

        // Eliminar el curso
        $course->delete();

        // Verificar que los medios fueron eliminados
        $this->assertTrue($course->getFirstMedia('cover') === null);
        $this->assertTrue($course->getFirstMedia('banner') === null);
        $this->assertTrue($course->getMedia('files')->count() === 0);
    }
}
