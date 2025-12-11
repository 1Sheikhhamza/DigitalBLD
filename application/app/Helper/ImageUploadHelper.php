<?php

namespace App\Helper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;

class ImageUploadHelper
{
    public static function upload(
        UploadedFile $file,
        string $folder = 'uploads',
        ?string $prefix = 'img',
        ?int $width = null,
        ?int $height = null,
        ?string $oldFile = null
    ): string {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = ($prefix ?? 'img') . '_' . time() . '.' . $extension;
        $directoryPath = public_path($folder);

        // Create directory if not exists
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $fullPath = $directoryPath . '/' . $filename;

        // Delete old image if exists
        if ($oldFile && file_exists($directoryPath . '/' . $oldFile)) {
            File::delete($directoryPath . '/' . $oldFile);
        }

        // Upload logic
        if ($extension === 'svg') {
            $file->move($directoryPath, $filename);
        } else {
            $image = Image::make($file);
            if ($width && $height) {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            $image->save($fullPath);
        }

        return $filename;
    }
}
