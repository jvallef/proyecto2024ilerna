<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\MorphMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\GeneratesSlug;

class Path extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, GeneratesSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',
        'parent_id',
        'area_id',
        'featured',
        'status'
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Devuelve las Medias asociadas a este Path.
     * @deprecated Use Spatie Media Library methods instead
     */
    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

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
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Devuelve los Paths hijos de este Path.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Path::class, 'parent_id');
    }

    /**
     * Para obtener los Paths hijas de un Path dado por el Id.
     * Si no se pasa un Id devolverá los Paths de primer nivel.
     */
    public function getPathsByParentId(?int $parentId = null): Collection
    {
        if ($parentId === null) {
            return Path::whereNull('parent_id')->get();
        } else {
            return Path::where('parent_id', $parentId)->get();
        }
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
    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'path_enrollments')
                    ->withPivot('role', 'progress', 'completed')
                    ->withTimestamps();
    }

}
