<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class PublicImage
{
    public static function url(?string $image): ?string
    {
        if ($image === null || $image === '') {
            return null;
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        return asset('uploads/' . ltrim(str_replace('\\', '/', $image), '/'));
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        $directory = trim(str_replace('\\', '/', $directory), '/');
        $destination = public_path('uploads/' . $directory);

        if (!is_dir($destination) && !mkdir($destination, 0755, true) && !is_dir($destination)) {
            throw new RuntimeException('Unable to create the public upload directory.');
        }

        $filename = $file->hashName();
        $file->move($destination, $filename);

        return $directory . '/' . $filename;
    }

    public static function delete(?string $image): void
    {
        if ($image === null || $image === '' || filter_var($image, FILTER_VALIDATE_URL)) {
            return;
        }

        $uploads = realpath(public_path('uploads'));
        if ($uploads === false) {
            return;
        }

        $path = realpath($uploads . DIRECTORY_SEPARATOR . str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            ltrim(str_replace('\\', '/', $image), '/')
        ));

        if ($path === false || !str_starts_with($path, $uploads . DIRECTORY_SEPARATOR) || !is_file($path)) {
            return;
        }

        if (!unlink($path)) {
            throw new RuntimeException('Unable to delete the public image file.');
        }
    }
}
