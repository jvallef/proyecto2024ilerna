<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\GeneratesSlug;

class Area extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, GeneratesSlug;

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
     * Devuelve las Medias asociadas a este Area.
     * @deprecated Use Spatie Media Library methods instead
     * PROBABLEMENTE HAY QUE ADAPTARLO O ELIMINARLO
     */
    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Para obteenr el User de esta Area.
     */
    public function cover()
    {
        return $this->medias()->where('type', 'picture')->orderBy('created_at', 'desc')->first();
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
}
