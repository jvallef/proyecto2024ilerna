<?php

namespace App\Services;

use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaService
{
    /**
     * Crea una nueva área
     *
     * @param array $data
     * @return Area
     */
    public function create(array $data): Area
    {
        try {
            DB::beginTransaction();

            // Asignar sort_order si no se proporciona
            if (!isset($data['sort_order'])) {
                $data['sort_order'] = $this->getNextSortOrder($data['parent_id'] ?? null);
            }

            // Crear el área
            $area = Area::create($data);

            // Procesar imagen si se proporciona
            if (isset($data['image'])) {
                $area->addMediaFromRequest('image')
                     ->toMediaCollection('cover');
            }

            DB::commit();
            return $area;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando área: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualiza un área existente
     *
     * @param Area $area
     * @param array $data
     * @return Area
     */
    public function update(Area $area, array $data): Area
    {
        try {
            DB::beginTransaction();

            // Si cambia el padre, actualizamos el sort_order
            if (isset($data['parent_id']) && $data['parent_id'] !== $area->parent_id) {
                $data['sort_order'] = $this->getNextSortOrder($data['parent_id']);
            }

            $area->update($data);

            // Procesar imagen si se proporciona
            if (isset($data['image'])) {
                $area->clearMediaCollection('cover');
                $area->addMediaFromRequest('image')
                     ->toMediaCollection('cover');
            }

            DB::commit();
            return $area;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando área: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina un área
     *
     * @param Area $area
     * @return bool
     */
    public function delete(Area $area): bool
    {
        try {
            DB::beginTransaction();

            // Reordenar las áreas hermanas
            $this->reorderSiblings($area);

            // Eliminar el área
            $area->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando área: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene el siguiente valor de sort_order para un padre específico
     *
     * @param int|null $parentId
     * @return int
     */
    protected function getNextSortOrder(?int $parentId): int
    {
        return Area::where('parent_id', $parentId)
                  ->max('sort_order') + 1;
    }

    /**
     * Reordena las áreas hermanas después de eliminar un área
     *
     * @param Area $area
     * @return void
     */
    protected function reorderSiblings(Area $area): void
    {
        Area::where('parent_id', $area->parent_id)
            ->where('sort_order', '>', $area->sort_order)
            ->decrement('sort_order');
    }

    /**
     * Actualiza el orden de las áreas
     *
     * @param array $orderedIds ID de áreas en el orden deseado
     * @param int|null $parentId ID del área padre
     * @return void
     */
    public function updateOrder(array $orderedIds, ?int $parentId = null): void
    {
        try {
            DB::beginTransaction();

            foreach ($orderedIds as $index => $id) {
                Area::where('id', $id)->update([
                    'sort_order' => $index,
                    'parent_id' => $parentId
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando orden de áreas: ' . $e->getMessage());
            throw $e;
        }
    }
}
