<?php

namespace App\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMediaTrait
{
    use InteractsWithMedia;

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->useDisk('public');

        // Colección específica para avatares
        if (method_exists($this, 'registerAvatarMediaCollection')) {
            $this->addMediaCollection('avatar')
                ->singleFile()
                ->useDisk('public');
        }

        // Colección específica para covers
        if (method_exists($this, 'registerCoverMediaCollection')) {
            $this->addMediaCollection('cover')
                ->singleFile()
                ->useDisk('public')
                ->acceptsFile(function ($file) {
                    return in_array(
                        $file->mimeType, 
                        array_map(fn($ext) => 'image/' . $ext, config('media.cover.allowed_types'))
                    );
                })
                ->withResponsiveImages();
        }

        // Colección específica para archivos
        if (method_exists($this, 'registerFileMediaCollection')) {
            $this->addMediaCollection('files')
                ->useDisk('public');
        }
    }

    /**
     * Register media conversions for the model.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $conversions = config('media.conversions');

        $this->addMediaConversion('thumb')
            ->width($conversions['thumb']['width'])
            ->height($conversions['thumb']['height'])
            ->nonQueued()
            ->keepOriginalImageFormat()
            ->performOnCollections('default', 'avatar');

        $this->addMediaConversion('medium')
            ->width($conversions['medium']['width'])
            ->height($conversions['medium']['height'])
            ->nonQueued()
            ->keepOriginalImageFormat()
            ->performOnCollections('default', 'avatar');

        $this->addMediaConversion('large')
            ->width($conversions['large']['width'])
            ->height($conversions['large']['height'])
            ->nonQueued()
            ->keepOriginalImageFormat()
            ->performOnCollections('default', 'avatar');
    }

    /**
     * Get the user who uploaded the media.
     */
    public function getMediaUser()
    {
        return $this->media()->first()?->custom_properties['user_id'] ?? null;
    }

    /**
     * Set the user who uploaded the media.
     */
    public function setMediaUser($userId)
    {
        $this->media()->update([
            'custom_properties->user_id' => $userId
        ]);
    }
}
