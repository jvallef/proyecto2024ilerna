<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['index', 'show']);
    }

    /**
     * Display a listing of areas.
     */
    public function index()
    {
        $areas = Area::with(['user', 'parent'])
            ->published()
            ->ordered()
            ->paginate(12);

        $featuredAreas = Area::with(['user', 'parent'])
            ->featured()
            ->published()
            ->ordered()
            ->take(6)
            ->get();

        return view('areas.index', compact('areas', 'featuredAreas'));
    }

    /**
     * Show the form for creating a new area.
     */
    public function create()
    {
        $parentAreas = Area::where('parent_id', null)->pluck('name', 'id');
        return view('admin.areas.create', compact('parentAreas'));
    }

    /**
     * Store a newly created area.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:areas,id'],
            'featured' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'sort_order' => ['nullable', 'integer'],
            'meta' => ['nullable', 'array'],
            'cover' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        $area = Area::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
            'featured' => $validated['featured'] ?? false,
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'meta' => $validated['meta'] ?? null,
            'user_id' => auth()->id(),
        ]);

        if ($request->hasFile('cover')) {
            $area->medias()->create([
                'file' => $request->file('cover')->store('areas', 'public'),
                'type' => 'picture'
            ]);
        }

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area created successfully.');
    }

    /**
     * Display the specified area.
     */
    public function show(Area $area)
    {
        if (!$area->status === 'published' && !auth()->user()?->hasRole('admin')) {
            abort(404);
        }

        $area->load(['user', 'parent', 'children' => function ($query) {
            $query->published()->ordered();
        }, 'paths']);

        return view('areas.show', compact('area'));
    }

    /**
     * Show the form for editing the specified area.
     */
    public function edit(Area $area)
    {
        $parentAreas = Area::where('id', '!=', $area->id)
            ->where('parent_id', null)
            ->pluck('name', 'id');
            
        return view('admin.areas.edit', compact('area', 'parentAreas'));
    }

    /**
     * Update the specified area.
     */
    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'parent_id' => [
                'nullable',
                'exists:areas,id',
                Rule::notIn([$area->id]), // Prevenir auto-referencia
            ],
            'featured' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'sort_order' => ['nullable', 'integer'],
            'meta' => ['nullable', 'array'],
            'cover' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        $area->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
            'featured' => $validated['featured'] ?? false,
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'] ?? $area->sort_order,
            'meta' => $validated['meta'] ?? $area->meta,
        ]);

        if ($request->hasFile('cover')) {
            // Eliminar cover anterior si existe
            $area->medias()->where('type', 'picture')->delete();
            
            $area->medias()->create([
                'file' => $request->file('cover')->store('areas', 'public'),
                'type' => 'picture'
            ]);
        }

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area updated successfully.');
    }

    /**
     * Remove the specified area.
     */
    public function destroy(Area $area)
    {
        // Verificar si tiene áreas hijas
        if ($area->children()->exists()) {
            return back()->with('error', 'Cannot delete area with child areas.');
        }

        // Verificar si tiene paths asociados
        if ($area->paths()->exists()) {
            return back()->with('error', 'Cannot delete area with associated paths.');
        }

        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area deleted successfully.');
    }

    /**
     * Reorder areas.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'areas' => ['required', 'array'],
            'areas.*.id' => ['required', 'exists:areas,id'],
            'areas.*.sort_order' => ['required', 'integer'],
        ]);

        foreach ($validated['areas'] as $areaData) {
            Area::where('id', $areaData['id'])->update([
                'sort_order' => $areaData['sort_order']
            ]);
        }

        return response()->json(['message' => 'Areas reordered successfully.']);
    }
}
