<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PostImageManager
{
    public function __construct(
        private readonly PostImageResizer $resizer,
        private readonly PostImageStorage $storage,
    ) {
    }

    /**
     * Store all optimized image variants.
     *
     * @return array{
     *     large: string,
     *     medium: string,
     *     thumbnail: string,
     * }
     */
    public function storeVariants(UploadedFile $file): array
    {
        $directory = 'posts/' . Str::uuid();

        $variants = $this->resizer->optimizedVariants($file);

        $paths = [];

        try {
            foreach ($variants as $variant => $image) {
                $path = $this->storage->store(
                    image: $image,
                    directory: $directory,
                    filename: "{$variant}.webp",
                );

                if ($path === false) {
                    throw new RuntimeException(
                        "Unable to store {$variant} featured image."
                    );
                }

                $paths[$variant] = $path;
            }

            return $paths;
        } catch (Throwable $e) {
            if ($paths !== []) {
                $this->storage->delete(array_values($paths));
            }

            $this->deleteEmptyDirectory($directory);

            throw $e;
        }
    }

    /**
     * Replace an existing post image safely.
     */
    public function replaceVariants(
        Post $post,
        UploadedFile $file,
    ): void {
        /*
         * Keep the current image paths untouched until the
         * new image set has been successfully stored.
         */
        $oldPaths = array_values(array_filter([
            $post->featured_image,
            $post->featured_image_medium,
            $post->featured_image_thumbnail,
        ]));

        /*
         * Create the complete new image set first.
         */
        $newPaths = $this->storeVariants($file);

        try {
            /*
             * Update all image references atomically.
             *
             * Filesystem writes are intentionally outside the
             * database transaction because they cannot be rolled back
             * by the database.
             */
            DB::transaction(function () use ($post, $newPaths): void {
                $post->update([
                    'featured_image' => $newPaths['large'],
                    'featured_image_medium' => $newPaths['medium'],
                    'featured_image_thumbnail' => $newPaths['thumbnail'],
                ]);
            });
        } catch (Throwable $e) {
            /*
             * The database still points to the old image set,
             * so the newly-created files are now orphaned and
             * must be removed.
             */
            $this->storage->delete(
                array_values($newPaths)
            );

            $this->deleteEmptyDirectoriesForPaths(
                array_values($newPaths)
            );

            throw $e;
        }

        /*
         * The database now points to the new image set.
         * The old image set is no longer referenced.
         */
        if ($oldPaths !== []) {
            $this->storage->delete($oldPaths);

            $this->deleteEmptyDirectoriesForPaths($oldPaths);
        }
    }

    /**
     * Find post image directories that are no longer referenced by any post.
     *
     * Soft-deleted posts are included because their images must remain
     * available until the post is permanently deleted.
     *
     * @return array<int, string>
     */
    public function orphanDirectories(): array
    {
        $referencedDirectories = Post::withTrashed()
            ->get([
                'featured_image',
                'featured_image_medium',
                'featured_image_thumbnail',
            ])
            ->flatMap(function (Post $post): array {
                return array_filter([
                    $post->featured_image
                        ? dirname($post->featured_image)
                        : null,

                    $post->featured_image_medium
                        ? dirname($post->featured_image_medium)
                        : null,

                    $post->featured_image_thumbnail
                        ? dirname($post->featured_image_thumbnail)
                        : null,
                ]);
            })
            ->unique()
            ->values()
            ->all();

        return collect(
            $this->storage->directories('posts')
        )
            ->reject(
                fn (string $directory): bool =>
                in_array($directory, $referencedDirectories, true)
            )
            ->values()
            ->all();
    }

    /**
     * Delete post image directories that are no longer referenced.
     *
     * @return int Number of successfully deleted directories.
     */
    public function cleanupOrphans(array $directories): int
    {
        $deleted = 0;

        foreach ($directories as $directory) {
            if ($this->storage->deleteDirectory($directory)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Delete all stored image variants belonging to a post.
     */
    public function deleteVariants(Post $post): bool
    {
        $paths = array_values(array_filter([
            $post->featured_image,
            $post->featured_image_medium,
            $post->featured_image_thumbnail,
        ]));

        if ($paths === []) {
            return true;
        }

        $deleted = $this->storage->delete($paths);

        $this->deleteEmptyDirectoriesForPaths($paths);

        return $deleted;
    }

    /**
     * Delete a set of newly stored image variants.
     *
     * @param array<string, string> $paths
     */
    public function deleteStoredVariants(array $paths): bool
    {
        if ($paths === []) {
            return true;
        }

        $paths = array_values($paths);

        $deleted = $this->storage->delete($paths);

        $this->deleteEmptyDirectoriesForPaths($paths);

        return $deleted;
    }

    /**
     * Delete a directory only when it is completely empty.
     */
    private function deleteEmptyDirectory(string $directory): void
    {
        if (
            $this->storage->files($directory) === []
            && $this->storage->directories($directory) === []
        ) {
            $this->storage->deleteDirectory($directory);
        }
    }

    /**
     * Delete every empty directory represented by the given file paths.
     *
     * @param array<int, string> $paths
     */
    private function deleteEmptyDirectoriesForPaths(array $paths): void
    {
        $directories = collect($paths)
            ->map(fn (string $path): string => dirname($path))
            ->filter(fn (string $directory): bool => $directory !== '.')
            ->unique()
            ->values();

        foreach ($directories as $directory) {
            $this->deleteEmptyDirectory($directory);
        }
    }
}
