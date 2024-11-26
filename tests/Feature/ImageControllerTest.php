<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_upload_image()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->post('/api/images', [
            'file' => $file
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'path',
                'url',
                'thumbnail_url'
            ]);

        // Verify files exist
        $path = $response->json('path');
        $thumbnailPath = str_replace('images/', 'images/thumbnails/', $path);
        
        Storage::disk('public')->assertExists($path);
        Storage::disk('public')->assertExists($thumbnailPath);
    }

    public function test_rejects_invalid_file_type()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post('/api/images', [
            'file' => $file
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid file type']);
    }

    public function test_can_delete_image()
    {
        // First upload an image
        $file = UploadedFile::fake()->image('test.jpg');
        $response = $this->post('/api/images', ['file' => $file]);
        $path = $response->json('path');
        $filename = basename($path);

        // Then delete it
        $response = $this->delete('/api/images/' . $filename);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify files are deleted
        $thumbnailPath = str_replace('images/', 'images/thumbnails/', $path);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing($thumbnailPath);
    }
}
