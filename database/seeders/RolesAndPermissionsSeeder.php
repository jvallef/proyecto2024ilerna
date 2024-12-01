<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $teacher = Role::create(['name' => 'teacher']);
        $student = Role::create(['name' => 'student']);

        // Crear permisos específicos

        // Users (sólo admin puede gestionar usuarios)
        Permission::create(['name' => 'manage users']);

        // Areas (admins pueden gestionar todas las áreas)
        Permission::create(['name' => 'manage areas']);
        Permission::create(['name' => 'view areas']);
        Permission::create(['name' => 'restore areas']);    // Para restore/force-delete

        // Paths (admins pueden gestionar, teachers ver y editar sólo los suyos)
        Permission::create(['name' => 'manage paths']);
        Permission::create(['name' => 'view paths']);
        Permission::create(['name' => 'edit own paths']);

        // Courses (admins y teachers gestionan, students solo visualizan)
        Permission::create(['name' => 'manage courses']);
        Permission::create(['name' => 'view courses']);
        Permission::create(['name' => 'edit own courses']);

        // Contents (teachers pueden gestionar los suyos, admins gestionan todos, students solo ven)
        Permission::create(['name' => 'manage contents']);
        Permission::create(['name' => 'view contents']);
        Permission::create(['name' => 'edit own contents']);

        // Comments (todos los roles pueden ver, students y teachers solo gestionan los propios, admin gestiona todos)
        Permission::create(['name' => 'manage comments']);
        Permission::create(['name' => 'view comments']);
        Permission::create(['name' => 'edit own comments']);

        // Medias (admins gestionan todos, teachers pueden gestionar los suyos y students solo pueden visualizar)
        Permission::create(['name' => 'manage medias']);
        Permission::create(['name' => 'view medias']);
        Permission::create(['name' => 'edit own medias']);

        // Asignar permisos a roles

        // Admin tiene permisos completos
        $admin->givePermissionTo([
            'manage users',
            'manage areas',
            'view areas',
            'restore areas',
            'manage paths',
            'view paths',
            'manage courses',
            'view courses',
            'manage contents',
            'view contents',
            'manage comments',
            'view comments',
            'manage medias',
            'view medias',
        ]);

        // Teacher puede gestionar sus propios cursos, paths, y contenidos
        $teacher->givePermissionTo([
            'view areas',
            'view paths',
            'edit own paths',
            'view courses',
            'edit own courses',
            'view contents',
            'edit own contents',
            'view comments',
            'edit own comments',
            'view medias',
            'edit own medias',
        ]);

        // Student tiene permisos de solo lectura en la mayoría de las entidades, y puede gestionar sus propios comentarios
        $student->givePermissionTo([
            'view areas',
            'view paths',
            'view courses',
            'view contents',
            'view comments',
            'edit own comments',
            'view medias',
        ]);
    }
}
