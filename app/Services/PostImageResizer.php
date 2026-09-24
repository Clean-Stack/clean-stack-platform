<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Image\Image;
use Illuminate\Support\Facades\Image as ImageFacade;

class PostImageResizer
{
    public const THUMBNAIL_WIDTH = 400;
    public const THUMBNAIL_HEIGHT = 210;

    public const MEDIUM_WIDTH = 800;
    public const MEDIUM_HEIGHT = 420;

    public const LARGE_WIDTH = 1200;
    public const LARGE_HEIGHT = 630;

    public const WEBP_QUALITY = 80;

    /**
     * Create all optimized responsive image variants.
     *
     * Each variant is processed independently from the original upload.
     *
     * @return array{
     *     thumbnail: Image,
     *     medium: Image,
     *     large: Image,
     * }
     */
    public function optimizedVariants(UploadedFile $file): array
    {
        return [
            'thumbnail' => $this->process(
                $file,
                self::THUMBNAIL_WIDTH,
                self::THUMBNAIL_HEIGHT,
            ),

            'medium' => $this->process(
                $file,
                self::MEDIUM_WIDTH,
                self::MEDIUM_HEIGHT,
            ),

            'large' => $this->process(
                $file,
                self::LARGE_WIDTH,
                self::LARGE_HEIGHT,
            ),
        ];
    }

    private function process(
        UploadedFile $file,
        int $width,
        int $height,
    ): Image {
        return ImageFacade::fromUpload($file)
            ->orient()
            ->cover($width, $height)
            ->toWebp()
            ->quality(self::WEBP_QUALITY);
    }
}
