<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FixArchives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-archives';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Base64 archives to file paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting conversion...');

        $archives = Archive::all();
        $this->info("Found " . $archives->count() . " archives.");

        foreach ($archives as $archive) {
            if (Str::startsWith($archive->file_path, 'data:')) {
                $this->info("Converting archive ID: " . $archive->id . "...");

                try {
                    // Extract base64 data
                    $data = explode(',', $archive->file_path);
                    if (count($data) < 2) {
                        $this->warn("Invalid base64 data for ID " . $archive->id);
                        continue;
                    }

                    $meta = $data[0]; // data:application/pdf;base64
                    $content = base64_decode($data[1]);

                    // Determine extension
                    $extension = 'bin';
                    if (str_contains($meta, 'application/pdf'))
                        $extension = 'pdf';
                    elseif (str_contains($meta, 'image/jpeg'))
                        $extension = 'jpg';
                    elseif (str_contains($meta, 'image/png'))
                        $extension = 'png';
                    elseif (str_contains($meta, 'image/gif'))
                        $extension = 'gif';
                    elseif (str_contains($meta, 'wordprocessingml'))
                        $extension = 'docx';
                    elseif (str_contains($meta, 'spreadsheetml'))
                        $extension = 'xlsx';

                    // Generate filename
                    $filename = 'archives/' . time() . '_' . $archive->id . '.' . $extension;

                    // Store file
                    Storage::disk('public')->put($filename, $content);

                    // Update record
                    $archive->file_path = $filename;
                    $archive->save();

                    $this->info("Done. Saved to $filename");
                } catch (\Exception $e) {
                    $this->error("Failed: " . $e->getMessage());
                }
            } else {
                $this->line("Archive ID " . $archive->id . " is already a file or invalid format. Skipping.");
            }
        }

        $this->info("Conversion complete.");
    }
}
