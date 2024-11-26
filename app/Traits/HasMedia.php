<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function attachMedia($uploadedFile, string $type = 'file'): ?Media
    {
        $service = $type === 'picture' 
            ? new ImageUploadService() 
            : new FileUploadService();

        $result = $service->upload($uploadedFile);

        if (!$result) {
            return null;
        }

        return $this->medias()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'url' => $result['url'],
            'path' => $result['path'],
            'extra' => [
                'original_name' => $result['original_name'],
                'size' => $result['size'],
                'mime_type' => $result['mime_type'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
            ],
        ]);
    }
}