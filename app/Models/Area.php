<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Spatie\MediaLibrary\HasMedia;
use App\Traits\HasMediaTrait;
use App\Traits\GeneratesSlug;

class Area extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasMediaTrait, GeneratesSlug;

    /**
     * Register the cover media collection for this model
     */
    public function registerCoverMediaCollection(): void
    {
        // Este método vacío es suficiente para activar la colección 'cover' en HasMediaTrait
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        parent::booted();

        // Cuando se elimine el área, eliminar sus medios asociados
        static::deleting(function ($area) {
            $area->clearMediaCollection('cover');
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',
        'parent_id',
        'featured',
        'status',
        'sort_order',
        'meta'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'meta' => 'json'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the user that created the area.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent area.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }

    /**
     * Get the child areas.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Area::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get child areas ordered alphabetically
     */
    public function childrenAlphabetically(): HasMany
    {
        return $this->hasMany(Area::class, 'parent_id')
            ->orderBy(DB::raw('LOWER(name)'));
    }

    /**
     * Get the paths associated with this area.
     */
    public function paths(): HasMany
    {
        return $this->hasMany(Path::class);
    }

    /**
     * Scope a query to only include published areas.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured areas.
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('featured', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order');
    }

    /**
     * Get the full hierarchical path of the area.
     */
    public function getFullPathAttribute(): string
    {
        if (!$this->relationLoaded('parent')) {
            $this->load('parent');
        }

        $path = collect([$this->name]);
        $area = $this;

        while ($area->parent) {
            $area = $area->parent;
            $path->prepend($area->name);
        }

        return $path->join(' > ');
    }

    /**
     * Para obtener las Areas hijas de un Area dada por el Id.
     * Si no se pasa un Id devolverá las Areas de primer nivel.
     */
    public function getAreasByParentId(?int $parentId = null): Collection
    {
        if ($parentId === null) {
            return Area::whereNull('parent_id')->get();
        } else {
            return Area::where('parent_id', $parentId)->get();
        }
    }

    /**
     * Obtiene una lista jerárquica de áreas para mostrar en un select
     * @return \Illuminate\Support\Collection
     */
    public static function getHierarchicalList(): \Illuminate\Support\Collection
    {
        // Obtener todas las áreas ordenadas por nombre
        $query = static::whereNull('parent_id')
            ->orderBy(DB::raw('LOWER(name)'));
            
        // Debug de la consulta SQL
        \Log::info('SQL Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        $areas = $query->with(['children' => function ($query) {
                $query->orderBy(DB::raw('LOWER(name)'));
            }])
            ->get();

        // Debug de todas las áreas
        \Log::info('Todas las áreas:', Area::pluck('name')->toArray());
        \Log::info('Areas de primer nivel:', $areas->pluck('name')->toArray());
            
        return $areas->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'depth' => 0,
                    'full_name' => $area->name,
                    'children' => $area->getChildrenHierarchy(1)
                ];
            });
    }

    /**
     * Función auxiliar recursiva para construir la jerarquía
     * @param int $depth
     * @return \Illuminate\Support\Collection
     */
    protected function getChildrenHierarchy(int $depth): \Illuminate\Support\Collection
    {
        return $this->childrenAlphabetically()
            ->get()
            ->map(function ($child) use ($depth) {
                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'depth' => $depth,
                    'full_name' => $prefix . $child->name,
                    'children' => $child->getChildrenHierarchy($depth + 1)
                ];
            });
    }

    /**
     * Get the area's cover URL.
     */
    public function getCoverUrlAttribute()
    {
        return $this->getFirstMediaUrl('cover');
    }

    /**
     * Get the area's cover thumbnail URL.
     */
    public function getCoverThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('cover', 'thumb');
    }
}
