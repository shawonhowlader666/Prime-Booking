<?php

declare(strict_types=1);

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * MediaService — Enterprise Media Architecture
 *
 * Designed for High-Scale (1M+ properties):
 *  1. Decoupled Storage: Supports Local Public, Cloudflare R2, AWS S3, or CDN Edge.
 *  2. CDN Edge Resolution: Resolves paths dynamically through Global CDN with $0 app overhead.
 *  3. Next-Gen WebP Conversion: Automatically converts images to WebP to reduce payload by ~80%.
 *  4. Zero Server Lock-in: Changing storage provider requires only changing .env variables.
 */
class MediaService
{
    /**
     * Get the active media storage disk.
     */
    public static function disk(): string
    {
        return (string) config('filesystems.media_disk', 'public');
    }

    /**
     * Resolve any media path into a fully qualified, CDN-optimized public URL.
     *
     * @param string|null $path Relative path, local storage path, or absolute URL
     * @param string|null $fallback Default image URL if path is missing
     * @return string Fully qualified URL
     */
    public static function url(?string $path, ?string $fallback = null): string
    {
        if (empty($path)) {
            return $fallback ?? '/images/placeholder-hotel.svg';
        }

        $trimmed = trim($path);

        // Already an absolute external URL (e.g., Unsplash, Cloudflare CDN, S3, Youtube)
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        // Clean any leading slashes or redundant 'storage/' prefixes
        $cleanRelative = ltrim($trimmed, '/');
        $rawPathWithoutStorage = str_starts_with($cleanRelative, 'storage/')
            ? substr($cleanRelative, 8)
            : $cleanRelative;

        // 1. Check if a dedicated Media CDN URL is configured (Cloudflare CDN / CloudFront)
        $cdnUrl = config('filesystems.media_cdn_url');
        if (!empty($cdnUrl)) {
            return rtrim((string)$cdnUrl, '/') . '/' . ltrim($rawPathWithoutStorage, '/');
        }

        // 2. Check if active disk is Cloud (R2 / S3)
        $diskName = self::disk();
        if ($diskName === 'r2' || $diskName === 's3') {
            try {
                return Storage::disk($diskName)->url($rawPathWithoutStorage);
            } catch (\Throwable) {
                // fallback to local asset if cloud URL generation fails
            }
        }

        // 3. Fallback to local Laravel public storage
        return asset('storage/' . ltrim($rawPathWithoutStorage, '/'));
    }

    /**
     * Upload and optimize an image file to the active media storage disk.
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @param array{maxWidth?: int, quality?: int, convertWebp?: bool} $options
     * @return string Relative path stored in database
     */
    public static function uploadImage(UploadedFile|string $file, string $folder = 'hotels', array $options = []): string
    {
        $disk = Storage::disk(self::disk());
        $folder = trim($folder, '/');
        $filename = Str::uuid()->toString();
        $quality = $options['quality'] ?? 85;
        $maxWidth = $options['maxWidth'] ?? 1920;

        // If it's a binary file or UploadedFile
        if ($file instanceof UploadedFile) {
            // extension() is derived from the detected MIME type. The client's file name is not
            // trusted: "photo.php" with image bytes inside must never be stored as a .php file.
            $ext = strtolower((string) $file->extension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'], true)) {
                throw new \InvalidArgumentException('Unsupported image type.');
            }
            $realPath = $file->getRealPath();

            // Auto-convert to WebP if GD is supported and it is JPEG/PNG
            if (($options['convertWebp'] ?? true) && function_exists('imagewebp') && in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $targetFile = "{$folder}/{$filename}.webp";
                $img = self::createGdImage($realPath, $ext);

                if ($img) {
                    $img = self::resizeGdImage($img, $maxWidth);
                    ob_start();
                    imagewebp($img, null, $quality);
                    $contents = ob_get_clean();
                    imagedestroy($img);

                    $disk->put($targetFile, $contents, 'public');
                    return "/storage/{$targetFile}";
                }
            }

            // Normal upload without conversion
            $targetFile = "{$folder}/{$filename}.{$ext}";
            $disk->putFileAs($folder, $file, "{$filename}.{$ext}", 'public');
            return "/storage/{$targetFile}";
        }

        // Fallback for path string
        return (string) $file;
    }

    /**
     * Upload a video file to the active storage disk.
     */
    public static function uploadVideo(UploadedFile $file, string $folder = 'hotels/videos'): string
    {
        $disk = Storage::disk(self::disk());
        $folder = trim($folder, '/');
        $ext = strtolower((string) $file->extension());
        if (!in_array($ext, ['mp4', 'webm', 'mov', 'qt'], true)) {
            throw new \InvalidArgumentException('Unsupported video type.');
        }
        $filename = Str::uuid()->toString() . '.' . ($ext === 'qt' ? 'mov' : $ext);

        $disk->putFileAs($folder, $file, $filename, 'public');
        return "/storage/{$folder}/{$filename}";
    }

    /**
     * Delete media from the active storage disk.
     */
    public static function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $cleanRelative = ltrim(trim($path), '/');
        $rawPathWithoutStorage = str_starts_with($cleanRelative, 'storage/')
            ? substr($cleanRelative, 8)
            : $cleanRelative;

        return Storage::disk(self::disk())->delete($rawPathWithoutStorage);
    }

    /**
     * Create GD resource from uploaded image.
     */
    private static function createGdImage(string $path, string $ext)
    {
        return match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    /**
     * Proportional downscale to max width while preserving aspect ratio.
     */
    private static function resizeGdImage($src, int $maxWidth)
    {
        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= $maxWidth) {
            return $src;
        }

        $ratio = $maxWidth / $w;
        $newW = $maxWidth;
        $newH = (int) round($h * $ratio);

        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($src);

        return $dst;
    }
}
