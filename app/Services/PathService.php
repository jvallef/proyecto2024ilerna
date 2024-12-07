<?php

namespace App\Services;

use App\Models\Path;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PathService
{
    /**
     * Crea un nuevo path
     *
     * @param array $data
     * @return Path
     */
    public function create(array $data): Path
    {
        try {
            DB::beginTransaction();

            // Asignar sort_order si no se proporciona
            if (!isset($data['sort_order'])) {
                $data['sort_order'] = $this->getNextSortOrder($data['parent_id'] ?? null, $data['area_id']);
            }

            // Crear el path
            $path = Path::create($data);

            // Procesar imagen si se proporciona
            if (isset($data['image'])) {
                $path->addMediaFromRequest('image')
                     ->toMediaCollection('cover');
            }

            DB::commit();
            return $path;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando path: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualiza un path existente
     *
     * @param Path $path
     * @param array $data
     * @return Path
     */
    public function update(Path $path, array $data): Path
    {
        try {
            DB::beginTransaction();

            // Si cambia el padre o el área, actualizamos el sort_order
            if ((isset($data['parent_id']) && $data['parent_id'] !== $path->parent_id) ||
                (isset($data['area_id']) && $data['area_id'] !== $path->area_id)) {
                $data['sort_order'] = $this->getNextSortOrder(
                    $data['parent_id'] ?? $path->parent_id,
                    $data['area_id'] ?? $path->area_id
                );
            }

            // Manejar el campo featured explícitamente
            $data['featured'] = isset($data['featured']) ? true : false;

            $path->update($data);

            // Procesar imagen si se proporciona
            if (isset($data['image'])) {
                $path->clearMediaCollection('cover');
                $path->addMediaFromRequest('image')
                     ->toMediaCollection('cover');
            }

            DB::commit();
            return $path;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando path: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina un path
     *
     * @param Path $path
     * @return bool
     * @throws \Exception Si el path tiene sub-paths o cursos asociados
     */
    public function delete(Path $path): bool
    {
        try {
            // Verificar si el path tiene sub-paths
            if ($path->children()->count() > 0) {
                throw new \Exception('No se puede eliminar una ruta que tiene sub-rutas.');
            }

            // TODO: Implementar validación de cursos cuando el módulo esté desarrollado
            /*
            if ($path->courses()->count() > 0) {
                throw new \Exception('No se puede eliminar una ruta que tiene cursos asociados.');
            }
            */

            DB::beginTransaction();

            // Reordenar los paths hermanos
            $this->reorderSiblings($path);

            // Eliminar el path
            $path->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando path: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene el siguiente valor de sort_order para un padre y área específicos
     *
     * @param int|null $parentId
     * @param int $areaId
     * @return int
     */
    protected function getNextSortOrder(?int $parentId, int $areaId): int
    {
        return Path::where('parent_id', $parentId)
                  ->where('area_id', $areaId)
                  ->max('sort_order') + 1;
    }

    /**
     * Reordena los paths hermanos después de eliminar un path
     *
     * @param Path $path
     * @return void
     */
    protected function reorderSiblings(Path $path): void
    {
        Path::where('parent_id', $path->parent_id)
            ->where('area_id', $path->area_id)
            ->where('sort_order', '>', $path->sort_order)
            ->decrement('sort_order');
    }

    /**
     * Actualiza el orden de los paths
     *
     * @param array $orderedIds ID de paths en el orden deseado
     * @param int|null $parentId ID del path padre
     * @param int $areaId ID del área
     * @return void
     */
    public function updateOrder(array $orderedIds, ?int $parentId = null, int $areaId): void
    {
        try {
            DB::beginTransaction();

            foreach ($orderedIds as $index => $id) {
                Path::where('id', $id)->update([
                    'sort_order' => $index,
                    'parent_id' => $parentId,
                    'area_id' => $areaId
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando orden de paths: ' . $e->getMessage());
            throw $e;
        }
    }
}
