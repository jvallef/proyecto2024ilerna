<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Spatie\MediaLibrary\HasMedia;
use App\Traits\HasMediaTrait;
use App\Traits\GeneratesSlug;

class Path extends Model implements HasMedia
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

        // Cuando se elimine el path, eliminar sus medios asociados
        static::deleting(function ($path) {
            $path->clearMediaCollection('cover');
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',
        'parent_id',
        'area_id',
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
     * Obtiene el usuario asociado a este path.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devuelve los Courses asociados a este Path.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_path');
    }

    /**
     * Devuelve el Parent de este Path.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Path::class, 'parent_id');
    }

    /**
     * Devuelve el Area de este Path.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Devuelve los Paths hijos de este Path.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Path::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get child paths ordered alphabetically
     */
    public function childrenAlphabetically(): HasMany
    {
        return $this->hasMany(Path::class, 'parent_id')
            ->orderBy(DB::raw('LOWER(name)'));
    }

    /**
     * Para obtener los Paths hijas de un Path dado por el Id.
     * Si no se pasa un Id devolverá los Paths de primer nivel.
     */
    public function getPathsByParentId(?int $parentId = null): Collection
    {
        $query = Path::query();

        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * Para obtener una coleccion de los cursos de este path.
     */
    public function getCourses(): Collection
    {
        return $this->courses()->get();
    }

    /**
     * Relación N:M entre Paths y Users
     */
    public function enrolledUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'path_enrollments')
                    ->withPivot('role', 'progress', 'completed')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include published paths.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured paths.
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
     * Get the full hierarchical path.
     */
    public function getFullPathAttribute(): string
    {
        if (!$this->relationLoaded('parent')) {
            $this->load('parent');
        }

        $path = collect([$this->name]);
        $currentPath = $this;

        while ($currentPath->parent) {
            $currentPath = $currentPath->parent;
            $path->prepend($currentPath->name);
        }

        return $path->join(' > ');
    }

    /**
     * Get the path's cover URL.
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover');
    }

    /**
     * Get the path's cover thumbnail URL.
     */
    public function getCoverThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'thumb');
    }
}
