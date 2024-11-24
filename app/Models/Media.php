<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    // me está dando errores identificando la tabla medias, no sé porqué
    // lo intenta con media, así que fuerzo el nombre a medias
    protected $table = 'medias';

    protected $fillable = [
        'user_id',
        'mediable_id',
        'mediable_type',
        'type',
        'url',
        'path',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Obtiene el User que subió el Media.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el modelo al que pertenece el Media.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

}
