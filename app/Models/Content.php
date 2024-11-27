<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'content',
        'author_id',
        'parent_id'
    ];

    protected $casts = [
        'content' => 'array', //se convierte en array
    ];

    protected $dates = ['deleted_at'];

    /**
     * Devuelve los archivos multimedia asociados a este curso.
     */
    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Devuelve el author del content
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Devuelve el parent del content si existe.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'parent_id');
    }

    /**
     * Obtiene los contents hijos de este content.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Content::class, 'parent_id');
    }


    /**
     * Devuelve los Courses asociados a este content.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'content_course');
    }

    /**
     * Devuelve los Contents de primer nivel o los Contents hijos de un Content concreto.
     */
    public function getContentsByParentId(?int $parentId = null): Collection
    {
        $query = Content::query();

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

}