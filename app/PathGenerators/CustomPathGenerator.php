<?php

namespace App\PathGenerators;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        if (is_object($media->model)) {
            $modelClass = get_class($media->model);
            $modelPath = Str::slug(str_replace('\\', '/', Str::after($modelClass, 'App\\Models\\')));
        } elseif (!empty($media->model_type)) {
            // Fallback jika model object sudah hilang, gunakan model_type string
            $modelPath = Str::slug(str_replace('\\', '/', Str::after($media->model_type, 'App\\Models\\')));
        } else {
            $modelPath = 'unknown-model';
        }

        return $modelPath . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}
