<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserAvatarValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles necesarios
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Crear un usuario admin para las pruebas
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $this->actingAs($admin);
    }

    public function test_oversized_dimensions_validation()
    {
        // Crear una imagen con dimensiones mayores a las permitidas (4096x4096)
        $width = 5000;
        $height = 5000;
        
        // Crear una imagen más pequeña para evitar problemas de memoria
        $image = imagecreatetruecolor($width, $height);
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 255, 255, 255));
        
        // Guardar la imagen en un archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'test_image_');
        imagejpeg($image, $tempFile);
        imagedestroy($image);
        
        // Crear un UploadedFile simulado
        $file = new UploadedFile(
            $tempFile,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['user'],
            'avatar' => $file
        ]);

        // La validación debería fallar y redirigir de vuelta
        $response->assertRedirect();
        $response->assertSessionHasErrors('avatar');
        
        // Verificar que hay un error de dimensiones
        $errors = $response->getSession()->get('errors')->getBag('default')->all();
        $this->assertContains('validation.dimensions', $errors);

        // Limpiar el archivo temporal
        @unlink($tempFile);
    }

    public function test_valid_image_validation()
    {
        // Crear una imagen válida
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['user'],
            'avatar' => $file
        ]);

        $response->assertSessionDoesntHaveErrors('avatar');
    }

    public function test_rejects_non_image_file()
    {
        // Intentar subir un archivo PHP malicioso disfrazado como imagen
        $maliciousFile = UploadedFile::fake()->create(
            'malicious.php.jpg',
            500, // tamaño en bytes
            'application/x-httpd-php' // MIME type malicioso
        );

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['user'],
            'avatar' => $maliciousFile
        ]);

        $response->assertSessionHasErrors('avatar');
        
        // Verificar que devuelve el mensaje de error en español
        $errors = $response->getSession()->get('errors')->getBag('default')->all();
        $this->assertContains('El archivo debe ser una imagen válida.', $errors);
    }

    public function test_rejects_svg_files()
    {
        // SVG puede contener código malicioso
        $svgFile = UploadedFile::fake()->create(
            'image.svg',
            500,
            'image/svg+xml'
        );

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['user'],
            'avatar' => $svgFile
        ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_rejects_modified_mime_type()
    {
        // Crear un archivo PHP pero con MIME type modificado a imagen
        $maliciousFile = UploadedFile::fake()->create(
            'malicious.jpg',
            500,
            'image/jpeg'
        );

        // Modificar el contenido para incluir código PHP
        file_put_contents(
            $maliciousFile->getPathname(),
            '<?php echo "malicious"; ?>' . file_get_contents($maliciousFile->getPathname())
        );

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['user'],
            'avatar' => $maliciousFile
        ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_rejects_zero_byte_image()
    {
        // Intentar subir un archivo de 0 bytes
        $emptyFile = UploadedFile::fake()->create(
            'empty.jpg',
            0,
            'image/jpeg'
        );

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['user'],
            'avatar' => $emptyFile
        ]);

        $response->assertSessionHasErrors('avatar');
    }
}
