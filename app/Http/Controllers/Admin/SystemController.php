<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class SystemController extends Controller
{
    public function index()
    {
        $hasStorageLink = file_exists(public_path('storage'));
        
        return view('admin.system.index', compact('hasStorageLink'));
    }

    public function storageLink()
    {
        try {
            Artisan::call('storage:link');
            return back()->with('success', 'Symlink storage berhasil dibuat! Gambar seharusnya sudah muncul.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat symlink: ' . $e->getMessage());
        }
    }

    public function updateApp()
    {
        try {
            // Set longer timeout for update process
            set_time_limit(300);

            $output = [];
            $output[] = "--- Starting Update Process ---";

            // 1. Git Pull
            $gitOutput = [];
            
            // Construct Command with Auth if Token exists
            $username = env('GITHUB_USERNAME');
            $token = env('GITHUB_TOKEN');
            $repo = env('GITHUB_REPO'); // example: "username/repo"

            if ($token && $repo) {
                // Use Basic Auth URL
                $repoUrl = "https://{$token}@github.com/{$repo}.git";
                if ($username) {
                    $repoUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
                }
                
                $command = "git pull {$repoUrl} main 2>&1";
                
                // For log safety, we will replace the command in output 
                $safeCommand = "git pull https://***:***@github.com/{$repo}.git main";
                $output[] = "Command: " . $safeCommand;

            } else {
                // Default to origin
                $command = "git pull origin main 2>&1";
                $output[] = "Command: git pull origin main";
            }

            exec($command, $gitOutput, $returnVar);
            
            // Mask token in output just in case
            if ($token) {
                foreach ($gitOutput as &$line) {
                    $line = str_replace($token, '*****', $line);
                }
            }
            
            $output = array_merge($output, $gitOutput);

            if ($returnVar !== 0) {
                 // Try fetching first if pull fails
                 if ($token && $repo) {
                     $repoUrl = "https://{$token}@github.com/{$repo}.git";
                     if ($username) $repoUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
                     exec("git fetch {$repoUrl} 2>&1", $fetchOutput);
                     exec("git reset --hard origin/main 2>&1", $resetOutput);
                 } else {
                     exec('git fetch --all 2>&1');
                     exec('git reset --hard origin/main 2>&1', $resetOutput);
                 }
                 // Add reset output to main output (masked)
                 // ... (Detailed masking omitted for brevity in reset fallback for now)
            }
            
            // 2. Migrate Database
            $output[] = "\n--- Running Migrations ---";
            Artisan::call('migrate', ['--force' => true]);
            $output[] = Artisan::output();

            // 3. Clear Caches
            $output[] = "\n--- Clearing Caches ---";
            Artisan::call('optimize:clear');
            $output[] = Artisan::output();

            $outputString = implode("\n", $output);

            return back()->with('success', 'Update selesai!')->with('update_log', $outputString);

        } catch (\Exception $e) {
            return back()->with('error', 'Update gagal: ' . $e->getMessage());
        }
    }

    public function cacheClear()
    {
        Artisan::call('optimize:clear');
        return back()->with('success', 'Cache sistem berhasil dibersihkan.');
    }
}
