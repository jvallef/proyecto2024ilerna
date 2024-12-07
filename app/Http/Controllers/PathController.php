<?php

namespace App\Http\Controllers;

use App\Models\Path;
use App\Models\Area;
use App\Services\PathService;
use App\Http\Requests\PathRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class PathController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected $pathService;

    public function __construct(PathService $pathService)
    {
        $this->pathService = $pathService;
        
        // Solo admin puede gestionar paths
        $this->middleware(['auth', 'role:admin'])->only([
            'privateIndex', 'privateShow', 'privateCreate', 'privateStore', 
            'privateEdit', 'privateUpdate', 'privateDestroy',
            'privateTrashed', 'privateRestore', 'privateForceDelete'
        ]);
        
        // Acceso a sección educativa requiere autenticación
        $this->middleware(['auth'])->only(['educaIndex', 'educaShow', 'educaProgress']);
    }

    /**
     * Display a public listing of published paths.
     */
    public function publicIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Path::with(['user', 'parent', 'area'])->published();

            // Aplicar búsqueda si existe
            $query = $this->applyPathFilters($query, $request->input('search'));

            // Obtener paths destacados y regulares
            $featuredQuery = clone $query;
            $regularQuery = clone $query;

            $featuredPaths = $featuredQuery->where('featured', true)->get();
            $regularPaths = $regularQuery->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $regularPaths->appends(['search' => $request->input('search')]);
            }

            return view('paths.index', compact('featuredPaths', 'regularPaths'));
            
        } catch (\Exception $e) {
            Log::error('Error loading public paths index: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de rutas.');
        }
    }

    /**
     * Display a private listing of all paths for admin.
     */
    public function privateIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Path::with(['user', 'parent', 'area']);

            // Aplicar búsqueda si existe
            $query = $this->applyPathFilters($query, $request->input('search'));

            // Obtener paths destacados y regulares
            $featuredQuery = clone $query;
            $regularQuery = clone $query;

            $featuredPaths = $featuredQuery->where('featured', true)->get();
            $regularPaths = $regularQuery->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $regularPaths->appends(['search' => $request->input('search')]);
            }

            return view('admin.paths.index', compact('featuredPaths', 'regularPaths'));
            
        } catch (\Exception $e) {
            Log::error('Error loading admin paths index: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de rutas.');
        }
    }

    /**
     * Display the educational listing of paths.
     */
    public function educaIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Path::with(['user', 'parent', 'area'])->published();

            // Aplicar búsqueda si existe
            $query = $this->applyPathFilters($query, $request->input('search'));

            $paths = $query->orderBy('sort_order')->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $paths->appends(['search' => $request->input('search')]);
            }

            return view('educa.paths.index', compact('paths'));
            
        } catch (\Exception $e) {
            Log::error('Error loading educational paths index: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar el listado de rutas.');
        }
    }

    /**
     * Show the form for creating a new path.
     */
    public function privateCreate()
    {
        $path = new Path();
        $pathsList = $this->flattenPathList(Path::getHierarchicalList());
        $areasList = $this->getAreasForSelect(Area::getHierarchicalList());
        return view('admin.paths.create', compact('path', 'pathsList', 'areasList'));
    }

    /**
     * Store a newly created path in storage.
     */
    public function privateStore(PathRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();
            
            $path = $this->pathService->create($data);

            if ($request->hasFile('cover')) {
                $path->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
            }
            
            \DB::commit();

            return redirect()->route('admin.paths.index')
                           ->with('success', 'La ruta se ha creado correctamente.')
                           ->withHeaders([
                               'Cache-Control' => 'no-cache, no-store, must-revalidate',
                               'Pragma' => 'no-cache',
                               'Expires' => '0'
                           ]);
                           
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error creating path: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified path for public view.
     */
    public function publicShow($slug)
    {
        try {
            $path = Path::where('slug', $slug)->published()->firstOrFail();
            return view('paths.show', compact('path'));
        } catch (\Exception $e) {
            Log::error('Error showing public path: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Display the specified path for admin.
     */
    public function privateShow(Path $path)
    {
        return view('admin.paths.show', compact('path'));
    }

    /**
     * Display the specified path for educational view.
     */
    public function educaShow($slug)
    {
        try {
            $path = Path::where('slug', $slug)->published()->firstOrFail();
            return view('educa.paths.show', compact('path'));
        } catch (\Exception $e) {
            Log::error('Error showing educational path: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified path.
     */
    public function privateEdit(Path $path)
    {
        $pathsList = $this->flattenPathList(Path::getHierarchicalList($path->id));
        $areasList = $this->getAreasForSelect(Area::getHierarchicalList());
        return view('admin.paths.edit', compact('path', 'pathsList', 'areasList'));
    }

    /**
     * Update the specified path in storage.
     */
    public function privateUpdate(PathRequest $request, Path $path)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();
            $this->pathService->update($path, $data);

            if ($request->hasFile('cover')) {
                $path->clearMediaCollection('cover');
                $path->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
            }

            \DB::commit();

            return redirect()->route('admin.paths.index')
                           ->with('success', 'La ruta se ha actualizado correctamente.');
                           
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error updating path: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified path from storage.
     */
    public function privateDestroy(Path $path)
    {
        try {
            $this->pathService->delete($path);
            return redirect()->route('admin.paths.index')
                           ->with('success', 'La ruta se ha eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error deleting path: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display a listing of trashed paths.
     */
    public function privateTrashed(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Path::onlyTrashed()->with(['user', 'parent', 'area']);

            // Aplicar búsqueda si existe
            $query = $this->applyPathFilters($query, $request->input('search'));

            $paths = $query->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $paths->appends(['search' => $request->input('search')]);
            }

            return view('admin.paths.trashed', compact('paths'));
        } catch (\Exception $e) {
            Log::error('Error loading trashed paths: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar las rutas eliminadas.');
        }
    }

    /**
     * Restore the specified path from trash.
     */
    public function privateRestore($id)
    {
        try {
            $path = Path::onlyTrashed()->findOrFail($id);
            $path->restore();
            return redirect()->route('admin.paths.trashed')
                           ->with('success', 'La ruta se ha restaurado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error restoring path: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Force delete the specified path from storage.
     */
    public function privateForceDelete($id)
    {
        try {
            $path = Path::onlyTrashed()->findOrFail($id);
            $path->forceDelete();
            return redirect()->route('admin.paths.trashed')
                           ->with('success', 'La ruta se ha eliminado permanentemente.');
        } catch (\Exception $e) {
            Log::error('Error force deleting path: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the educational progress for a path.
     */
    public function educaProgress($slug)
    {
        try {
            $path = Path::where('slug', $slug)
                       ->with(['user', 'parent', 'area'])
                       ->published()
                       ->firstOrFail();
            
            return view('educa.paths.progress', compact('path'));
        } catch (\Exception $e) {
            Log::error('Error showing path progress: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Apply search filters to the path query.
     */
    protected function applyPathFilters($query, $search = null)
    {
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Convierte la lista jerárquica en una lista plana para el select
     */
    protected function flattenPathList($paths, &$result = []): array
    {
        foreach ($paths as $path) {
            $result[$path['id']] = $path['full_name'];
            if (!empty($path['children'])) {
                $this->flattenPathList($path['children'], $result);
            }
        }
        return $result;
    }

    /**
     * Convierte la lista jerárquica de áreas en una lista plana para el select
     */
    protected function getAreasForSelect($areas, &$result = []): array
    {
        foreach ($areas as $area) {
            $result[$area['id']] = $area['full_name'];
            if (!empty($area['children'])) {
                $this->getAreasForSelect($area['children'], $result);
            }
        }
        return $result;
    }
}
