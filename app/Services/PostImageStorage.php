<?php

namespace App\Services;

use Illuminate\Image\Image;

interface PostImageStorage
{
    public function store(
        Image $image,
        string $directory,
        string $filename,
    ): string|false;

    public function delete(string|array $paths): bool;

    public function exists(string $path): bool;

    public function url(string $path): string;

    public function files(string $directory): array;

    public function directories(string $directory): array;

    public function deleteDirectory(string $directory): bool;
}
