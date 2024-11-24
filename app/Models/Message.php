<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'body',
        'user_id',
        'private',
        'user_to_id',
        'area_id',
        'path_id',
        'course_id',
        'content_id'
    ];

    protected $casts = [
        'private' => 'boolean',
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
     * Devolverá el User que envió el Message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devuelve el User al que se envió el Message, si existe.
     */
    public function userTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_to_id');
    }

    /**
     * Obtiene el Area asociada al Message, si existe.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Obtiene el Path asociado al Message, si existe.
     */
    public function path(): BelongsTo
    {
        return $this->belongsTo(Path::class);
    }

    /**
     * Devuelve el Course asociado al Message, si existe.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Obtiene el content asociado al Message si existe.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }


}
