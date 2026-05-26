<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class PhotoStorageService
{
    public function storeSubmissionPhoto(UploadedFile $file, string $prefix): string
    {
        $name = $prefix.'-'.now()->format('YmdHis').'-'.Str::uuid().'.jpg';
        $path = 'submissions/'.$name;

        $image = Image::read($file->getRealPath())
            ->scaleDown(width: 1600, height: 1600)
            ->toJpeg(quality: 78);

        Storage::disk('public')->put($path, (string) $image);

        return $path;
    }

    public function exif(UploadedFile $file): array
    {
        if (! function_exists('exif_read_data')) {
            return [];
        }

        return rescue(fn () => exif_read_data($file->getRealPath()) ?: [], [], report: false);
    }
}
