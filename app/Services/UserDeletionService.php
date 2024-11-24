<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserDeletionService
{
    /**
     * Realiza un soft delete del usuario y gestiona las relaciones.
     *
     * @param User $user
     * @return bool
     */
    public function softDeleteUser(User $user): bool
    {
        try {
            \Log::info('Iniciando soft delete de usuario:', ['user_id' => $user->id]);
            
            DB::beginTransaction();

            // 1. Marcar inscripciones como canceladas
            $user->enrolledCourses()->whereNull('completed_at')->update([
                'status' => 'cancelled',
                'deleted_at' => now()
            ]);

            $user->enrolledPaths()->whereNull('completed_at')->update([
                'status' => 'cancelled',
                'deleted_at' => now()
            ]);

            // 2. Si es profesor, actualizar la referencia de autor en sus cursos
            if ($user->hasRole('teacher')) {
                \Log::info('Usuario es profesor, actualizando cursos');
                // Marcar en los cursos que este profesor ya no está activo
                // pero mantener su referencia histórica
                $user->teacherCourses()->update([
                    'author_active' => false,
                    'author_deactivated_at' => now()
                ]);
            }

            // 3. Anonimizar datos sensibles pero mantener referencias
            $user->update([
                'email' => "deleted_{$user->id}_" . now()->timestamp . '@deleted.user',
                'phone' => null,
                'profile' => null,
            ]);

            // 4. Soft delete del usuario
            $deleted = $user->delete();
            
            \Log::info('Resultado de delete():', ['deleted' => $deleted]);

            DB::commit();
            \Log::info('Soft delete completado con éxito');
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar usuario {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina permanentemente al usuario y gestiona las relaciones.
     * Solo debe usarse en casos específicos o por comando administrativo.
     *
     * @param User $user
     * @return bool
     */
    public function forceDeleteUser(User $user): bool
    {
        try {
            DB::beginTransaction();

            // 1. Eliminar medias asociados
            $user->medias()->delete();

            // 2. Eliminar registros de inscripción manteniendo histórico
            // No eliminamos los registros, solo los marcamos como del usuario eliminado
            $user->enrolledCourses()->update([
                'deleted_by_user' => true,
                'deleted_at' => now()
            ]);
            
            $user->enrolledPaths()->update([
                'deleted_by_user' => true,
                'deleted_at' => now()
            ]);

            // 3. Gestionar áreas creadas
            foreach ($user->areas as $area) {
                // Si el área tiene hijos o contenido, reasignar a admin
                if ($area->children()->exists() || $area->paths()->exists()) {
                    $area->update(['user_id' => 1]); // Asumiendo que ID 1 es admin
                } else {
                    $area->delete(); // Soft delete si no tiene dependencias
                }
            }

            // 4. En cursos donde era profesor, mantener referencia histórica
            if ($user->hasRole('teacher')) {
                $user->teacherCourses()->update([
                    'author_active' => false,
                    'author_deactivated_at' => now(),
                    'author_permanently_deleted' => true
                ]);
            }

            // 5. Eliminar permanentemente al usuario
            $user->forceDelete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar permanentemente usuario {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Restaura un usuario previamente eliminado (soft delete).
     *
     * @param User $user
     * @return bool
     */
    public function restoreUser(User $user): bool
    {
        try {
            DB::beginTransaction();

            // 1. Restaurar usuario
            $user->restore();

            // 2. Restaurar inscripciones canceladas por la eliminación
            $user->enrolledCourses()->onlyTrashed()
                ->where('status', 'cancelled')
                ->restore();

            $user->enrolledPaths()->onlyTrashed()
                ->where('status', 'cancelled')
                ->restore();

            // 3. Si es profesor, reactivar su estado como autor en los cursos
            if ($user->hasRole('teacher')) {
                $user->teacherCourses()
                    ->where('author_active', false)
                    ->whereNull('author_permanently_deleted')
                    ->update([
                        'author_active' => true,
                        'author_deactivated_at' => null
                    ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al restaurar usuario {$user->id}: " . $e->getMessage());
            return false;
        }
    }
}
