<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseService
{
    /**
     * Crea un nuevo curso
     *
     * @param array $data
     * @return Course
     */
    public function create(array $data): Course
    {
        try {
            DB::beginTransaction();

            // Crear el curso
            $course = Course::create($data);

            // Procesar imagen si se proporciona
            if (isset($data['image'])) {
                $course->addMediaFromRequest('image')
                     ->toMediaCollection('cover');
            }

            DB::commit();
            return $course;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando curso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualiza un curso existente
     *
     * @param Course $course
     * @param array $data
     * @return Course
     */
    public function update(Course $course, array $data): Course
    {
        try {
            DB::beginTransaction();

            // Manejar el campo featured explícitamente
            $data['featured'] = isset($data['featured']) ? true : false;

            $course->update($data);

            // Procesar imagen si se proporciona
            if (isset($data['image'])) {
                $course->addMediaFromRequest('image')
                     ->toMediaCollection('cover');
            }

            DB::commit();
            return $course;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando curso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina un curso
     *
     * @param Course $course
     * @return bool
     * @throws \Exception Si el curso tiene matrículas activas
     */
    public function delete(Course $course): bool
    {
        try {
            DB::beginTransaction();

            // Verificar si hay matrículas activas
            if ($course->enrolledUsers()->count() > 0) {
                throw new \Exception('No se puede eliminar un curso que tiene estudiantes matriculados.');
            }

            // Eliminar relaciones
            $course->paths()->detach();
            $course->contents()->detach();
            
            // Eliminar media
            $course->clearMediaCollection('cover');
            $course->clearMediaCollection('banner');
            $course->clearMediaCollection('files');

            // Eliminar el curso
            $course->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando curso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Matricula un usuario en un curso
     *
     * @param Course $course
     * @param User $user
     * @param string $role
     * @return void
     */
    public function enrollUser(Course $course, User $user, string $role = 'student'): void
    {
        try {
            DB::beginTransaction();

            // Verificar si el usuario ya está matriculado
            if (!$course->enrolledUsers()->where('user_id', $user->id)->exists()) {
                $course->enrolledUsers()->attach($user->id, [
                    'role' => $role,
                    'progress' => 0,
                    'completed' => false
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error matriculando usuario en curso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Desmatricula un usuario de un curso
     *
     * @param Course $course
     * @param User $user
     * @return void
     */
    public function unenrollUser(Course $course, User $user): void
    {
        try {
            DB::beginTransaction();

            // Verificar si el usuario está matriculado
            if ($course->enrolledUsers()->where('user_id', $user->id)->exists()) {
                $course->enrolledUsers()->detach($user->id);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error desmatriculando usuario del curso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualiza el progreso de un usuario en un curso
     *
     * @param Course $course
     * @param User $user
     * @param float $progress
     * @param bool $completed
     * @return void
     */
    public function updateProgress(Course $course, User $user, float $progress, bool $completed = false): void
    {
        try {
            DB::beginTransaction();

            $course->enrolledUsers()->updateExistingPivot($user->id, [
                'progress' => $progress,
                'completed' => $completed
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando progreso: ' . $e->getMessage());
            throw $e;
        }
    }
}
