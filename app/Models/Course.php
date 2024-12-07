<?php

namespace App\Models;

use App\Enums\AgeGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\GeneratesSlug;
use App\Traits\HasMediaTrait;

class Course extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasMediaTrait, GeneratesSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'featured',
        'age_group',
        'status',
        'author_id',
        'author_active',
        'author_deactivated_at',
        'author_permanently_deleted'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'author_active' => 'boolean',
        'author_deactivated_at' => 'datetime',
        'author_permanently_deleted' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Devuelve los Paths asociados a este Course.
     */
    public function paths(): BelongsToMany
    {
        return $this->belongsToMany(Path::class, 'course_path')->withPivot('sort');
    }

    /**
     * Devuelve las Medias asociadas a este Course.
     * @deprecated Use Spatie Media Library methods instead
     * PROBABLEMENTE HAY QUE ADAPTARLO O ELIMINARLO
     */
    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Devuelve los Contents asociados a este Course.
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_course');
    }

    /**
     * Devuelve el Author del Course.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Devuelve los Contents de primer nivel o los Contents hijos de un Content concreto.
     */
    public function getContentsByParentId(?int $parentId = null): Collection
    {
        $query = Content::query(); // Inicia una consulta nueva

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

    /**
     * Relación N:M entre Courses y Users
     */
    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot('role', 'progress', 'completed')
            ->withTimestamps();
    }

    /**
     * Get the author's status information.
     */
    public function getAuthorStatusAttribute(): array
    {
        return [
            'active' => $this->author_active,
            'deactivated_at' => $this->author_deactivated_at,
            'permanently_deleted' => $this->author_permanently_deleted,
        ];
    }

    /**
     * Register the cover media collection for this model
     */
    public function registerCoverMediaCollection(): void
    {
        // Este método vacío es suficiente para activar la colección 'cover' en HasMediaTrait
    }

    /**
     * Register the banner media collection for this model
     */
    public function registerBannerMediaCollection(): void
    {
        $this->addMediaCollection('banner')
            ->singleFile()
            ->useDisk('public')
            ->acceptsFile(function ($file) {
                return in_array(
                    $file->mimeType,
                    array_map(fn($ext) => 'image/' . $ext, ['jpg', 'jpeg', 'png', 'webp'])
                );
            })
            ->withResponsiveImages();
    }

    /**
     * Register the file media collection for this model
     */
    public function registerFileMediaCollection(): void
    {
        // Este método vacío es suficiente para activar la colección 'files' en HasMediaTrait
    }

    public function setAgeGroupAttribute(?string $value)
    {
        $this->attributes['age_group'] = AgeGroup::toDatabase($value);
    }

    public function getAgeGroupAttribute(?string $value): string
    {
        return AgeGroup::fromDatabase($value);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        parent::booted();

        // Cuando se elimine el curso, eliminar sus medios asociados
        static::deleting(function ($course) {
            $course->clearMediaCollection('cover');
            $course->clearMediaCollection('banner');
            $course->clearMediaCollection('files');
        });
    }
}
