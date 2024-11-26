<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile' => 'json',
    ];

    /**
     * Register avatar media collection for the model.
     */
    public function registerAvatarMediaCollection(): void
    {
        // Este método vacío activa la colección de avatar en HasMediaTrait
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar');
    }

    /**
     * Get the user's avatar thumbnail URL.
     */
    public function getAvatarThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar', 'thumb');
    }

    /**
     * Get the areas created by the user.
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Devuelve los Courses de este User y los datos relacionados:
     * role, progress, completed
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot('role', 'progress', 'completed')
                    ->withTimestamps();
    }

    /**
     * Devuelve los Paths de este User y los datos relacionados:
     * role, progress, completed
     */
    public function enrolledPaths()
    {
        return $this->belongsToMany(Path::class, 'path_enrollments')
                    ->withPivot('role', 'progress', 'completed')
                    ->withTimestamps();
    }

}
