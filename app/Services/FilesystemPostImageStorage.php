<?php

namespace App\Services;

use Illuminate\Image\Image;
use Illuminate\Support\Facades\Storage;

class FilesystemPostImageStorage implements PostImageStorage
{
    public function __construct(
        private readonly string $disk = 'public',
    ) {
    }

    public function store(
        Image $image,
        string $directory,
        string $filename,
    ): string|false {
        return $image->storePubliclyAs(
            path: $directory,
            name: $filename,
            disk: $this->disk,
        );
    }

    public function delete(string|array $paths): bool
    {
        return Storage::disk($this->disk)->delete($paths);
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    public function url(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    public function files(string $directory): array
    {
        return Storage::disk($this->disk)->files($directory);
    }

    public function directories(string $directory): array
    {
        return Storage::disk($this->disk)->directories($directory);
    }

    public function deleteDirectory(string $directory): bool
    {
        return Storage::disk($this->disk)->deleteDirectory($directory);
    }
}
