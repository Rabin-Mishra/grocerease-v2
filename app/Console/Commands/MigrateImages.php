<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grocerease:migrate-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy product images to the default filesystem disk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting legacy product images migration...');

        // Path to the legacy image directory
        $legacyDir = base_path('../GrocerEase-Website/admin_area/product_images/');

        $legacyDirExists = is_dir($legacyDir);
        if (!$legacyDirExists) {
            $this->warn("Legacy images directory not found at: {$legacyDir}. Only already-uploaded target files will be linked.");
        }

        $images = ProductImage::all();
        $migratedCount = 0;
        $skippedCount = 0;
        $disk = Storage::disk(config('filesystems.default'));

        foreach ($images as $img) {
            $path = $img->image_path;

            // Check if we should migrate:
            // Either it has no slash, or it starts with "legacy_images/"
            if (!Str::contains($path, '/') || Str::startsWith($path, 'legacy_images/')) {
                $filename = basename($path);
                $sourceFile = $legacyDir . $filename;
                $targetPath = "products/migrated/{$filename}";

                // 1. If target file already exists in our storage, we just update the DB
                if ($disk->exists($targetPath)) {
                    $img->update([
                        'image_path' => $targetPath
                    ]);
                    $this->line("Found in target storage: {$filename} -> updated database path.");
                    $migratedCount++;
                }
                // 2. If target file doesn't exist but the legacy source file is available, migrate it
                elseif (file_exists($sourceFile)) {
                    // Put content to default storage disk
                    $disk->put($targetPath, file_get_contents($sourceFile));

                    // Update database path
                    $img->update([
                        'image_path' => $targetPath
                    ]);

                    $this->line("Migrated from legacy source: {$filename} -> {$targetPath}");
                    $migratedCount++;
                } else {
                    $this->warn("Source file not found for: {$filename}");
                    $skippedCount++;
                }
            } else {
                $this->line("Skipped (already migrated or custom path): {$path}");
                $skippedCount++;
            }
        }

        $this->info("Migration completed!");
        $this->info("Successfully Migrated: {$migratedCount} images");
        $this->info("Skipped/Not Found: {$skippedCount} images");

        return Command::SUCCESS;
    }
}
