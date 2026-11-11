<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * SyncMediaToCloudCommand
 *
 * One-click migration tool for senior enterprise media handling:
 * Transfers all local hotel media (photos/videos) from local storage to
 * Cloudflare R2 or Amazon AWS S3 with zero downtime.
 */
class SyncMediaToCloudCommand extends Command
{
    protected $signature = 'media:sync-to-cloud {--disk=r2 : Target cloud storage disk (r2 or s3)} {--dry-run : Test without actually uploading}';
    protected $description = 'Sync local hotel media files to Cloudflare R2 or AWS S3 Object Storage';

    public function handle(): int
    {
        $targetDisk = (string) $this->option('disk');
        $isDryRun = (bool) $this->option('dry-run');

        if (!in_array($targetDisk, ['r2', 's3'])) {
            $this->error("Invalid target disk '{$targetDisk}'. Supported: 'r2', 's3'");
            return self::FAILURE;
        }

        $sourcePath = storage_path('app/public/hotels');
        if (!File::isDirectory($sourcePath)) {
            $this->error("Local directory {$sourcePath} does not exist.");
            return self::FAILURE;
        }

        $files = File::allFiles($sourcePath);
        $totalFiles = count($files);

        $this->info("Found {$totalFiles} media files to sync to [{$targetDisk}] storage.");

        if ($isDryRun) {
            $this->warn("DRY RUN MODE: No files will be uploaded.");
            foreach (array_slice($files, 0, 5) as $f) {
                $rel = 'hotels/' . str_replace('\\', '/', $f->getRelativePathname());
                $this->line(" - Would upload: {$rel} (" . round($f->getSize() / 1024, 1) . " KB)");
            }
            return self::SUCCESS;
        }

        $cloud = Storage::disk($targetDisk);
        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        $uploaded = 0;
        $failed = 0;

        foreach ($files as $file) {
            $rel = 'hotels/' . str_replace('\\', '/', $file->getRelativePathname());
            try {
                $stream = fopen($file->getRealPath(), 'r+');
                $cloud->put($rel, $stream, 'public');
                if (is_resource($stream)) {
                    fclose($stream);
                }
                $uploaded++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed to upload {$rel}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Media Sync Completed: {$uploaded} uploaded, {$failed} failed.");

        return self::SUCCESS;
    }
}
