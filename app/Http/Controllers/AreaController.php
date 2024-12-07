<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Services\AreaService;
use App\Http\Requests\AreaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AreaController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected $areaService;

    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
        
        // Solo admin puede gestionar áreas
        $this->middleware(['auth', 'role:admin'])->only([
            'privateIndex', 'privateShow', 'privateCreate', 'privateStore', 
            'privateEdit', 'privateUpdate', 'privateDestroy',
            'privateTrashed', 'privateRestore', 'privateForceDelete'
        ]);
        
        // Acceso a sección educativa requiere autenticación
        $this->middleware(['auth'])->only(['educaIndex', 'educaShow', 'educaProgress']);
    }

    /**
     * Display a public listing of published areas.
     */
    public function publicIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Area::with(['user', 'parent'])->published();

            // Aplicar búsqueda si existe
            $query = $this->applyAreaFilters($query, $request->input('search'));

            // Obtener áreas destacadas y regulares
            $featuredQuery = clone $query;
            $regularQuery = clone $query;

            $featuredAreas = $featuredQuery->where('featured', true)->get();
            $regularAreas = $regularQuery->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $regularAreas->appends(['search' => $request->input('search')]);
            }

            return view('areas.index', compact('featuredAreas', 'regularAreas'));
            
        } catch (\Exception $e) {
            Log::error('Error loading public areas index: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de áreas.');
        }
    }

    /**
     * Display a private listing of all areas for admin.
     */
    public function privateIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Area::with(['user', 'parent']);

            // Aplicar búsqueda si existe
            $query = $this->applyAreaFilters($query, $request->input('search'));

            // Obtener áreas destacadas y regulares
            $featuredQuery = clone $query;
            $regularQuery = clone $query;

            $featuredAreas = $featuredQuery->where('featured', true)->get();
            $regularAreas = $regularQuery->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $regularAreas->appends(['search' => $request->input('search')]);
            }

            return view('admin.areas.index', compact('featuredAreas', 'regularAreas'));
            
        } catch (\Exception $e) {
            Log::error('Error loading admin areas index: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de áreas.');
        }
    }

    /**
     * Display the educational listing of areas.
     */
    public function educaIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Area::with(['user', 'parent'])->published();

            // Aplicar búsqueda si existe
            $query = $this->applyAreaFilters($query, $request->input('search'));

            $areas = $query->orderBy('sort_order')->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $areas->appends(['search' => $request->input('search')]);
            }

            return view('educa.areas.index', compact('areas'));
            
        } catch (\Exception $e) {
            Log::error('Error loading educational areas index: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de áreas.');
        }
    }

    /**
     * Show the form for creating a new area.
     */
    public function privateCreate()
    {
        $area = new Area();
        $areasList = $this->flattenAreaList(Area::getHierarchicalList());
        return view('admin.areas.create', compact('area', 'areasList'));
    }

    /**
     * Store a newly created area in storage.
     */
    public function privateStore(AreaRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();
            
            $area = $this->areaService->create($data);

            if ($request->hasFile('cover')) {
                $area->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
            }
            
            \DB::commit();

            return redirect()->route('admin.areas.index')
                           ->with('success', 'Área creada correctamente.')
                           ->withHeaders([
                               'Cache-Control' => 'no-cache, no-store, must-revalidate',
                               'Pragma' => 'no-cache',
                               'Expires' => '0'
                           ]);
                           
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error creating area: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified area for public view.
     */
    public function publicShow($slug)
    {
        try {
            $area = Area::where('slug', $slug)->published()->firstOrFail();
            return view('areas.show', compact('area'));
        } catch (\Exception $e) {
            Log::error('Error showing public area: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Display the specified area for admin.
     */
    public function privateShow(Area $area)
    {
        return view('admin.areas.show', compact('area'));
    }

    /**
     * Display the specified area for educational view.
     */
    public function educaShow($slug)
    {
        try {
            $area = Area::where('slug', $slug)->published()->firstOrFail();
            return view('educa.areas.show', compact('area'));
        } catch (\Exception $e) {
            Log::error('Error showing educational area: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified area.
     */
    public function privateEdit(Area $area)
    {
        $areasList = $this->flattenAreaList(Area::getHierarchicalList());
        return view('admin.areas.edit', compact('area', 'areasList'));
    }

    /**
     * Update the specified area in storage.
     */
    public function privateUpdate(AreaRequest $request, Area $area)
    {
        try {
            \DB::beginTransaction();

            $area = $this->areaService->update($area, $request->validated());

            if ($request->hasFile('cover')) {
                $area->clearMediaCollection('cover');
                $area->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
            }

            \DB::commit();

            return redirect()->route('admin.areas.index')
                ->with('success', 'Área actualizada correctamente.');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error updating area: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified area from storage.
     */
    public function privateDestroy(Area $area)
    {
        try {
            $this->areaService->delete($area);
            return redirect()->route('admin.areas.index')
                ->with('success', 'Área eliminada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error deleting area: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display a listing of trashed areas.
     */
    public function privateTrashed(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Area::onlyTrashed()->with(['user', 'parent']);

            // Aplicar búsqueda si existe
            $query = $this->applyAreaFilters($query, $request->input('search'));

            $areas = $query->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $areas->appends(['search' => $request->input('search')]);
            }

            return view('admin.areas.trashed', compact('areas'));
        } catch (\Exception $e) {
            Log::error('Error loading trashed areas: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de áreas eliminadas.');
        }
    }

    /**
     * Restore the specified area from trash.
     */
    public function privateRestore($id)
    {
        try {
            $area = Area::onlyTrashed()->findOrFail($id);
            $area->restore();
            return redirect()->route('admin.areas.trashed')
                ->with('success', 'Área restaurada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error restoring area: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Permanently delete the specified area from storage.
     */
    public function privateForceDelete($id)
    {
        try {
            $area = Area::onlyTrashed()->findOrFail($id);
            $area->forceDelete();
            return redirect()->route('admin.areas.trashed')
                ->with('success', 'Área eliminada permanentemente.');
        } catch (\Exception $e) {
            Log::error('Error force deleting area: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display area progress for educational view.
     */
    public function educaProgress($slug)
    {
        try {
            $area = Area::where('slug', $slug)->published()->firstOrFail();
            return view('educa.areas.progress', compact('area'));
        } catch (\Exception $e) {
            Log::error('Error showing area progress: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Aplica los filtros de búsqueda al query de áreas
     */
    private function applyAreaFilters($query, $search = null)
    {
        if ($search) {
            Log::info('Búsqueda de área recibida', ['search' => $search]);
            
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            
            Log::info('SQL generado', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        }
        return $query;
    }

    /**
     * Convierte la lista jerárquica en una lista plana para el select
     */
    protected function flattenAreaList($areas, &$result = []): array
    {
        foreach ($areas as $area) {
            $result[$area['id']] = $area['full_name'];
            if (!empty($area['children'])) {
                $this->flattenAreaList($area['children'], $result);
            }
        }
        return $result;
    }
}
